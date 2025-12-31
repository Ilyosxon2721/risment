<?php

namespace App\Observers;

use App\Models\PricingRate;
use App\Models\TariffAuditLog;
use App\Services\PricingService;

class PricingRateObserver
{
    public function updated(PricingRate $rate): void
    {
        TariffAuditLog::create([
            'user_id' => auth()->id(),
            'entity_type' => PricingRate::class,
            'entity_id' => $rate->id,
            'before_json' => $rate->getOriginal(),
            'after_json' => $rate->getAttributes(),
        ]);
        
        // Clear pricing cache
        PricingService::clearCache();
    }

    public function created(PricingRate $rate): void
    {
        TariffAuditLog::create([
            'user_id' => auth()->id(),
            'entity_type' => PricingRate::class,
            'entity_id' => $rate->id,
            'before_json' => null,
            'after_json' => $rate->getAttributes(),
        ]);
        
        PricingService::clearCache();
    }

    public function deleted(PricingRate $rate): void
    {
        TariffAuditLog::create([
            'user_id' => auth()->id(),
            'entity_type' => PricingRate::class,
            'entity_id' => $rate->id,
            'before_json' => $rate->getAttributes(),
            'after_json' => null,
        ]);
        
        PricingService::clearCache();
    }
}
