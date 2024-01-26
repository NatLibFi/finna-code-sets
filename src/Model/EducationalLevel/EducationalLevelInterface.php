<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalLevel;

use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;
use NatLibFi\FinnaCodeSets\Model\EducationalData\EducationalDataObjectInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Model\StudyContents\StudyContentsInterface;

interface EducationalLevelInterface extends EducationalDataObjectInterface
{
    public const EARLY_CHILDHOOD_EDUCATION = 'earlyChildhoodEducation';
    public const BASIC_EDUCATION = 'basicEducation';
    public const BASIC_EDUCATION_GRADES_1_2 = 'basicEducationGrades1-2';
    public const BASIC_EDUCATION_GRADES_3_4 = 'basicEducationGrades3-4';
    public const BASIC_EDUCATION_GRADES_5_6 = 'basicEducationGrades5-6';
    public const BASIC_EDUCATION_GRADES_7_9 = 'basicEducationGrades7-9';
    public const VOLUNTARY_ADDITIONAL_BASIC_EDUCATION = 'voluntaryAdditionalBasicEducation';
    public const UPPER_SECONDARY_SCHOOL = 'upperSecondarySchool';
    public const VOCATIONAL_EDUCATION = 'vocationalEducation';
    public const HIGHER_EDUCATION = 'higherEducation';

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
