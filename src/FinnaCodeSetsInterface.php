<?php

namespace NatLibFi\FinnaCodeSets;

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
    public const VOCABULARY_FINTO_YSO = FintoSourceInterface::VOCABULARY_FINTO_YSO;
    public const VOCABULARY_FINTO_YSO_PLACES = FintoSourceInterface::VOCABULARY_FINTO_YSO_PLACES;
    public const VOCABULARY_FINTO_YSO_TIME = FintoSourceInterface::VOCABULARY_FINTO_YSO_TIME;

    /**
     * Return educational data utility class instance.
     *
     * @return EducationalData
     */
    public function getEducationalData(): EducationalData;
}
