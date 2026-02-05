<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingItem extends Model
{
    protected $fillable = [
        'company_id',
        'period',
        'billed_at',
        'scope',
        'source_type',
        'source_id',
        'addon_code',
        'title_ru',
        'title_uz',
        'unit_price',
        'qty',
        'amount',
        'comment',
        'created_by',
    ];

    protected $casts = [
        'billed_at' => 'datetime',
        'unit_price' => 'integer',
        'qty' => 'decimal:2',
        'amount' => 'integer',
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

    public function getTitle(): string
    {
        $locale = app()->getLocale();
        return $locale === 'uz' ? $this->title_uz : $this->title_ru;
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
        ?int $createdBy = null
    ): self {
        return self::create([
            'company_id' => $companyId,
            'period' => now()->format('Y-m'),
            'billed_at' => now(),
            'scope' => $addon->scope,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'addon_code' => $addon->code,
            'title_ru' => $addon->title_ru,
            'title_uz' => $addon->title_uz,
            'unit_price' => $unitPrice,
            'qty' => $qty,
            'amount' => (int) round($unitPrice * $qty),
            'comment' => $comment,
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
        ?int $createdBy = null
    ): self {
        return self::create([
            'company_id' => $companyId,
            'period' => now()->format('Y-m'),
            'billed_at' => now(),
            'scope' => $scope,
            'addon_code' => null,
            'title_ru' => $titleRu,
            'title_uz' => $titleUz,
            'unit_price' => $unitPrice,
            'qty' => $qty,
            'amount' => (int) round($unitPrice * $qty),
            'comment' => $comment,
            'created_by' => $createdBy ?? auth()->id(),
        ]);
    }

    /**
     * Scope: by company
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope: by period (YYYY-MM)
     */
    public function scopeForPeriod($query, string $period)
    {
        return $query->where('period', $period);
    }

    /**
     * Scope: by scope type
     */
    public function scopeByScope($query, string $scope)
    {
        return $query->where('scope', $scope);
    }

    /**
     * Get totals grouped by scope for a company and period
     */
    public static function getTotalsByScope(int $companyId, string $period): array
    {
        return self::forCompany($companyId)
            ->forPeriod($period)
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
