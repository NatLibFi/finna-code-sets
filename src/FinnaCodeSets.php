<?php

namespace NatLibFi\FinnaCodeSets;

use GuzzleHttp\Client;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Source\Dvv\Koodistot\DvvKoodistot;
use NatLibFi\FinnaCodeSets\Source\Dvv\Koodistot\DvvKoodistotInterface;
use NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet\OphEPerusteet;
use NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet\OphEPerusteetInterface;
use NatLibFi\FinnaCodeSets\Source\Oph\Koodisto\OphKoodisto;
use NatLibFi\FinnaCodeSets\Source\Oph\Koodisto\OphKoodistoInterface;
use NatLibFi\FinnaCodeSets\Source\Oph\Organisaatio\OphOrganisaatio;
use NatLibFi\FinnaCodeSets\Source\Oph\Organisaatio\OphOrganisaatioInterface;
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
     * @param CacheItemPoolInterface|null $cache
     *     PSR-6 compliant caching system, or null for default cache.
     * @param string $dvvKoodistotApiBaseUrl
     *     DVV koodistot API base URL (optional).
     * @param string $ophEPerusteetApiBaseUrl
     *     OPH ePerusteet API base URL (optional).
     * @param string $ophKoodistoApiBaseUrl
     *     OPH Koodisto API base URL (optional).
     * @param string $ophOrganisaatioApiBaseUrl
     *     OPH Organisaatio API base URL (optional).
     */
    public function __construct(
        ClientInterface $httpClient = null,
        CacheItemPoolInterface $cache = null,
        string $dvvKoodistotApiBaseUrl = DvvKoodistotInterface::DEFAULT_API_BASE_URL,
        string $ophEPerusteetApiBaseUrl = OphEPerusteetInterface::DEFAULT_API_BASE_URL,
        string $ophKoodistoApiBaseUrl = OphKoodistoInterface::DEFAULT_API_BASE_URL,
        string $ophOrganisaatioApiBaseUrl = OphOrganisaatioInterface::DEFAULT_API_BASE_URL
    ) {
        if (null === $httpClient) {
            $httpClient = new Client();
        }
        if (null === $cache) {
            $cache = new DefaultCacheItemPool();
        }
        $this->cache = $cache;
        $this->dvvKoodistot = new DvvKoodistot($httpClient, $cache, $dvvKoodistotApiBaseUrl);
        $this->ophEPerusteet = new OphEPerusteet($httpClient, $cache, $ophEPerusteetApiBaseUrl);
        $this->ophKoodisto = new OphKoodisto($httpClient, $cache, $ophKoodistoApiBaseUrl);
        $this->ophOrganisaatio = new OphOrganisaatio($httpClient, $cache, $ophOrganisaatioApiBaseUrl);
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
