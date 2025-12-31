<?php

namespace App\Filament\Resources\ShipmentFboResource\Pages;

use App\Filament\Resources\ShipmentFboResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShipmentFbo extends EditRecord
{
    protected static string $resource = ShipmentFboResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
