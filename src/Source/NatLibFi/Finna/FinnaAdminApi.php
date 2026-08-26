<?php

namespace NatLibFi\FinnaCodeSets\Source\NatLibFi\Finna;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Model\Organisation\FinnaAdminOrganisation;
use NatLibFi\FinnaCodeSets\Source\AbstractApiSource;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;

class FinnaAdminApi extends AbstractApiSource implements FinnaAdminApiInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaultConfig(): array
    {
        return [
            'apiBaseUrl' => '',
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
    public function getOrganisations(): array
    {
        $response = $this->apiGet('/organization/list');
        $organisations = [];
        if (!is_array($response['organizations'] ?? null)) {
            throw new MissingValueException('organizations');
        }
        foreach ($response['organizations'] as $result) {
            $organisation = new FinnaAdminOrganisation($result, $this->getApiBaseUrl());
            $organisations[$organisation->getId()] = $organisation;
        }
        return $organisations;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrganisation(string $id): ?FinnaAdminOrganisation
    {
        $response = $this->apiGet('/organization/organization', ['id' => $id]);
        if (!is_array($response['organization'] ?? null)) {
            return null;
        }
        return new FinnaAdminOrganisation($response['organization'], $this->getApiBaseUrl());
    }
}
