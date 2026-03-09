<?php

namespace App\Notifications;

class TicketReplyNotification extends PushNotification
{
    public function __construct(int $ticketId)
    {
        parent::__construct(
            title: 'Risment',
            body: __('Новый ответ в тикете #:id', ['id' => $ticketId]),
            url: "/cabinet/tickets/{$ticketId}",
        );
    }
}
