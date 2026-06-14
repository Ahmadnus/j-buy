<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Token authentication that works on cPanel / shared Apache hosting.
 *
 * Reads the bearer token from every place Apache might put it,
 * in order from most reliable to least.
 */
class AuthenticateWithToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $this->resolveToken($request);

        if (! $token) {
            return $this->deny('No token provided');
        }

        $pat = PersonalAccessToken::findToken($token);

        if (! $pat) {
            return $this->deny('Invalid token');
        }

        if ($pat->expires_at && $pat->expires_at->isPast()) {
            return $this->deny('Token expired');
        }

        $user = $pat->tokenable;

        if (! $user) {
            return $this->deny('User not found');
        }

        if (isset($user->is_active) && ! $user->is_active) {
            return $this->deny('Account disabled');
        }

        $pat->forceFill(['last_used_at' => now()])->save();

        $user->withAccessToken($pat);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }

    /**
     * Try every source in priority order.
     *
     * Source                          | When it works
     * --------------------------------|------------------------------------------
     * 1. X-Auth-Token header          | Apache doesn't strip custom headers
     * 2. $_SERVER[HTTP_AUTHORIZATION] | .htaccess RewriteRule rescue
     * 3. $_SERVER[REDIRECT_HTTP_AUTHORIZATION] | Some FastCGI setups
     * 4. Authorization header         | Nginx / localhost / non-broken Apache
     * 5. ?api_token= query string     | ALWAYS works, URL is never stripped
     */
    private function resolveToken(Request $request): ?string
    {
        // ── Source 1: Custom header ───────────────────────────────────────────
        if ($v = $request->header('X-Auth-Token')) {
            return trim($v);
        }

        // ── Source 2: Rescued by .htaccess into $_SERVER ──────────────────────
        foreach ([
            'HTTP_AUTHORIZATION',
            'REDIRECT_HTTP_AUTHORIZATION',
            'HTTP_X_AUTH_TOKEN',
            'REDIRECT_HTTP_X_AUTH_TOKEN',
        ] as $key) {
            $v = $_SERVER[$key] ?? '';
            if ($v) {
                if (stripos($v, 'Bearer ') === 0) {
                    return trim(substr($v, 7));
                }
                // X-Auth-Token is stored raw (no Bearer prefix)
                if (! empty($v) && stripos($v, 'Bearer ') === false) {
                    return trim($v);
                }
            }
        }

        // ── Source 3: Standard Authorization header ───────────────────────────
        $auth = $request->header('Authorization', '');
        if (is_string($auth) && stripos($auth, 'Bearer ') === 0) {
            return trim(substr($auth, 7));
        }

        // ── Source 4: Query string ─────────────────────────────────────────────
        if ($v = $request->query('api_token')) {
            return trim((string) $v);
        }

        return null;
    }

    private function deny(string $reason = ''): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'يجب تسجيل الدخول أولاً',
            // Only include reason in debug mode so production stays clean
            'debug'   => app()->hasDebugModeEnabled() ? $reason : null,
        ], 401);
    }
}