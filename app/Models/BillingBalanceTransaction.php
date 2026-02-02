<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BillingBalanceTransaction extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'amount',
        'balance_after',
        'description',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'topup' => __('Top-up'),
            'charge' => __('Charge'),
            'refund' => __('Refund'),
            'adjustment' => __('Adjustment'),
            default => $this->type,
        };
    }

    public function getTypeBadgeClass(): string
    {
        return match ($this->type) {
            'topup', 'refund' => 'badge-success',
            'charge' => 'badge-error',
            'adjustment' => 'badge-info',
            default => 'badge-secondary',
        };
    }
}
