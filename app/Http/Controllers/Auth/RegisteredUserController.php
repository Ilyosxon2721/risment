<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Company;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create a default company for the new user
        $company = Company::create([
            'name' => $request->name . "'s Company",
            'contact_name' => $request->name,
            'email' => $request->email,
            'phone' => '', // Will be filled later in profile
            'status' => 'active',
        ]);

        // Attach user to the company as owner
        $user->companies()->attach($company->id, [
            'role_in_company' => 'owner',
        ]);

        event(new Registered($user));

        Auth::login($user);
        
        // Set the company in session
        session(['current_company_id' => $company->id]);

        return redirect()->route('cabinet.dashboard');
    }
}
