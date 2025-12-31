<?php

namespace App\Services\Payment;

use App\Models\PaymentTransaction;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class ClickService implements PaymentGatewayInterface
{
    private string $merchantId;
    private string $serviceId;
    private string $secretKey;
    private string $merchantUserId;

    public function __construct()
    {
        $this->merchantId = config('payment.click.merchant_id');
        $this->serviceId = config('payment.click.service_id');
        $this->secretKey = config('payment.click.secret_key');
        $this->merchantUserId = config('payment.click.merchant_user_id');
    }

    /**
     * Generate Click payment URL
     */
    public function generatePaymentUrl(int $invoiceId, float $amount): string
    {
        $params = [
            'service_id' => $this->serviceId,
            'merchant_id' => $this->merchantId,
            'amount' => $amount,
            'transaction_param' => $invoiceId,
            'return_url' => route('payment.click.return'),
        ];

        $url = 'https://my.click.uz/services/pay?' . http_build_query($params);
        
        return $url;
    }

    /**
     * Verify Click callback signature
     */
    public function verifySignature(array $data): bool
    {
        $signString = implode('', [
            $data['click_trans_id'] ?? '',
            $this->serviceId,
            $this->secretKey,
            $data['merchant_trans_id'] ?? '',
            $data['amount'] ?? '',
            $data['action'] ?? '',
            $data['sign_time'] ?? ''
        ]);

        $signKey =md5($signString);
        
        return $signKey === ($data['sign_string'] ?? '');
    }

    /**
     * Process Click callback
     * 
     * @param array $data Callback data from Click
     * @return array Response for Click
     */
    public function processCallback(array $data): array
    {
        try {
            // Verify signature
            if (!$this->verifySignature($data)) {
                Log::error('Click: Invalid signature', $data);
                return $this->errorResponse(-1, 'Invalid signature');
            }

            $action = $data['action'] ?? 0;

            if ($action == 0) {
                // Prepare
                return $this->handlePrepare($data);
            } elseif ($action == 1) {
                // Complete
                return $this->handleComplete($data);
            }

            return $this->errorResponse(-3, 'Unknown action');
            
        } catch (\Exception $e) {
            Log::error('Click callback error: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse(-9, 'System error');
        }
    }

    /**
     * Handle prepare callback (action = 0)
     */
    private function handlePrepare(array $data): array
    {
        $invoiceId = $data['merchant_trans_id'] ?? null;
        $amount = $data['amount'] ?? 0;

        if (!$invoiceId) {
            return $this->errorResponse(-5, 'Invalid invoice ID');
        }

        // Find invoice
        $invoice = Invoice::find($invoiceId);
        
        if (!$invoice) {
            return $this->errorResponse(-5, 'Invoice not found');
        }

        // Check amount
        if ((float)$amount !== (float)$invoice->total) {
            return $this->errorResponse(-2, 'Incorrect amount');
        }

        // Check if invoice is already paid
        if ($invoice->status === 'paid') {
            return $this->errorResponse(-4, 'Already paid');
        }

        // Create or find transaction
        $transaction = PaymentTransaction::updateOrCreate(
            [
                'merchant_trans_id' => $invoiceId,
                'gateway' => 'click',
            ],
            [
                'invoice_id' => $invoiceId,
                'company_id' => $invoice->company_id,
                'transaction_id' => $data['click_trans_id'],
                'amount' => $amount,
                'status' => 'processing',
                'gateway_data' => $data,
                'prepare_id' => $data['click_trans_id'],
            ]
        );

        return [
            'click_trans_id' => $data['click_trans_id'],
            'merchant_trans_id' => $invoiceId,
            'merchant_prepare_id' => $transaction->id,
            'error' => 0,
            'error_note' => 'Success',
        ];
    }

    /**
     * Handle complete callback (action = 1)
     */
    private function handleComplete(array $data): array
    {
        $invoiceId = $data['merchant_trans_id'] ?? null;
        $merchantPrepareId = $data['merchant_prepare_id'] ?? null;

        if (!$merchantPrepareId) {
            return $this->errorResponse(-6, 'Transaction not found');
        }

        // Find transaction
        $transaction = PaymentTransaction::find($merchantPrepareId);
        
        if (!$transaction) {
            return $this->errorResponse(-6, 'Transaction not found');
        }

        // Check if already completed
        if ($transaction->isCompleted()) {
            return [
                'click_trans_id' => $data['click_trans_id'],
                'merchant_trans_id' => $invoiceId,
                'merchant_confirm_id' => $transaction->id,
                'error' => 0,
                'error_note' => 'Already completed',
            ];
        }

        // Mark transaction as completed
        $transaction->markAsCompleted();
        $transaction->update([
            'callback_time' => now(),
            'gateway_data' => array_merge($transaction->gateway_data ?? [], ['complete' => $data]),
        ]);

        // Update invoice status
        $invoice = $transaction->invoice;
        if ($invoice) {
            $invoice->update(['status' => 'paid']);
        }

        return [
            'click_trans_id' => $data['click_trans_id'],
            'merchant_trans_id' => $invoiceId,
            'merchant_confirm_id' => $transaction->id,
            'error' => 0,
            'error_note' => 'Success',
        ];
    }

    /**
     * Generate error response
     */
    private function errorResponse(int $errorCode, string $errorNote): array
    {
        return [
            'error' => $errorCode,
            'error_note' => $errorNote,
        ];
    }
}
