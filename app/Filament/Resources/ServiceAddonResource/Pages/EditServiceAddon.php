<?php

namespace App\Filament\Resources\ServiceAddonResource\Pages;

use App\Filament\Resources\ServiceAddonResource;
use App\Services\AddonService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceAddon extends EditRecord
{
    protected static string $resource = ServiceAddonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        AddonService::clearCache();
    }
}
