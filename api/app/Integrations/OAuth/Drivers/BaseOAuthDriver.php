<?php

namespace App\Integrations\OAuth\Drivers;

use App\Integrations\OAuth\Drivers\Contracts\OAuthDriver;

abstract class BaseOAuthDriver implements OAuthDriver
{
    protected array $additionalParameters = [];

    /**
     * Default implementation - no email restrictions support
     */
    public function setEmailRestrictions(array $emails): self
    {
        // No-op for drivers that don't support email restrictions
        return $this;
    }

    /**
     * Default implementation - no email restrictions support
     */
    public function supportsEmailRestrictions(): bool
    {
        return false;
    }

    /**
     * Set additional OAuth parameters
     */
    public function setAdditionalParameters(array $parameters): self
    {
        $this->additionalParameters = array_merge($this->additionalParameters, $parameters);
        return $this;
    }

    /**
     * Get all additional parameters for OAuth redirect
     */
    protected function getAdditionalParameters(): array
    {
        return $this->additionalParameters;
    }
}
