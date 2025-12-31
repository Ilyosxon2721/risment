<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'ensureCompany' => \App\Http\Middleware\EnsureUserHasCompany::class,
        ]);
        
        // Exclude payment gateway callbacks from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'payment/click',
            'payment/payme',
        ]);
        
        // Use custom Authenticate middleware
        $middleware->redirectGuestsTo(fn () => route('login', ['locale' => 'ru']));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
