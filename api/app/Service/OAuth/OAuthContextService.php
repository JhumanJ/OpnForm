<?php

namespace App\Service\OAuth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OAuthContextService
{
    private const CACHE_TTL_MINUTES = 5;

    /**
     * Store OAuth context with a unique state token
     */
    public function storeContext(array $context): string
    {
        // Generate a unique state token for this OAuth flow
        $stateToken = bin2hex(random_bytes(16));
        $key = "oauth-context:state:" . $stateToken;

        Cache::put($key, $context, now()->addMinutes(self::CACHE_TTL_MINUTES));

        return $stateToken;
    }

    /**
     * Get context from cache using state token
     */
    public function getContext(?string $stateToken = null): ?array
    {
        $stateToken = $stateToken ?? request()->input('state');

        if (!$stateToken) {
            return null;
        }

        $key = "oauth-context:state:" . $stateToken;
        return Cache::get($key);
    }

    /**
     * Clear context after use
     */
    public function clearContext(?string $stateToken = null): void
    {
        $stateToken = $stateToken ?? request()->input('state');

        if ($stateToken) {
            $key = "oauth-context:state:" . $stateToken;
            Cache::forget($key);
        }
    }

    /**
     * Determine intent from stored context
     */
    public function getIntent(): string
    {
        $context = $this->getContext();
        if (!isset($context['intent'])) {
            abort(419, 'OAuth context expired');
        }
        return $context['intent'];
    }

    /**
     * Get invited email from context
     */
    public function getInvitedEmail(): ?string
    {
        $context = $this->getContext();
        return $context['invited_email'] ?? null;
    }

    /**
     * Get invite token from context if present
     */
    public function getInviteToken(): ?string
    {
        $context = $this->getContext();
        return $context['invite_token'] ?? null;
    }

    /**
     * Get UTM data from context
     */
    public function getUtmData(): ?array
    {
        $context = $this->getContext();
        return $context['utm_data'] ?? null;
    }
}
