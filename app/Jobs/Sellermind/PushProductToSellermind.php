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
        $product = Product::with(['variants.images', 'variants.marketplaceLinks'])->find($this->productId);
        if (!$product) {
            return;
        }

        $link = SellermindAccountLink::where('company_id', $this->companyId)
            ->where('status', 'active')
            ->where('sync_products', true)
            ->first();

        if (!$link) {
            $product->update([
                'sellermind_sync_status' => 'pending',
                'sellermind_sync_error' => 'Нет активной связки с SellerMind',
            ]);
            return;
        }

        $variants = $product->variants->map(function ($variant) {
            return [
                'risment_variant_id' => $variant->id,
                'name' => $variant->variant_name,
                'sku' => $variant->sku_code,
                'barcode' => $variant->barcode,
                'price' => (float) $variant->price,
                'cost_price' => (float) $variant->cost_price,
                'weight' => (float) $variant->weight,
                'length' => (float) $variant->dims_l,
                'width' => (float) $variant->dims_w,
                'height' => (float) $variant->dims_h,
                'is_active' => (bool) $variant->is_active,
            ];
        })->toArray();

        $images = $product->variants
            ->flatMap(fn ($variant) => $variant->images)
            ->map(fn ($image) => $image->url)
            ->unique()
            ->values()
            ->toArray();

        $payload = json_encode([
            'event' => $product->sellermind_product_id ? 'product.updated' : 'product.created',
            'timestamp' => now()->toIso8601String(),
            'link_token' => $link->link_token,
            'data' => [
                'product_id' => $product->id,
                'name' => $product->title,
                'article' => $product->article,
                'description' => $product->description,
                'short_description' => $product->short_description,
                'brand_name' => null,
                'category' => null,
                'is_active' => (bool) $product->is_active,
                'images' => $images,
                'variants' => $variants,
            ],
        ]);

        try {
            Redis::connection('integration')->rpush('sellermind:products', $payload);

            $product->update([
                'sellermind_sync_status' => 'pending',
                'sellermind_sync_error' => null,
            ]);

            Log::channel('daily')->info('Pushed product to sellermind:products queue', [
                'product_id' => $this->productId,
                'company_id' => $this->companyId,
            ]);
        } catch (\Exception $e) {
            $product->update([
                'sellermind_sync_status' => 'error',
                'sellermind_sync_error' => 'Ошибка отправки: ' . $e->getMessage(),
            ]);

            Log::channel('daily')->error('Failed to push product to Redis', [
                'product_id' => $this->productId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
