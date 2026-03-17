<?php

namespace App\Observers;

use App\Jobs\SendTelegramNotificationJob;
use App\Models\Ticket;

class TicketObserver
{
    public function created(Ticket $ticket): void
    {
        $ticket->load(['company', 'user']);

        SendTelegramNotificationJob::dispatch('notifyNewTicket', [$ticket]);
    }
}
