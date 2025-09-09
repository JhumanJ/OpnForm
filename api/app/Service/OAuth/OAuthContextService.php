<?php

namespace App\Service\OAuth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OAuthContextService
{
    private const CACHE_TTL_MINUTES = 5;

    /**
     * Store OAuth context (automatically determines appropriate key)
     */
    public function storeContext(array $context): void
    {
        $key = $this->getContextKey();
        Cache::put($key, $context, now()->addMinutes(self::CACHE_TTL_MINUTES));
    }

    /**
     * Get context from cache
     */
    public function getContext(): ?array
    {
        return Cache::get($this->getContextKey());
    }

    /**
     * Clear context after use
     */
    public function clearContext(): void
    {
        Cache::forget($this->getContextKey());
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
     * Get UTM data from context
     */
    public function getUtmData(): ?array
    {
        $context = $this->getContext();
        return $context['utm_data'] ?? null;
    }

    /**
     * Get appropriate cache key based on authentication state
     */
    private function getContextKey(): string
    {
        if (Auth::check()) {
            // For authenticated users (integration flows)
            return "oauth-context:" . Auth::id();
        }

        // For guest users (auth flows)
        return "oauth-context:auth:" . session()->getId();
    }
}
