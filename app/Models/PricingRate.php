<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingRate extends Model
{
    protected $fillable = [
        'code',
        'value',
        'unit_ru',
        'unit_uz',
        'description_ru',
        'description_uz',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Code is immutable after creation
    protected static function booted()
    {
        static::updating(function ($rate) {
            if ($rate->isDirty('code')) {
                throw new \Exception('Rate code cannot be changed after creation.');
            }
        });
    }
}
