<?php

namespace App\Filament\Resources\SurchargeTierResource\Pages;

use App\Filament\Resources\SurchargeTierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSurchargeTier extends EditRecord
{
    protected static string $resource = SurchargeTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
