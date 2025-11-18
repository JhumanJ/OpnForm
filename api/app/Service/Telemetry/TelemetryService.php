<?php

namespace App\Service\Telemetry;

use App\Models\Setting;
use App\Models\SettingsKey;

class TelemetryService
{
    /**
     * Check if telemetry should be sent.
     *
     * Telemetry only runs in production environment or when self-hosted mode
     * is enabled. It can be explicitly disabled via OPNFORM_TELEMETRY_DISABLED.
     *
     * @return bool
     */
    public function shouldSendTelemetry(): bool
    {
        // Explicitly disabled via environment variable
        if (!config('telemetry.enabled', true)) {
            return false;
        }

        // Only enable in production or self-hosted mode
        $isProduction = app()->environment('production');
        $isSelfHosted = config('app.self_hosted', false);

        return $isProduction || $isSelfHosted;
    }

    /**
     * Get the instance ID from settings.
     *
     * @return string|null
     */
    public function getInstanceId(): ?string
    {
        return Setting::get(SettingsKey::INSTANCE_ID);
    }

    /**
     * Get the telemetry endpoint URL.
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return config('telemetry.endpoint');
    }

    /**
     * Get the OpenPanel client ID.
     *
     * @return string|null
     */
    public function getClientId(): ?string
    {
        return config('telemetry.client_id');
    }

    /**
     * Get the OpenPanel client secret.
     *
     * @return string|null
     */
    public function getClientSecret(): ?string
    {
        return config('telemetry.client_secret');
    }
}
