<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\CacheTrait;
use NatLibFi\FinnaCodeSets\ClientInterface;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientExceptionInterface;

abstract class AbstractApi
{
    use CacheTrait;

    /**
     * HTTP client.
     *
     * @var ClientInterface
     */
    protected ClientInterface $httpClient;

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
        CacheItemPoolInterface $cache,
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
     */
    protected function apiGet(string $method, array|object $query = []): array
    {
        $uri = $this->apiBaseUrl . $method;
        if (!empty($query)) {
            $uri .= '?' . http_build_query($query);
        }
        $cacheKey = md5($uri);
        if (!$this->cacheHasItem($cacheKey)) {
            $response = $this->httpClient->get($uri);
            return $this->cacheSet($cacheKey, json_decode($response->getBody(), true));
        }
        return $this->cacheGet($cacheKey);
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
