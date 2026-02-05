<?php

namespace App\Filament\Resources\BillingItemResource\Pages;

use App\Filament\Resources\BillingItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBillingItems extends ListRecords
{
    protected static string $resource = BillingItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
