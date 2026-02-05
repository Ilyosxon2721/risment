<?php

namespace App\Services;

use App\Models\BillingItem;
use App\Models\ServiceAddon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AddonService
{
    protected const CACHE_TTL = 600; // 10 minutes

    /**
     * Get all active addons (cached)
     */
    public function getActiveAddons(): Collection
    {
        return Cache::remember('service_addons.active', self::CACHE_TTL, function () {
            return ServiceAddon::active()
                ->orderBy('scope')
                ->orderBy('sort')
                ->get();
        });
    }

    /**
     * Get active addons by scope
     */
    public function getAddonsByScope(string $scope): Collection
    {
        return $this->getActiveAddons()->where('scope', $scope)->values();
    }

    /**
     * Get addon by code
     */
    public function getAddonByCode(string $code): ?ServiceAddon
    {
        return $this->getActiveAddons()->firstWhere('code', $code);
    }

    /**
     * Calculate price for an addon
     *
     * @param string $code Addon code
     * @param string|null $category Size category (MICRO/MGT/SGT/KGT)
     * @param float $baseAmount Base amount for percent calculation
     * @param float|null $manualPrice Manual price override
     * @return float
     */
    public function calculateAddonPrice(
        string $code,
        ?string $category = null,
        float $baseAmount = 0,
        ?float $manualPrice = null
    ): float {
        $addon = $this->getAddonByCode($code);

        if (!$addon) {
            return 0;
        }

        return $addon->calculatePrice($category, $baseAmount, $manualPrice);
    }

    /**
     * Calculate total for multiple addons
     *
     * @param array $selections Array of ['code' => qty] or ['code' => ['qty' => x, 'category' => 'MGT']]
     * @param float $baseAmount Base amount for percent addons
     * @return array ['items' => [...], 'total' => int]
     */
    public function calculateAddonsTotal(array $selections, float $baseAmount = 0): array
    {
        $items = [];
        $total = 0;

        foreach ($selections as $code => $data) {
            $addon = $this->getAddonByCode($code);
            if (!$addon) {
                continue;
            }

            $qty = is_array($data) ? ($data['qty'] ?? 1) : $data;
            $category = is_array($data) ? ($data['category'] ?? null) : null;
            $manualPrice = is_array($data) ? ($data['manual_price'] ?? null) : null;

            $unitPrice = $addon->calculatePrice($category, $baseAmount, $manualPrice);
            $amount = (int) round($unitPrice * $qty);

            $items[] = [
                'addon' => $addon,
                'code' => $code,
                'title' => $addon->getTitle(),
                'unit' => $addon->getUnit(),
                'unit_price' => $unitPrice,
                'qty' => $qty,
                'amount' => $amount,
            ];

            $total += $amount;
        }

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * Bill addons to a company
     *
     * @param int $companyId
     * @param array $selections Array of addon selections with qty
     * @param string|null $sourceType
     * @param int|null $sourceId
     * @param string|null $category Size category
     * @param float $baseAmount Base amount for percent
     * @return array Created billing items
     */
    public function billAddons(
        int $companyId,
        array $selections,
        ?string $sourceType = null,
        ?int $sourceId = null,
        ?string $category = null,
        float $baseAmount = 0
    ): array {
        $billingItems = [];

        foreach ($selections as $code => $data) {
            $addon = $this->getAddonByCode($code);
            if (!$addon) {
                continue;
            }

            $qty = is_array($data) ? ($data['qty'] ?? 1) : $data;
            $itemCategory = is_array($data) ? ($data['category'] ?? $category) : $category;
            $manualPrice = is_array($data) ? ($data['manual_price'] ?? null) : null;
            $comment = is_array($data) ? ($data['comment'] ?? null) : null;

            $unitPrice = (int) $addon->calculatePrice($itemCategory, $baseAmount, $manualPrice);

            $billingItems[] = BillingItem::createFromAddon(
                $companyId,
                $addon,
                $qty,
                $unitPrice,
                $sourceType,
                $sourceId,
                $comment
            );
        }

        return $billingItems;
    }

    /**
     * Get billing summary for a company and period
     */
    public function getBillingSummary(int $companyId, string $period): array
    {
        $items = BillingItem::forCompany($companyId)
            ->forPeriod($period)
            ->orderBy('scope')
            ->orderBy('created_at')
            ->get();

        $byScope = [];
        $grandTotal = 0;

        foreach (ServiceAddon::getScopeOptions() as $scope => $label) {
            $scopeItems = $items->where('scope', $scope);
            $scopeTotal = $scopeItems->sum('amount');

            $byScope[$scope] = [
                'label' => $label,
                'items' => $scopeItems->values(),
                'total' => $scopeTotal,
            ];

            $grandTotal += $scopeTotal;
        }

        return [
            'period' => $period,
            'by_scope' => $byScope,
            'grand_total' => $grandTotal,
        ];
    }

    /**
     * Get addons for calculator display (grouped by scope)
     */
    public function getAddonsForCalculator(): array
    {
        $addons = $this->getActiveAddons();
        $result = [];

        foreach (ServiceAddon::getScopeOptions() as $scope => $label) {
            $scopeAddons = $addons->where('scope', $scope)->values();

            if ($scopeAddons->isNotEmpty()) {
                $result[$scope] = [
                    'label' => $label,
                    'addons' => $scopeAddons->map(function ($addon) {
                        return [
                            'code' => $addon->code,
                            'title' => $addon->getTitle(),
                            'unit' => $addon->getUnit(),
                            'pricing_type' => $addon->pricing_type,
                            'value' => $addon->value,
                            'meta' => $addon->meta,
                            'description' => $addon->getDescription(),
                        ];
                    })->toArray(),
                ];
            }
        }

        return $result;
    }

    /**
     * Clear cache
     */
    public static function clearCache(): void
    {
        Cache::forget('service_addons.active');
    }
}
