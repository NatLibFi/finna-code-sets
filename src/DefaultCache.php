<?php

namespace NatLibFi\FinnaCodeSets;

use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;

/**
 * Default cache implementation.
 *
 * @internal
 */
class DefaultCache implements CacheInterface
{
    /**
     * Cache.
     *
     * @var array<mixed>
     */
    protected array $cache = [];

    /**
     * {@inheritdoc}
     */
    public function exists(string $key): bool
    {
        return array_key_exists($key, $this->cache);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key): mixed
    {
        if (!$this->exists($key)) {
            throw new ValueNotSetException($key);
        }
        return $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, mixed $value): void
    {
        $this->cache[$key] = $value;
    }
}
