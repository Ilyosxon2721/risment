<?php

namespace App\Filament\Resources\ServiceAddonResource\Pages;

use App\Filament\Resources\ServiceAddonResource;
use App\Services\AddonService;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceAddon extends CreateRecord
{
    protected static string $resource = ServiceAddonResource::class;

    protected function afterCreate(): void
    {
        AddonService::clearCache();
    }
}
