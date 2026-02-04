<?php

namespace App\Console\Commands;

use App\Models\BillableOperation;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SellermindAccountLink;
use App\Models\ShipmentFbo;
use App\Models\ShipmentItem;
use App\Jobs\Sellermind\PushStockToSellermind;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ProcessSellermindQueues extends Command
{
    protected $signature = 'integration:process-sellermind {--once : Process one message per queue and exit}';
    protected $description = 'Process incoming messages from SellerMind Redis queues';

    private array $queues = [
        'risment:orders',
        'risment:stock',
        'risment:returns',
        'risment:link',
        'risment:marketplace_confirm',
    ];

    public function handle(): int
    {
        $this->info('Starting SellerMind queue processor...');
        $once = $this->option('once');

        while (true) {
            $processed = false;

            foreach ($this->queues as $queue) {
                try {
                    $message = Redis::connection('integration')->lpop($queue);
                    if ($message) {
                        $processed = true;
                        $this->processMessage($queue, $message);
                    }
                } catch (\Exception $e) {
                    Log::error("Error reading from $queue: " . $e->getMessage());
                    $this->error("Error on $queue: " . $e->getMessage());
                }
            }

            if ($once) {
                break;
            }

            if (!$processed) {
                usleep(500000); // 500ms pause when idle
            }
        }

        return Command::SUCCESS;
    }

    private function processMessage(string $queue, string $raw): void
    {
        $data = json_decode($raw, true);
        if (!$data) {
            Log::warning("Invalid JSON from $queue", ['raw' => substr($raw, 0, 500)]);
            return;
        }

        $this->info("Processing from $queue: " . ($data['action'] ?? 'unknown'));

        match ($queue) {
            'risment:orders' => $this->handleOrder($data),
            'risment:stock' => $this->handleStockUpdate($data),
            'risment:returns' => $this->handleReturn($data),
            'risment:link' => $this->handleLinkConfirmation($data),
            'risment:marketplace_confirm' => $this->handleMarketplaceConfirmation($data),
            default => Log::warning("Unknown queue: $queue"),
        };
    }

    private function resolveLink(array $data): ?SellermindAccountLink
    {
        $token = $data['link_token'] ?? null;
        if (!$token) {
            Log::warning('Message missing link_token');
            return null;
        }

        return SellermindAccountLink::where('link_token', $token)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Handle incoming order from SellerMind (marketplace order → shipment in RISMENT)
     */
    private function handleOrder(array $data): void
    {
        $event = $data['event'] ?? 'order.created';

        match ($event) {
            'order.created' => $this->createOrder($data),
            'order.cancelled' => $this->cancelOrder($data),
            default => Log::warning('Unknown order event: ' . $event),
        };
    }

    private function createOrder(array $data): void
    {
        $link = $this->resolveLink($data);
        if (!$link) {
            return;
        }

        $orderData = $data['data'] ?? [];
        $sellermindOrderId = $orderData['sellermind_order_id'] ?? null;

        // Check if already exists
        if ($sellermindOrderId) {
            $existing = ShipmentFbo::where('sellermind_order_id', $sellermindOrderId)->first();
            if ($existing) {
                $this->info("Order already exists: shipment #{$existing->id}");
                return;
            }
        }

        // Validate items — only include those with existing products in RISMENT
        $validItems = collect($orderData['items'] ?? [])->filter(function ($item) use ($link) {
            $productId = $item['risment_product_id'] ?? null;
            if (!$productId) {
                return false;
            }
            return Product::where('id', $productId)
                ->where('company_id', $link->company_id)
                ->exists();
        });

        if ($validItems->isEmpty()) {
            Log::warning('Order rejected: no valid RISMENT products', [
                'sellermind_order_id' => $sellermindOrderId,
            ]);
            return;
        }

        DB::transaction(function () use ($link, $orderData, $sellermindOrderId, $validItems) {
            $shipment = ShipmentFbo::create([
                'company_id' => $link->company_id,
                'marketplace' => $orderData['marketplace'] ?? 'unknown',
                'warehouse_name' => $orderData['warehouse'] ?? '',
                'planned_at' => isset($orderData['shipping']['deadline'])
                    ? $orderData['shipping']['deadline']
                    : now(),
                'status' => 'submitted',
                'sellermind_order_id' => $sellermindOrderId,
                'notes' => 'Auto-created from SellerMind order #' . $sellermindOrderId,
            ]);

            foreach ($validItems as $item) {
                ShipmentItem::create([
                    'shipment_id' => $shipment->id,
                    'sku_id' => $item['sku_id'] ?? null,
                    'product_variant_id' => $item['risment_variant_id'] ?? null,
                    'item_name' => $item['name'] ?? null,
                    'qty' => $item['quantity'] ?? $item['qty'] ?? 1,
                    'price' => $item['price'] ?? null,
                ]);
            }

            $totalItems = $validItems->sum(fn ($i) => $i['quantity'] ?? $i['qty'] ?? 1);
            BillableOperation::create([
                'company_id' => $link->company_id,
                'operation_type' => 'shipment',
                'quantity' => max(1, $totalItems),
                'unit_cost' => 0,
                'total_cost' => 0,
                'source_type' => ShipmentFbo::class,
                'source_id' => $shipment->id,
                'operation_date' => now()->toDateString(),
            ]);

            $this->info("Created shipment #{$shipment->id} from SellerMind order #{$sellermindOrderId} ({$validItems->count()} items)");
        });
    }

    private function cancelOrder(array $data): void
    {
        $link = $this->resolveLink($data);
        if (!$link) {
            return;
        }

        $sellermindOrderId = $data['data']['sellermind_order_id'] ?? null;
        if (!$sellermindOrderId) {
            return;
        }

        $shipment = ShipmentFbo::where('sellermind_order_id', $sellermindOrderId)
            ->where('company_id', $link->company_id)
            ->first();

        if ($shipment && $shipment->status !== 'shipped') {
            $shipment->update(['status' => 'cancelled']);
            $this->info("Cancelled shipment #{$shipment->id} (SellerMind order #{$sellermindOrderId})");
        }
    }

    /**
     * Handle stock update from SellerMind
     */
    private function handleStockUpdate(array $data): void
    {
        $link = $this->resolveLink($data);
        if (!$link || !$link->sync_stock) {
            return;
        }

        foreach ($data['items'] ?? [] as $item) {
            $variantId = $item['risment_variant_id'] ?? null;
            if (!$variantId) {
                continue;
            }

            $inventory = Inventory::where('company_id', $link->company_id)
                ->where('product_variant_id', $variantId)
                ->first();

            if ($inventory) {
                $inventory->update([
                    'qty_reserved' => $item['qty_reserved'] ?? $inventory->qty_reserved,
                ]);
            }
        }

        $this->info('Stock update processed for company #' . $link->company_id);
    }

    /**
     * Handle returns from SellerMind
     */
    private function handleReturn(array $data): void
    {
        $link = $this->resolveLink($data);
        if (!$link) {
            return;
        }

        DB::transaction(function () use ($link, $data) {
            $returnData = $data['data'] ?? [];
            $returnItems = $returnData['items'] ?? [];

            foreach ($returnItems as $item) {
                $variantId = $item['risment_variant_id'] ?? null;
                $qty = $item['qty'] ?? 1;

                if ($variantId && $qty > 0) {
                    $inventory = Inventory::firstOrCreate(
                        [
                            'company_id' => $link->company_id,
                            'product_variant_id' => $variantId,
                        ],
                        ['qty_total' => 0, 'qty_reserved' => 0]
                    );
                    $inventory->increment('qty_total', $qty);
                }
            }

            // Record billable operation for return
            $totalUnits = collect($returnItems)->sum('qty');
            if ($totalUnits > 0) {
                BillableOperation::create([
                    'company_id' => $link->company_id,
                    'operation_type' => 'return',
                    'quantity' => $totalUnits,
                    'unit_cost' => 0,
                    'total_cost' => 0,
                    'operation_date' => now()->toDateString(),
                ]);
            }

            // Push updated stock back to SellerMind
            (new PushStockToSellermind($link->company_id))->handle();
        });

        $this->info('Return processed for company #' . $link->company_id);
    }

    /**
     * Handle link confirmation from SellerMind
     */
    private function handleLinkConfirmation(array $data): void
    {
        $token = $data['link_token'] ?? null;
        if (!$token) {
            return;
        }

        $link = SellermindAccountLink::where('link_token', $token)->first();
        if (!$link) {
            Log::warning('Link token not found for confirmation', ['token' => substr($token, 0, 10) . '...']);
            return;
        }

        $link->update([
            'sellermind_user_id' => $data['sellermind_user_id'] ?? null,
            'sellermind_company_id' => $data['sellermind_company_id'] ?? null,
            'status' => 'active',
            'linked_at' => now(),
        ]);

        $this->info("Account linked: company #{$link->company_id} ↔ SellerMind company #{$link->sellermind_company_id}");
    }

    /**
     * Handle marketplace account confirmation from SellerMind.
     */
    private function handleMarketplaceConfirmation(array $data): void
    {
        $credentialId = $data['risment_credential_id'] ?? null;
        $sellermindAccountId = $data['sellermind_account_id'] ?? null;

        if (!$credentialId) {
            return;
        }

        $credential = \App\Models\MarketplaceCredential::find($credentialId);
        if (!$credential) {
            Log::warning('Marketplace credential not found', ['id' => $credentialId]);
            return;
        }

        $credential->update([
            'sellermind_account_id' => $sellermindAccountId,
            'synced_to_sellermind_at' => now(),
        ]);

        $this->info("Marketplace confirmed: credential #{$credentialId} → SellerMind account #{$sellermindAccountId}");
    }
}
