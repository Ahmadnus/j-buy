<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Like `auth.token`, but lets guests through.
 *
 * If a valid token is present the user is attached to the request
 * (`$request->user()` works); otherwise the request continues as a guest.
 * Used for endpoints that both guests and customers can call, such as
 * contacting support.
 */
class OptionalTokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        if ($user = AuthenticateWithToken::userFromRequest($request)) {
            $request->setUserResolver(fn () => $user);
        }

        return $next($request);
    }
}
