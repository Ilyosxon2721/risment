<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyUsage extends Model
{
    protected $fillable = [
        'company_id',
        'month',
        'fbs_shipments_count',
        'inbound_boxes_count',
        'storage_boxes_peak',
        'storage_bags_peak',
    ];

    protected $casts = [
        'fbs_shipments_count' => 'integer',
        'inbound_boxes_count' => 'integer',
        'storage_boxes_peak' => 'integer',
        'storage_bags_peak' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get or create usage for company and month
     */
    public static function getForCompanyMonth(int $companyId, string $month): self
    {
        return self::firstOrCreate(
            ['company_id' => $companyId, 'month' => $month],
            [
                'fbs_shipments_count' => 0,
                'inbound_boxes_count' => 0,
                'storage_boxes_peak' => 0,
                'storage_bags_peak' => 0,
            ]
        );
    }

    /**
     * Get current month string (YYYY-MM)
     */
    public static function getCurrentMonth(): string
    {
        return now()->format('Y-m');
    }
}
