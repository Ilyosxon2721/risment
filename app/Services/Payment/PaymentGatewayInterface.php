<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    /**
     * Generate payment URL for invoice
     */
    public function generatePaymentUrl(int $invoiceId, float $amount): string;
    
    /**
     * Verify callback signature
     */
    public function verifySignature(array $data): bool;
    
    /**
     * Process payment callback
     */
    public function processCallback(array $data): array;
}
