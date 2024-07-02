<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SelfHostedCredentialsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedRouteNames = ['login', 'credentials.update', 'user.current', 'logout'];
        $routeName = request()->route()->getName();
        if(in_array($routeName, $allowedRouteNames)) {
            ray('allowed route');
            return $next($request);
        }
        if (
            config('app.self_host_mode') &&
            $request->user() &&
            !$request->user()->credentials_changed &&
            ($request)
        ) {
            if ($request->expectsJson()) {
                return response([
                    'message' => 'You must change your credentials when in self host mode',
                    'type' => 'error',
                ], 403);
            }
        }
        return $next($request);
    }
}
