<?php

namespace NatLibFi\FinnaCodeSets;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Default PSR-6 cache item pool implementation.
 *
 * Partial in-memory implementation with no support for deferred items or
 * time to live (TTL).
 *
 * @internal
 */
class DefaultCacheItemPool implements CacheItemPoolInterface
{
    /**
     * Cache items.
     *
     * @var array<mixed>
     */
    protected array $cache = [];

    /**
     * {@inheritdoc}
     */
    public function getItem($key): CacheItemInterface
    {
        return new DefaultCacheItem($key, $this->cache[$key] ?? null, $this->hasItem($key));
    }

    /**
     * {@inheritdoc}
     *
     * @return array<CacheItemInterface>|\Traversable<CacheItemInterface>
     */
    public function getItems(array $keys = []): iterable
    {
        $items = [];
        foreach ($keys as $key) {
            $item = $this->getItem($key);
            $items[$item->getKey()] = $item;
        }
        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key): bool
    {
        return array_key_exists($key, $this->cache);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): bool
    {
        $this->cache = [];
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key): bool
    {
        unset($this->cache[$key]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->deleteItem($key);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item): bool
    {
        $this->cache[$item->getKey()] = $item->get();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        // Not supported.
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): bool
    {
        // Not supported.
        return false;
    }
}
