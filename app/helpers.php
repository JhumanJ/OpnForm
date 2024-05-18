<?php

if(!function_exists('front_url')) {
    function front_url($path = '')
    {
        $baseUrl = config('app.front_url');
        if (! $baseUrl) {
            return $path;
        }

        return rtrim($baseUrl, '/').'/'.ltrim($path, '/');
    }
}
