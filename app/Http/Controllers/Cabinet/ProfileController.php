<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $currentCompany = $request->attributes->get('currentCompany');
        return view('cabinet.profile', compact('user', 'currentCompany'));
    }
    
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'locale' => 'nullable|string|in:ru,uz',
        ]);
        
        auth()->user()->update($validated);
        
        return back()->with('success', __('Profile updated successfully'));
    }
    
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        
        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);
        
        return back()->with('success', __('Password updated successfully'));
    }
    
    public function updateLocale(Request $request)
    {
        $validated = $request->validate([
            'locale' => 'required|string|in:ru,uz,en',
        ]);
        
        auth()->user()->update([
            'locale' => $validated['locale'],
        ]);
        
        return back()->with('success', __('Language updated successfully'));
    }
    
    public function switchCompany(Request $request, $companyId)
    {
        $user = auth()->user();
        
        // Verify user has access to this company
        if (!$user->companies()->where('companies.id', $companyId)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        session(['current_company_id' => $companyId]);
        
        return response()->json(['success' => true]);
    }
}
