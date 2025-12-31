<?php

namespace App\Filament\Resources\SizeCategoryResource\Pages;

use App\Filament\Resources\SizeCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSizeCategories extends ListRecords
{
    protected static string $resource = SizeCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
