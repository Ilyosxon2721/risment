<?php

namespace App\Services;

use App\Models\BillingItem;
use App\Models\BillingInvoice;
use App\Models\Company;
use App\Models\Inbound;
use App\Models\PricingRate;
use App\Models\ServiceAddon;
use App\Models\ShipmentFbo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BillingService
{
    protected PricingService $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Get rate by code (cached)
     */
    protected function getRate(string $code): int
    {
        $rates = Cache::remember('billing.rates', 600, function () {
            return PricingRate::where('is_active', true)->pluck('value', 'code');
        });

        return (int) ($rates[$code] ?? 0);
    }

    /**
     * Accrue charges for completed inbound (reception)
     *
     * @param Inbound $inbound
     * @param array $addons Optional addon codes to bill (e.g., ['ADDON_INBOUND_PHOTO' => 5])
     */
    public function accrueForInbound(Inbound $inbound, array $addons = []): array
    {
        $items = [];

        // Calculate total boxes/items received
        $totalBoxes = $inbound->items->sum('qty_received');

        // Base inbound rate
        $inboundRate = $this->getRate('INBOUND_BOX');

        if ($inboundRate > 0 && $totalBoxes > 0) {
            $item = BillingItem::accrue(
                companyId: $inbound->company_id,
                scope: 'inbound',
                titleRu: 'Приёмка товаров',
                titleUz: 'Tovarlarni qabul qilish',
                unitPrice: $inboundRate,
                qty: $totalBoxes,
                sourceType: BillingItem::SOURCE_INBOUND,
                sourceId: $inbound->id,
                addonCode: 'INBOUND_BOX',
                comment: "Поставка #{$inbound->reference}",
                meta: ['boxes' => $totalBoxes],
                occurredAt: $inbound->received_at ?? now()
            );

            if ($item) {
                $items[] = $item;
            }
        }

        // Bill additional services
        foreach ($addons as $addonCode => $qty) {
            $addon = ServiceAddon::where('code', $addonCode)->active()->first();
            if ($addon && $qty > 0) {
                $price = $addon->calculatePrice();
                $item = BillingItem::accrue(
                    companyId: $inbound->company_id,
                    scope: 'inbound',
                    titleRu: $addon->title_ru,
                    titleUz: $addon->title_uz,
                    unitPrice: (int) $price,
                    qty: $qty,
                    sourceType: BillingItem::SOURCE_INBOUND,
                    sourceId: $inbound->id,
                    addonCode: $addonCode,
                    occurredAt: $inbound->received_at ?? now()
                );

                if ($item) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    /**
     * Accrue Pick&Pack charges for a shipment
     *
     * @param ShipmentFbo $shipment
     * @param array $addons Optional addon codes to bill
     */
    public function accrueForShipmentPickPack(ShipmentFbo $shipment, array $addons = []): array
    {
        $items = [];

        // Group items by size category
        $categoryCounts = $this->getShipmentCategoryCounts($shipment);

        foreach ($categoryCounts as $category => $data) {
            if ($data['count'] === 0) {
                continue;
            }

            $categoryUpper = strtoupper($category);

            // First item rate
            $firstRate = $this->getRate("PICKPACK_{$categoryUpper}_FIRST");
            if ($firstRate > 0) {
                $item = BillingItem::accrue(
                    companyId: $shipment->company_id,
                    scope: 'pickpack',
                    titleRu: "Сборка {$categoryUpper} (первая позиция)",
                    titleUz: "{$categoryUpper} yig'ish (birinchi pozitsiya)",
                    unitPrice: $firstRate,
                    qty: 1,
                    sourceType: BillingItem::SOURCE_SHIPMENT,
                    sourceId: $shipment->id,
                    addonCode: "PICKPACK_{$categoryUpper}_FIRST",
                    meta: ['category' => $category],
                    idempotencySuffix: $category . '_first'
                );

                if ($item) {
                    $items[] = $item;
                }
            }

            // Additional items
            if ($data['count'] > 1) {
                $addRate = $this->getRate("PICKPACK_{$categoryUpper}_ADD");
                if ($addRate > 0) {
                    $addCount = $data['count'] - 1;
                    $item = BillingItem::accrue(
                        companyId: $shipment->company_id,
                        scope: 'pickpack',
                        titleRu: "Сборка {$categoryUpper} (доп. позиции)",
                        titleUz: "{$categoryUpper} yig'ish (qo'shimcha pozitsiyalar)",
                        unitPrice: $addRate,
                        qty: $addCount,
                        sourceType: BillingItem::SOURCE_SHIPMENT,
                        sourceId: $shipment->id,
                        addonCode: "PICKPACK_{$categoryUpper}_ADD",
                        meta: ['category' => $category, 'additional_count' => $addCount],
                        idempotencySuffix: $category . '_add'
                    );

                    if ($item) {
                        $items[] = $item;
                    }
                }
            }
        }

        // Bill additional services (protection, gift wrap, etc.)
        foreach ($addons as $addonCode => $data) {
            $addon = ServiceAddon::where('code', $addonCode)->active()->first();
            if (!$addon) {
                continue;
            }

            $qty = is_array($data) ? ($data['qty'] ?? 1) : $data;
            $category = is_array($data) ? ($data['category'] ?? null) : null;

            $price = $addon->calculatePrice($category);
            if ($price > 0 && $qty > 0) {
                $item = BillingItem::accrue(
                    companyId: $shipment->company_id,
                    scope: 'pickpack',
                    titleRu: $addon->title_ru,
                    titleUz: $addon->title_uz,
                    unitPrice: (int) $price,
                    qty: $qty,
                    sourceType: BillingItem::SOURCE_SHIPMENT,
                    sourceId: $shipment->id,
                    addonCode: $addonCode,
                    meta: ['category' => $category]
                );

                if ($item) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    /**
     * Accrue delivery charges for a dispatched shipment
     *
     * @param ShipmentFbo $shipment
     * @param array $addons Optional addon codes to bill (insurance, COD, etc.)
     */
    public function accrueForShipmentDelivery(ShipmentFbo $shipment, array $addons = []): array
    {
        $items = [];

        // Group items by size category
        $categoryCounts = $this->getShipmentCategoryCounts($shipment);

        foreach ($categoryCounts as $category => $data) {
            if ($data['count'] === 0) {
                continue;
            }

            $categoryUpper = strtoupper($category);
            $deliveryRate = $this->getRate("DELIVERY_{$categoryUpper}");

            if ($deliveryRate > 0) {
                $item = BillingItem::accrue(
                    companyId: $shipment->company_id,
                    scope: 'shipping',
                    titleRu: "Доставка {$categoryUpper}",
                    titleUz: "{$categoryUpper} yetkazib berish",
                    unitPrice: $deliveryRate,
                    qty: $data['count'],
                    sourceType: BillingItem::SOURCE_SHIPMENT,
                    sourceId: $shipment->id,
                    addonCode: "DELIVERY_{$categoryUpper}",
                    meta: ['category' => $category],
                    idempotencySuffix: 'delivery_' . $category
                );

                if ($item) {
                    $items[] = $item;
                }
            }
        }

        // Bill shipping addons (insurance, COD, express, etc.)
        foreach ($addons as $addonCode => $data) {
            $addon = ServiceAddon::where('code', $addonCode)->active()->first();
            if (!$addon) {
                continue;
            }

            $qty = is_array($data) ? ($data['qty'] ?? 1) : $data;
            $baseAmount = is_array($data) ? ($data['base_amount'] ?? 0) : 0;
            $category = is_array($data) ? ($data['category'] ?? null) : null;

            $price = $addon->calculatePrice($category, $baseAmount);
            if ($price > 0 && $qty > 0) {
                $item = BillingItem::accrue(
                    companyId: $shipment->company_id,
                    scope: 'shipping',
                    titleRu: $addon->title_ru,
                    titleUz: $addon->title_uz,
                    unitPrice: (int) $price,
                    qty: $qty,
                    sourceType: BillingItem::SOURCE_SHIPMENT,
                    sourceId: $shipment->id,
                    addonCode: $addonCode,
                    meta: ['category' => $category, 'base_amount' => $baseAmount]
                );

                if ($item) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    /**
     * Accrue charges for a return
     *
     * @param mixed $return Return model instance
     * @param array $addons Optional addon codes to bill
     */
    public function accrueForReturn($return, array $addons = []): array
    {
        $items = [];

        // Calculate return logistics (reverse delivery)
        $category = $this->determineReturnCategory($return);
        $categoryUpper = strtoupper($category);

        $deliveryRate = $this->getRate("DELIVERY_{$categoryUpper}");
        if ($deliveryRate > 0) {
            $item = BillingItem::accrue(
                companyId: $return->company_id,
                scope: 'returns',
                titleRu: "Обратная логистика {$categoryUpper}",
                titleUz: "{$categoryUpper} qaytish logistikasi",
                unitPrice: $deliveryRate,
                qty: 1,
                sourceType: BillingItem::SOURCE_RETURN,
                sourceId: $return->id,
                addonCode: "RETURN_DELIVERY_{$categoryUpper}",
                meta: ['category' => $category]
            );

            if ($item) {
                $items[] = $item;
            }
        }

        // Bill return addons (inspection, repack, etc.)
        foreach ($addons as $addonCode => $data) {
            $addon = ServiceAddon::where('code', $addonCode)->active()->first();
            if (!$addon) {
                continue;
            }

            $qty = is_array($data) ? ($data['qty'] ?? 1) : $data;
            $category = is_array($data) ? ($data['category'] ?? null) : null;

            $price = $addon->calculatePrice($category);
            if ($price > 0 && $qty > 0) {
                $item = BillingItem::accrue(
                    companyId: $return->company_id,
                    scope: 'returns',
                    titleRu: $addon->title_ru,
                    titleUz: $addon->title_uz,
                    unitPrice: (int) $price,
                    qty: $qty,
                    sourceType: BillingItem::SOURCE_RETURN,
                    sourceId: $return->id,
                    addonCode: $addonCode,
                    meta: ['category' => $category]
                );

                if ($item) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    /**
     * Accrue daily storage charges for a company
     * Called by storage:accrue-daily command
     */
    public function accrueStorageDaily(int $companyId, string $date): array
    {
        $items = [];

        // Get storage snapshot for this company
        $snapshot = $this->getStorageSnapshot($companyId, $date);

        // Storage rates
        $boxDayRate = $this->getRate('STORAGE_BOX_DAY');
        $bagDayRate = $this->getRate('STORAGE_BAG_DAY');

        // Boxes
        if ($boxDayRate > 0 && $snapshot['boxes'] > 0) {
            $item = BillingItem::accrue(
                companyId: $companyId,
                scope: 'storage',
                titleRu: 'Хранение (коробки)',
                titleUz: 'Saqlash (qutlar)',
                unitPrice: $boxDayRate,
                qty: $snapshot['boxes'],
                sourceType: BillingItem::SOURCE_STORAGE_DAILY,
                sourceId: null,
                addonCode: 'STORAGE_BOX_DAY',
                meta: ['date' => $date, 'boxes' => $snapshot['boxes']],
                idempotencySuffix: $date . '_boxes',
                occurredAt: \Carbon\Carbon::parse($date)
            );

            if ($item) {
                $items[] = $item;
            }
        }

        // Bags
        if ($bagDayRate > 0 && $snapshot['bags'] > 0) {
            $item = BillingItem::accrue(
                companyId: $companyId,
                scope: 'storage',
                titleRu: 'Хранение (мешки)',
                titleUz: 'Saqlash (sumkalar)',
                unitPrice: $bagDayRate,
                qty: $snapshot['bags'],
                sourceType: BillingItem::SOURCE_STORAGE_DAILY,
                sourceId: null,
                addonCode: 'STORAGE_BAG_DAY',
                meta: ['date' => $date, 'bags' => $snapshot['bags']],
                idempotencySuffix: $date . '_bags',
                occurredAt: \Carbon\Carbon::parse($date)
            );

            if ($item) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * Create manual billing item
     */
    public function accrueManual(
        int $companyId,
        string $scope,
        string $titleRu,
        string $titleUz,
        int $unitPrice,
        float $qty,
        ?string $comment = null
    ): BillingItem {
        return BillingItem::createManual(
            $companyId,
            $scope,
            $titleRu,
            $titleUz,
            $unitPrice,
            $qty,
            $comment
        );
    }

    /**
     * Generate invoice for a company and period
     */
    public function generateInvoice(int $companyId, string $period): BillingInvoice
    {
        return DB::transaction(function () use ($companyId, $period) {
            return BillingInvoice::createFromBillingItems($companyId, $period);
        });
    }

    /**
     * Get billing summary for current month (live ledger)
     */
    public function getCurrentMonthSummary(int $companyId): array
    {
        $period = now()->format('Y-m');

        return [
            'period' => $period,
            'totals_by_scope' => BillingItem::getTotalsByScope($companyId, $period),
            'grand_total' => BillingItem::forCompany($companyId)
                ->forPeriod($period)
                ->notVoid()
                ->sum('amount'),
            'item_count' => BillingItem::forCompany($companyId)
                ->forPeriod($period)
                ->notVoid()
                ->count(),
        ];
    }

    /**
     * Get shipment items grouped by size category
     */
    protected function getShipmentCategoryCounts(ShipmentFbo $shipment): array
    {
        $counts = [
            'micro' => ['count' => 0, 'items' => []],
            'mgt' => ['count' => 0, 'items' => []],
            'sgt' => ['count' => 0, 'items' => []],
            'kgt' => ['count' => 0, 'items' => []],
        ];

        foreach ($shipment->items as $item) {
            $variant = $item->variant ?? null;
            if (!$variant) {
                continue;
            }

            // Calculate dimension sum
            $dimensions = ($variant->length ?? 0) + ($variant->width ?? 0) + ($variant->height ?? 0);
            $category = $this->pricingService->getDimensionCategory($dimensions);

            $counts[$category]['count'] += $item->qty ?? 1;
            $counts[$category]['items'][] = $item->id;
        }

        return $counts;
    }

    /**
     * Determine category for a return
     */
    protected function determineReturnCategory($return): string
    {
        // TODO: Implement based on actual return model structure
        // For now, default to MGT
        return 'mgt';
    }

    /**
     * Get storage snapshot for a company on a specific date
     * This should calculate actual storage units from inventory
     */
    protected function getStorageSnapshot(int $companyId, string $date): array
    {
        // TODO: Implement actual storage calculation from inventory
        // This is a placeholder - should query inventory table
        // and exclude returns within free storage period

        return [
            'boxes' => 0,
            'bags' => 0,
            'pallets' => 0,
        ];
    }

    /**
     * Clear billing cache
     */
    public static function clearCache(): void
    {
        Cache::forget('billing.rates');
    }
}
