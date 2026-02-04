<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Jobs\Sellermind\PushMarketplaceToSellermind;
use App\Models\MarketplaceCredential;
use Illuminate\Http\Request;

class MarketplaceCredentialController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->attributes->get('currentCompany');

        $credentials = MarketplaceCredential::where('company_id', $company->id)
            ->orderBy('marketplace')
            ->orderBy('name')
            ->get();

        $grouped = $credentials->groupBy('marketplace');

        $marketplaces = ['wildberries', 'ozon', 'uzum', 'yandex_market'];

        return view('cabinet.marketplaces.index', compact('grouped', 'marketplaces'));
    }

    public function create(Request $request)
    {
        $marketplace = $request->query('marketplace');

        return view('cabinet.marketplaces.form', [
            'credential' => null,
            'marketplace' => $marketplace,
        ]);
    }

    public function store(Request $request)
    {
        $company = $request->attributes->get('currentCompany');
        $validated = $this->validateCredential($request, $company->id);

        $credential = MarketplaceCredential::create(array_merge($validated, [
            'company_id' => $company->id,
        ]));

        (new PushMarketplaceToSellermind($credential->id, $company->id, 'marketplace.created'))->handle();

        return redirect()->route('cabinet.marketplaces.index')
            ->with('success', __('marketplaces.created'));
    }

    public function edit(Request $request, MarketplaceCredential $marketplace)
    {
        $company = $request->attributes->get('currentCompany');
        abort_unless($marketplace->company_id === $company->id, 403);

        return view('cabinet.marketplaces.form', [
            'credential' => $marketplace,
            'marketplace' => $marketplace->marketplace,
        ]);
    }

    public function update(Request $request, MarketplaceCredential $marketplace)
    {
        $company = $request->attributes->get('currentCompany');
        abort_unless($marketplace->company_id === $company->id, 403);

        $validated = $this->validateCredential($request, $company->id, $marketplace->id);
        $marketplace->update($validated);

        (new PushMarketplaceToSellermind($marketplace->id, $company->id, 'marketplace.updated'))->handle();

        return redirect()->route('cabinet.marketplaces.index')
            ->with('success', __('marketplaces.updated'));
    }

    public function destroy(Request $request, MarketplaceCredential $marketplace)
    {
        $company = $request->attributes->get('currentCompany');
        abort_unless($marketplace->company_id === $company->id, 403);

        $credentialId = $marketplace->id;
        $marketplace->delete();

        (new PushMarketplaceToSellermind($credentialId, $company->id, 'marketplace.deleted'))->handle();

        return redirect()->route('cabinet.marketplaces.index')
            ->with('success', __('marketplaces.deleted'));
    }

    private function validateCredential(Request $request, int $companyId, ?int $ignoreId = null): array
    {
        $uniqueRule = $ignoreId
            ? "unique:marketplace_credentials,name,{$ignoreId},id,company_id,{$companyId},marketplace,{$request->marketplace}"
            : "unique:marketplace_credentials,name,NULL,id,company_id,{$companyId},marketplace,{$request->marketplace}";

        $rules = [
            'marketplace' => 'required|in:wildberries,ozon,uzum,yandex_market',
            'name' => "required|string|max:100|{$uniqueRule}",
            'is_active' => 'sometimes|boolean',
        ];

        $mp = $request->input('marketplace');

        $mpRules = match ($mp) {
            'wildberries' => [
                'wb_api_token' => 'required|string',
                'wb_supplier_id' => 'nullable|string|max:50',
            ],
            'ozon' => [
                'ozon_client_id' => 'required|string|max:50',
                'ozon_api_key' => 'required|string',
            ],
            'uzum' => [
                'uzum_api_token' => 'required|string',
                'uzum_seller_id' => 'nullable|string|max:50',
            ],
            'yandex_market' => [
                'yandex_oauth_token' => 'required|string',
                'yandex_campaign_id' => 'nullable|string|max:50',
                'yandex_business_id' => 'nullable|string|max:50',
            ],
            default => [],
        };

        return $request->validate(array_merge($rules, $mpRules));
    }
}
