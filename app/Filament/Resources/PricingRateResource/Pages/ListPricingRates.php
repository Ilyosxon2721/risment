<?php

namespace App\Filament\Resources\PricingRateResource\Pages;

use App\Filament\Resources\PricingRateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPricingRates extends ListRecords
{
    protected static string $resource = PricingRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
