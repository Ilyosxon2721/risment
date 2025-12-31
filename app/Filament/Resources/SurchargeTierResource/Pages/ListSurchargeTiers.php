<?php

namespace App\Filament\Resources\SurchargeTierResource\Pages;

use App\Filament\Resources\SurchargeTierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSurchargeTiers extends ListRecords
{
    protected static string $resource = SurchargeTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
