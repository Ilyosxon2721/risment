<?php

namespace App\Http\Middleware;

use App\Models\BillingBalance;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasCompany
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login', ['locale' => 'ru']);
        }
        
        // Allow access to company creation routes without having a company
        if ($request->routeIs('cabinet.company.create') || $request->routeIs('cabinet.company.store')) {
            return $next($request);
        }
        
        // Check if user has at least one company
        if ($user->companies()->count() === 0) {
            return redirect()->route('cabinet.company.create')
                ->with('info', __('Please create a company to continue.'));
        }
        
        // Set current company in session if not set
        if (!session()->has('current_company_id')) {
            $firstCompany = $user->companies()->first();
            session(['current_company_id' => $firstCompany->id]);
        }
        
        // Share current company with all views
        $currentCompanyId = session('current_company_id');
        $currentCompany = $user->companies()->find($currentCompanyId);
        
        if (!$currentCompany) {
            // Company not found or user doesn't have access, reset to first company
            $firstCompany = $user->companies()->first();
            session(['current_company_id' => $firstCompany->id]);
            $currentCompany = $firstCompany;
        }
        
        $billingBalance = BillingBalance::getOrCreate($currentCompany->id);

        view()->share('currentCompany', $currentCompany);
        view()->share('currentBillingBalance', $billingBalance);
        $request->attributes->set('currentCompany', $currentCompany);
        
        return $next($request);
    }
}
