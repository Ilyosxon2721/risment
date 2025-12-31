<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MarketplaceLogo extends Model
{
    protected $fillable = [
        'marketplace_code',
        'name_ru',
        'name_uz',
        'logo_image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getName(): string
    {
        return app()->getLocale() === 'uz' 
            ? ($this->name_uz ?? $this->name_ru)
            : $this->name_ru;
    }

    public function getLogoUrl(): string
    {
        if ($this->logo_image) {
            return Storage::url($this->logo_image);
        }
        
        // Fallback to existing logos
        $fallbacks = [
            'uzum' => 'images/logos/uzum.png',
            'wildberries' => 'images/logos/wildberries.svg',
            'ozon' => 'images/logos/ozon.svg',
            'yandex' => 'images/logos/yandex.svg',
        ];
        
        return asset($fallbacks[$this->marketplace_code] ?? 'images/logo-risment.png');
    }
}
