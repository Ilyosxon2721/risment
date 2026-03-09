<?php

namespace App\Notifications;

class NewInvoiceNotification extends PushNotification
{
    public function __construct(int $invoiceId, string $amount)
    {
        parent::__construct(
            title: 'Risment',
            body: __('Выставлен счёт #:id на сумму :amount', ['id' => $invoiceId, 'amount' => $amount]),
            url: "/cabinet/billing/invoices/{$invoiceId}",
        );
    }
}
