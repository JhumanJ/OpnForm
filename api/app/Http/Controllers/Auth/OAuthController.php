<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Integrations\OAuth\OAuthConnectionService;
use App\Integrations\OAuth\OAuthProviderService;
use App\Integrations\OAuth\Strategies\AuthenticationStrategy;
use App\Integrations\OAuth\Strategies\IntegrationStrategy;
use App\Integrations\OAuth\Strategies\WidgetStrategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OAuthController extends Controller
{
    public function __construct(private OAuthConnectionService $oauthService)
    {
    }

    /**
     * Redirect the user to the provider authentication page.
     */
    public function redirect(Request $request, string $provider)
    {
        $providerService = OAuthProviderService::from($provider);
        $intent = $request->input('intent', 'auth');

        // Guard Clause: Prevent auth flow if provider can't create users
        if ($intent === 'auth' && !$providerService->getDriver()->canCreateUser()) {
            return response()->json(['message' => 'This provider does not support user sign-ups.'], 422);
        }

        $scopes = $providerService->getDriver()->getScopesForIntent($intent);

        // For integrations, store context for the callback
        if ($intent === 'integration') {
            $context = [
                'intention' => $request->input('intention'),
                'autoClose' => $request->boolean('autoClose', false),
            ];
            Cache::put("oauth-context:" . Auth::id(), $context, now()->addMinutes(5));
        }

        $url = $this->oauthService->getRedirectUrl($providerService, $scopes);
        ray($url);
        return response()->json(['url' => $url]);
    }

    /**
     * Handle the OAuth callback from the provider.
     */
    public function callback(string $provider)
    {
        $providerService = OAuthProviderService::from($provider);

        // Strategy is determined by the user's authentication state
        $strategy = Auth::check()
            ? app(IntegrationStrategy::class)
            : app(AuthenticationStrategy::class);

        return $this->oauthService->handleCallback($providerService, $strategy);
    }

    /**
     * Handle widget-based OAuth callback (for providers that support widget authentication).
     */
    public function handleWidgetCallback(string $service, Request $request)
    {
        $providerService = OAuthProviderService::from($service);
        $widgetStrategy = app(WidgetStrategy::class);
        return $widgetStrategy->handleWidgetCallback($providerService, $request);
    }
}
