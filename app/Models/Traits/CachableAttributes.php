<?php

namespace App\Models\Traits;

use Closure;

interface CachableAttributes
{
    /**
     * Get an item from the cache, or execute the given Closure and store the result.
     *
     * @param string $key
     * @param int|null $ttl
     * @param Closure $callback
     *
     * @return mixed
     */
    public function remember(string $key, ?int $ttl, Closure $callback);

    /**
     * Get an item from the cache, or execute the given Closure and store the result forever.
     *
     * @param string $key
     * @param \Closure $callback
     *
     * @return mixed
     */
    public function rememberForever(string $key, Closure $callback);

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function forget(string $key): bool;

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush(): bool;
}
