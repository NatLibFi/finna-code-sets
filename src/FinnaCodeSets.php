<?php

namespace NatLibFi\FinnaCodeSets;

use GuzzleHttp\Client;
use InvalidArgumentException;
use NatLibFi\FinnaCodeSets\Exception\NotFoundException;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\Concept\ConceptInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Model\Organisation\OrganisationInterface;
use NatLibFi\FinnaCodeSets\Model\StudyContents\StudyContentsInterface;
use NatLibFi\FinnaCodeSets\Source\ConfigurableSourceInterface;
use NatLibFi\FinnaCodeSets\Source\Dvv\Koodistot\DvvKoodistot;
use NatLibFi\FinnaCodeSets\Source\EducationalLevelsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\EducationalSubjectsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\KeywordsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\LicencesSourceInterface;
use NatLibFi\FinnaCodeSets\Source\NatLibFi\Finna\FinnaAdminApi;
use NatLibFi\FinnaCodeSets\Source\NatLibFi\Finna\FinnaCodeSetsSource;
use NatLibFi\FinnaCodeSets\Source\NatLibFi\Finto\FintoSource;
use NatLibFi\FinnaCodeSets\Source\NatLibFi\Finto\FintoSourceInterface;
use NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet\OphEPerusteet;
use NatLibFi\FinnaCodeSets\Source\Oph\Koodisto\OphKoodisto;
use NatLibFi\FinnaCodeSets\Source\Oph\Organisaatio\OphOrganisaatio;
use NatLibFi\FinnaCodeSets\Source\OrganisationsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\SourceInterface;
use NatLibFi\FinnaCodeSets\Source\TransversalCompetencesSourceInterface;
use NatLibFi\FinnaCodeSets\Source\VocabularySourceInterface;
use NatLibFi\FinnaCodeSets\Source\VocationalQualificationsSourceInterface;
use NatLibFi\FinnaCodeSets\Utility\EducationalData;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;

class FinnaCodeSets implements FinnaCodeSetsInterface
{
    protected CacheItemPoolInterface $cache;

    /**
     * Sources keyed by their concrete class name.
     *
     * @var array<SourceInterface>
     */
    protected array $classes;

    /**
     * Sources keyed by the source interface they implement.
     *
     * @var array<SourceInterface>
     */
    protected array $sources = [];

    /**
     * Educational subjects sources keyed by educational level.
     *
     * @var array<EducationalSubjectsSourceInterface>
     */
    protected array $educationalSubjectsSources = [];

    /**
     * Transversal competences sources keyed by educational level.
     *
     * @var array<TransversalCompetencesSourceInterface>
     */
    protected array $transversalCompetencesSources = [];

    /**
     * Vocabulary sources keyed by vocabulary.
     *
     * @var array<VocabularySourceInterface>
     */
    protected array $vocabularySources = [];

    protected EducationalData $educationalData;

