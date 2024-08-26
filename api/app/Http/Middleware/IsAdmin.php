<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && ! $request->user()->admin) {
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
