<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingInvoice extends Model
{
    protected $fillable = [
        'company_id',
        'invoice_number',
        'period_start',
        'period_end',
        'subtotal',
        'tax',
        'total',
        'status',
        'issue_date',
        'due_date',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
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

    public function lines(): HasMany
    {
        return $this->hasMany(BillingInvoiceLine::class);
    }

    public function operations(): HasMany
    {
        return $this->hasMany(BillableOperation::class, 'billing_invoice_id');
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'draft' => __('Draft'),
            'issued' => __('Issued'),
            'paid' => __('Paid'),
            'overdue' => __('Overdue'),
            'cancelled' => __('Cancelled'),
            default => $this->status,
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'paid' => 'badge-success',
            'overdue' => 'badge-error',
            'issued' => 'badge-info',
            'draft' => 'badge-warning',
            default => 'badge-secondary',
        };
    }

    public static function generateNumber(int $companyId): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::where('company_id', $companyId)
            ->whereYear('created_at', $year)
            ->count() + 1;

        return sprintf('BIL-%s%s-%04d-%d', $year, $month, $companyId, $count);
    }
}
