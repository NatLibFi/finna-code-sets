<?php

namespace NatLibFi\FinnaCodeSets\Source\Oph\Organisaatio;

use NatLibFi\FinnaCodeSets\Model\Organisation\Organisation;
use NatLibFi\FinnaCodeSets\Source\AbstractApiSource;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;

class OphOrganisaatio extends AbstractApiSource implements OphOrganisaatioInterface
{
    public function __construct(
        ClientInterface $httpClient,
        CacheItemPoolInterface $cache,
        string $apiBaseUrl = OphOrganisaatioInterface::DEFAULT_API_BASE_URL
    ) {
        parent::__construct($httpClient, $cache, $apiBaseUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrganisations(): array
    {
        $response = $this->apiGet(
            '/hae',
            [
                'aktiiviset' => 'true',
                'suunnitellut' => 'false',
                'lakkautetut' => 'false',
            ]
        );
        // Create objects from response.
        $organisations = [];
        foreach ($response['organisaatiot'] as $result) {
            $organisation = new Organisation($result, $this->getApiBaseUrl());
            $organisations[$organisation->getId()] = $organisation;
        }
        // Build object hierarchy.
        foreach ($organisations as $organisation) {
            if (count($path = $organisation->getParentOidPath()) > 1) {
                $parentId = $path[1];
                if (isset($organisations[$parentId])) {
                    $organisations[$parentId]->addChild($organisation);
                }
            }
        }
        // Leave only the first hierarchy level in the array.
        foreach ($organisations as $id => $organisation) {
            if ($organisation->getHierarchyLevel() > 1) {
                unset($organisations[$id]);
            }
        }
        return $organisations;
    }
}
