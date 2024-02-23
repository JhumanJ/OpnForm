<?php

namespace App\Models\Traits;

use Closure;

interface CachableAttributes
{
    /**
     * Get an item from the cache, or execute the given Closure and store the result.
     *
     *
     * @return mixed
     */
    public function remember(string $key, ?int $ttl, Closure $callback);

    /**
     * Get an item from the cache, or execute the given Closure and store the result forever.
     *
     *
     * @return mixed
     */
    public function rememberForever(string $key, Closure $callback);

    /**
     * Remove an item from the cache.
     */
    public function forget(string $key): bool;

    /**
     * Remove all items from the cache.
     */
    public function flush(): bool;
}
