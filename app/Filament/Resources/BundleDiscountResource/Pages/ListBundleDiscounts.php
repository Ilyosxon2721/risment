<?php

namespace App\Filament\Resources\BundleDiscountResource\Pages;

use App\Filament\Resources\BundleDiscountResource;
use Filament\Resources\Pages\ListRecords;

class ListBundleDiscounts extends ListRecords
{
    protected static string $resource = BundleDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
