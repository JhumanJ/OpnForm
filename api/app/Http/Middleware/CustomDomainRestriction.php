<?php

namespace App\Http\Middleware;

use App\Http\Requests\Workspace\CustomDomainRequest;
use App\Models\Forms\Form;
use App\Models\Workspace;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CustomDomainRestriction
{
    public const CUSTOM_DOMAIN_HEADER = 'x-custom-domain';

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->hasHeader(self::CUSTOM_DOMAIN_HEADER) || ! config('custom-domains.enabled')) {
            return $next($request);
        }

        $customDomain = $request->header(self::CUSTOM_DOMAIN_HEADER);
        if (! preg_match(CustomDomainRequest::CUSTOM_DOMAINS_REGEX, $customDomain)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid domain',
                'error' => 'invalid_domain',
            ], 420);
        }

        // Check if domain is different from current domain
        $notionFormsDomain = parse_url(config('app.url'))['host'];
        if ($customDomain == $notionFormsDomain) {
            return $next($request);
        }

        // Check if domain is known
        if (! $workspaces = Workspace::whereJsonContains('custom_domains', $customDomain)->get()) {
            return response()->json([
                'success' => false,
                'message' => 'Unknown domain',
                'error' => 'invalid_domain',
            ], 420);
        }

        $workspacesIds = $workspaces->pluck('id')->toArray();
        Workspace::addGlobalScope('domain-restricted', function (Builder $builder) use ($workspacesIds) {
            $builder->whereIn('id', $workspacesIds);
        });
        Form::addGlobalScope('domain-restricted', function (Builder $builder) use ($workspacesIds) {
            $builder->whereIn('workspace_id', $workspacesIds);
        });

        return $next($request);
    }
}
