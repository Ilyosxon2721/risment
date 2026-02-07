<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingItem extends Model
{
    // Status constants
    public const STATUS_ACCRUED = 'accrued';
    public const STATUS_VOID = 'void';
    public const STATUS_INVOICED = 'invoiced';

    // Source types
    public const SOURCE_INBOUND = 'inbound';
    public const SOURCE_SHIPMENT = 'shipment';
    public const SOURCE_RETURN = 'return';
    public const SOURCE_STORAGE_DAILY = 'storage_daily';
    public const SOURCE_MANUAL = 'manual';

    protected $fillable = [
        'company_id',
        'period',
        'billed_at',
        'occurred_at',
        'scope',
        'source_type',
        'source_id',
        'addon_code',
        'title_ru',
        'title_uz',
        'unit_price',
        'qty',
        'amount',
        'status',
        'invoice_id',
        'idempotency_key',
        'comment',
        'meta',
        'created_by',
    ];

    protected $casts = [
        'billed_at' => 'datetime',
        'occurred_at' => 'datetime',
        'unit_price' => 'integer',
        'qty' => 'decimal:2',
        'amount' => 'integer',
        'meta' => 'array',
    ];

    protected $attributes = [
        'status' => self::STATUS_ACCRUED,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function addon(): BelongsTo
    {
        return $this->belongsTo(ServiceAddon::class, 'addon_code', 'code');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(BillingInvoice::class, 'invoice_id');
    }

    public function getTitle(): string
    {
        $locale = app()->getLocale();
        return $locale === 'uz' ? $this->title_uz : $this->title_ru;
    }

    /**
     * Generate idempotency key to prevent duplicate billing
     */
    public static function generateIdempotencyKey(string $sourceType, ?int $sourceId, ?string $addonCode = null, ?string $suffix = null): string
    {
        $parts = [$sourceType, $sourceId ?? 'null', $addonCode ?? 'base'];
        if ($suffix) {
            $parts[] = $suffix;
        }
        return implode(':', $parts);
    }

    /**
     * Check if a billing item with given idempotency key already exists
     */
    public static function idempotencyKeyExists(string $idempotencyKey): bool
    {
        return self::where('idempotency_key', $idempotencyKey)->exists();
    }

    /**
     * Create billing item from addon with snapshot
     */
    public static function createFromAddon(
        int $companyId,
        ServiceAddon $addon,
        float $qty,
        int $unitPrice,
        ?string $sourceType = null,
        ?int $sourceId = null,
        ?string $comment = null,
        ?int $createdBy = null,
        ?array $meta = null,
        ?\DateTimeInterface $occurredAt = null
    ): self {
        $idempotencyKey = self::generateIdempotencyKey($sourceType ?? 'addon', $sourceId, $addon->code);

        // Check idempotency
        if (self::idempotencyKeyExists($idempotencyKey)) {
            return self::where('idempotency_key', $idempotencyKey)->first();
        }

        return self::create([
            'company_id' => $companyId,
            'period' => now()->format('Y-m'),
            'billed_at' => now(),
            'occurred_at' => $occurredAt ?? now(),
            'scope' => $addon->scope,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'addon_code' => $addon->code,
            'title_ru' => $addon->title_ru,
            'title_uz' => $addon->title_uz,
            'unit_price' => $unitPrice,
            'qty' => $qty,
            'amount' => (int) round($unitPrice * $qty),
            'status' => self::STATUS_ACCRUED,
            'idempotency_key' => $idempotencyKey,
            'comment' => $comment,
            'meta' => $meta,
            'created_by' => $createdBy ?? auth()->id(),
        ]);
    }

    /**
     * Create manual billing item (not from addon catalog)
     */
    public static function createManual(
        int $companyId,
        string $scope,
        string $titleRu,
        string $titleUz,
        int $unitPrice,
        float $qty,
        ?string $comment = null,
        ?int $createdBy = null,
        ?array $meta = null
    ): self {
        return self::create([
            'company_id' => $companyId,
            'period' => now()->format('Y-m'),
            'billed_at' => now(),
            'occurred_at' => now(),
            'scope' => $scope,
            'source_type' => self::SOURCE_MANUAL,
            'addon_code' => null,
            'title_ru' => $titleRu,
            'title_uz' => $titleUz,
            'unit_price' => $unitPrice,
            'qty' => $qty,
            'amount' => (int) round($unitPrice * $qty),
            'status' => self::STATUS_ACCRUED,
            'comment' => $comment,
            'meta' => $meta,
            'created_by' => $createdBy ?? auth()->id(),
        ]);
    }

    /**
     * Create accrual with idempotency check
     */
    public static function accrue(
        int $companyId,
        string $scope,
        string $titleRu,
        string $titleUz,
        int $unitPrice,
        float $qty,
        string $sourceType,
        ?int $sourceId = null,
        ?string $addonCode = null,
        ?string $comment = null,
        ?array $meta = null,
        ?string $idempotencySuffix = null,
        ?\DateTimeInterface $occurredAt = null
    ): ?self {
        $idempotencyKey = self::generateIdempotencyKey($sourceType, $sourceId, $addonCode, $idempotencySuffix);

        // Check idempotency
        if (self::idempotencyKeyExists($idempotencyKey)) {
            return null;
        }

        return self::create([
            'company_id' => $companyId,
            'period' => now()->format('Y-m'),
            'billed_at' => now(),
            'occurred_at' => $occurredAt ?? now(),
            'scope' => $scope,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'addon_code' => $addonCode,
            'title_ru' => $titleRu,
            'title_uz' => $titleUz,
            'unit_price' => $unitPrice,
            'qty' => $qty,
            'amount' => (int) round($unitPrice * $qty),
            'status' => self::STATUS_ACCRUED,
            'idempotency_key' => $idempotencyKey,
            'comment' => $comment,
            'meta' => $meta,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Void this billing item
     */
    public function void(): bool
    {
        if ($this->status === self::STATUS_INVOICED) {
            return false; // Cannot void invoiced items
        }

        $this->update(['status' => self::STATUS_VOID]);
        return true;
    }

    /**
     * Mark as invoiced
     */
    public function markInvoiced(int $invoiceId): void
    {
        $this->update([
            'status' => self::STATUS_INVOICED,
            'invoice_id' => $invoiceId,
        ]);
    }

    // Scopes

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForPeriod($query, string $period)
    {
        return $query->where('period', $period);
    }

    public function scopeByScope($query, string $scope)
    {
        return $query->where('scope', $scope);
    }

    public function scopeAccrued($query)
    {
        return $query->where('status', self::STATUS_ACCRUED);
    }

    public function scopeNotVoid($query)
    {
        return $query->where('status', '!=', self::STATUS_VOID);
    }

    /**
     * Get totals grouped by scope for a company and period
     */
    public static function getTotalsByScope(int $companyId, string $period): array
    {
        return self::forCompany($companyId)
            ->forPeriod($period)
            ->notVoid()
            ->selectRaw('scope, SUM(amount) as total')
            ->groupBy('scope')
            ->pluck('total', 'scope')
            ->toArray();
    }

    /**
     * Auto-calculate amount before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->amount = (int) round($item->unit_price * $item->qty);
        });
    }
}
