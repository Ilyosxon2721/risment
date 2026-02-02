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

        $stockData = $inventoryItems->map(function ($inv) {
            return [
                'risment_variant_id' => $inv->product_variant_id,
                'sku_code' => $inv->productVariant?->sku_code,
                'barcode' => $inv->productVariant?->barcode,
                'qty_total' => $inv->qty_total,
                'qty_reserved' => $inv->qty_reserved,
                'qty_available' => $inv->qty_total - $inv->qty_reserved,
            ];
        })->toArray();

        if (empty($stockData)) {
            return;
        }

        $payload = json_encode([
            'action' => 'stock_update',
            'link_token' => $link->link_token,
            'sellermind_company_id' => $link->sellermind_company_id,
            'items' => $stockData,
            'timestamp' => now()->toIso8601String(),
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
