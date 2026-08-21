<?php

namespace NatLibFi\FinnaCodeSets\Source\NatLibFi\Finto;

use NatLibFi\FinnaCodeSets\Model\Concept\ConceptInterface;
use NatLibFi\FinnaCodeSets\Model\Concept\FintoChildConcept;
use NatLibFi\FinnaCodeSets\Model\Concept\FintoGraphConcept;
use NatLibFi\FinnaCodeSets\Model\Concept\FintoIndexConcept;
use NatLibFi\FinnaCodeSets\Model\Concept\FintoTopConcept;
use NatLibFi\FinnaCodeSets\Model\Keyword\Keyword;
use NatLibFi\FinnaCodeSets\Source\AbstractApiSource;
use NatLibFi\FinnaCodeSets\Utility\Assert;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;

class FintoSource extends AbstractApiSource implements FintoSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaultConfig(): array
    {
        return [
            'apiBaseUrl' => FintoSourceInterface::DEFAULT_API_BASE_URL,
        ];
    }

    /**
     * Constructor.
     *
     * @param ClientInterface $httpClient
     *     PSR-18 compliant HTTP Client
     * @param CacheItemPoolInterface $cache
     *     PSR-6 compliant caching system
     * @param array<string, mixed> $config
     *     Configuration
     */
    public function __construct(
        ClientInterface $httpClient,
        CacheItemPoolInterface $cache,
        array $config
    ) {
        parent::__construct($httpClient, $cache, $config['apiBaseUrl']);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config): void
    {
        $this->setApiBaseUrl($config['apiBaseUrl']);
    }

    /**
     * {@inheritdoc}
     */
    public function getVocabularyTopConcepts(string $vocid, ?string $langcode = null): array
    {
        $query = null !== $langcode ? ['lang' => $langcode] : [];
        $response = $this->apiGet('/' . $vocid . '/topConcepts', $query);
        $langcode = null !== $langcode ? $langcode : $response['@context']['@language'];
        $concepts = [];
        foreach ($response['topconcepts'] as $result) {
            $concept = new FintoTopConcept($result, $this->getApiBaseUrl(), $vocid, $langcode);
            $concepts[$concept->getId()] = $concept;
        }
        return $concepts;
    }

    /**
     * {@inheritdoc}
     */
    public function getVocabularyConceptData(string $vocid, string $uri, ?string $langcode = null): ConceptInterface
    {
        $query = ['format' => 'application/json', 'uri' => $uri];
        if (null !== $langcode) {
            $query['lang'] = $langcode;
        }
        $response = $this->apiGet('/' . $vocid . '/data', $query);
        return FintoGraphConcept::fromConceptData($response, $this->getApiBaseUrl(), $vocid, $uri);
    }

    /**
     * {@inheritdoc}
     */
    public function getVocabularyConceptChildren(string $vocid, string $uri, ?string $langcode = null): array
    {
        $query = ['uri' => $uri];
        if (null !== $langcode) {
            $query['lang'] = $langcode;
        }
        $response = $this->apiGet('/' . $vocid . '/children', $query);
        $langcode = null !== $langcode ? $langcode : $response['@context']['@language'];
        $concepts = [];
        foreach ($response['narrower'] as $result) {
            if (null !== $result['prefLabel']) {
                $concept = new FintoChildConcept($result, $this->getApiBaseUrl(), $vocid, $langcode);
                $concepts[$concept->getId()] = $concept;
            }
        }
        return $concepts;
    }

    /**
     * {@inheritdoc}
     */
    public function getVocabularyIndexLetters(string $vocid, ?string $langcode = null): array
    {
        $query = null !== $langcode ? ['lang' => $langcode] : [];
        $response = $this->apiGet('/' . $vocid . '/index/', $query);
        return $response['indexLetters'];
    }

    /**
     * {@inheritdoc}
     */
    public function getVocabularyIndex(string $vocid, string $letter, ?string $langcode = null): array
    {
        $query = null !== $langcode ? ['lang' => $langcode] : [];
        $response = $this->apiGet('/' . $vocid . '/index/' . $letter, $query);
        $concepts = [];
        foreach ($response['indexConcepts'] as $result) {
            if (FintoSourceInterface::VOCABULARY_FINTO_YSO === $vocid) {
                $concept = new Keyword($result, $this->getApiBaseUrl(), $vocid);
            } else {
                $concept = new FintoIndexConcept($result, $this->getApiBaseUrl(), $vocid);
            }
            $concepts[$concept->getId()] = $concept;
        }
        return $concepts;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywordsIndexLetters(string $langcode): array
    {
        return $this->getVocabularyIndexLetters(FintoSourceInterface::VOCABULARY_FINTO_YSO, $langcode);
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywordsIndex(string $langcode, string $letter): array
    {
        return Assert::keywords(
            $this->getVocabularyIndex(FintoSourceInterface::VOCABULARY_FINTO_YSO, $letter, $langcode),
        );
    }
}
