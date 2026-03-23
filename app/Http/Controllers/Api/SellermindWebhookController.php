<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SellermindAccountLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SellermindWebhookController extends Controller
{
    /**
     * Handle link confirmation from SellerMind via HTTP webhook.
     * SellerMind calls this endpoint after accepting the link token.
     */
    public function confirmLink(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'link_token' => 'required|string|size:64',
            'sellermind_user_id' => 'nullable|integer',
            'sellermind_company_id' => 'nullable|integer',
        ]);

        $link = SellermindAccountLink::where('link_token', $validated['link_token'])
            ->where('status', 'pending')
            ->first();

        if (!$link) {
            return response()->json([
                'success' => false,
                'message' => 'Link token not found or already confirmed.',
            ], 404);
        }

        $link->update([
            'sellermind_user_id' => $validated['sellermind_user_id'] ?? null,
            'sellermind_company_id' => $validated['sellermind_company_id'] ?? null,
            'status' => 'active',
            'linked_at' => now(),
        ]);

        Log::info("SellerMind link confirmed via webhook: company #{$link->company_id}");

        return response()->json([
            'success' => true,
            'message' => 'Link confirmed successfully.',
            'risment_company_id' => $link->company_id,
        ]);
    }
}
