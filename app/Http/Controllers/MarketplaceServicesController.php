<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceService;
use Illuminate\Http\Request;

class MarketplaceServicesController extends Controller
{
    public function index()
    {
        $services = [
            'launch' => MarketplaceService::active()
                ->byGroup('launch')
                ->orderBy('sort')
                ->get(),
            'management' => MarketplaceService::active()
                ->byGroup('management')
                ->orderBy('sort')
                ->get(),
            'ads_addon' => MarketplaceService::active()
                ->byGroup('ads_addon')
                ->first(),
            'infographics' => MarketplaceService::active()
                ->byGroup('infographics')
                ->orderBy('sort')
                ->get(),
        ];

        // Separate packages for toggle pricing table
        $uzumPackages = MarketplaceService::active()
            ->byGroup('management')
            ->where('marketplace', 'uzum')
            ->where('sku_limit', '>', 0)
            ->orderBy('sort')
            ->get();

        $complexPackages = MarketplaceService::active()
            ->byGroup('management')
            ->where('code', 'LIKE', '%_COMPLEX')
            ->where('sku_limit', '>', 0)
            ->orderBy('sort')
            ->get();

        return view('services-marketplace', compact('services', 'uzumPackages', 'complexPackages'));
    }

    public function calculator()
    {
        $managementPackages = MarketplaceService::active()
            ->byGroup('management')
            ->whereNotNull('sku_limit')
            ->orderBy('sort')
            ->get();

        return view('marketplace-calculator', compact('managementPackages'));
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'marketplaces' => 'required|array|min:1|max:4',
            'marketplaces.*' => 'in:uzum,wildberries,ozon,yandex',
            'package_code' => 'required|string',
            'sku_count' => 'required|integer|min:0',
            'ads_addon' => 'boolean',
        ]);

        // Get selected package
        $package = MarketplaceService::where('code', $validated['package_code'])->first();
        
        if (!$package) {
            return back()->withErrors(['package_code' => 'Invalid package']);
        }

        $marketplaces = $validated['marketplaces'];
        $marketplacesCount = count($marketplaces);
        $skuCount = $validated['sku_count'];

        // 1. Calculate base sum (same price for all marketplaces in MVP)
        $basePerMarketplace = $package->price;
        $baseSum = $basePerMarketplace * $marketplacesCount;

        // 2. Get bundle discount
        $discountPercent = \App\Models\BundleDiscount::getManagementDiscount($marketplacesCount);
        $discountAmount = $baseSum * ($discountPercent / 100);
        $discountedSum = $baseSum - $discountAmount;

        // 3. SKU overage (same SKU count applied to all marketplaces in MVP)
        $skuLimit = $package->sku_limit ?? 100;
        $overageCount = max(0, $skuCount - $skuLimit);
        $overagePacks = ceil($overageCount / 10);
        $overageFeePerMarketplace = $overagePacks * 50000;
        $totalOverageFee = $overageFeePerMarketplace * $marketplacesCount;

        // 4. Ads add-on (optional, NOT discounted by default)
        $adsFee = 0;
        if ($validated['ads_addon'] ?? false) {
            $adsAddon = MarketplaceService::where('code', 'ADS_ADDON')->first();
            $adsFee = $adsAddon ? $adsAddon->price * $marketplacesCount : 0;
        }

        // 5. Total
        $total = $discountedSum + $totalOverageFee + $adsFee;

        // Marketplace labels
        $marketplaceLabels = [
            'uzum' => 'Uzum',
            'wildberries' => 'Wildberries',
            'ozon' => 'Ozon',
            'yandex' => 'Yandex Market',
        ];

        $selectedMarketplaces = array_map(fn($m) => $marketplaceLabels[$m] ?? $m, $marketplaces);

        return view('marketplace-calculator', [
            'managementPackages' => MarketplaceService::active()
                ->byGroup('management')
                ->whereNotNull('sku_limit')
                ->orderBy('sort')
                ->get(),
            'result' => [
                'marketplaces' => $marketplaces,
                'marketplace_labels' => $selectedMarketplaces,
                'marketplaces_count' => $marketplacesCount,
                'package' => $package,
                'sku_count' => $skuCount,
                'base_per_marketplace' => $basePerMarketplace,
                'base_sum' => $baseSum,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'discounted_sum' => $discountedSum,
                'overage_count' => $overageCount,
                'overage_packs' => $overagePacks,
                'overage_fee_per_marketplace' => $overageFeePerMarketplace,
                'overage_fee' => $totalOverageFee,
                'ads_addon' => $validated['ads_addon'] ?? false,
                'ads_fee' => $adsFee,
                'total' => $total,
            ],
        ]);
    }
}
