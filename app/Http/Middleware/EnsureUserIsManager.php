<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsManager
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login', ['locale' => 'ru']);
        }

        // Allow access if user has manager or admin Spatie role
        if ($user->hasAnyRole(['manager', 'admin'])) {
            return $next($request);
        }

        // Fallback: allow if user can access Filament admin panel
        try {
            $panel = \Filament\Facades\Filament::getPanel('admin');
            if ($panel && method_exists($user, 'canAccessPanel') && $user->canAccessPanel($panel)) {
                return $next($request);
            }
        } catch (\Exception $e) {
            // Panel not found or other error — fall through to deny
        }

        abort(403, 'Доступ только для менеджеров');
    }
}
