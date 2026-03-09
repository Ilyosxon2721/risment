<?php

namespace App\Notifications;

class NewTaskNotification extends PushNotification
{
    public function __construct(string $type, string $companyName)
    {
        parent::__construct(
            title: 'Risment',
            body: __('Новая задача: :type для :company', ['type' => $type, 'company' => $companyName]),
            url: '/manager/tasks',
        );
    }
}
