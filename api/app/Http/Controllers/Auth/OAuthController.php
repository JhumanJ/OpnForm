<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\Traits\ManagesJWT;
use App\Http\Resources\OAuthProviderResource;
use App\Integrations\OAuth\Drivers\Contracts\OAuthDriver;
use App\Integrations\OAuth\Drivers\Contracts\WidgetOAuthDriver;
use App\Integrations\OAuth\Drivers\OAuthGoogleDriver;
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
            'intent' => 'required|in:auth,integration',
            'invite_token' => 'sometimes|string',
        ]);

        $providerService = OAuthProviderService::from($provider);
        $intent = $request->input('intent');
        $inviteToken = $request->input('invite_token');

        // Validate intent requirements
        $this->validateIntentRequirements($intent, $providerService);

        // Validate invite token and get invited email if provided
        $invitedEmail = null;
        if ($inviteToken && $intent === self::INTENT_AUTH) {
            $invitedEmail = $this->validateInviteToken($inviteToken);
        }

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
            $this->setCacheContextAuth([
                'intent' => $intent,
                'utm_data' => $request->input('utm_data'),
                'invited_email' => $invitedEmail
            ]);
        }

        // Set email restrictions on the driver if it's Google and we have an invited email
        $driver = $providerService->getDriver();
        if ($provider === 'google' && $invitedEmail && $driver instanceof OAuthGoogleDriver) {
            $driver->setEmailRestrictions([$invitedEmail]);
        }

        $url = $this->getRedirectUrl($driver, $scopes);
        return response()->json(['url' => $url]);
    }

    /**
     * Handle the OAuth callback from the provider.
     */
    public function callback(string $provider, Request $request)
    {
        $providerService = OAuthProviderService::from($provider);

        // Get user data from OAuth redirect
        $userData = $this->getUserDataFromRedirect($providerService);

        // Determine intent from cached context
        $intent = $this->getIntentFromContext();

        // For auth intent with invite token, validate and set cache context
        $invitedEmail = null;
        $inviteToken = $request->input('invite_token');
        if ($inviteToken && $intent === self::INTENT_AUTH) {
            $invitedEmail = $this->validateInviteToken($inviteToken);
        }

        return $this->handleIntent($intent, $providerService, $userData, $inviteToken, $invitedEmail);
    }

    /**
     * Handle widget-based OAuth callback.
     */
    public function handleWidgetCallback(string $service, Request $request)
    {
        $request->validate([
            'intent' => 'required|in:auth,integration',
            'invite_token' => 'sometimes|string',
        ]);

        $providerService = OAuthProviderService::from($service);
        $intent = $request->input('intent');
        $inviteToken = $request->input('invite_token');

        // Validate intent requirements
        $this->validateIntentRequirements($intent, $providerService);

        // For auth intent with invite token, validate and set cache context
        $invitedEmail = null;
        if ($inviteToken && $intent === self::INTENT_AUTH) {
            $invitedEmail = $this->validateInviteToken($inviteToken);
        }

        // Get user data from widget
        $userData = $this->getUserDataFromWidget($providerService, $request);

        return $this->handleIntent($intent, $providerService, $userData, $inviteToken, $invitedEmail);
    }

    private function setCacheContextAuth(array $context)
    {
        Cache::put("oauth-context:auth:" . session()->getId(), $context, now()->addMinutes(5));
    }

    private function getCacheContextAuth(): array
    {
        return Cache::get("oauth-context:auth:" . session()->getId(), []);
    }

    /**
     * Handle intent-based flow
     */
    private function handleIntent(string $intent, OAuthProviderService $providerService, array $userData, ?string $inviteToken = null, ?string $invitedEmail = null)
    {
        return match ($intent) {
            self::INTENT_AUTH => $this->handleAuthenticationFlow($providerService, $userData, $inviteToken, $invitedEmail),
            self::INTENT_INTEGRATION => $this->handleIntegrationFlow($providerService, $userData),
            default => abort(400, 'Invalid intent')
        };
    }

    /**
     * Handle authentication flow (create user + authenticate)
     */
    private function handleAuthenticationFlow(OAuthProviderService $providerService, array $userData, ?string $inviteToken = null, ?string $invitedEmail = null)
    {
        // Validate invite token restrictions for Google OAuth
        if ($providerService === OAuthProviderService::Google || $providerService === OAuthProviderService::GoogleOneTap) {
            $this->validateInviteEmailRestrictions($userData['email'], $invitedEmail);
        }

        // Find or create user
        $user = $this->oauthUserService->findOrCreateUser($userData, $providerService, $inviteToken);

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
            'name' => $socialiteUser->getName() ?? $socialiteUser->getNickname(),
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
        $context = $this->getCacheContextAuth();
        if ($context && isset($context['intent'])) {
            return $context['intent'];
        }

        // Default to auth if no context found
        return self::INTENT_AUTH;
    }

    private function getRedirectUrl(OAuthDriver $driver, array $scopes = []): string
    {
        if (!empty($scopes)) {
            $driver->setScopes($scopes);
        }

        return $driver->getRedirectUrl();
    }

    /**
     * Validate invite token and return invited email
     */
    private function validateInviteToken(string $inviteToken): string
    {
        $userInvite = \App\Models\UserInvite::where('token', $inviteToken)
            ->where('status', \App\Models\UserInvite::PENDING_STATUS)
            ->first();

        if (!$userInvite) {
            abort(400, 'Invalid invite token');
        }

        if ($userInvite->hasExpired()) {
            abort(400, 'Invite token has expired');
        }

        return $userInvite->email;
    }

    /**
     * Validate email matches invited email for OAuth authentication
     */
    private function validateInviteEmailRestrictions(string $email, ?string $invitedEmail = null): void
    {
        if ($invitedEmail && $email !== $invitedEmail) {
            abort(400, 'You must login with the invited email address: ' . $invitedEmail);
        }
    }
}
