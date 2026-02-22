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

        // Admin and manager roles can access the manager panel
        if (!$user->hasAnyRole(['manager', 'admin'])) {
            abort(403, 'Доступ только для менеджеров');
        }

        return $next($request);
    }
}
