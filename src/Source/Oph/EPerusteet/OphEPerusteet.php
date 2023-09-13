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
     * OphEPerusteet constructor.
     */
    public function __construct(
        ClientInterface $httpClient,
        CacheItemPoolInterface $cache,
        string $apiBaseUrl = OphEPerusteetInterface::DEFAULT_API_BASE_URL
    ) {
        $this->educationalLevels = new EducationalLevelsSource($httpClient, $cache, $apiBaseUrl);
        $this->educationalSubjects
            = new EducationalSubjectsSource($httpClient, $cache, $apiBaseUrl, $this->educationalLevels);
        $this->transversalCompetences = new TransversalCompetencesSource($httpClient, $cache, $apiBaseUrl);
        $this->vocationalQualifications = new VocationalQualificationsSource($httpClient, $cache, $apiBaseUrl);
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
    public function addEquivalentEducationalLevels(EducationalLevelInterface $educationalLevel): void
    {
        $this->educationalLevels->addEquivalentEducationalLevels($educationalLevel);
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
}
