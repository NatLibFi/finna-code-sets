<?php

namespace NatLibFi\FinnaCodeSets\Source;

/**
 * Implemented by source classes making API calls.
 */
interface ApiSourceInterface
{
    /**
     * Get base URL.
     *
     * @return string
     */
    public function getApiBaseUrl(): string;

    /**
     * Set base URL.
     *
     * @param string $baseUrl
     */
    public function setApiBaseUrl(string $baseUrl): void;
}
