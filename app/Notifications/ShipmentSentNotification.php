<?php

namespace App\Notifications;

class ShipmentSentNotification extends PushNotification
{
    public function __construct(int $shipmentId)
    {
        parent::__construct(
            title: 'Risment',
            body: __('Отгрузка #:id передана в доставку', ['id' => $shipmentId]),
            url: "/cabinet/shipments/{$shipmentId}",
        );
    }
}
