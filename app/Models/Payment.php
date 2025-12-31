<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'company_id', 'invoice_id', 'amount',
        'payment_date', 'method', 'reference', 'notes'
    ];
    
    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];
    
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
    
    public function getMethodLabel(): string
    {
        return match($this->method) {
            'cash' => __('Cash'),
            'bank_transfer' => __('Bank Transfer'),
            'card' => __('Card'),
            'payme' => 'Payme',
            'click' => 'Click',
            default => $this->method
        };
    }
}
