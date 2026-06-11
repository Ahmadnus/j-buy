<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * Sets the application locale from the user's session for every dashboard
 * request. Defaults to Arabic if no preference is stored.
 *
 * The user toggles language via the language switch link in the topbar;
 * that controller writes to `session('dashboard_locale')`.
 */
class SetDashboardLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->session()->get('dashboard_locale', 'ar');
        if (!in_array($locale, ['ar', 'en'], true)) {
            $locale = 'ar';
        }
        App::setLocale($locale);
        return $next($request);
    }
}