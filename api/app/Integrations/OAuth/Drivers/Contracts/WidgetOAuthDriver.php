<?php

namespace App\Integrations\OAuth\Drivers\Contracts;

interface WidgetOAuthDriver extends OAuthDriver
{
    /**
     * Verify the widget authentication data
     *
     * @param array $data The data received from the widget
     * @return bool Whether the data is valid
     */
    public function verifyWidgetData(array $data): bool;

    /**
     * Get user data from widget authentication data
     *
     * @param array $data The data received from the widget
     * @return array User data in a standardized format
     */
    public function getUserFromWidgetData(array $data): array;

    /**
     * Check if this driver uses widget-based authentication
     *
     * @return bool
     */
    public function isWidgetBased(): bool;
}
