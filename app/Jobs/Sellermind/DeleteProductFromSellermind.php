<?php

namespace App\Jobs\Sellermind;

use App\Models\SellermindAccountLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class DeleteProductFromSellermind implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

        $payload = json_encode([
            'event' => 'product.deleted',
            'link_token' => $link->link_token,
            'data' => [
                'product_id' => $this->productId,
            ],
            'timestamp' => now()->toIso8601String(),
        ]);

        try {
            Redis::connection('integration')->rpush('sellermind:products', $payload);
            Log::channel('daily')->info('Pushed product.deleted to sellermind:products', [
                'product_id' => $this->productId,
                'company_id' => $this->companyId,
            ]);
        } catch (\Exception $e) {
            Log::channel('daily')->error('Failed to push product.deleted to Redis', [
                'product_id' => $this->productId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
