<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceCredential extends Model
{
    protected $fillable = [
        'company_id',
        'marketplace',
        'name',
        'wb_api_token',
        'wb_supplier_id',
        'ozon_client_id',
        'ozon_api_key',
        'uzum_api_token',
        'uzum_seller_id',
        'yandex_oauth_token',
        'yandex_campaign_id',
        'yandex_business_id',
        'is_active',
        'sellermind_account_id',
        'synced_to_sellermind_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'synced_to_sellermind_at' => 'datetime',
    ];

    protected $hidden = [
        'wb_api_token',
        'ozon_api_key',
        'uzum_api_token',
        'yandex_oauth_token',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getMarketplaceNameAttribute(): string
    {
        return match ($this->marketplace) {
            'wildberries' => 'Wildberries',
            'ozon' => 'Ozon',
            'uzum' => 'Uzum Market',
            'yandex_market' => 'Yandex Market',
            default => $this->marketplace,
        };
    }

    public function isSyncedToSellermind(): bool
    {
        return $this->sellermind_account_id !== null;
    }
}
