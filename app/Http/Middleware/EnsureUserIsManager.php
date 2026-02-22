<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsManager
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('manager');

        if (!$user) {
            return redirect()->route('manager.login');
        }

        if ($user->hasAnyRole(['manager', 'admin'])) {
            return $next($request);
        }

        abort(403, 'Доступ только для менеджеров');
    }
}
