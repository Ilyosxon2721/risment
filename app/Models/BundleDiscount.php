<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BundleDiscount extends Model
{
    protected $fillable = [
        'type',
        'marketplaces_count',
        'discount_percent',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'discount_percent' => 'decimal:2',
        'marketplaces_count' => 'integer',
    ];

    /**
     * Get management discount percentage by marketplaces count
     *
     * @param int $count Number of marketplaces (2, 3, or 4)
     * @return float Discount percentage (0 if not found or count is 1)
     */
    public static function getManagementDiscount(int $count): float
    {
        if ($count <= 1) {
            return 0;
        }

        return (float) self::where('type', 'management')
            ->where('marketplaces_count', $count)
            ->where('is_active', true)
            ->value('discount_percent') ?? 0;
    }

    /**
     * Scope to active discounts only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
