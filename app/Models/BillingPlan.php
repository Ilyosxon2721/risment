<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingPlan extends Model
{
    protected $fillable = [
        'code',
        'name_ru',
        'name_uz',
        'description_ru',
        'description_uz',
        'features_ru',
        'features_uz',
        'monthly_fee',
        'storage_rate',
        'shipment_rate',
        'receiving_rate',
        'return_rate',
        'returns_included',
        'included_storage_units',
        'included_shipments',
        'included_receiving_units',
        'discount_percent',
        'min_orders_month',
        'max_orders_month',
        'max_storage_units',
        'free_storage_days',
        'free_return_days',
        'billing_model',
        'badge',
        'is_popular',
        'is_visible',
        'is_active',
        'sort',
    ];

    protected $casts = [
        'monthly_fee' => 'integer',
        'storage_rate' => 'integer',
        'shipment_rate' => 'integer',
        'receiving_rate' => 'integer',
        'return_rate' => 'integer',
        'returns_included' => 'boolean',
        'features_ru' => 'array',
        'features_uz' => 'array',
        'is_popular' => 'boolean',
        'is_visible' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(BillingSubscription::class);
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->subscriptions()->where('status', 'active');
    }

    public function getName(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'uz' && !empty($this->name_uz)) {
            return $this->name_uz;
        }
        return $this->name_ru ?: $this->code;
    }

    public function getDescription(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'uz' && !empty($this->description_uz)) {
            return $this->description_uz;
        }
        return $this->description_ru ?? '';
    }

    public function getFeatures(): array
    {
        $locale = app()->getLocale();
        if ($locale === 'uz' && !empty($this->features_uz)) {
            return $this->features_uz;
        }
        return $this->features_ru ?? [];
    }

    public function getFormattedMonthlyFee(): string
    {
        return number_format($this->monthly_fee, 0, '', ' ') . ' сум';
    }

    public function getBillingModelLabel(): string
    {
        return match ($this->billing_model) {
            'subscription' => 'Подписка',
            'monthly' => 'Помесячно',
            'payg' => 'По факту',
            default => $this->billing_model,
        };
    }

    /**
     * Check if order count is within plan limits
     */
    public function isWithinOrderLimit(int $orderCount): bool
    {
        if ($this->max_orders_month === null) {
            return true;
        }
        return $orderCount <= $this->max_orders_month;
    }

    /**
     * Check if storage is within plan limits
     */
    public function isWithinStorageLimit(int $storageUnits): bool
    {
        if ($this->max_storage_units === null) {
            return true;
        }
        return $storageUnits <= $this->max_storage_units;
    }

    /**
     * Calculate effective rate with discount
     */
    public function getEffectiveRate(string $rateType, int $baseRate): int
    {
        if ($this->discount_percent <= 0) {
            return $baseRate;
        }

        return (int) round($baseRate * (100 - $this->discount_percent) / 100);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort')->orderBy('monthly_fee');
    }
}
