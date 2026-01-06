<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceService extends Model
{
    protected $fillable = [
        'service_group',
        'marketplace',
        'code',
        'name_ru',
        'name_uz',
        'description_ru',
        'description_uz',
        'unit_ru',
        'unit_uz',
        'price',
        'sku_limit',
        'sort',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sku_limit' => 'integer',
        'sort' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getName(): string
    {
        return app()->getLocale() === 'ru' ? $this->name_ru : $this->name_uz;
    }

    public function getShortName(): string
    {
        // For packages like "Управление на Uzum до 100 SKU" return just "До 100 SKU"
        if ($this->sku_limit) {
            return "До {$this->sku_limit} SKU";
        }
        return $this->getName();
    }

    public function getDescription(): ?string
    {
        return app()->getLocale() === 'ru' ? $this->description_ru : $this->description_uz;
    }

    public function getUnit(): string
    {
        return app()->getLocale() === 'ru' ? $this->unit_ru : $this->unit_uz;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('service_group', $group);
    }

    public function scopeByMarketplace($query, ?string $marketplace = null)
    {
        if ($marketplace) {
            return $query->where(function ($q) use ($marketplace) {
                $q->where('marketplace', $marketplace)
                  ->orWhere('marketplace', 'all')
                  ->orWhereNull('marketplace');
            });
        }
        return $query;
    }
}
