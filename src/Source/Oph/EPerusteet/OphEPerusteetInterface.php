<?php

namespace NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet;

use NatLibFi\FinnaCodeSets\Source\EducationalLevelsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\EducationalSubjectsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\TransversalCompetencesSourceInterface;
use NatLibFi\FinnaCodeSets\Source\VocationalQualificationsSourceInterface;

interface OphEPerusteetInterface extends
    EducationalLevelsSourceInterface,
    EducationalSubjectsSourceInterface,
    TransversalCompetencesSourceInterface,
    VocationalQualificationsSourceInterface
{
    public const DEFAULT_API_BASE_URL = 'https://virkailija.opintopolku.fi/eperusteet-service/api';

    public const BASIC_EDUCATION_API_BASE_METHOD = '/external/peruste/419550/perusopetus';

    public const BASIC_EDUCATION_EDUCATIONAL_LEVELS_API_METHOD
        = self::BASIC_EDUCATION_API_BASE_METHOD . '/vuosiluokkakokonaisuudet';

    public const BASIC_EDUCATION_EDUCATIONAL_SUBJECTS_API_METHOD
        = self::BASIC_EDUCATION_API_BASE_METHOD . '/oppiaineet';

    public const BASIC_EDUCATION_EDUCATIONAL_SYLLABUS_API_METHOD
        = self::BASIC_EDUCATION_API_BASE_METHOD . '/oppiaineet/oppimaarat';

    public const BASIC_EDUCATION_TRANSVERSAL_COMPETENCES_API_METHOD
        = self::BASIC_EDUCATION_API_BASE_METHOD . '/laajaalaisetosaamiset';

    public const UPPER_SECONDARY_SCHOOL_API_BASE_METHOD = '/external/peruste/6828810/lops2019';

    public const UPPER_SECONDARY_SCHOOL_EDUCATIONAL_SUBJECTS_API_METHOD
        = self::UPPER_SECONDARY_SCHOOL_API_BASE_METHOD . '/oppiaineet';

    public const UPPER_SECONDARY_SCHOOL_EDUCATIONAL_SYLLABUS_API_METHOD
        = self::UPPER_SECONDARY_SCHOOL_API_BASE_METHOD . '/oppiaineet/oppimaarat';

    public const UPPER_SECONDARY_SCHOOL_EDUCATIONAL_MODULE_API_METHOD
        = self::UPPER_SECONDARY_SCHOOL_API_BASE_METHOD . '/oppiaineet/%SUBJECT_ID%/moduulit/%MODULE_ID%';

    public const UPPER_SECONDARY_SCHOOL_TRANSVERSAL_COMPETENCES_API_METHOD
        = self::UPPER_SECONDARY_SCHOOL_API_BASE_METHOD . '/laajaAlainenOsaaminen/laajaAlaisetOsaamiset';

    public const VOCATIONAL_QUALIFICATIONS_API_METHOD = '/external/perusteet';

    public const VOCATIONAL_QUALIFICATIONS_API_PARAMETERS = [
        'sivu' => 0,
        'tuleva' => 'true',
        'siirtyma' => 'true',
        'voimassaolo' => 'true',
        'poistunut' => 'false',
    ];

    public const VOCATIONAL_UPPER_SECONDARY_QUALIFICATIONS_API_PARAMETERS = [
        'koulutustyyppi' => 'koulutustyyppi_1',
    ];

    public const FURTHER_VOCATIONAL_QUALIFICATIONS_API_PARAMETERS = [
        'koulutustyyppi' => 'koulutustyyppi_11',
    ];

    public const SPECIALIST_VOCATIONAL_QUALIFICATIONS_API_PARAMETERS = [
        'koulutustyyppi' => 'koulutustyyppi_12',
    ];

    public const VOCATIONAL_QUALIFICATION_API_METHOD = '/external/peruste';

    public const VOCATIONAL_UNIT_API_METHOD
        = '/external/peruste/%QUALIFICATION_ID%/tutkinnonOsat/%UNIT_ID%';

    public const VOCATIONAL_COMMON_UNITS_API_METHOD = '/external/peruste/yto';

    public const VOCATIONAL_COMMON_UNIT_API_METHOD
        = '/external/peruste/yto/tutkinnonOsat';

    public const VOCATIONAL_COMMON_UNIT_PART_API_METHOD
        = '/external/peruste/yto/tutkinnonOsat/%UNIT_ID%/osaAlueet/%UNIT_PART_ID%';
}
