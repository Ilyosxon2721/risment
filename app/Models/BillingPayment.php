<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingPayment extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    public const METHOD_PAYME = 'payme';
    public const METHOD_CLICK = 'click';
    public const METHOD_TRANSFER = 'transfer';
    public const METHOD_CASH = 'cash';
    public const METHOD_OTHER = 'other';

    protected $fillable = [
        'company_id',
        'invoice_id',
        'amount',
        'method',
        'paid_at',
        'external_ref',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(BillingInvoice::class, 'invoice_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getMethodOptions(): array
    {
        return [
            self::METHOD_PAYME => 'Payme',
            self::METHOD_CLICK => 'Click',
            self::METHOD_TRANSFER => __('Банковский перевод'),
            self::METHOD_CASH => __('Наличные'),
            self::METHOD_OTHER => __('Другое'),
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => __('Ожидает'),
            self::STATUS_COMPLETED => __('Завершён'),
            self::STATUS_FAILED => __('Ошибка'),
            self::STATUS_REFUNDED => __('Возврат'),
        ];
    }

    public function getMethodLabel(): string
    {
        return self::getMethodOptions()[$this->method] ?? $this->method;
    }

    public function getStatusLabel(): string
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    /**
     * Create payment and optionally apply to invoice
     */
    public static function recordPayment(
        int $companyId,
        int $amount,
        string $method,
        ?\DateTimeInterface $paidAt = null,
        ?int $invoiceId = null,
        ?string $externalRef = null,
        ?string $notes = null
    ): self {
        $payment = self::create([
            'company_id' => $companyId,
            'invoice_id' => $invoiceId,
            'amount' => $amount,
            'method' => $method,
            'paid_at' => $paidAt ?? now(),
            'external_ref' => $externalRef,
            'status' => self::STATUS_COMPLETED,
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);

        // If linked to invoice, check if fully paid
        if ($invoiceId) {
            $invoice = BillingInvoice::find($invoiceId);
            if ($invoice) {
                $totalPaid = self::where('invoice_id', $invoiceId)
                    ->where('status', self::STATUS_COMPLETED)
                    ->sum('amount');

                if ($totalPaid >= $invoice->total) {
                    $invoice->markPaid();
                }
            }
        }

        return $payment;
    }

    // Scopes

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
