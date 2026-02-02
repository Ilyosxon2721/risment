<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingSubscription extends Model
{
    protected $fillable = [
        'company_id',
        'billing_plan_id',
        'started_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'started_at' => 'date',
        'expires_at' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function billingPlan(): BelongsTo
    {
        return $this->belongsTo(BillingPlan::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
