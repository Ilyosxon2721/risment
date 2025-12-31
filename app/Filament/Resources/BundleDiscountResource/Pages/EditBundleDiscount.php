<?php

namespace App\Filament\Resources\BundleDiscountResource\Pages;

use App\Filament\Resources\BundleDiscountResource;
use Filament\Resources\Pages\EditRecord;

class EditBundleDiscount extends EditRecord
{
    protected static string $resource = BundleDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
