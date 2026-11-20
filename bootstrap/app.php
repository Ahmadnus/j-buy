<?php

use App\Http\Middleware\SetDashboardLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
   $middleware->alias([
    'dashboard.locale' => \App\Http\Middleware\SetDashboardLocale::class,
    'auth.token'       => \App\Http\Middleware\AuthenticateWithToken::class,
    'auth.optional'    => \App\Http\Middleware\OptionalTokenAuth::class,
]);
   // The dashboard's login route is named `dashboard.login`, not Laravel's
   // default `login`. Without this, opening /admin while signed out crashed
   // with "Route [login] not defined" (500) instead of showing the login page.
   $middleware->redirectGuestsTo(fn () => route('dashboard.login'));
})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
