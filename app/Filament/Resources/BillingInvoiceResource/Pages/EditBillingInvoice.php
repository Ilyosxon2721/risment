<?php

namespace App\Filament\Resources\BillingInvoiceResource\Pages;

use App\Filament\Resources\BillingInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBillingInvoice extends EditRecord
{
    protected static string $resource = BillingInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
