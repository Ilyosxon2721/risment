<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorageSnapshot extends Model
{
    protected $fillable = [
        'company_id',
        'snapshot_date',
        'total_units',
        'total_pallets',
        'total_sqm',
        'daily_cost',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'total_sqm' => 'decimal:2',
        'daily_cost' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
