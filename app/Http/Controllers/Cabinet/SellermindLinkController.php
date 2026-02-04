<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\SellermindAccountLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SellermindLinkController extends Controller
{
    /**
     * Show integration status page.
     * Auto-generates a pending token on first visit if none exists.
     */
    public function index(Request $request)
    {
        $company = $request->attributes->get('currentCompany');

        $link = SellermindAccountLink::where('company_id', $company->id)
            ->whereIn('status', ['active', 'pending'])
            ->orderByRaw("FIELD(status, 'active', 'pending')")
            ->first();

        // Auto-generate token on first visit (no active/pending link)
        if (!$link) {
            $link = $this->createPendingLink($company);
        }

        return view('cabinet.integrations.sellermind', compact('link'));
    }

    /**
     * Regenerate link token.
     */
    public function generateToken(Request $request)
    {
        $company = $request->attributes->get('currentCompany');

        // Prevent regeneration if already actively linked
        $existing = SellermindAccountLink::where('company_id', $company->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return redirect()->route('cabinet.integrations.sellermind')
                ->with('error', __('integrations.link_exists'));
        }

        // Deactivate old pending tokens, then create fresh one
        SellermindAccountLink::where('company_id', $company->id)
            ->where('status', 'pending')
            ->update(['status' => 'disabled']);

        $this->createPendingLink($company);

        return redirect()->route('cabinet.integrations.sellermind')
            ->with('success', __('integrations.token_generated'))
            ->with('newToken', true);
    }

    /**
     * Update sync settings.
     */
    public function updateSettings(Request $request)
    {
        $company = $request->attributes->get('currentCompany');

        $validated = $request->validate([
            'sync_products' => 'boolean',
            'sync_orders' => 'boolean',
            'sync_stock' => 'boolean',
        ]);

        $link = SellermindAccountLink::where('company_id', $company->id)
            ->where('status', 'active')
            ->firstOrFail();

        $link->update([
            'sync_products' => $validated['sync_products'] ?? false,
            'sync_orders' => $validated['sync_orders'] ?? false,
            'sync_stock' => $validated['sync_stock'] ?? false,
        ]);

        return redirect()->route('cabinet.integrations.sellermind')
            ->with('success', __('integrations.settings_updated'));
    }

    /**
     * Disconnect SellerMind account.
     */
    public function disconnect(Request $request)
    {
        $company = $request->attributes->get('currentCompany');

        SellermindAccountLink::where('company_id', $company->id)
            ->whereIn('status', ['active', 'pending'])
            ->update(['status' => 'disabled']);

        return redirect()->route('cabinet.integrations.sellermind')
            ->with('success', __('integrations.disconnected'));
    }

    /**
     * Create a new pending link with generated token and push to Redis.
     */
    private function createPendingLink($company): SellermindAccountLink
    {
        $link = SellermindAccountLink::create([
            'company_id' => $company->id,
            'link_token' => SellermindAccountLink::generateToken(),
            'sync_products' => true,
            'sync_orders' => true,
            'sync_stock' => true,
            'status' => 'pending',
        ]);

        // Push link request to SellerMind via Redis
        try {
            $payload = json_encode([
                'action' => 'link_request',
                'link_token' => $link->link_token,
                'risment_company_id' => $company->id,
                'risment_company_name' => $company->name,
                'timestamp' => now()->toIso8601String(),
            ]);
            Redis::connection('integration')->rpush('sellermind:link', $payload);
        } catch (\Exception $e) {
            // Redis not available — link will work when SellerMind reads the token
        }

        return $link;
    }
}
