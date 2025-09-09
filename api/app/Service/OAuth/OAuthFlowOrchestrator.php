<?php

namespace App\Service\OAuth;

use App\Http\Controllers\Auth\Traits\ManagesJWT;
use App\Http\Resources\OAuthProviderResource;
use App\Integrations\OAuth\OAuthProviderService;
use App\Service\OAuth\OAuthProviderService as OAuthProviderServiceClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OAuthFlowOrchestrator
{
    use ManagesJWT;

    public const INTENT_AUTH = 'auth';
    public const INTENT_INTEGRATION = 'integration';
    public const INTENTS = [self::INTENT_AUTH, self::INTENT_INTEGRATION];

    public function __construct(
        private OAuthContextService $contextService,
        private OAuthInviteService $inviteService,
        private OAuthUserDataService $userDataService,
        private OAuthUserService $oauthUserService,
        private OAuthProviderServiceClass $oauthProviderService
    ) {}

    /**
     * Process OAuth redirect request
     */
    public function processRedirect(string $provider, array $params): array
    {
        $providerService = OAuthProviderService::from($provider);
        $intent = $params['intent'];
        $inviteToken = $params['invite_token'] ?? null;

        // Validate intent requirements
        $this->validateIntentRequirements($intent, $providerService);

        // Handle invite validation for auth flows
        $invitedEmail = null;
        if ($inviteToken && $intent === self::INTENT_AUTH) {
            $invitedEmail = $this->inviteService->getInvitedEmail($inviteToken);
        }

        // Store context and get state token
        $stateToken = $this->contextService->storeContext([
            'intent' => $intent,
            'utm_data' => $params['utm_data'] ?? null,
            'invited_email' => $invitedEmail,
            'invite_token' => $inviteToken,
            'intention' => $params['intention'] ?? null,
            'autoClose' => $params['autoClose'] ?? false,
        ]);

        // Configure OAuth driver
        $driver = $providerService->getDriver();
        $scopes = $driver->getScopesForIntent($intent);

        // Generic approach: Set email restrictions if supported
        $this->inviteService->configureDriverEmailRestrictions($driver, $invitedEmail);

        // Set scopes
        if (!empty($scopes)) {
            $driver->setScopes($scopes);
        }

        // Set state parameter for OAuth flow
        $driver->setState($stateToken);

        return [
            'url' => $driver->getRedirectUrl(),
            'state' => $stateToken
        ];
    }

    /**
     * Process OAuth callback
     */
    public function processCallback(string $provider, array $params): array
    {
        $providerService = OAuthProviderService::from($provider);

        // Get user data from OAuth provider
        $userData = $this->userDataService->extractFromRedirect($providerService);

        // Get context using state token from OAuth callback
        $stateToken = $params['state'] ?? null;
        $context = $this->contextService->getContext($stateToken);

        if (!$context) {
            abort(419, 'OAuth context expired or invalid state');
        }

        $intent = $context['intent'];
        $invitedEmail = $context['invited_email'] ?? null;
        $inviteToken = $params['invite_token'] ?? $context['invite_token'] ?? null;

        // Clear the context after use
        $this->contextService->clearContext($stateToken);

        return $this->handleIntent($intent, $providerService, $userData, $inviteToken, $invitedEmail);
    }

    /**
     * Process widget-based OAuth callback
     */
    public function processWidgetCallback(string $service, Request $request): array
    {
        $providerService = OAuthProviderService::from($service);
        $intent = $request->input('intent');
        $inviteToken = $request->input('invite_token');

        // Validate intent requirements
        $this->validateIntentRequirements($intent, $providerService);

        // Handle invite validation for auth flows
        $invitedEmail = null;
        if ($inviteToken && $intent === self::INTENT_AUTH) {
            $invitedEmail = $this->inviteService->getInvitedEmail($inviteToken);
        }

        // Get user data from widget
        $userData = $this->userDataService->extractFromWidget($providerService, $request);

        return $this->handleIntent($intent, $providerService, $userData, $inviteToken, $invitedEmail);
    }

    /**
     * Handle intent-based flow
     */
    private function handleIntent(
        string $intent,
        OAuthProviderService $providerService,
        array $userData,
        ?string $inviteToken = null,
        ?string $invitedEmail = null
    ): array {
        return match ($intent) {
            self::INTENT_AUTH => $this->handleAuthenticationFlow(
                $providerService,
                $userData,
                $inviteToken,
                $invitedEmail
            ),
            self::INTENT_INTEGRATION => $this->handleIntegrationFlow(
                $providerService,
                $userData
            ),
            default => abort(400, 'Invalid intent')
        };
    }

    /**
     * Handle authentication flow (create user + authenticate)
     */
    private function handleAuthenticationFlow(
        OAuthProviderService $providerService,
        array $userData,
        ?string $inviteToken = null,
        ?string $invitedEmail = null
    ): array {
        // Generic email validation (no provider-specific logic)
        $this->inviteService->validateEmailRestrictions($userData['email'], $invitedEmail);

        // Find or create user with invite context
        $user = $this->oauthUserService->findOrCreateUser($userData, $providerService, $inviteToken);

        // Create/update OAuth provider record
        $this->oauthProviderService->createOrUpdateProvider($user, $providerService, $userData);

        // Return JWT token for authentication
        $response = $this->sendLoginResponse($user);
        return $response->getData(true);
    }

    /**
     * Handle integration flow (connect OAuth provider to existing user)
     */
    private function handleIntegrationFlow(OAuthProviderService $providerService, array $userData): array
    {
        if (!Auth::check()) {
            abort(401, 'Authentication required for integration connections');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Create/update OAuth provider record
        $oauthProvider = $this->oauthProviderService->createOrUpdateProvider(
            $user,
            $providerService,
            $userData
        );

        // Get cached context for additional response data
        $context = $this->contextService->getContext() ?? [];

        return [
            'provider' => OAuthProviderResource::make($oauthProvider),
            'autoClose' => $context['autoClose'] ?? false,
            'intention' => $context['intention'] ?? null,
        ];
    }

    /**
     * Validate intent requirements
     */
    private function validateIntentRequirements(string $intent, OAuthProviderService $providerService): void
    {
        if ($intent === self::INTENT_INTEGRATION && !Auth::check()) {
            abort(401, 'Integration requires authentication');
        }

        if (!$providerService->supportsIntent($intent)) {
            abort(400, "Service {$providerService->value} does not support intent {$intent}");
        }
    }
}
