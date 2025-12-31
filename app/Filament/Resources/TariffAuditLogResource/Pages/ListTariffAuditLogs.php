<?php

namespace App\Filament\Resources\TariffAuditLogResource\Pages;

use App\Filament\Resources\TariffAuditLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTariffAuditLogs extends ListRecords
{
    protected static string $resource = TariffAuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
