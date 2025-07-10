<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ThrottleEmailChanges
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Check if email is being changed
        $currentEmail = $user->email;
        $newEmail = strtolower($request->input('email'));

        // Only throttle if email is actually being changed
        if ($currentEmail === $newEmail) {
            return $next($request);
        }

        $cacheKey = "email_changes_{$user->id}";
        $attempts = Cache::get($cacheKey, 0);

        if ($attempts >= 2) {
            return response()->json([
                'message' => 'You have reached the limit of 2 email changes per hour. Please try again later.',
            ], 429);
        }

        // Increment the counter
        Cache::put($cacheKey, $attempts + 1, now()->addHour());

        return $next($request);
    }
}
