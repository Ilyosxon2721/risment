<?php

namespace App\Filament\Resources\BillingSubscriptionResource\Pages;

use App\Filament\Resources\BillingSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBillingSubscription extends EditRecord
{
    protected static string $resource = BillingSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->status !== 'active'),
        ];
    }
}
