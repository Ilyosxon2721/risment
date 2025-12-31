<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurchargeTier extends Model
{
    protected $fillable = [
        'min_shipments',
        'max_shipments',
        'surcharge_percent',
        'is_active',
        'sort',
    ];

    protected $casts = [
        'min_shipments' => 'integer',
        'max_shipments' => 'integer',
        'surcharge_percent' => 'decimal:2',
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];

    // Validation: tiers should not overlap
    protected static function booted()
    {
        static::saving(function ($tier) {
            // Check for overlapping tiers
            $overlapping = self::where('id', '!=', $tier->id)
                ->where('is_active', true)
                ->where(function ($query) use ($tier) {
                    $query->where(function ($q) use ($tier) {
                        // Min is between another tier's range
                        $q->where('min_shipments', '<=', $tier->min_shipments);
                        if ($tier->max_shipments !== null) {
                            $q->where(function ($sq) use ($tier) {
                                $sq->whereNull('max_shipments')
                                    ->orWhere('max_shipments', '>=', $tier->min_shipments);
                            });
                        }
                    })->orWhere(function ($q) use ($tier) {
                        // Max is between another tier's range
                        if ($tier->max_shipments !== null) {
                            $q->where('min_shipments', '<=', $tier->max_shipments)
                                ->where(function ($sq) use ($tier) {
                                    $sq->whereNull('max_shipments')
                                        ->orWhere('max_shipments', '>=', $tier->max_shipments);
                                });
                        }
                    });
                })
                ->exists();

            if ($overlapping) {
                throw new \Exception('Surcharge tier ranges cannot overlap.');
            }
        });
    }
}
