<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\CacheInterface;
use NatLibFi\FinnaCodeSets\ClientInterface;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;
use Psr\Http\Client\ClientExceptionInterface;

abstract class AbstractApi
{
    /**
     * HTTP client.
     *
     * @var ClientInterface
     */
    protected ClientInterface $httpClient;

    /**
     * Cache.
     *
     * @var CacheInterface
     */
    protected CacheInterface $cache;

    /**
     * API base URL.
     *
     * @var string
     */
    protected string $apiBaseUrl;

    /**
     * AbstractConnection constructor.
     */
    public function __construct(
        ClientInterface $httpClient,
        CacheInterface $cache,
        string $apiBaseUrl
    ) {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
        $this->apiBaseUrl = $apiBaseUrl;
    }

    /**
     * Make an API request.
     *
     * @param string $method
     *   API method.
     * @param array<mixed>|object $query
     *   Query parameters as an argument for http_build_query().
     *
     * @return array<mixed>
     *   The response as an array.
     *
     * @throws ClientExceptionInterface
     * @throws ValueNotSetException
     */
    protected function apiGet(string $method, array|object $query = []): array
    {
        $uri = $this->apiBaseUrl . $method;
        if (!empty($query)) {
            $uri .= '?' . http_build_query($query);
        }
        if (!$this->cache->exists($uri)) {
            $response = $this->httpClient->get($uri);
            $this->cache->set($uri, json_decode($response->getBody(), true));
        }
        return $this->cache->get($uri);
    }

    /**
     * Asserts that the URL is for this API.
     *
     * @param string $url
     *
     * @return string
     *
     * @throws NotSupportedException
     */
    protected function assertBaseUrl(string $url): string
    {
        if (!str_starts_with($url, $this->apiBaseUrl)) {
            throw new NotSupportedException('API URL ' . $url);
        }
        return $url;
    }
}
