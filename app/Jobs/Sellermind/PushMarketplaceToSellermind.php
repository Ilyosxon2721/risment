<?php

namespace App\Jobs\Sellermind;

use App\Models\MarketplaceCredential;
use App\Models\SellermindAccountLink;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class PushMarketplaceToSellermind
{
    public function __construct(
        protected int $credentialId,
        protected int $companyId,
        protected string $event = 'marketplace.created',
    ) {}

    public function handle(): void
    {
        $link = SellermindAccountLink::where('company_id', $this->companyId)
            ->where('status', 'active')
            ->first();

        if (!$link) {
            return;
        }

        $credential = MarketplaceCredential::find($this->credentialId);
        if (!$credential) {
            return;
        }

        $payload = json_encode([
            'event' => $this->event,
            'timestamp' => now()->toIso8601String(),
            'link_token' => $link->link_token,
            'data' => [
                'risment_credential_id' => $credential->id,
                'marketplace' => $credential->marketplace,
                'name' => $credential->name,
                'credentials' => $this->getCredentials($credential),
                'is_active' => $credential->is_active,
            ],
        ]);

        try {
            Redis::connection('integration')->rpush('sellermind:marketplaces', $payload);
            Log::channel('daily')->info('Pushed marketplace credential to SellerMind', [
                'credential_id' => $this->credentialId,
                'marketplace' => $credential->marketplace,
            ]);
        } catch (\Exception $e) {
            Log::channel('daily')->error('Failed to push marketplace to Redis', [
                'credential_id' => $this->credentialId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function getCredentials(MarketplaceCredential $credential): array
    {
        return match ($credential->marketplace) {
            'wildberries' => [
                'api_token' => $credential->wb_api_token,
                'supplier_id' => $credential->wb_supplier_id,
            ],
            'ozon' => [
                'client_id' => $credential->ozon_client_id,
                'api_key' => $credential->ozon_api_key,
            ],
            'uzum' => [
                'api_token' => $credential->uzum_api_token,
                'seller_id' => $credential->uzum_seller_id,
            ],
            'yandex_market' => [
                'oauth_token' => $credential->yandex_oauth_token,
                'campaign_id' => $credential->yandex_campaign_id,
                'business_id' => $credential->yandex_business_id,
            ],
            default => [],
        };
    }
}
