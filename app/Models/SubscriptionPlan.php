<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubscriptionPlan extends Model
{
    // Pricing constants
    const PICK_PACK_FEE = 7000; // Per order pick&pack
    const DELIVERY_RATES = [
        'mgt' => 4000,
        'sgt' => 8000,
        'kgt' => 20000,
    ];
    
    protected $fillable = [
        'code',
        'name_ru',
        'name_uz',
        'description_ru',
        'description_uz',
        'price_month',
        'recommended_price_month',
        'is_custom',
        'min_price_month',
        'fbs_shipments_included',
        'sku_included',
        'storage_included_boxes',
        'storage_included_bags',
        'inbound_included_boxes',
        'shipping_included',
        'schedule_ru',
        'schedule_uz',
        'sla_cutoff_time',
        'priority_processing',
        'sla_high',
        'personal_manager',
        'over_fbs_shipment_fee', // Deprecated, kept for BC
        'over_fbs_mgt_fee',
        'over_fbs_sgt_fee',
        'over_fbs_kgt_fee',
        'over_storage_box_fee',
        'over_storage_bag_fee',
        'over_inbound_box_fee',
        'sort',
        'is_active',
    ];

    protected $casts = [
        'price_month' => 'decimal:2',
        'recommended_price_month' => 'decimal:2',
        'min_price_month' => 'decimal:2',
        'is_custom' => 'boolean',
        'shipping_included' => 'boolean',
        'priority_processing' => 'boolean',
        'sla_high' => 'boolean',
        'personal_manager' => 'boolean',
        'is_active' => 'boolean',
        'over_fbs_shipment_fee' => 'decimal:2',
        'over_fbs_mgt_fee' => 'decimal:2',
        'over_fbs_sgt_fee' => 'decimal:2',
        'over_fbs_kgt_fee' => 'decimal:2',
        'over_storage_box_fee' => 'decimal:2',
        'over_storage_bag_fee' => 'decimal:2',
        'over_inbound_box_fee' => 'decimal:2',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'subscription_plan_id');
    }

    public function limits(): HasOne
    {
        return $this->hasOne(PlanLimit::class, 'plan_id');
    }

    public function overageRules(): BelongsToMany
    {
        return $this->belongsToMany(OverageRule::class, 'plan_overage_rules');
    }

    /**
     * Get localized display name based on current locale
     */
    public function getName(): string
    {
        $locale = app()->getLocale();
        
        if ($locale === 'uz' && !empty($this->name_uz)) {
            return $this->name_uz;
        }
        
        if ($locale === 'ru' && !empty($this->name_ru)) {
            return $this->name_ru;
        }
        
        // Fallback to Russian name or code
        return $this->name_ru ?: $this->code;
    }
    
    /**
     * Get display name for specific locale
     */
    public function getDisplayName(string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        
        if ($locale === 'uz' && !empty($this->name_uz)) {
            return $this->name_uz;
        }
        
        return $this->name_ru ?: $this->code;
    }

    public function getDescription(): string
    {
        return app()->getLocale() === 'ru' ? $this->description_ru : $this->description_uz;
    }

    public function getSchedule(): ?string
    {
        return app()->getLocale() === 'ru' ? $this->schedule_ru : $this->schedule_uz;
    }

    /**
     * Calculate overage fees for given usage
     * Uses base pricing logic: overage per shipment = pick&pack + delivery (NO surcharge)
     * Storage is calculated per day
     */
    public function calculateOverage(
        int $mgtCount,
        int $sgtCount,
        int $kgtCount,
        int $storageBoxDays,
        int $storageBagDays,
        int $inboundBoxes,
        int $microCount = 0
    ): array {
        $totalOverage = 0;
        $breakdown = [];

        $totalShipments = $microCount + $mgtCount + $sgtCount + $kgtCount;

        // FBS shipments overage by size
        // Apply included shipments to benefit client: MICRO first, then MGT, SGT, KGT
        if (!$this->is_custom && $this->fbs_shipments_included && $totalShipments > $this->fbs_shipments_included) {
            $remainingIncluded = $this->fbs_shipments_included;

            // Apply to MICRO first (cheapest)
            $microIncluded = min($microCount, $remainingIncluded);
            $microOver = $microCount - $microIncluded;
            $remainingIncluded -= $microIncluded;

            // Apply to MGT
            $mgtIncluded = min($mgtCount, $remainingIncluded);
            $mgtOver = $mgtCount - $mgtIncluded;
            $remainingIncluded -= $mgtIncluded;

            // Apply remaining to SGT
            $sgtIncluded = min($sgtCount, $remainingIncluded);
            $sgtOver = $sgtCount - $sgtIncluded;
            $remainingIncluded -= $sgtIncluded;

            // Apply remaining to KGT
            $kgtIncluded = min($kgtCount, $remainingIncluded);
            $kgtOver = $kgtCount - $kgtIncluded;

            // Get MICRO overage fee from PricingService
            $microFeeRate = $this->over_fbs_micro_fee ?? app(\App\Services\PricingService::class)->getOverageRates()['shipments']['micro_fee'] ?? 4000;

            // Calculate overage fees using base rates (NO surcharge)
            $microFee = $microOver * $microFeeRate;
            $mgtFee = $mgtOver * $this->over_fbs_mgt_fee;
            $sgtFee = $sgtOver * $this->over_fbs_sgt_fee;
            $kgtFee = $kgtOver * $this->over_fbs_kgt_fee;

            $totalShipmentFee = $microFee + $mgtFee + $sgtFee + $kgtFee;

            if ($totalShipmentFee > 0) {
                $breakdown['shipments'] = [
                    'total_over' => $microOver + $mgtOver + $sgtOver + $kgtOver,
                    'micro' => ['count' => $microOver, 'fee' => $microFee],
                    'mgt' => ['count' => $mgtOver, 'fee' => $mgtFee],
                    'sgt' => ['count' => $sgtOver, 'fee' => $sgtFee],
                    'kgt' => ['count' => $kgtOver, 'fee' => $kgtFee],
                    'fee' => $totalShipmentFee,
                    'total' => $totalShipmentFee,
                ];
                $totalOverage += $totalShipmentFee;
            }
        }

        // Storage box-days overage
        if (!$this->is_custom && $this->storage_included_boxes && $storageBoxDays > $this->storage_included_boxes) {
            $overBoxDays = $storageBoxDays - $this->storage_included_boxes;
            $fee = $overBoxDays * $this->over_storage_box_fee;
            $breakdown['storage_boxes'] = [
                'days' => $overBoxDays,
                'fee' => $fee,
            ];
            $totalOverage += $fee;
        }

        // Storage bag-days overage
        if (!$this->is_custom && $this->storage_included_bags && $storageBagDays > $this->storage_included_bags) {
            $overBagDays = $storageBagDays - $this->storage_included_bags;
            $fee = $overBagDays * $this->over_storage_bag_fee;
            $breakdown['storage_bags'] = [
                'days' => $overBagDays,
                'fee' => $fee,
            ];
            $totalOverage += $fee;
        }

        // Inbound boxes overage
        if (!$this->is_custom && $this->inbound_included_boxes && $inboundBoxes > $this->inbound_included_boxes) {
            $overInbound = $inboundBoxes - $this->inbound_included_boxes;
            $fee = $overInbound * $this->over_inbound_box_fee;
            $breakdown['inbound_boxes'] = [
                'count' => $overInbound,
                'fee' => $fee,
            ];
            $totalOverage += $fee;
        }

        return [
            'total' => $totalOverage,
            'breakdown' => $breakdown,
        ];
    }
}
