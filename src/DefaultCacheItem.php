<?php

namespace NatLibFi\FinnaCodeSets;

use Psr\Cache\CacheItemInterface;

/**
 * Default PSR-6 cache item implementation.
 *
 * Partial implementation with no support for time to live (TTL).
 *
 * @internal
 */
class DefaultCacheItem implements CacheItemInterface
{
    protected string $key;

    protected mixed $value;

    protected bool $isHit;

    public function __construct(
        string $key,
        mixed $value,
        bool $isHit
    ) {
        $this->key = $key;
        $this->value = $value;
        $this->isHit = $isHit;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function get(): mixed
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function isHit(): bool
    {
        return $this->isHit;
    }

    /**
     * {@inheritdoc}
     */
    public function set(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt(?\DateTimeInterface $expiration): static
    {
        // Not supported.
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAfter(\DateInterval|int|null $time): static
    {
        // Not supported.
        return $this;
    }
}
