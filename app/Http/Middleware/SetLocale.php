<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from URL (first segment)
        $locale = $request->segment(1);

        // If no locale in URL (e.g., cabinet routes), use session or default
        if (!in_array($locale, ['ru', 'uz'])) {
            $locale = Session::get('locale', config('app.locale', 'ru'));
        }

        // Set application locale
        App::setLocale($locale);
        
        // CRITICAL FIX: Set default URL parameter so all route() calls automatically include locale
        // This prevents UrlGenerationException when calling route('home'), route('login'), etc.
        \Illuminate\Support\Facades\URL::defaults(['locale' => $locale]);
        
        // Store in session
        Session::put('locale', $locale);
        
        return $next($request);
    }
}
