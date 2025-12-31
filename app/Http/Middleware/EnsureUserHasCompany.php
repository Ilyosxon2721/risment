<?php

namespace App\Http\Middleware;

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
        
        // Check if user has at least one company
        if ($user->companies()->count() === 0) {
            return redirect('/ru')
                ->with('error', __('You need to be assigned to a company to access the cabinet.'));
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
        
        view()->share('currentCompany', $currentCompany);
        $request->attributes->set('currentCompany', $currentCompany);
        
        return $next($request);
    }
}