    /**
     * {@inheritdoc}
     */
    public static function getDefaultConfig(): array
    {
        return [
            'classes' => [
                DvvKoodistot::class => DvvKoodistot::getDefaultConfig(),
                FinnaAdminApi::class => FinnaAdminApi::getDefaultConfig(),
                FinnaCodeSetsSource::class => FinnaCodeSetsSource::getDefaultConfig(),
                FintoSource::class => FintoSource::getDefaultConfig(),
                OphEPerusteet::class => OphEPerusteet::getDefaultConfig(),
                OphKoodisto::class => OphKoodisto::getDefaultConfig(),
                OphOrganisaatio::class => OPhOrganisaatio::getDefaultConfig(),
            ],
            'sources' => [
                EducationalLevelsSourceInterface::class => DvvKoodistot::class,
                KeywordsSourceInterface::class => FintoSource::class,
                LicencesSourceInterface::class => DvvKoodistot::class,
                OrganisationsSourceInterface::class => OphOrganisaatio::class,
                VocabularySourceInterface::class => FintoSource::class,
                VocationalQualificationsSourceInterface::class => OphEPerusteet::class,
            ],
            'educationalSubjectsSources' => [
                EducationalLevelInterface::EARLY_CHILDHOOD_EDUCATION => FinnaCodeSetsSource::class,
                EducationalLevelInterface::BASIC_EDUCATION => OphEPerusteet::class,
                EducationalLevelInterface::UPPER_SECONDARY_SCHOOL => OphEPerusteet::class,
                EducationalLevelInterface::VOCATIONAL_EDUCATION => OphEPerusteet::class,
                EducationalLevelInterface::HIGHER_EDUCATION => OphKoodisto::class,
            ],
            'transversalCompetencesSources' => [
                EducationalLevelInterface::EARLY_CHILDHOOD_EDUCATION => FinnaCodeSetsSource::class,
                EducationalLevelInterface::BASIC_EDUCATION => OphEPerusteet::class,
                EducationalLevelInterface::UPPER_SECONDARY_SCHOOL => OphEPerusteet::class,
            ],
            'vocabularySources' => [
                FintoSourceInterface::VOCABULARY_FINTO_YSO => FintoSource::class,
                FintoSourceInterface::VOCABULARY_FINTO_YSO_PLACES => FintoSource::class,
                FintoSourceInterface::VOCABULARY_FINTO_YSO_TIME => FintoSource::class,
            ],
        ];
    }

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
     * @param array<string, mixed>|null $config
     *     Configuration.
     */
    public function __construct(
        ClientInterface $httpClient = null,
        CacheItemPoolInterface $cache = null,
        array $config = null
    ) {
        if (null === $httpClient) {
            $httpClient = new Client();
        }
        if (null === $cache) {
            $cache = new DefaultCacheItemPool();
        }
        if (null === $config) {
            $config = self::getDefaultConfig();
        }
        $this->cache = $cache;

        $this->classes = $classes = [
            DvvKoodistot::class => new DvvKoodistot($httpClient, $cache, $config['classes'][DvvKoodistot::class]),
            FinnaAdminApi::class => new FinnaAdminApi($httpClient, $cache, $config['classes'][FinnaAdminApi::class]),
            FinnaCodeSetsSource::class
                => new FinnaCodeSetsSource($httpClient, $cache, $config['classes'][FinnaCodeSetsSource::class]),
            FintoSource::class => new FintoSource($httpClient, $cache, $config['classes'][FintoSource::class]),
            OphEPerusteet::class => new OphEPerusteet($httpClient, $cache, $config['classes'][OphEPerusteet::class]),
            OphKoodisto::class => new OphKoodisto($httpClient, $cache, $config['classes'][OphKoodisto::class]),
            OphOrganisaatio::class
                => new OphOrganisaatio($httpClient, $cache, $config['classes'][OphOrganisaatio::class]),
        ];

        foreach ($config['sources'] ?? [] as $interface => $class) {
            $this->sources[$interface] = $classes[$class];
        }
        foreach ($config['educationalSubjectsSources'] ?? [] as $levelCodeValue => $class) {
            assert($classes[$class] instanceof EducationalSubjectsSourceInterface);
            $this->educationalSubjectsSources[$levelCodeValue] = $classes[$class];
        }
        foreach ($config['transversalCompetencesSources'] ?? [] as $levelCodeValue => $class) {
            assert($classes[$class] instanceof TransversalCompetencesSourceInterface);
            $this->transversalCompetencesSources[$levelCodeValue] = $classes[$class];
        }
        foreach ($config['vocabularySources'] ?? [] as $vocid => $class) {
            assert($classes[$class] instanceof VocabularySourceInterface);
            $this->vocabularySources[$vocid] = $classes[$class];
        }

        $this->educationalData = new EducationalData($this, $classes[OphEPerusteet::class]);
    }

