<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticateJWT
{
    const API_SERVER_SECRET_HEADER_NAME = 'x-api-secret';

    /**
     * Verifies the JWT token and validates the IP and User Agent
     * Invalidates token otherwise
     */
    public function handle(Request $request, Closure $next)
    {
        // Parse JWT Payload
        try {
            $payload = \JWTAuth::parseToken()->getPayload();
        } catch (JWTException $e) {
            return $next($request);
        }

        // Validate IP and User Agent
        if ($payload) {
            if ($frontApiSecret = $request->header(self::API_SERVER_SECRET_HEADER_NAME)) {
                // If it's a trusted SSR request, skip the rest
                if ($frontApiSecret === config('app.front_api_secret')) {
                    return $next($request);
                }
            }

            $error = null;
            if (!\Hash::check($request->ip(), $payload->get('ip'))) {
                $error = 'Origin IP is invalid';
            }

            if (!\Hash::check($request->userAgent(), $payload->get('ua'))) {
                $error = 'Origin User Agent is invalid';
            }

            if ($error) {
                auth()->invalidate();
                return response()->json([
                    'message' => $error
                ], 403);
            }
        }

        return $next($request);
    }
}
