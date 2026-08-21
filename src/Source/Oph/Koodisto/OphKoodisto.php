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
    /**
     * {@inheritdoc}
     */
    public static function getDefaultConfig(): array
    {
        return [
            'apiBaseUrl' => OphKoodistoInterface::DEFAULT_API_BASE_URL,
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
