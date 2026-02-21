<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetManagerCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $managedCompanies = Company::where('manager_user_id', $user->id)->get();

        if ($managedCompanies->isEmpty()) {
            return redirect()->route('manager.no-companies');
        }

        $currentId = session('manager_company_id');
        $currentCompany = $managedCompanies->firstWhere('id', $currentId);

        if (!$currentCompany) {
            $currentCompany = $managedCompanies->first();
            session(['manager_company_id' => $currentCompany->id]);
        }

        view()->share('managedCompanies', $managedCompanies);
        view()->share('managerCompany', $currentCompany);
        $request->attributes->set('managerCompany', $currentCompany);

        return $next($request);
    }
}
