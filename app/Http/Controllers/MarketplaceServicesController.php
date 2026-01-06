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
            ->orderBy('sort')
            ->get();

        $complexPackages = MarketplaceService::active()
            ->byGroup('management')
            ->where('code', 'LIKE', '%_COMPLEX%')
            ->orderBy('sort')
            ->get();

        return view('services-marketplace', compact('services', 'uzumPackages', 'complexPackages'));
    }

    public function calculator()
    {
        // Get Uzum-specific packages
        $uzumPackages = MarketplaceService::active()
            ->byGroup('management')
            ->byMarketplace('uzum')
            ->whereNotNull('sku_limit')
            ->orderBy('sort')
            ->get();
        
        // Get complex marketplace packages (WB, Ozon, Yandex)
        // These are marked as 'all' but should exclude Uzum-specific ones
        $complexPackages = MarketplaceService::active()
            ->byGroup('management')
            ->where('marketplace', 'all')
            ->whereNotNull('sku_limit')
            ->where('code', 'LIKE', 'MGMT_COMPLEX%') // Filter only complex packages
            ->orderBy('sort')
            ->get();
        
        // Fallback: if no specific packages, use general management packages
        if ($uzumPackages->isEmpty()) {
            $uzumPackages = MarketplaceService::active()
                ->byGroup('management')
                ->whereNotNull('sku_limit')
                ->orderBy('sort')
                ->get();
        }
        
        if ($complexPackages->isEmpty()) {
            $complexPackages = MarketplaceService::active()
                ->byGroup('management')
                ->whereNotNull('sku_limit')
                ->orderBy('sort')
                ->get();
        }

        return view('marketplace-calculator', compact('uzumPackages', 'complexPackages'));
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'marketplaces' => 'required|array|min:1|max:4',
            'marketplaces.*' => 'in:uzum,wildberries,ozon,yandex',
            'configs' => 'required|array',
            'configs.*.package' => 'required|string',
            'configs.*.sku_count' => 'required|integer|min:0',
            'configs.*.ads' => 'sometimes',
        ]);

        $selectedMarketplaces = $validated['marketplaces'];
        $configs = $validated['configs'];
        
        $results = [
            'items' => [],
            'base_sum' => 0,
            'total_overage' => 0,
            'total_ads' => 0,
            'total' => 0,
        ];

        foreach ($selectedMarketplaces as $mp) {
            $mpConfig = $configs[$mp] ?? null;
            if (!$mpConfig) continue;

            $package = MarketplaceService::where('code', $mpConfig['package'])->first();
            if (!$package) continue;

            $skuCount = (int)$mpConfig['sku_count'];
            $hasAds = isset($mpConfig['ads']) && $mpConfig['ads'];

            // 1. Base price
            $basePrice = $package->price;
            
            // 2. Overage
            $skuLimit = $package->sku_limit ?? 100;
            $overageCount = max(0, $skuCount - $skuLimit);
            $overagePacks = ceil($overageCount / 10);
            $overageFee = $overagePacks * 50000;

            // 3. Ads
            $adsFee = 0;
            if ($hasAds) {
                $adsAddon = MarketplaceService::where('code', 'ADS_ADDON')->first();
                $adsFee = $adsAddon ? $adsAddon->price : 690000;
            }

            $mpTotal = $basePrice + $overageFee + $adsFee;

            $results['items'][$mp] = [
                'name' => ucfirst($mp),
                'package' => $package,
                'sku_count' => $skuCount,
                'base_price' => $basePrice,
                'overage_count' => $overageCount,
                'overage_fee' => $overageFee,
                'ads_fee' => $adsFee,
                'total' => $mpTotal,
            ];

            $results['base_sum'] += $basePrice;
            $results['total_overage'] += $overageFee;
            $results['total_ads'] += $adsFee;
        }

        // Apply bundle discount on base sum of all packages
        $marketplacesCount = count($results['items']);
        $discountPercent = \App\Models\BundleDiscount::getManagementDiscount($marketplacesCount);
        $discountAmount = $results['base_sum'] * ($discountPercent / 100);
        $discountedBaseSum = $results['base_sum'] - $discountAmount;

        $results['discount_percent'] = $discountPercent;
        $results['discount_amount'] = $discountAmount;
        $results['total'] = $discountedBaseSum + $results['total_overage'] + $results['total_ads'];

        // Repass packages for view
        $uzumPackages = MarketplaceService::active()
            ->byGroup('management')
            ->byMarketplace('uzum')
            ->whereNotNull('sku_limit')
            ->orderBy('sort')
            ->get();
        
        $complexPackages = MarketplaceService::active()
            ->byGroup('management')
            ->where('marketplace', 'all')
            ->whereNotNull('sku_limit')
            ->where('code', 'LIKE', 'MGMT_COMPLEX%')
            ->orderBy('sort')
            ->get();

        return view('marketplace-calculator', array_merge($results, compact('uzumPackages', 'complexPackages')));
    }
}
