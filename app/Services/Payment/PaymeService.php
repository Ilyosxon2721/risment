<?php

namespace App\Services\Payment;

use App\Models\PaymentTransaction;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class PaymeService implements PaymentGatewayInterface
{
    private string $merchantId;
    private string $secretKey;
    private string $endpoint;

    public function __construct()
    {
        $this->merchantId = config('payment.payme.merchant_id');
        $this->secretKey = config('payment.payme.secret_key');
        $this->endpoint = config('payment.payme.endpoint');
    }

    /**
     * Generate Payme payment URL
     */
    public function generatePaymentUrl(int $invoiceId, float $amount): string
    {
        $params = [
            'm' => $this->merchantId,
            'ac.invoice_id' => $invoiceId,
            'a' => $amount * 100, // Convert to tiyin (1 sum = 100 tiyin)
            'c' => route('payment.payme.callback'),
        ];

        $url = $this->endpoint . '?' . http_build_query($params);
        
        return $url;
    }

    /**
     * Verify Payme authorization header
     */
    public function verifyAuth(?string $auth): bool
    {
        if (!$auth || !str_starts_with($auth, 'Basic ')) {
            return false;
        }

        $credentials = base64_decode(substr($auth, 6));
        [$username, $password] = explode(':', $credentials, 2);

        return $username === 'Paycom' && $password === $this->secretKey;
    }

    /**
     * Verify signature (not used for Payme, kept for interface compatibility)
     */
    public function verifySignature(array $data): bool
    {
        return true; // Payme uses Authorization header instead
    }

    /**
     * Process Payme JSON-RPC callback
     */
    public function processCallback(array $data): array
    {
        try {
            $method = $data['method'] ?? '';
            $params = $data['params'] ?? [];
            $id = $data['id'] ?? null;

            return match($method) {
                'CheckPerformTransaction' => $this->checkPerformTransaction($params, $id),
                'CreateTransaction' => $this->createTransaction($params, $id),
                'PerformTransaction' => $this->performTransaction($params, $id),
                'CancelTransaction' => $this->cancelTransaction($params, $id),
                'CheckTransaction' => $this->checkTransaction($params, $id),
                'GetStatement' => $this->getStatement($params, $id),
                default => $this->errorResponse(-32601, 'Method not found', $id),
            };
        } catch (\Exception $e) {
            Log::error('Payme callback error: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse(-32400, $e->getMessage(), $data['id'] ?? null);
        }
    }

    /**
     * Check if transaction can be performed
     */
    private function checkPerformTransaction(array $params, $id): array
    {
        $invoiceId = $params['account']['invoice_id'] ?? null;
        $amount = ($params['amount'] ?? 0) / 100; // Convert from tiyin to sum

        if (!$invoiceId) {
            return $this->errorResponse(-31050, 'Invalid invoice ID', $id);
        }

        $invoice = Invoice::find($invoiceId);
        
        if (!$invoice) {
            return $this->errorResponse(-31050, 'Invoice not found', $id);
        }

        if ((float)$amount !== (float)$invoice->total) {
            return $this->errorResponse(-31001, 'Incorrect amount', $id);
        }

        if ($invoice->status === 'paid') {
            return $this->errorResponse(-31008, 'Already paid', $id);
        }

        return [
            'result' => [
                'allow' => true,
            ],
            'id' => $id,
        ];
    }

    /**
     * Create transaction
     */
    private function createTransaction(array $params, $id): array
    {
        $transactionId = $params['id'] ?? null;
        $invoiceId = $params['account']['invoice_id'] ?? null;
        $amount = ($params['amount'] ?? 0) / 100;
        $time = $params['time'] ?? null;

        $invoice = Invoice::find($invoiceId);
        
        if (!$invoice) {
            return $this->errorResponse(-31050, 'Invoice not found', $id);
        }

        // Check if transaction already exists
        $transaction = PaymentTransaction::where('transaction_id', $transactionId)
            ->where('gateway', 'payme')
            ->first();

        if ($transaction) {
            if ($transaction->isCompleted()) {
                return $this->errorResponse(-31008, 'Already completed', $id);
            }
            
            // Return existing transaction
            return [
                'result' => [
                    'create_time' => $transaction->created_at->timestamp * 1000,
                    'transaction' => (string)$transaction->id,
                    'state' => 1,
                ],
                'id' => $id,
            ];
        }

        // Create new transaction
        $transaction = PaymentTransaction::create([
            'invoice_id' => $invoiceId,
            'company_id' => $invoice->company_id,
            'gateway' => 'payme',
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'status' => 'processing',
            'gateway_data' => $params,
        ]);

        return [
            'result' => [
                'create_time' => $transaction->created_at->timestamp * 1000,
                'transaction' => (string)$transaction->id,
                'state' => 1,
            ],
            'id' => $id,
        ];
    }

    /**
     * Perform (complete) transaction
     */
    private function performTransaction(array $params, $id): array
    {
        $transactionId = $params['id'] ?? null;

        $transaction = PaymentTransaction::where('transaction_id', $transactionId)
            ->where('gateway', 'payme')
            ->first();

        if (!$transaction) {
            return $this->errorResponse(-31003, 'Transaction not found', $id);
        }

        if ($transaction->isCompleted()) {
            return [
                'result' => [
                    'transaction' => (string)$transaction->id,
                    'perform_time' => $transaction->completed_at->timestamp * 1000,
                    'state' => 2,
                ],
                'id' => $id,
            ];
        }

        // Mark as completed
        $transaction->markAsCompleted();

        // Update invoice
        if ($transaction->invoice) {
            $transaction->invoice->update(['status' => 'paid']);
        }

        return [
            'result' => [
                'transaction' => (string)$transaction->id,
                'perform_time' => $transaction->completed_at->timestamp * 1000,
                'state' => 2,
            ],
            'id' => $id,
        ];
    }

    /**
     * Cancel transaction
     */
    private function cancelTransaction(array $params, $id): array
    {
        $transactionId = $params['id'] ?? null;

        $transaction = PaymentTransaction::where('transaction_id', $transactionId)
            ->where('gateway', 'payme')
            ->first();

        if (!$transaction) {
            return $this->errorResponse(-31003, 'Transaction not found', $id);
        }

        $transaction->markAsCancelled();

        return [
            'result' => [
                'transaction' => (string)$transaction->id,
                'cancel_time' => now()->timestamp * 1000,
                'state' => -1,
            ],
            'id' => $id,
        ];
    }

    /**
     * Check transaction status
     */
    private function checkTransaction(array $params, $id): array
    {
        $transactionId = $params['id'] ?? null;

        $transaction = PaymentTransaction::where('transaction_id', $transactionId)
            ->where('gateway', 'payme')
            ->first();

        if (!$transaction) {
            return $this->errorResponse(-31003, 'Transaction not found', $id);
        }

        $state = match($transaction->status) {
            'processing' => 1,
            'completed' => 2,
            'cancelled' => -1,
            'failed' => -2,
            default => 0,
        };

        return [
            'result' => [
                'create_time' => $transaction->created_at->timestamp * 1000,
                'perform_time' => $transaction->completed_at ? $transaction->completed_at->timestamp * 1000 : 0,
                'cancel_time' => 0,
                'transaction' => (string)$transaction->id,
                'state' => $state,
                'reason' => null,
            ],
            'id' => $id,
        ];
    }

    /**
     * Get statement (list of transactions)
     */
    private function getStatement(array $params, $id): array
    {
        $from = $params['from'] ?? null;
        $to = $params['to'] ?? null;

        $query = PaymentTransaction::where('gateway', 'payme');

        if ($from) {
            $query->where('created_at', '>=', date('Y-m-d H:i:s', $from / 1000));
        }

        if ($to) {
            $query->where('created_at', '<=', date('Y-m-d H:i:s', $to / 1000));
        }

        $transactions = $query->get()->map(function ($transaction) {
            $state = match($transaction->status) {
                'processing' => 1,
                'completed' => 2,
                'cancelled' => -1,
                'failed' => -2,
                default => 0,
            };

            return [
                'id' => $transaction->transaction_id,
                'time' => $transaction->created_at->timestamp * 1000,
                'amount' => $transaction->amount * 100,
                'account' => [
                    'invoice_id' => $transaction->invoice_id,
                ],
                'create_time' => $transaction->created_at->timestamp * 1000,
                'perform_time' => $transaction->completed_at ? $transaction->completed_at->timestamp * 1000 : 0,
                'cancel_time' => 0,
                'transaction' => (string)$transaction->id,
                'state' => $state,
                'reason' => null,
            ];
        });

        return [
            'result' => [
                'transactions' => $transactions->toArray(),
            ],
            'id' => $id,
        ];
    }

    /**
     * Generate error response
     */
    private function errorResponse(int $code, string $message, $id): array
    {
        return [
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
            'id' => $id,
        ];
    }
}
