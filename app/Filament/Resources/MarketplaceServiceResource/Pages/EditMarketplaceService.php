<?php

namespace App\Filament\Resources\MarketplaceServiceResource\Pages;

use App\Filament\Resources\MarketplaceServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMarketplaceService extends EditRecord
{
    protected static string $resource = MarketplaceServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
