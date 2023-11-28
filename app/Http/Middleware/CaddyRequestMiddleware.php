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
        if (!config('custom-domains.enabled')) {
            return $next($request);
        }

        if (config('custom-domains.enabled') && config('services.caddy.authorized_ip') !== $request->ip()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $secret = $request->route('secret');
        if (config('services.caddy.secret') && (!$secret || $secret !== config('services.caddy.secret'))) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        return $next($request);
    }
}
