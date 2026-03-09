<?php

namespace App\Notifications;

class InboundReceivedNotification extends PushNotification
{
    public function __construct(int $inboundId)
    {
        parent::__construct(
            title: 'Risment',
            body: __('Ваша поставка #:id принята на склад', ['id' => $inboundId]),
            url: "/cabinet/inbounds/{$inboundId}",
        );
    }
}
