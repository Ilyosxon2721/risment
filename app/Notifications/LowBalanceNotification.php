<?php

namespace App\Notifications;

class LowBalanceNotification extends PushNotification
{
    public function __construct(string $companyName, string $balance)
    {
        parent::__construct(
            title: 'Risment',
            body: __('Баланс :company: :balance', ['company' => $companyName, 'balance' => $balance]),
            url: '/cabinet/billing',
        );
    }
}
