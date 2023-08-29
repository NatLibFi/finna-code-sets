<?php

namespace NatLibFi\FinnaCodeSets;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class DefaultClient implements ClientInterface
{
    /**
     * Guzzle HTTP client.
     *
     * @var Client
     */
    protected Client $client;

    /**
     * DefaultClient constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * {@inheritdoc}
     */
    public function get(string|UriInterface $uri, array $options = []): ResponseInterface
    {
        return $this->client->get($uri, $options);
    }
}
