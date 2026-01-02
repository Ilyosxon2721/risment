<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CompanyController extends Controller
{
    /**
     * Show company creation form.
     */
    public function create(): View
    {
        return view('cabinet.company.create');
    }

    /**
     * Store a new company.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'inn' => ['required', 'string', 'max:20'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $company = Company::create([
            'name' => $validated['name'],
            'inn' => $validated['inn'],
            'contact_name' => $validated['contact_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => 'active',
        ]);

        // Attach user as owner
        Auth::user()->companies()->attach($company->id, ['role_in_company' => 'owner']);

        // Set as current company
        session(['current_company_id' => $company->id]);

        return redirect()->route('cabinet.dashboard')
            ->with('success', __('Company created successfully!'));
    }

    /**
     * Show company details.
     */
    public function show(Request $request): View
    {
        $company = $request->attributes->get('currentCompany');
        
        return view('cabinet.company.show', compact('company'));
    }

    /**
     * Show company edit form.
     */
    public function edit(Request $request): View
    {
        $company = $request->attributes->get('currentCompany');
        
        // Check if user is owner
        $userRole = Auth::user()->companies()
            ->where('companies.id', $company->id)
            ->first()
            ->pivot
            ->role_in_company ?? null;
        
        if ($userRole !== 'owner') {
            abort(403, __('Only company owner can edit company details.'));
        }
        
        return view('cabinet.company.edit', compact('company'));
    }

    /**
     * Update company details.
     */
    public function update(Request $request): RedirectResponse
    {
        $company = $request->attributes->get('currentCompany');
        
        // Check if user is owner
        $userRole = Auth::user()->companies()
            ->where('companies.id', $company->id)
            ->first()
            ->pivot
            ->role_in_company ?? null;
        
        if ($userRole !== 'owner') {
            abort(403, __('Only company owner can edit company details.'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'inn' => ['required', 'string', 'max:20'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $company->update($validated);

        return redirect()->route('cabinet.company.show')
            ->with('success', __('Company updated successfully!'));
    }
}
