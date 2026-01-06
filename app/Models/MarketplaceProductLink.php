<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceProductLink extends Model
{
    protected $fillable = [
        'product_variant_id',
        'marketplace',
        'marketplace_sku',
        'marketplace_barcode',
        'sync_stock',
        'is_active',
    ];

    protected $casts = [
        'sync_stock' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the variant that owns the link
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get human-readable marketplace name
     */
    public function getMarketplaceNameAttribute(): string
    {
        return match($this->marketplace) {
            'uzum' => 'Uzum Market',
            'wildberries' => 'Wildberries',
            'ozon' => 'Ozon',
            'yandex' => 'Yandex Market',
            default => $this->marketplace,
        };
    }

    /**
     * Scope for specific marketplace
     */
    public function scopeForMarketplace($query, string $marketplace)
    {
        return $query->where('marketplace', $marketplace);
    }

    /**
     * Scope for active links
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for links with stock sync enabled
     */
    public function scopeSyncEnabled($query)
    {
        return $query->where('sync_stock', true);
    }
}
