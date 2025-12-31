<?php

namespace App\Filament\Resources\TariffAuditLogResource\Pages;

use App\Filament\Resources\TariffAuditLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTariffAuditLog extends EditRecord
{
    protected static string $resource = TariffAuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
