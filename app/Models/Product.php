<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    const SYNC_STATUS_PENDING = 'pending';
    const SYNC_STATUS_SYNCED = 'synced';
    const SYNC_STATUS_ERROR = 'error';

    protected $fillable = [
        'company_id',
        'title',
        'short_description',
        'article',
        'description',
        'is_active',
        'sellermind_product_id',
        'sellermind_sync_status',
        'sellermind_sync_error',
        'sellermind_synced_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sellermind_synced_at' => 'datetime',
    ];

    public function isSyncedToSellermind(): bool
    {
        return $this->sellermind_sync_status === self::SYNC_STATUS_SYNCED;
    }

    public function getSyncStatusLabelAttribute(): string
    {
        return match($this->sellermind_sync_status) {
            self::SYNC_STATUS_SYNCED => 'Синхронизирован',
            self::SYNC_STATUS_ERROR => 'Ошибка',
            default => 'Ожидает',
        };
    }

    public function getSyncStatusColorAttribute(): string
    {
        return match($this->sellermind_sync_status) {
            self::SYNC_STATUS_SYNCED => 'green',
            self::SYNC_STATUS_ERROR => 'red',
            default => 'yellow',
        };
    }

    /**
     * Get the company that owns the product
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the variants for the product
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Scope active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
