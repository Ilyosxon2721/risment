<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('manager')->check()) {
            return redirect()->route('manager.dashboard');
        }

        return view('manager.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::guard('manager')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => __('auth.failed'),
            ])->onlyInput('email');
        }

        $user = Auth::guard('manager')->user();

        // Verify the user has manager or admin role
        if (!$user->hasAnyRole(['manager', 'admin'])) {
            Auth::guard('manager')->logout();

            return back()->withErrors([
                'email' => 'Доступ только для менеджеров.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('manager.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('manager')->logout();

        // Only regenerate token, do NOT invalidate entire session
        // (user might be logged into cabinet via 'web' guard)
        $request->session()->regenerateToken();

        return redirect()->route('manager.login');
    }
}
