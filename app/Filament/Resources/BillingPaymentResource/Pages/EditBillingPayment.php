<?php

namespace App\Filament\Resources\BillingPaymentResource\Pages;

use App\Filament\Resources\BillingPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBillingPayment extends EditRecord
{
    protected static string $resource = BillingPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->hasRole('admin')),
        ];
    }
}
