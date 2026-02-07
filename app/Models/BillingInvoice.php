<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingInvoice extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_PARTIALLY_PAID = 'partially_paid';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'company_id',
        'invoice_number',
        'period',
        'period_start',
        'period_end',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
        'issue_date',
        'due_date',
        'issued_at',
        'due_at',
        'paid_at',
        'cancelled_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'issue_date' => 'date',
        'due_date' => 'date',
        'issued_at' => 'datetime',
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'subtotal' => 'integer',
        'tax' => 'integer',
        'discount' => 'integer',
        'total' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(BillingInvoiceLine::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillingInvoiceItem::class, 'invoice_id');
    }

    public function billingItems(): HasMany
    {
        return $this->hasMany(BillingItem::class, 'invoice_id');
    }

    public function operations(): HasMany
    {
        return $this->hasMany(BillableOperation::class, 'billing_invoice_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate next invoice number
     */
    public static function generateNumber(int $companyId, ?string $period = null): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::where('company_id', $companyId)
            ->whereYear('created_at', $year)
            ->count() + 1;

        return sprintf('BIL-%s%s-%04d-%d', $year, $month, $companyId, $count);
    }

    /**
     * Create invoice from billing items for a period
     */
    public static function createFromBillingItems(int $companyId, string $period): self
    {
        $billingItems = BillingItem::forCompany($companyId)
            ->forPeriod($period)
            ->accrued()
            ->get();

        if ($billingItems->isEmpty()) {
            throw new \Exception('No accrued billing items for this period');
        }

        // Calculate period dates
        $periodDate = \Carbon\Carbon::createFromFormat('Y-m', $period);
        $periodStart = $periodDate->copy()->startOfMonth();
        $periodEnd = $periodDate->copy()->endOfMonth();

        $subtotal = $billingItems->sum('amount');

        // Create invoice
        $invoice = self::create([
            'company_id' => $companyId,
            'invoice_number' => self::generateNumber($companyId, $period),
            'period' => $period,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'status' => self::STATUS_DRAFT,
            'subtotal' => $subtotal,
            'tax' => 0,
            'discount' => 0,
            'total' => $subtotal,
            'issue_date' => now(),
            'due_date' => now()->addDays(15),
            'created_by' => auth()->id(),
        ]);

        // Create invoice items (snapshots)
        foreach ($billingItems as $billingItem) {
            BillingInvoiceItem::create([
                'invoice_id' => $invoice->id,
                'billing_item_id' => $billingItem->id,
                'scope' => $billingItem->scope,
                'title_ru' => $billingItem->title_ru,
                'title_uz' => $billingItem->title_uz,
                'unit_price' => $billingItem->unit_price,
                'qty' => $billingItem->qty,
                'amount' => $billingItem->amount,
            ]);

            // Mark billing item as invoiced
            $billingItem->markInvoiced($invoice->id);
        }

        return $invoice;
    }

    /**
     * Issue the invoice
     */
    public function issue(int $dueDays = 15): void
    {
        $this->update([
            'status' => self::STATUS_ISSUED,
            'issue_date' => now(),
            'issued_at' => now(),
            'due_date' => now()->addDays($dueDays),
            'due_at' => now()->addDays($dueDays),
        ]);
    }

    /**
     * Mark as paid
     */
    public function markPaid(): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }

    /**
     * Cancel the invoice
     */
    public function cancel(): void
    {
        // Revert billing items to accrued status
        $this->billingItems()->update([
            'status' => BillingItem::STATUS_ACCRUED,
            'invoice_id' => null,
        ]);

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'draft' => __('Черновик'),
            'issued' => __('Выставлен'),
            'partially_paid' => __('Частично оплачен'),
            'paid' => __('Оплачен'),
            'overdue' => __('Просрочен'),
            'cancelled' => __('Отменён'),
            default => $this->status,
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'paid' => 'badge-success',
            'partially_paid' => 'badge-warning',
            'overdue' => 'badge-error',
            'issued' => 'badge-info',
            'draft' => 'badge-warning',
            'cancelled' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_ISSUED
            && $this->due_date
            && $this->due_date->isPast();
    }
}
