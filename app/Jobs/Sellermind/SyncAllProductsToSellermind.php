<?php

namespace App\Jobs\Sellermind;

use App\Models\Product;
use App\Models\SellermindAccountLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncAllProductsToSellermind implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
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

        $productIds = Product::where('company_id', $this->companyId)
            ->where('is_active', true)
            ->pluck('id');

        foreach ($productIds as $productId) {
            PushProductToSellermind::dispatch($productId, $this->companyId);
        }

        Log::info("Bulk product sync dispatched: {$productIds->count()} products for company #{$this->companyId}");
    }
}
