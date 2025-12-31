<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanySettings extends Model
{
    protected $fillable = [
        'company_logo',
        'company_name',
        'phone',
        'email',
        'address_ru',
        'address_uz',
        'warehouse_address_ru',
        'warehouse_address_uz',
        'social_facebook',
        'social_instagram',
        'social_telegram',
        'stat_orders',
        'stat_sla',
        'stat_support',
        'stat_warehouse_size',
    ];

    // Singleton pattern - only one record allowed
    public static function current()
    {
        return static::firstOrCreate([], [
            'company_name' => 'RISMENT',
            'stat_orders' => '10 000+',
            'stat_sla' => '99%',
            'stat_support' => '24/7',
            'stat_warehouse_size' => '5 000+',
        ]);
    }

    /**
     * Get the logo URL
     */
    public function getLogoUrl(): ?string
    {
        if (!$this->company_logo) {
            return null;
        }
        
        return Storage::url($this->company_logo);
    }

    public function getAddress(): string
    {
        return app()->getLocale() === 'uz' 
            ? ($this->address_uz ?? $this->address_ru ?? '')
            : ($this->address_ru ?? '');
    }

    public function getWarehouseAddress(): string
    {
        return app()->getLocale() === 'uz' 
            ? ($this->warehouse_address_uz ?? $this->warehouse_address_ru ?? '')
            : ($this->warehouse_address_ru ?? '');
    }
}