    /**
     * {@inheritdoc}
     */
    public function setClassConfig(string $class, array $config): void
    {
        if (!isset($this->classes[$class])) {
            throw new NotFoundException($class);
        }
        if (!is_subclass_of($class, ConfigurableSourceInterface::class)) {
            throw new NotSupportedException($class);
        }
        assert($this->classes[$class] instanceof ConfigurableSourceInterface);
        $this->classes[$class]->setConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceClass(string $interface, string $class): void
    {
        if (!interface_exists($interface)) {
            throw new NotSupportedException($interface);
        }
        if (!class_exists($class)) {
            throw new NotSupportedException($class);
        }
        if (!is_subclass_of($interface, SourceInterface::class)) {
            throw new NotSupportedException($class);
        }
        if (!is_subclass_of($class, $interface)) {
            throw new InvalidArgumentException($class . ' does not implement ' . $interface);
        }
        $this->sources[$interface] = $this->classes[$class];
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
        $source = $this->getSource(EducationalLevelsSourceInterface::class);
        assert($source instanceof EducationalLevelsSourceInterface);
        return $source->getEducationalLevels();
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjects(string $levelCodeValue): array
    {
        return $this->getEducationalSubjectsSource($levelCodeValue)->getEducationalSubjects($levelCodeValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjectByUrl(string $url): EducationalSubjectInterface
    {
        foreach ($this->getEducationalSubjectsSources() as $source) {
            if ($source->isSupportedEducationalSubjectUrl($url)) {
                return $source->getEducationalSubjectByUrl($url);
            }
        }
        throw new NotSupportedException($url);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedEducationalSubjectUrl(string $url): bool
    {
        foreach ($this->getEducationalSubjectsSources() as $source) {
            if ($source->isSupportedEducationalSubjectUrl($url)) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    public function getKeywordsIndexLetters(string $langcode): array
    {
        $source = $this->getSource(KeywordsSourceInterface::class);
        assert($source instanceof KeywordsSourceInterface);
        return $source->getKeywordsIndexLetters($langcode);
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    public function getKeywordsIndex(string $langcode, string $letter): array
    {
        $source = $this->getSource(KeywordsSourceInterface::class);
        assert($source instanceof KeywordsSourceInterface);
        return $source->getKeywordsIndex($langcode, $letter);
    }

    /**
     * {@inheritdoc}
     */
    public function getLicences(): array
    {
        $source = $this->getSource(LicencesSourceInterface::class);
        assert($source instanceof LicencesSourceInterface);
        return $source->getLicences();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrganisations(): array
    {
        $source = $this->getSource(OrganisationsSourceInterface::class);
        assert($source instanceof OrganisationsSourceInterface);
        return $source->getOrganisations();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrganisation(string $id): ?OrganisationInterface
    {
        $source = $this->getSource(OrganisationsSourceInterface::class);
        assert($source instanceof OrganisationsSourceInterface);
        return $source->getOrganisation($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransversalCompetences(string $levelCodeValue): array
    {
        return $this->getTransversalCompetencesSource($levelCodeValue)->getTransversalCompetences($levelCodeValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransversalCompetenceByUrl(string $url): StudyContentsInterface
    {
        foreach ($this->getTransversalCompetencesSources() as $source) {
            if ($source->isSupportedTransversalCompetenceUrl($url)) {
                return $source->getTransversalCompetenceByUrl($url);
            }
        }
        throw new NotSupportedException($url);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedTransversalCompetenceUrl(string $url): bool
    {
        foreach ($this->getTransversalCompetencesSources() as $source) {
            if ($source->isSupportedTransversalCompetenceUrl($url)) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getVocabularyTopConcepts(string $vocid, ?string $langcode = null): array
    {
        return $this->getVocabularySource($vocid)->getVocabularyTopConcepts($vocid, $langcode);
    }

    /**
     * {@inheritdoc}
     */
    public function getVocabularyConceptData(string $vocid, string $uri, ?string $langcode = null): ConceptInterface
    {
        return $this->getVocabularySource($vocid)->getVocabularyConceptData($vocid, $uri, $langcode);
    }

    /**
     * {@inheritdoc}
     */
    public function getVocabularyConceptChildren(string $vocid, string $uri, ?string $langcode = null): array
    {
        return $this->getVocabularySource($vocid)->getVocabularyConceptChildren($vocid, $uri, $langcode);
    }

    /**
     * {@inheritdoc}
     */
    public function getVocabularyIndexLetters(string $vocid, ?string $langcode = null): array
    {
        return $this->getVocabularySource($vocid)->getVocabularyIndexLetters($vocid, $langcode);
    }

    /**
     * {@inheritdoc}
     */
    public function getVocabularyIndex(string $vocid, string $letter, ?string $langcode = null): array
    {
        return $this->getVocabularySource($vocid)->getVocabularyIndex($vocid, $letter, $langcode);
    }

    /**
     * {@inheritdoc}
     */
    public function getVocationalUpperSecondaryQualifications(bool $includeUnits = true): array
    {
        return $this->getVocationalQualificationsSource()->getVocationalUpperSecondaryQualifications($includeUnits);
    }

    /**
     * {@inheritdoc}
     */
    public function getFurtherVocationalQualifications(bool $includeUnits = true): array
    {
        return $this->getVocationalQualificationsSource()->getFurtherVocationalQualifications($includeUnits);
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecialistVocationalQualifications(bool $includeUnits = true): array
    {
        return $this->getVocationalQualificationsSource()->getSpecialistVocationalQualifications($includeUnits);
    }

    /**
     * {@inheritdoc}
     */
    public function getVocationalCommonUnits(): array
    {
        return $this->getVocationalQualificationsSource()->getVocationalCommonUnits();
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedVocationalUnitUrl(string $url): bool
    {
        return $this->getVocationalQualificationsSource()->isSupportedVocationalUnitUrl($url);
    }

    protected function getSource(string $interface): SourceInterface
    {
        if ($source = $this->sources[$interface] ?? false) {
            return $source;
        }
        throw new NotSupportedException($interface);
    }

    protected function getEducationalSubjectsSource(string $levelCodeValue): EducationalSubjectsSourceInterface
    {
        if ($source = $this->educationalSubjectsSources[$levelCodeValue] ?? false) {
            return $source;
        }
        throw NotSupportedException::forEducationalLevel($levelCodeValue);
    }

    /**
     * Get all educational subjects sources.
     *
     * @return array<EducationalSubjectsSourceInterface>
     */
    protected function getEducationalSubjectsSources(): array
    {
        return array_unique(array_values($this->educationalSubjectsSources), SORT_REGULAR);
    }

    protected function getTransversalCompetencesSource(string $levelCodeValue): TransversalCompetencesSourceInterface
    {
        if ($source = $this->transversalCompetencesSources[$levelCodeValue] ?? false) {
            return $source;
        }
        throw NotSupportedException::forEducationalLevel($levelCodeValue);
    }

    /**
     * Get all transversal competences sources.
     *
     * @return array<TransversalCompetencesSourceInterface>
     */
    protected function getTransversalCompetencesSources(): array
    {
        return array_unique(array_values($this->transversalCompetencesSources), SORT_REGULAR);
    }

    protected function getVocabularySource(string $vocid): VocabularySourceInterface
    {
        if ($source = $this->vocabularySources[$vocid] ?? false) {
            return $source;
        }
        throw new NotSupportedException($vocid);
    }

    protected function getVocationalQualificationsSource(): VocationalQualificationsSourceInterface
    {
        $source = $this->getSource(VocationalQualificationsSourceInterface::class);
        assert($source instanceof VocationalQualificationsSourceInterface);
        return $source;
    }
}
