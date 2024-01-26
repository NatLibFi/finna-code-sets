<?php

namespace NatLibFi\FinnaCodeSets;

use GuzzleHttp\Client;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Model\StudyContents\StudyContentsInterface;
use NatLibFi\FinnaCodeSets\Source\Dvv\Koodistot\DvvKoodistot;
use NatLibFi\FinnaCodeSets\Source\NatLibFi\Finna\FinnaCodeSetsSource;
use NatLibFi\FinnaCodeSets\Source\NatLibFi\Finto\FintoSource;
use NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet\OphEPerusteet;
use NatLibFi\FinnaCodeSets\Source\Oph\Koodisto\OphKoodisto;
use NatLibFi\FinnaCodeSets\Source\Oph\Organisaatio\OphOrganisaatio;
use NatLibFi\FinnaCodeSets\Utility\EducationalData;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;

class FinnaCodeSets implements FinnaCodeSetsInterface
{
    protected CacheItemPoolInterface $cache;

    protected DvvKoodistot $dvvKoodistot;

    protected OphEPerusteet $ophEPerusteet;

    protected OphKoodisto $ophKoodisto;

    protected OphOrganisaatio $ophOrganisaatio;

    protected FinnaCodeSetsSource $finna;

    protected FintoSource $finto;

    protected EducationalData $educationalData;

    /**
     * FinnaCodeSets constructor.
     *
     * @param ClientInterface|null $httpClient
     *     PSR-18 compliant HTTP Client, or null for default client.
     *     Note that if using this library in a project that requires Guzzle 6
     *     you will have to pass a PSR-18 compliant HTTP Client because the
     *     default client depends on the PSR-18 support in Guzzle 7.
     * @param CacheItemPoolInterface|null $cache
     *     PSR-6 compliant caching system, or null for default cache.
     */
    public function __construct(
        ClientInterface $httpClient = null,
        CacheItemPoolInterface $cache = null
    ) {
        if (null === $httpClient) {
            $httpClient = new Client();
        }
        if (null === $cache) {
            $cache = new DefaultCacheItemPool();
        }
        $this->cache = $cache;
        $this->dvvKoodistot = new DvvKoodistot($httpClient, $cache);
        $this->ophEPerusteet = new OphEPerusteet($httpClient, $cache);
        $this->ophKoodisto = new OphKoodisto($httpClient, $cache);
        $this->ophOrganisaatio = new OphOrganisaatio($httpClient, $cache);
        $this->finna = new FinnaCodeSetsSource($httpClient, $cache);
        $this->finto = new FintoSource($httpClient, $cache);
        $this->educationalData = new EducationalData($this, $this->ophEPerusteet);
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalData(): EducationalData
    {
        return $this->educationalData;
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalLevels(): array
    {
        return $this->dvvKoodistot->getEducationalLevels();
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjects(string $levelCodeValue): array
    {
        switch ($levelCodeValue) {
            case EducationalLevelInterface::EARLY_CHILDHOOD_EDUCATION:
                return $this->finna->getEducationalSubjects($levelCodeValue);

            case EducationalLevelInterface::BASIC_EDUCATION:
            case EducationalLevelInterface::UPPER_SECONDARY_SCHOOL:
            case EducationalLevelInterface::VOCATIONAL_EDUCATION:
                return $this->ophEPerusteet->getEducationalSubjects($levelCodeValue);

            case EducationalLevelInterface::HIGHER_EDUCATION:
                return $this->ophKoodisto->getEducationalSubjects($levelCodeValue);
        }
        throw NotSupportedException::forEducationalLevel($levelCodeValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjectByUrl(string $url): EducationalSubjectInterface
    {
        if ($this->ophEPerusteet->isSupportedEducationalSubjectUrl($url)) {
            return $this->ophEPerusteet->getEducationalSubjectByUrl($url);
        } elseif ($this->finna->isSupportedEducationalSubjectUrl($url)) {
            return $this->finna->getEducationalSubjectByUrl($url);
        }
        throw new NotSupportedException($url);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedEducationalSubjectUrl(string $url): bool
    {
        return $this->ophEPerusteet->isSupportedEducationalSubjectUrl($url)
            || $this->finna->isSupportedEducationalSubjectUrl($url);
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywordsIndexLetters(string $langcode): array
    {
        return $this->finto->getKeywordsIndexLetters($langcode);
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywordsIndex(string $langcode, string $letter): array
    {
        return $this->finto->getKeywordsIndex($langcode, $letter);
    }

    /**
     * {@inheritdoc}
     */
    public function getLicences(): array
    {
        return $this->dvvKoodistot->getLicences();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrganisations(): array
    {
        return $this->ophOrganisaatio->getOrganisations();
    }

    /**
     * {@inheritdoc}
     */
    public function getTransversalCompetences(string $levelCodeValue): array
    {
        switch ($levelCodeValue) {
            case EducationalLevelInterface::EARLY_CHILDHOOD_EDUCATION:
                return $this->finna->getTransversalCompetences($levelCodeValue);

            case EducationalLevelInterface::BASIC_EDUCATION:
            case EducationalLevelInterface::UPPER_SECONDARY_SCHOOL:
                return $this->ophEPerusteet->getTransversalCompetences($levelCodeValue);
        }
        throw NotSupportedException::forEducationalLevel($levelCodeValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransversalCompetenceByUrl(string $url): StudyContentsInterface
    {
        if ($this->ophEPerusteet->isSupportedTransversalCompetenceUrl($url)) {
            return $this->ophEPerusteet->getTransversalCompetenceByUrl($url);
        } elseif ($this->finna->isSupportedTransversalCompetenceUrl($url)) {
            return $this->finna->getTransversalCompetenceByUrl($url);
        }
        throw new NotSupportedException($url);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedTransversalCompetenceUrl(string $url): bool
    {
        return $this->ophEPerusteet->isSupportedTransversalCompetenceUrl($url)
            || $this->finna->isSupportedTransversalCompetenceUrl($url);
    }

    /**
     * {@inheritdoc}
     */
    public function getVocationalUpperSecondaryQualifications(bool $includeUnits = true): array
    {
        return $this->ophEPerusteet->getVocationalUpperSecondaryQualifications($includeUnits);
    }

    /**
     * {@inheritdoc}
     */
    public function getFurtherVocationalQualifications(bool $includeUnits = true): array
    {
        return $this->ophEPerusteet->getFurtherVocationalQualifications($includeUnits);
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecialistVocationalQualifications(bool $includeUnits = true): array
    {
        return $this->ophEPerusteet->getSpecialistVocationalQualifications($includeUnits);
    }

    /**
     * {@inheritdoc}
     */
    public function getVocationalCommonUnits(): array
    {
        return $this->ophEPerusteet->getVocationalCommonUnits();
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedVocationalUnitUrl(string $url): bool
    {
        return $this->ophEPerusteet->isSupportedVocationalUnitUrl($url);
    }
}
