<?php

namespace App\Filament\Resources\BillingPaymentResource\Pages;

use App\Filament\Resources\BillingPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBillingPayment extends ViewRecord
{
    protected static string $resource = BillingPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
