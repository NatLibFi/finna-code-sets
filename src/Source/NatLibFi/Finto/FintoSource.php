<?php

namespace NatLibFi\FinnaCodeSets\Source\NatLibFi\Finto;

use NatLibFi\FinnaCodeSets\Model\Keyword\Keyword;
use NatLibFi\FinnaCodeSets\Source\AbstractApiSource;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;

class FintoSource extends AbstractApiSource implements FintoSourceInterface
{
    /**
     * FintoSource constructor.
     */
    public function __construct(
        ClientInterface $httpClient,
        CacheItemPoolInterface $cache,
        string $apiBaseUrl = FintoSourceInterface::DEFAULT_API_BASE_URL
    ) {
        parent::__construct($httpClient, $cache, $apiBaseUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywordsIndexLetters(string $langcode): array
    {
        $response = $this->apiGet('/yso/index/', ['lang' => $langcode]);
        return $response['indexLetters'];
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywordsIndex(string $langcode, string $letter): array
    {
        $response = $this->apiGet('/yso/index/' . $letter, ['lang' => $langcode]);
        $keywords = [];
        foreach ($response['indexConcepts'] as $result) {
            $keyword = new Keyword($result, $this->getApiBaseUrl());
            $keywords[$keyword->getId()] = $keyword;
        }
        return $keywords;
    }
}
