<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingInvoiceLine extends Model
{
    protected $fillable = [
        'billing_invoice_id',
        'service_type',
        'description',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(BillingInvoice::class, 'billing_invoice_id');
    }

    public function getServiceTypeLabel(): string
    {
        return match ($this->service_type) {
            'subscription' => __('Subscription'),
            'storage' => __('Storage'),
            'shipment' => __('Shipment'),
            'receiving' => __('Receiving'),
            'return' => __('Return'),
            'other' => __('Other'),
            default => $this->service_type,
        };
    }
}
