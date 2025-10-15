<?php

if (!function_exists('front_url')) {
    function front_url($path = '')
    {
        $baseUrl = config('app.front_url') ?? config('app.url');
        if (! $baseUrl) {
            return $path;
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('pricing_enabled')) {
    function pricing_enabled(): bool
    {
        return !is_null(config('cashier.key')) && !config('app.self_hosted');
    }
}
