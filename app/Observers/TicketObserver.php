<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Services\TelegramService;

class TicketObserver
{
    protected TelegramService $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        // Load relationships for the notification
        $ticket->load(['company', 'user']);
        
        // Send Telegram notification
        $this->telegram->notifyNewTicket($ticket);
    }
}
