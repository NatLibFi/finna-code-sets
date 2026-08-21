<?php

namespace NatLibFi\FinnaCodeSets\Source\Dvv\Koodistot;

use NatLibFi\FinnaCodeSets\Model\EducationalLevel\DvvKoodistotEducationalLevel;
use NatLibFi\FinnaCodeSets\Model\Licence\Licence;
use NatLibFi\FinnaCodeSets\Source\AbstractApiSource;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;

class DvvKoodistot extends AbstractApiSource implements DvvKoodistotInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaultConfig(): array
    {
        return [
            'apiBaseUrl' => DvvKoodistotInterface::DEFAULT_API_BASE_URL,
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
    public function getLicences(): array
    {
        $response = $this->apiGet('/coderegistries/edtech/codeschemes/Licence/codes');
        $licences = [];
        foreach ($response['results'] as $result) {
            $licences[$result['id']] = new Licence($result, $this->getApiBaseUrl());
        }
        return $licences;
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalLevels(): array
    {
        $response = $this->apiGet('/coderegistries/edtech/codeschemes/Koulutusaste/codes');
        // Create objects from response.
        $educationalLevels = [];
        foreach ($response['results'] as $result) {
            $educationalLevel = new DvvKoodistotEducationalLevel($result, $this->getApiBaseUrl());
            $educationalLevels[$educationalLevel->getId()] = $educationalLevel;
        }
        // Build object hierarchy.
        foreach ($educationalLevels as $id => $educationalLevel) {
            $broaderCodeId = $educationalLevel->getBroaderCodeId();
            if ($broaderCodeId && isset($educationalLevels[$broaderCodeId])) {
                $educationalLevels[$broaderCodeId]->addChild($educationalLevel);
                // Leave only the first hierarchy level in the array.
                unset($educationalLevels[$id]);
            }
        }
        return $educationalLevels;
    }
}
