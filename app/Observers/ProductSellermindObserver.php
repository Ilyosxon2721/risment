<?php

namespace App\Observers;

use App\Jobs\Sellermind\PushProductToSellermind;
use App\Models\Product;
use App\Models\SellermindAccountLink;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ProductSellermindObserver
{
    public function created(Product $product): void
    {
        if (!$this->shouldSync($product->company_id)) {
            return;
        }

        (new PushProductToSellermind($product->id, $product->company_id))->handle();
    }

    public function updated(Product $product): void
    {
        if (!$this->shouldSync($product->company_id)) {
            return;
        }

        (new PushProductToSellermind($product->id, $product->company_id))->handle();
    }

    public function deleted(Product $product): void
    {
        $link = SellermindAccountLink::where('company_id', $product->company_id)
            ->where('status', 'active')
            ->where('sync_products', true)
            ->first();

        if (!$link) {
            return;
        }

        $payload = json_encode([
            'event' => 'product.deleted',
            'link_token' => $link->link_token,
            'data' => [
                'product_id' => $product->id,
            ],
            'timestamp' => now()->toIso8601String(),
        ]);

        try {
            Redis::connection('integration')->rpush('sellermind:products', $payload);
            Log::channel('daily')->info('Pushed product.deleted to sellermind:products', [
                'product_id' => $product->id,
                'company_id' => $product->company_id,
            ]);
        } catch (\Exception $e) {
            Log::channel('daily')->error('Failed to push product.deleted to Redis', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function shouldSync(int $companyId): bool
    {
        return SellermindAccountLink::where('company_id', $companyId)
            ->where('status', 'active')
            ->where('sync_products', true)
            ->exists();
    }
}
