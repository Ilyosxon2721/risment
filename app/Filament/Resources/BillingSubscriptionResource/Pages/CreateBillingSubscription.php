<?php

namespace App\Filament\Resources\BillingSubscriptionResource\Pages;

use App\Filament\Resources\BillingSubscriptionResource;
use App\Models\BillingSubscription;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBillingSubscription extends CreateRecord
{
    protected static string $resource = BillingSubscriptionResource::class;

    protected function beforeCreate(): void
    {
        // Check if company already has active subscription
        $existingActive = BillingSubscription::where('company_id', $this->data['company_id'])
            ->where('status', 'active')
            ->exists();

        if ($existingActive) {
            Notification::make()
                ->title('У компании уже есть активная подписка')
                ->body('Сначала отмените или приостановите текущую подписку.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
