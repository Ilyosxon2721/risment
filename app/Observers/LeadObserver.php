<?php

namespace App\Observers;

use App\Jobs\SendTelegramNotificationJob;
use App\Models\Lead;

class LeadObserver
{
    public function created(Lead $lead): void
    {
        SendTelegramNotificationJob::dispatch('notifyNewLead', [$lead]);
    }
}
