<?php

namespace NatLibFi\FinnaCodeSets\Source;

use GuzzleHttp\Psr7\Request;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Exception\UnexpectedValueException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

abstract class AbstractApiSource implements ApiSourceInterface
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
     * @var CacheItemPoolInterface
     */
    protected CacheItemPoolInterface $cache;

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
     * Assert base URL.
     *
     * @param string $url
     *
     * @return string
     *
     * @throws NotSupportedException
     */
    protected function assertApiBaseUrl(string $url): string
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
     * @throws UnexpectedValueException
     */
    protected function apiGet(string $method, array|object $query = []): array
    {
        $uri = $this->getApiBaseUrl() . $method;
        if (!empty($query)) {
            $uri .= '?' . http_build_query($query);
        }
        $item = $this->cache->getItem(md5($uri));
        if (!$item->isHit()) {
            $response = $this->httpClient->sendRequest(new Request('GET', $uri));
            if (null === ($decoded = json_decode($response->getBody(), true))) {
                throw new UnexpectedValueException('Unable to decode API response');
            }
            $this->cache->save($item->set($decoded));
        }
        return $item->get();
    }
}
