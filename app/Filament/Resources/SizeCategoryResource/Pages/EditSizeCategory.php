<?php

namespace App\Filament\Resources\SizeCategoryResource\Pages;

use App\Filament\Resources\SizeCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSizeCategory extends EditRecord
{
    protected static string $resource = SizeCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
