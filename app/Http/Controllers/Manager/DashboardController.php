<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\BillingItem;
use App\Models\Company;
use App\Models\ManagerTask;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->attributes->get('managerCompany');

        $pendingCount = ManagerTask::forCompany($company->id)->pending()->count();

        $currentMonth = now()->format('Y-m');
        $tasksThisMonth = ManagerTask::forCompany($company->id)
            ->confirmed()
            ->whereYear('confirmed_at', now()->year)
            ->whereMonth('confirmed_at', now()->month)
            ->count();

        $billedThisMonth = BillingItem::forCompany($company->id)
            ->forPeriod($currentMonth)
            ->notVoid()
            ->sum('amount');

        $recentTasks = ManagerTask::forCompany($company->id)
            ->with('creator')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('manager.dashboard', compact(
            'company',
            'pendingCount',
            'tasksThisMonth',
            'billedThisMonth',
            'recentTasks'
        ));
    }

    public function switchCompany(Request $request, Company $company)
    {
        if ($company->manager_user_id !== auth()->id()) {
            abort(403);
        }

        session(['manager_company_id' => $company->id]);

        return redirect()->route('manager.dashboard');
    }
}
