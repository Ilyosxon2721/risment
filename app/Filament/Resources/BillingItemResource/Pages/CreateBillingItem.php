<?php

namespace App\Filament\Resources\BillingItemResource\Pages;

use App\Filament\Resources\BillingItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBillingItem extends CreateRecord
{
    protected static string $resource = BillingItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['amount'] = (int) round(($data['unit_price'] ?? 0) * ($data['qty'] ?? 1));

        return $data;
    }
}
