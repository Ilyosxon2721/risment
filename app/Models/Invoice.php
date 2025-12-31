<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'company_id', 'invoice_number', 'status',
        'issue_date', 'due_date',
        'subtotal', 'tax', 'total', 'notes'
    ];
    
    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];
    
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
    
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }
    
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'paid' => 'badge-success',
            'overdue' => 'badge-error',
            'sent' => 'badge-info',
            default => 'badge-secondary'
        };
    }
    
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'draft' => __('Draft'),
            'sent' => __('Sent'),
            'paid' => __('Paid'),
            'overdue' => __('Overdue'),
            'cancelled' => __('Cancelled'),
            default => $this->status
        };
    }
    
    public function getPaidAmount(): float
    {
        return $this->payments()->sum('amount');
    }
    
    public function getRemainingAmount(): float
    {
        return max(0, $this->total - $this->getPaidAmount());
    }
}
