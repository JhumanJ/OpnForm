<?php

if (!function_exists('front_url')) {
    function front_url($path = '')
    {
        $baseUrl = config('app.front_url') ?? config('app.url');
        \Log::info('Base URL for front-end: ' . $baseUrl);
        if (! $baseUrl) {
            return $path;
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('pricing_enabled')) {
    function pricing_enabled(): bool
    {
        return App::environment() !== 'testing' && !is_null(config('cashier.key'));
    }
}
