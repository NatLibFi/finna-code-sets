<?php

namespace NatLibFi\FinnaCodeSets;

use GuzzleHttp\Client;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Source\Dvv\Koodistot\DvvKoodistot;
use NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet\OphEPerusteet;
use NatLibFi\FinnaCodeSets\Source\Oph\Koodisto\OphKoodisto;
use NatLibFi\FinnaCodeSets\Source\Oph\Organisaatio\OphOrganisaatio;
use NatLibFi\FinnaCodeSets\Utility\EducationalData;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;

class FinnaCodeSets implements FinnaCodeSetsInterface
{
    use CacheTrait;

    protected DvvKoodistot $dvvKoodistot;

    protected OphEPerusteet $ophEPerusteet;

    protected OphKoodisto $ophKoodisto;

    protected OphOrganisaatio $ophOrganisaatio;

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
        $this->educationalData = new EducationalData($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getCache(): CacheItemPoolInterface
    {
        return $this->cache;
    }

    /**
     * {@inheritdoc}
     */
    public function educationalData(): EducationalData
    {
        return $this->educationalData;
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalLevels(): array
    {
        $cacheKey = md5(__METHOD__);
        if (!$this->cacheHasItem($cacheKey)) {
            $educationalLevels = $this->dvvKoodistot->getEducationalLevels();
            foreach ($educationalLevels as $educationalLevel) {
                $this->addEquivalentEducationalLevels($educationalLevel);
            }
            return $this->cacheSet($cacheKey, $educationalLevels);
        }
        return $this->cacheGet($cacheKey);
    }

    /**
     * {@inheritdoc}
     */
    public function addEquivalentEducationalLevels(EducationalLevelInterface $educationalLevel): void
    {
        if ($educationalLevel->getCodeValue() === EducationalLevelInterface::BASIC_EDUCATION) {
            $this->ophEPerusteet->addEquivalentEducationalLevels($educationalLevel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjects(string $levelCodeValue): array
    {
        switch ($levelCodeValue) {
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
        return $this->ophEPerusteet->getEducationalSubjectByUrl($url);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedEducationalSubjectUrl(string $url): bool
    {
        return $this->ophEPerusteet->isSupportedEducationalSubjectUrl($url);
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
        return $this->ophEPerusteet->getTransversalCompetences($levelCodeValue);
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
}
