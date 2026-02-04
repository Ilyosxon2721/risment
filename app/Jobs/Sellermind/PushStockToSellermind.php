<?php

namespace App\Jobs\Sellermind;

use App\Models\Inventory;
use App\Models\SellermindAccountLink;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class PushStockToSellermind
{
    public function __construct(
        protected int $companyId,
        protected ?int $productVariantId = null,
        protected string $reason = 'stock_updated',
    ) {}

    public function handle(): void
    {
        $link = SellermindAccountLink::where('company_id', $this->companyId)
            ->where('status', 'active')
            ->where('sync_stock', true)
            ->first();

        if (!$link) {
            return;
        }

        $query = Inventory::where('company_id', $this->companyId)
            ->with('productVariant.product');

        if ($this->productVariantId) {
            $query->where('product_variant_id', $this->productVariantId);
        }

        $inventoryItems = $query->get();

        $stockData = $inventoryItems
            ->filter(fn ($inv) => $inv->productVariant?->product?->sellermind_product_id)
            ->map(function ($inv) {
                return [
                    'risment_product_id' => $inv->productVariant?->product?->id,
                    'risment_variant_id' => $inv->product_variant_id,
                    'sku' => $inv->productVariant?->sku_code,
                    'barcode' => $inv->productVariant?->barcode,
                    'quantity' => $inv->qty_total,
                    'reserved' => $inv->qty_reserved,
                    'available' => $inv->qty_total - $inv->qty_reserved,
                ];
            })
            ->values()
            ->toArray();

        if (empty($stockData)) {
            return;
        }

        $payload = json_encode([
            'event' => 'stock.updated',
            'timestamp' => now()->toIso8601String(),
            'link_token' => $link->link_token,
            'data' => [
                'reason' => $this->reason,
                'stocks' => $stockData,
            ],
        ]);

        try {
            Redis::connection('integration')->rpush('sellermind:stock', $payload);
            Log::channel('daily')->info('Pushed stock update to sellermind:stock queue', [
                'company_id' => $this->companyId,
                'items_count' => count($stockData),
            ]);
        } catch (\Exception $e) {
            Log::channel('daily')->error('Failed to push stock to Redis', [
                'company_id' => $this->companyId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
