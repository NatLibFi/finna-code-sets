<?php

namespace NatLibFi\FinnaCodeSets\Source\Oph\Koodisto;

use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\OphKoodistoEducationalSubject;
use NatLibFi\FinnaCodeSets\Source\AbstractApiSource;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;

class OphKoodisto extends AbstractApiSource implements OphKoodistoInterface
{
    public function __construct(
        ClientInterface $httpClient,
        CacheItemPoolInterface $cache,
        string $apiBaseUrl = OphKoodistoInterface::DEFAULT_API_BASE_URL
    ) {
        parent::__construct($httpClient, $cache, $apiBaseUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjects(string $levelCodeValue): array
    {
        if (EducationalLevelInterface::HIGHER_EDUCATION === $levelCodeValue) {
            return $this->processApiResponse(
                $this->apiGet('/tieteenala/koodi'),
                $levelCodeValue
            );
        }
        throw NotSupportedException::forEducationalLevel($levelCodeValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjectByUrl(string $url): EducationalSubjectInterface
    {
        // @todo Implement getEducationalSubjectByUrl() method.
        throw new NotSupportedException($url);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedEducationalSubjectUrl(string $url): bool
    {
        // @todo Implement getEducationalSubjectByUrl() method.
        return false;
    }

    /**
     * Process API response.
     *
     * @param array<mixed> $response
     *
     * @return array<OphKoodistoEducationalSubject>
     */
    protected function processApiResponse(array $response, string $levelCodeValue): array
    {
        $educationalSubjects = [];
        foreach ($response as $result) {
            $educationalSubject = new OphKoodistoEducationalSubject($result, $this->getApiBaseUrl(), $levelCodeValue);
            $educationalSubjects[$educationalSubject->getId()] = $educationalSubject;
        }
        // @todo Hierarchy
        return $educationalSubjects;
    }
}
