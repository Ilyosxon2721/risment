<?php

namespace App\Jobs\Sellermind;

use App\Models\Product;
use App\Models\SellermindAccountLink;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class PushProductToSellermind
{
    public function __construct(
        protected int $productId,
        protected int $companyId,
    ) {}

    public function handle(): void
    {
        $link = SellermindAccountLink::where('company_id', $this->companyId)
            ->where('status', 'active')
            ->where('sync_products', true)
            ->first();

        if (!$link) {
            return;
        }

        $product = Product::with(['variants.images', 'variants.marketplaceLinks'])->find($this->productId);
        if (!$product) {
            return;
        }

        $variants = $product->variants->map(function ($variant) {
            return [
                'risment_variant_id' => $variant->id,
                'variant_name' => $variant->variant_name,
                'sku_code' => $variant->sku_code,
                'barcode' => $variant->barcode,
                'price' => (float) $variant->price,
                'cost_price' => (float) $variant->cost_price,
                'weight' => (float) $variant->weight,
                'dims' => [
                    'l' => (float) $variant->dims_l,
                    'w' => (float) $variant->dims_w,
                    'h' => (float) $variant->dims_h,
                ],
                'is_active' => $variant->is_active,
            ];
        })->toArray();

        $payload = json_encode([
            'action' => $product->sellermind_product_id ? 'update' : 'create',
            'link_token' => $link->link_token,
            'sellermind_company_id' => $link->sellermind_company_id,
            'risment_product_id' => $product->id,
            'sellermind_product_id' => $product->sellermind_product_id,
            'data' => [
                'title' => $product->title,
                'article' => $product->article,
                'description' => $product->description,
                'is_active' => $product->is_active,
                'variants' => $variants,
            ],
            'timestamp' => now()->toIso8601String(),
        ]);

        try {
            Redis::connection('integration')->rpush('sellermind:products', $payload);
            Log::channel('daily')->info('Pushed product to sellermind:products queue', [
                'product_id' => $this->productId,
                'company_id' => $this->companyId,
            ]);
        } catch (\Exception $e) {
            Log::channel('daily')->error('Failed to push product to Redis', [
                'product_id' => $this->productId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
