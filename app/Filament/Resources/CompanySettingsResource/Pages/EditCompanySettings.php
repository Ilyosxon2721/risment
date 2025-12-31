<?php

namespace App\Filament\Resources\CompanySettingsResource\Pages;

use App\Filament\Resources\CompanySettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanySettings extends EditRecord
{
    protected static string $resource = CompanySettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
