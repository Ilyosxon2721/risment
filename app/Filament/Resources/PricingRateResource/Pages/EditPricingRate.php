<?php

namespace App\Filament\Resources\PricingRateResource\Pages;

use App\Filament\Resources\PricingRateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPricingRate extends EditRecord
{
    protected static string $resource = PricingRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
