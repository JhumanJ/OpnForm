<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array
     */
    protected $except = [
        'password',
        'password_confirmation',
    ];

    /**
     * The route name where this shouldn't be applied
     *
     * @var string[]
     */
    protected $exceptUrls = [
        '/\/api\/forms\/(.*)\/answer/',
    ];

    public function handle($request, \Closure $next)
    {
        // Check if URL matches
        foreach ($this->exceptUrls as $urlRegex) {
            $matches = null;
            preg_match($urlRegex, $request->url(), $matches);

            if (count($matches)) {
                return $next($request);
            }
        }

        return parent::handle($request, $next);
    }
}
