<?php

namespace App\Observers;

use App\Models\Lead;
use App\Services\TelegramService;

class LeadObserver
{
    protected TelegramService $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Handle the Lead "created" event.
     */
    public function created(Lead $lead): void
    {
        // Send Telegram notification
        $this->telegram->notifyNewLead($lead);
    }
}
