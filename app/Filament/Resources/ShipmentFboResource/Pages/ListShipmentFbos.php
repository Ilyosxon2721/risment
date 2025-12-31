<?php

namespace App\Filament\Resources\ShipmentFboResource\Pages;

use App\Filament\Resources\ShipmentFboResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShipmentFbos extends ListRecords
{
    protected static string $resource = ShipmentFboResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
