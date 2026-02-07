<?php

namespace App\Filament\Resources\BillingInvoiceResource\Pages;

use App\Filament\Resources\BillingInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBillingInvoices extends ListRecords
{
    protected static string $resource = BillingInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
