<?php

namespace App\Observers;

use App\Jobs\Sellermind\DeleteProductFromSellermind;
use App\Jobs\Sellermind\PushProductToSellermind;
use App\Models\Product;
use App\Models\SellermindAccountLink;

class ProductSellermindObserver
{
    public function created(Product $product): void
    {
        if (!$this->shouldSync($product->company_id)) {
            return;
        }

        PushProductToSellermind::dispatch($product->id, $product->company_id);
    }

    public function updated(Product $product): void
    {
        if (!$this->shouldSync($product->company_id)) {
            return;
        }

        PushProductToSellermind::dispatch($product->id, $product->company_id);
    }

    public function deleted(Product $product): void
    {
        if (!$this->shouldSync($product->company_id)) {
            return;
        }

        DeleteProductFromSellermind::dispatch($product->id, $product->company_id);
    }

    private function shouldSync(int $companyId): bool
    {
        return SellermindAccountLink::where('company_id', $companyId)
            ->where('status', 'active')
            ->where('sync_products', true)
            ->exists();
    }
}
