<?php

namespace NatLibFi\FinnaCodeSets\Source;

/**
 * Interface implemented by all configurable sources.
 */
interface ConfigurableSourceInterface
{
    /**
     * Get default configuration.
     *
     * @return array<string, mixed>
     */
    public static function getDefaultConfig(): array;

    /**
     * Set configuration.
     *
     * @param array<string, mixed> $config
     *     Configuration
     *
     * @return void
     */
    public function setConfig(array $config): void;
}
