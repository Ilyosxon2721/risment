<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BillableOperation extends Model
{
    protected $fillable = [
        'company_id',
        'operation_type',
        'quantity',
        'unit_cost',
        'total_cost',
        'billed',
        'billing_invoice_id',
        'source_type',
        'source_id',
        'operation_date',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'billed' => 'boolean',
        'operation_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function billingInvoice(): BelongsTo
    {
        return $this->belongsTo(BillingInvoice::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeUnbilled($query)
    {
        return $query->where('billed', false);
    }

    public function scopeForPeriod($query, $start, $end)
    {
        return $query->whereBetween('operation_date', [$start, $end]);
    }
}
