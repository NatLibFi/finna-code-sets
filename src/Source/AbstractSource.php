<?php

namespace NatLibFi\FinnaCodeSets\Source;

use GuzzleHttp\Psr7\Request;
use NatLibFi\FinnaCodeSets\CacheTrait;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

abstract class AbstractSource implements SourceInterface
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
    private string $apiBaseUrl;

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
        $this->setApiBaseUrl($apiBaseUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function getApiBaseUrl(): string
    {
        return $this->apiBaseUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setApiBaseUrl(string $apiBaseUrl): void
    {
        $this->apiBaseUrl = $apiBaseUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function assertApiBaseUrl(string $url): string
    {
        if (!str_starts_with($url, $this->getApiBaseUrl())) {
            throw new NotSupportedException('API URL ' . $url);
        }
        return $url;
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
        $uri = $this->getApiBaseUrl() . $method;
        if (!empty($query)) {
            $uri .= '?' . http_build_query($query);
        }
        $cacheKey = md5($uri);
        if (!$this->cacheHasItem($cacheKey)) {
            $response = $this->httpClient->sendRequest(new Request('GET', $uri));
            return $this->cacheSet($cacheKey, json_decode($response->getBody(), true));
        }
        return $this->cacheGet($cacheKey);
    }
}
