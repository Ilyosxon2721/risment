<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAddon extends Model
{
    protected $fillable = [
        'code',
        'scope',
        'title_ru',
        'title_uz',
        'unit_ru',
        'unit_uz',
        'pricing_type',
        'value',
        'meta',
        'description_ru',
        'description_uz',
        'is_active',
        'sort',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'meta' => 'array',
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];

    public const SCOPE_INBOUND = 'inbound';
    public const SCOPE_PICKPACK = 'pickpack';
    public const SCOPE_STORAGE = 'storage';
    public const SCOPE_SHIPPING = 'shipping';
    public const SCOPE_RETURNS = 'returns';
    public const SCOPE_OTHER = 'other';

    public const PRICING_FIXED = 'fixed';
    public const PRICING_BY_CATEGORY = 'by_category';
    public const PRICING_PERCENT = 'percent';
    public const PRICING_MANUAL = 'manual';

    public static function getScopeOptions(): array
    {
        return [
            self::SCOPE_INBOUND => __('Приёмка'),
            self::SCOPE_PICKPACK => __('Сборка'),
            self::SCOPE_STORAGE => __('Хранение'),
            self::SCOPE_SHIPPING => __('Отправка'),
            self::SCOPE_RETURNS => __('Возвраты'),
            self::SCOPE_OTHER => __('Другое'),
        ];
    }

    public static function getPricingTypeOptions(): array
    {
        return [
            self::PRICING_FIXED => __('Фиксированная'),
            self::PRICING_BY_CATEGORY => __('По категории (MICRO/MGT/SGT/KGT)'),
            self::PRICING_PERCENT => __('Процент'),
            self::PRICING_MANUAL => __('Ручной ввод'),
        ];
    }

    public function getTitle(): string
    {
        $locale = app()->getLocale();
        return $locale === 'uz' ? $this->title_uz : $this->title_ru;
    }

    public function getUnit(): string
    {
        $locale = app()->getLocale();
        return $locale === 'uz' ? $this->unit_uz : $this->unit_ru;
    }

    public function getDescription(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'uz' ? $this->description_uz : $this->description_ru;
    }

    /**
     * Calculate price for this addon
     *
     * @param string|null $category MICRO, MGT, SGT, KGT (for by_category pricing)
     * @param float $baseAmount Base amount (for percent pricing)
     * @param float|null $manualPrice Manual price override (for manual pricing)
     * @return float
     */
    public function calculatePrice(?string $category = null, float $baseAmount = 0, ?float $manualPrice = null): float
    {
        return match ($this->pricing_type) {
            self::PRICING_FIXED => (float) $this->value,
            self::PRICING_BY_CATEGORY => $this->getPriceByCategory($category),
            self::PRICING_PERCENT => $baseAmount * (float) $this->value,
            self::PRICING_MANUAL => $manualPrice ?? 0,
            default => 0,
        };
    }

    /**
     * Get price for a specific size category
     */
    public function getPriceByCategory(?string $category): float
    {
        if (!$category || !is_array($this->meta)) {
            return 0;
        }

        $category = strtoupper($category);
        return (float) ($this->meta[$category] ?? 0);
    }

    /**
     * Scope: active addons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: by scope type
     */
    public function scopeByScope($query, string $scope)
    {
        return $query->where('scope', $scope);
    }

    /**
     * Code is immutable after creation
     */
    protected static function booted()
    {
        static::updating(function ($addon) {
            if ($addon->isDirty('code')) {
                throw new \Exception('Addon code cannot be changed after creation.');
            }
        });
    }
}
