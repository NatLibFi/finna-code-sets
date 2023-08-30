<?php

namespace NatLibFi\FinnaCodeSets;

use NatLibFi\FinnaCodeSets\Exception\UnexpectedValueException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

/**
 * Cache trait.
 *
 * @internal
 */
trait CacheTrait
{
    protected CacheItemPoolInterface $cache;

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key
     *   The key for which to check existence.
     *
     * @return bool
     *   True if item exists in the cache, false otherwise.
     */
    protected function cacheHasItem(string $key): bool
    {
        try {
            return $this->cache->hasItem($key);
        } catch (InvalidArgumentException $e) {
            throw new UnexpectedValueException('Cache key ' . $key, $e->getCode(), $e);
        }
    }

    /**
     * Set a value in the cache.
     *
     * @param string $key
     *     The key string for this cache item.
     * @param mixed $value
     *     The serializable value to be stored.
     *
     * @return mixed
     *     The serializable value to be stored.
     */
    protected function cacheSet(string $key, mixed $value): mixed
    {
        try {
            $this->cache->save($this->cache->getItem($key)->set($value));
        } catch (InvalidArgumentException $e) {
            throw new UnexpectedValueException('Cache key ' . $key, $e->getCode(), $e);
        }
        return $value;
    }

    /**
     * Get a value from the cache.
     *
     * @param string $key
     *     The key for which to return the corresponding cache item value.
     *
     * @return mixed
     *     The value corresponding to this cache item's key, or null if not found.
     */
    protected function cacheGet(string $key): mixed
    {
        try {
            return $this->cache->getItem($key)->get();
        } catch (InvalidArgumentException $e) {
            throw new UnexpectedValueException('Cache key ' . $key, $e->getCode(), $e);
        }
    }
}
