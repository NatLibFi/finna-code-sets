<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalLevel;

use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;
use NatLibFi\FinnaCodeSets\Model\DataObjectInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalObjectInterface;
use NatLibFi\FinnaCodeSets\Model\StudyContents\StudyContentsInterface;

interface EducationalLevelInterface extends
    DataObjectInterface,
    HierarchicalObjectInterface
{
    // Code set levels.
    public const EARLY_CHILDHOOD_EDUCATION = 'earlyChildhoodEducation';
    public const BASIC_EDUCATION = 'basicEducation';
    public const BASIC_EDUCATION_GRADES_1_2 = 'basicEducationGrades1-2';
    public const BASIC_EDUCATION_GRADES_3_4 = 'basicEducationGrades3-4';
    public const BASIC_EDUCATION_GRADES_5_6 = 'basicEducationGrades5-6';
    public const BASIC_EDUCATION_GRADES_7_9 = 'basicEducationGrades7-9';
    public const VOLUNTARY_ADDITIONAL_BASIC_EDUCATION = 'voluntaryAdditionalBasicEducation';
    public const BASIC_EDUCATION_LEVELS = [
        self::BASIC_EDUCATION_GRADES_1_2,
        self::BASIC_EDUCATION_GRADES_3_4,
        self::BASIC_EDUCATION_GRADES_5_6,
        self::BASIC_EDUCATION_GRADES_7_9,
        self::VOLUNTARY_ADDITIONAL_BASIC_EDUCATION,
    ];
    public const UPPER_SECONDARY_SCHOOL = 'upperSecondarySchool';
    public const VOCATIONAL_EDUCATION = 'vocationalEducation';
    public const HIGHER_EDUCATION = 'higherEducation';

    // Additional primary school and lower secondary school levels.
    public const PRIMARY_SCHOOL = 'primarySchool';
    public const PRIMARY_SCHOOL_LEVELS = [
        self::BASIC_EDUCATION_GRADES_1_2,
        self::BASIC_EDUCATION_GRADES_3_4,
        self::BASIC_EDUCATION_GRADES_5_6,
    ];
    public const LOWER_SECONDARY_SCHOOL = 'lowerSecondarySchool';
    public const LOWER_SECONDARY_SCHOOL_LEVELS = [
        self::BASIC_EDUCATION_GRADES_7_9,
    ];
    public const PRIMARY_LOWER_SECONDARY_MAP = [
        self::BASIC_EDUCATION_GRADES_1_2 => self::PRIMARY_SCHOOL,
        self::BASIC_EDUCATION_GRADES_3_4 => self::PRIMARY_SCHOOL,
        self::BASIC_EDUCATION_GRADES_5_6 => self::PRIMARY_SCHOOL,
        self::BASIC_EDUCATION_GRADES_7_9 => self::LOWER_SECONDARY_SCHOOL,
    ];

    /**
     * DvvKoodistot code values mapped to OphEPerusteet IDs.
     *
     * @internal
     */
    public const DVV_KOODISTOT_OPH_PERUSTEET_MAP = [
        self::BASIC_EDUCATION => '419550',
        self::BASIC_EDUCATION_GRADES_1_2 => '428780',
        self::BASIC_EDUCATION_GRADES_3_4 => '428781',
        self::BASIC_EDUCATION_GRADES_5_6 => '428781',
        self::BASIC_EDUCATION_GRADES_7_9 => '428782',
    ];

    /**
     * Get equivalent levels.
     *
     * @return array<EducationalLevelInterface>
     */
    public function getEquivalentLevels(): array;

    /**
     * Add equivalent level.
     *
     * @param EducationalLevelInterface $equivalentLevel
     */
    public function addEquivalentLevel(EducationalLevelInterface $equivalentLevel): void;

    /**
     * Is this level equivalent to an educational level with the specified ID?
     *
     * @param string $id
     *     Educational level ID
     *
     * @return bool
     */
    public function isEquivalentToLevel(string $id): bool;

    /**
     * Have educational subjects been set?
     *
     * @return bool
     */
    public function educationalSubjectsSet(): bool;

    /**
     * Get educational subjects.
     *
     * @return array<EducationalSubjectInterface>
     *
     * @throws ValueNotSetException
     */
    public function getEducationalSubjects(): array;

    /**
     * Set educational subjects.
     *
     * @param array<EducationalSubjectInterface> $educationalSubjects
     */
    public function setEducationalSubjects(array $educationalSubjects): void;

    /**
     * Have transversal competences been set?
     *
     * @return bool
     */
    public function transversalCompetencesSet(): bool;

    /**
     * Get transversal competences.
     *
     * @return array<StudyContentsInterface>
     *
     * @throws ValueNotSetException
     */
    public function getTransversalCompetences(): array;

    /**
     * Set transversal competences.
     *
     * @param array<StudyContentsInterface> $transversalCompetences
     *
     * @return void
     */
    public function setTransversalCompetences(array $transversalCompetences): void;
}
