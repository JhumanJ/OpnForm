<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsModerator
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && ! $request->user()->moderator) {
            // This user is not a paying customer...
            if ($request->expectsJson()) {
                return response([
                    'message' => 'You are not allowed.',
                    'type' => 'error',
                ], 403);
            }

            return redirect('home');
        }

        return $next($request);
    }
}
