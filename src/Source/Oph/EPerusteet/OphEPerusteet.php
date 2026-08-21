<?php

namespace NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet;

use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Model\StudyContents\StudyContentsInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;

class OphEPerusteet implements OphEPerusteetInterface
{
    protected EducationalLevelsSource $educationalLevels;

    protected EducationalSubjectsSource $educationalSubjects;

    protected TransversalCompetencesSource $transversalCompetences;

    protected VocationalQualificationsSource $vocationalQualifications;

    /**
     * {@inheritdoc}
     */
    public static function getDefaultConfig(): array
    {
        return [
            'apiBaseUrl' => OphEPerusteetInterface::DEFAULT_API_BASE_URL,
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
        $apiBaseUrl = $config['apiBaseUrl'];
        $this->educationalLevels = new EducationalLevelsSource($httpClient, $cache, $apiBaseUrl);
        $this->educationalSubjects
            = new EducationalSubjectsSource($httpClient, $cache, $apiBaseUrl, $this->educationalLevels);
        $this->transversalCompetences = new TransversalCompetencesSource($httpClient, $cache, $apiBaseUrl);
        $this->vocationalQualifications = new VocationalQualificationsSource($httpClient, $cache, $apiBaseUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config): void
    {
        $this->educationalLevels->setApiBaseUrl($config['apiBaseUrl']);
        $this->educationalSubjects->setApiBaseUrl($config['apiBaseUrl']);
        $this->transversalCompetences->setApiBaseUrl($config['apiBaseUrl']);
        $this->vocationalQualifications->setApiBaseUrl($config['apiBaseUrl']);
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalLevels(): array
    {
        return $this->educationalLevels->getEducationalLevels();
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjects(string $levelCodeValue): array
    {
        switch ($levelCodeValue) {
            case EducationalLevelInterface::BASIC_EDUCATION:
            case EducationalLevelInterface::UPPER_SECONDARY_SCHOOL:
                return $this->educationalSubjects->getEducationalSubjects($levelCodeValue);

            case EducationalLevelInterface::VOCATIONAL_EDUCATION:
                return $this->vocationalQualifications->getEducationalSubjects($levelCodeValue);
        }
        throw NotSupportedException::forEducationalLevel($levelCodeValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjectByUrl(string $url): EducationalSubjectInterface
    {
        if ($this->educationalSubjects->isSupportedEducationalSubjectUrl($url)) {
            return $this->educationalSubjects->getEducationalSubjectByUrl($url);
        } elseif ($this->vocationalQualifications->isSupportedEducationalSubjectUrl($url)) {
            return $this->vocationalQualifications->getEducationalSubjectByUrl($url);
        }
        throw new NotSupportedException($url);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedEducationalSubjectUrl(string $url): bool
    {
        return $this->educationalSubjects->isSupportedEducationalSubjectUrl($url)
            || $this->vocationalQualifications->isSupportedEducationalSubjectUrl($url);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransversalCompetences(string $levelCodeValue): array
    {
        return $this->transversalCompetences->getTransversalCompetences($levelCodeValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransversalCompetenceByUrl(string $url): StudyContentsInterface
    {
        return $this->transversalCompetences->getTransversalCompetenceByUrl($url);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedTransversalCompetenceUrl(string $url): bool
    {
        return $this->transversalCompetences->isSupportedTransversalCompetenceUrl($url);
    }

    /**
     * {@inheritdoc}
     */
    public function getVocationalUpperSecondaryQualifications(bool $includeUnits = true): array
    {
        return $this->vocationalQualifications->getVocationalUpperSecondaryQualifications($includeUnits);
    }

    /**
     * {@inheritdoc}
     */
    public function getFurtherVocationalQualifications(bool $includeUnits = true): array
    {
        return $this->vocationalQualifications->getFurtherVocationalQualifications($includeUnits);
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecialistVocationalQualifications(bool $includeUnits = true): array
    {
        return $this->vocationalQualifications->getSpecialistVocationalQualifications($includeUnits);
    }

    /**
     * {@inheritdoc}
     */
    public function getVocationalCommonUnits(): array
    {
        return $this->vocationalQualifications->getVocationalCommonUnits();
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedVocationalUnitUrl(string $url): bool
    {
        return $this->vocationalQualifications->isSupportedVocationalUnitUrl($url);
    }
}
