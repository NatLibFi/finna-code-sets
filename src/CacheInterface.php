<?php

namespace NatLibFi\FinnaCodeSets;

use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;

interface CacheInterface
{
    /**
     * Does the cache key exist?
     *
     * @param string $key
     *     Cache key
     *
     * @return bool
     */
    public function exists(string $key): bool;

    /**
     * Get value for cache key.
     *
     * @param string $key
     *     Cache key
     *
     * @return mixed
     *
     * @throws ValueNotSetException If the cache key does not exist
     */
    public function get(string $key): mixed;

    /**
     * Set value for cache key.
     *
     * @param string $key
     *     Cache key
     * @param mixed $value
     *     Value
     */
    public function set(string $key, mixed $value): void;
}
