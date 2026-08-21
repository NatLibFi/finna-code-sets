<?php

namespace NatLibFi\FinnaCodeSets;

use InvalidArgumentException;
use NatLibFi\FinnaCodeSets\Exception\NotFoundException;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Source\EducationalLevelsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\EducationalSubjectsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\KeywordsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\LicencesSourceInterface;
use NatLibFi\FinnaCodeSets\Source\NatLibFi\Finto\FintoSourceInterface;
use NatLibFi\FinnaCodeSets\Source\OrganisationsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\TransversalCompetencesSourceInterface;
use NatLibFi\FinnaCodeSets\Source\VocabularySourceInterface;
use NatLibFi\FinnaCodeSets\Source\VocationalQualificationsSourceInterface;
use NatLibFi\FinnaCodeSets\Utility\EducationalData;

interface FinnaCodeSetsInterface extends
    EducationalLevelsSourceInterface,
    EducationalSubjectsSourceInterface,
    KeywordsSourceInterface,
    LicencesSourceInterface,
    OrganisationsSourceInterface,
    TransversalCompetencesSourceInterface,
    VocabularySourceInterface,
    VocationalQualificationsSourceInterface
{
    /**
     * Finto YSO vocabulary.
     *
     * @deprecated Use FintoSourceInterface::VOCABULARY_FINTO_YSO instead.
     */
    public const VOCABULARY_FINTO_YSO = FintoSourceInterface::VOCABULARY_FINTO_YSO;

    /**
     * Finto YSO places vocabulary.
     *
     * @deprecated Use FintoSourceInterface::VOCABULARY_FINTO_YSO instead.
     */
    public const VOCABULARY_FINTO_YSO_PLACES = FintoSourceInterface::VOCABULARY_FINTO_YSO_PLACES;

    /**
     * Finto YSO-time vocabulary.
     *
     * @deprecated Use FintoSourceInterface::VOCABULARY_FINTO_YSO instead.
     */
    public const VOCABULARY_FINTO_YSO_TIME = FintoSourceInterface::VOCABULARY_FINTO_YSO_TIME;

    /**
     * Get default configuration.
     *
     * @return array<string, mixed>
     */
    public static function getDefaultConfig(): array;

    /**
     * Set the configuration of a concrete class.
     *
     * @param string $class
     * @param array<string, mixed> $config
     *
     * @return void
     *
     * @throws NotFoundException
     * @throws NotSupportedException
     */
    public function setClassConfig(string $class, array $config): void;

    /**
     * Set the concrete class implementing a source interface.
     *
     * @param string $interface
     *     Source interface name
     * @param string $class
     *     Implementing class name
     *
     * @return void
     *
     * @throws InvalidArgumentException
     * @throws NotSupportedException
     */
    public function setSourceClass(string $interface, string $class): void;

    /**
     * Return educational data utility class instance.
     *
     * @return EducationalData
     */
    public function getEducationalData(): EducationalData;
}
