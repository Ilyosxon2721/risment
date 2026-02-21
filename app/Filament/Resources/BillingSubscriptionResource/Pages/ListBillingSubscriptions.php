<?php

namespace App\Filament\Resources\BillingSubscriptionResource\Pages;

use App\Filament\Resources\BillingSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBillingSubscriptions extends ListRecords
{
    protected static string $resource = BillingSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
