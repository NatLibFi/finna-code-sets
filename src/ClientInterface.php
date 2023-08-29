<?php

namespace NatLibFi\FinnaCodeSets;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

interface ClientInterface
{
    /**
     * Create and send an HTTP GET request.
     *
     * @param string|UriInterface $uri
     *     URI object or string.
     * @param array<mixed> $options
     *     Request options to apply.
     *
     * @throws ClientExceptionInterface
     */
    public function get(string|UriInterface $uri, array $options = []): ResponseInterface;
}
