<?php

function front_url($path = '')
{
    $baseUrl = config('app.front_url');
    if (! $baseUrl) {
        return $path;
    }

    return rtrim($baseUrl, '/').'/'.ltrim($path, '/');
}
