<?php

namespace App\Filament\Resources\BillingItemResource\Pages;

use App\Filament\Resources\BillingItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBillingItem extends ViewRecord
{
    protected static string $resource = BillingItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => $this->record->status === 'accrued'),
        ];
    }
}
