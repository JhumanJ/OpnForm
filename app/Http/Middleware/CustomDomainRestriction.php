<?php

namespace App\Http\Middleware;

use App\Models\Forms\Form;
use App\Models\Workspace;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class CustomDomainRestriction
{
    const CUSTOM_DOMAIN_HEADER = "User-Custom-Domain";

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader(self::CUSTOM_DOMAIN_HEADER) || !config('custom-domains.enabled')) {
            return $next($request);
        }

        $customDomain = $request->header(self::CUSTOM_DOMAIN_HEADER);
        if (!preg_match('/^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}$/', $customDomain)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid domain',
            ], 400);
        }

        // Check if domain is different from current domain
        $notionFormsDomain = parse_url(config('app.url'))['host'];
        if ($customDomain == $notionFormsDomain) {
            return $next($request);
        }

        // Check if domain is known
        if (!$workspace = Workspace::whereJsonContains('custom_domains',$customDomain)->first()) {
            return response()->json([
                'success' => false,
                'message' => 'Unknown domain',
            ], 400);
        }

        Workspace::addGlobalScope('domain-restricted', function (Builder $builder) use ($workspace) {
            $builder->where('workspaces.id', $workspace->id);
        });
        Form::addGlobalScope('domain-restricted', function (Builder $builder) use ($workspace) {
            $builder->where('forms.workspace_id', $workspace->id);
        });

        return $next($request);
    }
}
