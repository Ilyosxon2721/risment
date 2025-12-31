<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\ClickService;
use App\Services\Payment\PaymeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private ClickService $clickService,
        private PaymeService $paymeService
    ) {}

    /**
     * Handle Click prepare callback
     */
    public function clickCallback(Request $request): JsonResponse
    {
        $data = $request->all();
        
        Log::info('Click callback received', $data);
        
        $response = $this->clickService->processCallback($data);
        
        Log::info('Click callback response', $response);
        
        return response()->json($response);
    }

    /**
     * Handle Click return URL
     */
    public function clickReturn(Request $request)
    {
        $merchantTransId = $request->get('merchant_trans_id');
        
        if ($merchantTransId) {
            return redirect()
                ->route('cabinet.finance.invoices.show', $merchantTransId)
                ->with('success', __('Payment completed successfully!'));
        }
        
        return redirect()
            ->route('cabinet.finance.invoices.index')
            ->with('error', __('Payment failed'));
    }

    /**
     * Handle Payme JSON-RPC callback
     */
    public function paymeCallback(Request $request): JsonResponse
    {
        $data = $request->all();
        
        Log::info('Payme callback received', $data);
        
        // Verify Payme authorization
        $auth = $request->header('Authorization');
        if (!$this->paymeService->verifyAuth($auth)) {
            return response()->json([
                'error' => [
                    'code' => -32504,
                    'message' => 'Insufficient privileges',
                ],
                'id' => $data['id'] ?? null,
            ]);
        }
        
        $response = $this->paymeService->processCallback($data);
        
        Log::info('Payme callback response', $response);
        
        return response()->json($response);
    }
}
