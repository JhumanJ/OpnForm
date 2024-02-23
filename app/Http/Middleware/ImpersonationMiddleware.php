<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class ImpersonationMiddleware
{
    public const ADMIN_LOG_PREFIX = '[admin_action] ';

    public const LOG_ROUTES = [
        'open.forms.store',
        'open.forms.update',
        'open.forms.duplicate',
        'open.forms.regenerate-link',
    ];

    public const ALLOWED_ROUTES = [
        'logout',

        // Forms
        'forms.ai.generate',
        'forms.ai.show',
        'forms.assets.show',
        'forms.show',
        'forms.answer',
        'forms.fetchSubmission',
        'forms.users.index',
        'open.forms.index-all',
        'open.forms.store',
        'open.forms.assets.upload',
        'open.forms.update',
        'open.forms.duplicate',
        'open.forms.regenerate-link',
        'open.forms.submissions',
        'open.forms.submissions.file',

        // Workspaces
        'open.workspaces.index',
        'open.workspaces.create',
        'open.workspaces.delete',
        'open.workspaces.save-custom-domains',
        'open.workspaces.databases.search',
        'open.workspaces.databases.show',
        'open.workspaces.form.stats',
        'open.workspaces.forms.index',
        'open.workspaces.users.index',

        'templates.index',
        'templates.create',
        'templates.update',
        'templates.show',

        'user.current',
        'local.temp',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (! auth()->check() || ! auth()->payload()->get('impersonating')) {
                return $next($request);
            }
        } catch (JWTException $e) {
            return $next($request);
        }

        // Check that route is allowed
        $routeName = $request->route()->getName();
        if (! in_array($routeName, self::ALLOWED_ROUTES)) {
            return response([
                'message' => 'Unauthorized when impersonating',
                'route' => $routeName,
                'impersonator' => auth()->payload()->get('impersonator_id'),
                'impersonated_account' => auth()->id(),
                'url' => $request->fullUrl(),
                'payload' => $request->all(),
            ], 403);
        } elseif (in_array($routeName, self::LOG_ROUTES)) {
            \Log::warning(self::ADMIN_LOG_PREFIX.'Impersonator action', [
                'route' => $routeName,
                'url' => $request->fullUrl(),
                'impersonated_account' => auth()->id(),
                'impersonator' => auth()->payload()->get('impersonator_id'),
                'payload' => $request->all(),
            ]);
        }

        return $next($request);
    }
}
