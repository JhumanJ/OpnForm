<?php

namespace App\Http\Middleware;

use App\Http\Requests\Workspace\CustomDomainRequest;
use App\Models\Forms\Form;
use App\Models\Workspace;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class CustomDomainRestriction
{
    const CUSTOM_DOMAIN_HEADER = "x-custom-domain";

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader(self::CUSTOM_DOMAIN_HEADER) || !config('custom-domains.enabled')) {
            return $next($request);
        }

        $customDomain = $request->header(self::CUSTOM_DOMAIN_HEADER);
        if (!preg_match(CustomDomainRequest::CUSTOM_DOMAINS_REGEX, $customDomain)) {
            \Log::warning('Invalid domain', [
                'domain' => $customDomain,
                'ip' => $request->ip(),
            ]);
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
        if (!$workspace = Workspace::whereJsonContains('custom_domains',$customDomain)->first()) {
            \Log::warning('Unknown domain', [
                'domain' => $customDomain,
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unknown domain',
                'error' => 'invalid_domain',
            ], 420);
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
