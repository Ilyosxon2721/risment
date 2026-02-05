<?php

namespace App\Filament\Resources\BillingPaymentResource\Pages;

use App\Filament\Resources\BillingPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBillingPayments extends ListRecords
{
    protected static string $resource = BillingPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
