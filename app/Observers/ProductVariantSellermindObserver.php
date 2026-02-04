<?php

namespace App\Observers;

use App\Jobs\Sellermind\PushProductToSellermind;
use App\Models\ProductVariant;
use App\Models\SellermindAccountLink;

class ProductVariantSellermindObserver
{
    public function saved(ProductVariant $variant): void
    {
        $this->syncParentProduct($variant);
    }

    public function deleted(ProductVariant $variant): void
    {
        $this->syncParentProduct($variant);
    }

    private function syncParentProduct(ProductVariant $variant): void
    {
        $product = $variant->product;

        if (!$product) {
            return;
        }

        if (!$this->shouldSync($product->company_id)) {
            return;
        }

        (new PushProductToSellermind($product->id, $product->company_id))->handle();
    }

    private function shouldSync(int $companyId): bool
    {
        return SellermindAccountLink::where('company_id', $companyId)
            ->where('status', 'active')
            ->where('sync_products', true)
            ->exists();
    }
}
