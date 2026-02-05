<?php

namespace App\Filament\Resources\BillingItemResource\Pages;

use App\Filament\Resources\BillingItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBillingItem extends EditRecord
{
    protected static string $resource = BillingItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['amount'] = (int) round(($data['unit_price'] ?? 0) * ($data['qty'] ?? 1));

        return $data;
    }
}
