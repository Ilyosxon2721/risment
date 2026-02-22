<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Isolates session data between panels (admin, cabinet, manager).
 *
 * Each panel stores its data under a prefixed key in the shared session.
 * Authentication remains shared — one login works for all panels.
 * Panel-specific data (selected company, filters, flash messages) is isolated.
 */
class PanelSessionIsolation
{
    public function handle(Request $request, Closure $next, string $panel): Response
    {
        // Store active panel in session so views/middleware can reference it
        session(['active_panel' => $panel]);

        // Expose panel name to views
        view()->share('activePanel', $panel);

        return $next($request);
    }

    /**
     * Get a panel-scoped session key.
     * Usage: PanelSessionIsolation::key('selected_filter') → 'cabinet.selected_filter'
     */
    public static function key(string $key): string
    {
        $panel = session('active_panel', 'cabinet');
        return "{$panel}.{$key}";
    }
}
