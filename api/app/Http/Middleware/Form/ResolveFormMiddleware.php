<?php

namespace App\Http\Middleware\Form;

use App\Models\Forms\Form;
use Closure;
use Illuminate\Http\Request;

class ResolveFormMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $routeParamName = 'id')
    {
        $form = Form::where($routeParamName, $request->route($routeParamName))->firstOrFail();
        $request->merge([
            'form' => $form,
        ]);

        return $next($request);
    }
}
