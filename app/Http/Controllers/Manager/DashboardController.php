<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\BillingItem;
use App\Models\Company;
use App\Models\Inventory;
use App\Models\ManagerTask;
use App\Models\ShipmentFbo;
use App\Models\Inbound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->attributes->get('managerCompany');
        $companyId = $company->id;

        // Pending confirmations
        $pendingCount = ManagerTask::forCompany($companyId)->pending()->count();

        // Current month stats
        $currentMonth = now()->format('Y-m');
        $tasksThisMonth = ManagerTask::forCompany($companyId)
            ->confirmed()
            ->whereYear('confirmed_at', now()->year)
            ->whereMonth('confirmed_at', now()->month)
            ->count();

        $billedThisMonth = BillingItem::forCompany($companyId)
            ->forPeriod($currentMonth)
            ->notVoid()
            ->sum('amount');

        // Tasks by type for chart
        $tasksByType = ManagerTask::forCompany($companyId)
            ->confirmed()
            ->whereYear('confirmed_at', now()->year)
            ->whereMonth('confirmed_at', now()->month)
            ->select('task_type', DB::raw('count(*) as count'))
            ->groupBy('task_type')
            ->pluck('count', 'task_type')
            ->toArray();

        // Billing by scope for chart
        $billingByScope = BillingItem::forCompany($companyId)
            ->forPeriod($currentMonth)
            ->notVoid()
            ->select('scope', DB::raw('SUM(amount) as total'))
            ->groupBy('scope')
            ->pluck('total', 'scope')
            ->toArray();

        // Last 6 months billing trend
        $billingTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $period = $month->format('Y-m');
            $billingTrend[] = [
                'month' => $month->translatedFormat('M'),
                'amount' => BillingItem::forCompany($companyId)
                    ->forPeriod($period)
                    ->notVoid()
                    ->sum('amount'),
            ];
        }

        // Inventory summary
        $inventoryStats = [
            'total_skus' => Inventory::where('company_id', $companyId)->count(),
            'total_units' => Inventory::where('company_id', $companyId)->sum('qty_total'),
            'low_stock' => Inventory::where('company_id', $companyId)
                ->whereRaw('qty_total > 0 AND qty_total <= 10')
                ->count(),
        ];

        // Active shipments
        $activeShipments = ShipmentFbo::where('company_id', $companyId)
            ->whereIn('status', ['submitted', 'picking', 'packed'])
            ->count();

        // Pending inbounds
        $pendingInbounds = Inbound::where('company_id', $companyId)
            ->whereIn('status', ['draft', 'submitted'])
            ->count();

        // Recent tasks
        $recentTasks = ManagerTask::forCompany($companyId)
            ->with('creator')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('manager.dashboard', compact(
            'company',
            'pendingCount',
            'tasksThisMonth',
            'billedThisMonth',
            'tasksByType',
            'billingByScope',
            'billingTrend',
            'inventoryStats',
            'activeShipments',
            'pendingInbounds',
            'recentTasks'
        ));
    }

    public function switchCompany(Request $request, Company $company)
    {
        $user = auth('manager')->user();
        if (!$user->hasRole('admin') && $company->manager_user_id !== $user->id) {
            abort(403);
        }

        session(['manager_company_id' => $company->id]);

        return redirect()->route('manager.dashboard');
    }
}
