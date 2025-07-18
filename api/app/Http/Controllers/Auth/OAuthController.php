<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\Traits\ManagesJWT;
use App\Http\Resources\OAuthProviderResource;
use App\Integrations\OAuth\Drivers\Contracts\WidgetOAuthDriver;
use App\Integrations\OAuth\OAuthConnectionService;
use App\Integrations\OAuth\OAuthProviderService;
use App\Service\OAuth\OAuthUserService;
use App\Service\OAuth\OAuthProviderService as OAuthProviderServiceClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OAuthController extends Controller
{
    use ManagesJWT;

    public const INTENT_AUTH = 'auth';
    public const INTENT_INTEGRATION = 'integration';

    public function __construct(
        private OAuthConnectionService $oauthConnectionService,
        private OAuthUserService $oauthUserService,
        private OAuthProviderServiceClass $oauthProviderService
    ) {
    }

    /**
     * Redirect the user to the provider authentication page.
     */
    public function redirect(Request $request, string $provider)
    {
        $request->validate([
            'intent' => 'required|in:auth,integration'
        ]);

        $providerService = OAuthProviderService::from($provider);
        $intent = $request->input('intent');

        // Validate intent requirements
        $this->validateIntentRequirements($intent, $providerService);

        $scopes = $providerService->getDriver()->getScopesForIntent($intent);

        // For integrations, store context for the callback
        if ($intent === self::INTENT_INTEGRATION) {
            $context = [
                'intent' => $intent,
                'intention' => $request->input('intention'),
                'autoClose' => $request->boolean('autoClose', false),
            ];
            Cache::put("oauth-context:" . Auth::id(), $context, now()->addMinutes(5));
        } else {
            // For auth, store UTM data
            Cache::put("oauth-context:auth:" . session()->getId(), [
                'intent' => $intent,
                'utm_data' => $request->input('utm_data')
            ], now()->addMinutes(5));
        }

        $url = $this->oauthConnectionService->getRedirectUrl($providerService, $scopes);
        return response()->json(['url' => $url]);
    }

    /**
     * Handle the OAuth callback from the provider.
     */
    public function callback(string $provider)
    {
        $providerService = OAuthProviderService::from($provider);

        // Get user data from OAuth redirect
        $userData = $this->getUserDataFromRedirect($providerService);

        // Determine intent from cached context
        $intent = $this->getIntentFromContext();

        return $this->handleIntent($intent, $providerService, $userData);
    }

    /**
     * Handle widget-based OAuth callback.
     */
    public function handleWidgetCallback(string $service, Request $request)
    {
        $request->validate([
            'intent' => 'required|in:auth,integration'
        ]);

        $providerService = OAuthProviderService::from($service);
        $intent = $request->input('intent');

        // Validate intent requirements
        $this->validateIntentRequirements($intent, $providerService);

        // Get user data from widget
        $userData = $this->getUserDataFromWidget($providerService, $request);

        return $this->handleIntent($intent, $providerService, $userData);
    }

    /**
     * Handle intent-based flow
     */
    private function handleIntent(string $intent, OAuthProviderService $providerService, array $userData)
    {
        return match ($intent) {
            self::INTENT_AUTH => $this->handleAuthenticationFlow($providerService, $userData),
            self::INTENT_INTEGRATION => $this->handleIntegrationFlow($providerService, $userData),
            default => abort(400, 'Invalid intent')
        };
    }

    /**
     * Handle authentication flow (create user + authenticate)
     */
    private function handleAuthenticationFlow(OAuthProviderService $providerService, array $userData)
    {
        // Find or create user
        $user = $this->oauthUserService->findOrCreateUser($userData, $providerService);

        // Create/update OAuth provider record
        $this->oauthProviderService->createOrUpdateProvider($user, $providerService, $userData);

        // Return JWT token for authentication
        return $this->sendLoginResponse($user);
    }

    /**
     * Handle integration flow (connect OAuth provider to existing user)
     */
    private function handleIntegrationFlow(OAuthProviderService $providerService, array $userData)
    {
        if (!Auth::check()) {
            abort(401, 'Authentication required for integration connections');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Create/update OAuth provider record
        $oauthProvider = $this->oauthProviderService->createOrUpdateProvider($user, $providerService, $userData);

        // Get cached context
        $context = Cache::pull("oauth-context:{$user->id}", [
            'intention' => null,
            'autoClose' => false
        ]);

        return response()->json([
            'provider' => OAuthProviderResource::make($oauthProvider),
            'autoClose' => $context['autoClose'],
            'intention' => $context['intention'],
        ]);
    }

    // Helper methods
    private function validateIntentRequirements(string $intent, OAuthProviderService $providerService): void
    {
        if ($intent === self::INTENT_INTEGRATION && !Auth::check()) {
            abort(401, 'Integration requires authentication');
        }

        if (!$providerService->supportsIntent($intent)) {
            abort(400, "Service {$providerService->value} does not support intent {$intent}");
        }
    }

    private function getUserDataFromRedirect(OAuthProviderService $providerService): array
    {
        $driver = $providerService->getDriver();
        $socialiteUser = $driver->getUser();

        return [
            'id' => $socialiteUser->getId(),
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
            'provider_user_id' => $socialiteUser->getId(),
            'access_token' => $socialiteUser->token,
            'refresh_token' => $socialiteUser->refreshToken,
            'avatar' => $socialiteUser->getAvatar(),
            'scopes' => $socialiteUser->approvedScopes ?? [],
        ];
    }

    private function getUserDataFromWidget(OAuthProviderService $providerService, Request $request): array
    {
        $driver = $providerService->getDriver();

        if (!$driver instanceof WidgetOAuthDriver) {
            abort(400, 'This provider does not support widget authentication');
        }

        if (!$driver->verifyWidgetData($request->all())) {
            abort(400, 'Invalid widget data');
        }

        return $driver->getUserFromWidgetData($request->all());
    }

    private function getIntentFromContext(): string
    {
        // Try authenticated user context first
        if (Auth::check()) {
            $context = Cache::get("oauth-context:" . Auth::id());
            if ($context && isset($context['intent'])) {
                return $context['intent'];
            }
        }

        // Try session context for auth flows
        $context = Cache::get("oauth-context:auth:" . session()->getId());
        if ($context && isset($context['intent'])) {
            return $context['intent'];
        }

        // Default to auth if no context found
        return self::INTENT_AUTH;
    }
}
