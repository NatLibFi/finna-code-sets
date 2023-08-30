<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;

interface SourceInterface
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

    /**
     * Asserts that the URL is for this source.
     *
     * @param string $url
     *
     * @return string
     *
     * @throws NotSupportedException
     */
    public function assertApiBaseUrl(string $url): string;
}
