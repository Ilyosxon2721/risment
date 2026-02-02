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
        'monthly_fee',
        'storage_rate',
        'shipment_rate',
        'receiving_rate',
        'return_rate',
        'returns_included',
        'included_storage_units',
        'included_shipments',
        'included_receiving_units',
        'billing_model',
        'is_active',
        'sort',
    ];

    protected $casts = [
        'monthly_fee' => 'decimal:2',
        'storage_rate' => 'decimal:2',
        'shipment_rate' => 'decimal:2',
        'receiving_rate' => 'decimal:2',
        'return_rate' => 'decimal:2',
        'returns_included' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(BillingSubscription::class);
    }

    public function getName(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'uz' && !empty($this->name_uz)) {
            return $this->name_uz;
        }
        return $this->name_ru ?: $this->code;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
