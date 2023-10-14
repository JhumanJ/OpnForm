<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class EmbeddableForms
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->expectsJson() || $request->wantsJson()) {
            return $next($request);
        }

        $response = $next($request);

        if (!str_starts_with($request->url(), url('/forms/'))) {
            if ($response instanceof Response) {
                $response->header('X-Frame-Options', 'SAMEORIGIN');
            } elseif ($response instanceof \Symfony\Component\HttpFoundation\Response) {
                $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            }
        }

        return $response;
    }
}
