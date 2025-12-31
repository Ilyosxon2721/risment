<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCabinetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get authenticated user's preferred locale
        $user = $request->user();
        
        if ($user && $user->locale) {
            $locale = $user->locale;
        } else {
            // Fallback to default locale
            $locale = config('app.locale', 'ru');
        }
        
        // Set application locale
        app()->setLocale($locale);
        
        return $next($request);
    }
}
