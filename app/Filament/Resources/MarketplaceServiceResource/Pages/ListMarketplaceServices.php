<?php

namespace App\Filament\Resources\MarketplaceServiceResource\Pages;

use App\Filament\Resources\MarketplaceServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarketplaceServices extends ListRecords
{
    protected static string $resource = MarketplaceServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
