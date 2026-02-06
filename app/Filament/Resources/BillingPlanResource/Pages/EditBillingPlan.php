<?php

namespace App\Filament\Resources\BillingPlanResource\Pages;

use App\Filament\Resources\BillingPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBillingPlan extends EditRecord
{
    protected static string $resource = BillingPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->activeSubscriptions()->count() === 0),
        ];
    }
}
