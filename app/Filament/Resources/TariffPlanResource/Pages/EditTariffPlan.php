<?php

namespace App\Filament\Resources\TariffPlanResource\Pages;

use App\Filament\Resources\TariffPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTariffPlan extends EditRecord
{
    protected static string $resource = TariffPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
