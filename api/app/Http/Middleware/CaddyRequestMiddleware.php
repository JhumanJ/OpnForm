<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CaddyRequestMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! config('custom-domains.enabled')) {
            return response()->json([
                'success' => false,
                'message' => 'Custom domains not enabled',
            ], 401);
        }

        if (config('custom-domains.enabled') && ! in_array($request->ip(), config('custom-domains.authorized_ips'))) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized IP',
            ], 401);
        }

        $secret = $request->route('secret');
        if (config('custom-domains.caddy_secret') && (! $secret || $secret !== config('custom-domains.caddy_secret'))) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        return $next($request);
    }
}
