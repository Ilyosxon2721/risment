<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\BillingItem;
use App\Services\BillingService;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request, BillingService $billingService)
    {
        $company = $request->attributes->get('managerCompany');

        $summary = $billingService->getCurrentMonthSummary($company->id);

        $recentItems = BillingItem::forCompany($company->id)
            ->forPeriod(now()->format('Y-m'))
            ->notVoid()
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        return view('manager.billing.index', compact('summary', 'recentItems', 'company'));
    }
}
