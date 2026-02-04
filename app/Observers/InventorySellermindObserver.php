<?php

namespace App\Observers;

use App\Jobs\Sellermind\PushStockToSellermind;
use App\Models\Inventory;
use App\Models\SellermindAccountLink;

class InventorySellermindObserver
{
    public function updated(Inventory $inventory): void
    {
        $this->syncStock($inventory);
    }

    public function created(Inventory $inventory): void
    {
        $this->syncStock($inventory);
    }

    private function syncStock(Inventory $inventory): void
    {
        if (!$inventory->product_variant_id) {
            return;
        }

        $variant = $inventory->productVariant;
        if (!$variant) {
            return;
        }

        $product = $variant->product;
        if (!$product || !$product->sellermind_product_id) {
            return;
        }

        if (!$this->shouldSync($product->company_id)) {
            return;
        }

        (new PushStockToSellermind($product->company_id, $variant->id))->handle();
    }

    private function shouldSync(int $companyId): bool
    {
        return SellermindAccountLink::where('company_id', $companyId)
            ->where('status', 'active')
            ->where('sync_stock', true)
            ->exists();
    }
}
