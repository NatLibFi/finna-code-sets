<?php

namespace NatLibFi\FinnaCodeSets\Utility;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Exception\NotFoundException;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Exception\UnexpectedValueException;
use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;
use NatLibFi\FinnaCodeSets\FinnaCodeSets;
use NatLibFi\FinnaCodeSets\Model\EducationalData\EducationalDataObjectInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalData\StudyDataObjectInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\OphEPerusteetEducationalLevel;
use NatLibFi\FinnaCodeSets\Model\EducationalModule\EducationalModuleInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSyllabus\EducationalSyllabusInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalObjectInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalProxyDataObject;
use NatLibFi\FinnaCodeSets\Model\HierarchicalProxyDataObjectInterface;
use NatLibFi\FinnaCodeSets\Model\LearningArea\LearningAreaInterface;
use NatLibFi\FinnaCodeSets\Model\ProxyObjectInterface;
use NatLibFi\FinnaCodeSets\Model\StudyContents\StudyContentsInterface;
use NatLibFi\FinnaCodeSets\Model\StudyObjective\StudyObjectiveInterface;
use NatLibFi\FinnaCodeSets\Model\VocationalQualification\VocationalQualificationInterface;
use NatLibFi\FinnaCodeSets\Model\VocationalUnit\VocationalUnitInterface;
use NatLibFi\FinnaCodeSets\Model\VocationalUnitSubject\VocationalUnitSubjectInterface;
use NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet\OphEPerusteet;

/**
 * Utility methods for working with educational data.
 */
class EducationalData
{
    // Used as educational data array keys.
    public const EDUCATIONAL_LEVELS = 'educationalLevels';
    public const LEARNING_AREAS = 'learningAreas';
    public const EDUCATIONAL_SUBJECTS = 'educationalSubjects';
    public const EDUCATIONAL_SYLLABUSES = 'educationalSyllabuses';
    public const EDUCATIONAL_MODULES = 'educationalModules';
    public const STUDY_CONTENTS = 'studyContents';
    public const STUDY_OBJECTIVES = 'studyObjectives';
    public const TRANSVERSAL_COMPETENCES = 'transversalCompetences';
    public const VOCATIONAL_QUALIFICATIONS = 'vocationalQualifications';
    public const VOCATIONAL_UNITS = 'vocationalUnits';
    public const VOCATIONAL_COMMON_UNITS = 'vocationalCommonUnits';
    public const VOCATIONAL_COMMON_UNIT_SUBJECTS = 'vocationalCommonUnitSubjects';

    // Data groups.
    public const EDUCATIONAL_SUBJECT_LEVEL_KEYS = [
        self::EDUCATIONAL_SUBJECTS,
        self::EDUCATIONAL_SYLLABUSES,
        self::EDUCATIONAL_MODULES,
        self::VOCATIONAL_QUALIFICATIONS,
        self::VOCATIONAL_UNITS,
        self::VOCATIONAL_COMMON_UNIT_SUBJECTS,
    ];
    public const STUDY_DATA_KEYS = [
        self::STUDY_CONTENTS,
        self::STUDY_OBJECTIVES,
    ];

    // Additional primary school and lower secondary school levels.
    public const PRIMARY_SCHOOL = 'primarySchool';
    public const LOWER_SECONDARY_SCHOOL = 'lowerSecondarySchool';

    // Level groups and mappings.
    public const ALL_EDUCATIONAL_LEVELS = [
        EducationalLevelInterface::EARLY_CHILDHOOD_EDUCATION,
        EducationalLevelInterface::BASIC_EDUCATION,
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_1_2,
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_3_4,
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_5_6,
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_7_9,
        EducationalLevelInterface::VOLUNTARY_ADDITIONAL_BASIC_EDUCATION,
        EducationalLevelInterface::UPPER_SECONDARY_SCHOOL,
        EducationalLevelInterface::VOCATIONAL_EDUCATION,
        EducationalLevelInterface::HIGHER_EDUCATION,
        self::PRIMARY_SCHOOL,
        self::LOWER_SECONDARY_SCHOOL,
    ];
    public const BASIC_EDUCATION_LEVELS = [
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_1_2,
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_3_4,
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_5_6,
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_7_9,
        EducationalLevelInterface::VOLUNTARY_ADDITIONAL_BASIC_EDUCATION,
    ];
    public const PRIMARY_SCHOOL_LEVELS = [
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_1_2,
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_3_4,
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_5_6,
    ];
    public const LOWER_SECONDARY_SCHOOL_LEVELS = [
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_7_9,
    ];
    public const PRIMARY_LOWER_SECONDARY_MAP = [
        EducationalLevelInterface::BASIC_EDUCATION => [
            self::PRIMARY_SCHOOL,
            self::LOWER_SECONDARY_SCHOOL,
        ],
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_1_2 => [self::PRIMARY_SCHOOL],
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_3_4 => [self::PRIMARY_SCHOOL],
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_5_6 => [self::PRIMARY_SCHOOL],
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_7_9 => [self::LOWER_SECONDARY_SCHOOL],
    ];

    /**
     * DvvKoodistot code values mapped to OphEPerusteet IDs.
     *
     * @internal
     */
    public const DVV_KOODISTOT_OPH_PERUSTEET_MAP = [
        EducationalLevelInterface::BASIC_EDUCATION => '419550',
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_1_2 => '428780',
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_3_4 => '428781',
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_5_6 => '428781',
        EducationalLevelInterface::BASIC_EDUCATION_GRADES_7_9 => '428782',
    ];

    protected FinnaCodeSets $codeSets;

    protected OphEPerusteet $ophEPerusteet;

    /**
     * EducationalData constructor.
     *
     * @param FinnaCodeSets $codeSets
     *     Library instance used by utility methods
     */
    public function __construct(FinnaCodeSets $codeSets, OphEPerusteet $ophEPerusteet)
    {
        $this->codeSets = $codeSets;
        $this->ophEPerusteet = $ophEPerusteet;
    }

    /**
     * Get the corresponding educational data array key for the educational data object.
     *
     * @param EducationalDataObjectInterface $educationalDataObject
     *     Educational data object
     *
     * @return string
     */
    public static function getKeyForInstance(EducationalDataObjectInterface $educationalDataObject): string
    {
        if ($educationalDataObject instanceof EducationalLevelInterface) {
            return EducationalData::EDUCATIONAL_LEVELS;
        } elseif ($educationalDataObject instanceof EducationalSubjectInterface) {
            if ($educationalDataObject instanceof LearningAreaInterface) {
                return EducationalData::LEARNING_AREAS;
            } elseif ($educationalDataObject instanceof EducationalSyllabusInterface) {
                return EducationalData::EDUCATIONAL_SYLLABUSES;
            } elseif ($educationalDataObject instanceof EducationalModuleInterface) {
                return EducationalData::EDUCATIONAL_MODULES;
            } elseif ($educationalDataObject instanceof VocationalQualificationInterface) {
                return EducationalData::VOCATIONAL_QUALIFICATIONS;
            } elseif ($educationalDataObject instanceof VocationalUnitInterface) {
                if ($educationalDataObject->isCommonUnit()) {
                    if ($educationalDataObject instanceof VocationalUnitSubjectInterface) {
                        return EducationalData::VOCATIONAL_COMMON_UNIT_SUBJECTS;
                    }
                    return EducationalData::VOCATIONAL_COMMON_UNITS;
                } else {
                    return EducationalData::VOCATIONAL_UNITS;
                }
            } else {
                return EducationalData::EDUCATIONAL_SUBJECTS;
            }
        } elseif ($educationalDataObject instanceof StudyContentsInterface) {
            $root = Data::deProxify($educationalDataObject->getRoot());
            if ($root === $educationalDataObject) {
                return EducationalData::TRANSVERSAL_COMPETENCES;
            } elseif ($root instanceof EducationalSubjectInterface) {
                return EducationalData::STUDY_CONTENTS;
            }
        } elseif ($educationalDataObject instanceof StudyObjectiveInterface) {
            return EducationalData::STUDY_OBJECTIVES;
        }
        throw new UnexpectedValueException();
    }

    /**
     * Find and return an educational level from the provided array or all
     * educational levels.
     *
     * @param string $levelCodeValue
     *     Educational level code value
     * @param ?array<HierarchicalObjectInterface> $educationalLevels
     *     Array of educational levels to search from
     *
     * @return ?EducationalLevelInterface
     *     Returns null if the level was not found
     *
     * @throws MissingValueException
     * @throws UnexpectedValueException
     */
    public function getEducationalLevelByCodeValue(
        string $levelCodeValue,
        ?array $educationalLevels = null
    ): ?EducationalLevelInterface {
        if (null === $educationalLevels) {
            $educationalLevels = $this->codeSets->getEducationalLevels();
        }
        foreach ($educationalLevels as $level) {
            $level = Assert::educationalLevel($level);
            if ($level->getCodeValue() === $levelCodeValue) {
                return $level;
            }
            $child = $this->getEducationalLevelByCodeValue($levelCodeValue, $level->getChildren());
            if (null !== $child) {
                return $child;
            }
        }
        return null;
    }

    /**
     * Set educational subjects for an educational level.
     *
     * @param EducationalLevelInterface $educationalLevel
     *     Educational level
     *
     * @throws MissingValueException
     * @throws NotFoundException
     * @throws NotSupportedException
     */
    public function setEducationalSubjects(EducationalLevelInterface $educationalLevel): void
    {
        if (!$educationalLevel->educationalSubjectsSet()) {
            $educationalLevel->setEducationalSubjects(
                $this->codeSets->getEducationalSubjects($educationalLevel->getCodeValue())
            );
        }
    }

    /**
     * Get equivalent levels.
     *
     * @param EducationalLevelInterface $educationalLevel
     *      Educational level
     *
     * @return array<EducationalLevelInterface>
     *
     * @throws MissingValueException
     */
    public function getEquivalentLevels(EducationalLevelInterface $educationalLevel): array
    {
        if ($educationalLevel instanceof OphEPerusteetEducationalLevel) {
            if ($levelCodeValue = array_search($educationalLevel->getId(), self::DVV_KOODISTOT_OPH_PERUSTEET_MAP)) {
                if (null !== ($equivalentLevel = $this->getEducationalLevelByCodeValue($levelCodeValue))) {
                    return [$equivalentLevel];
                }
            }
        } else {
            $levelCodeValue = $educationalLevel->getCodeValue();
            if ($ophLevelId = self::DVV_KOODISTOT_OPH_PERUSTEET_MAP[$levelCodeValue] ?? false) {
                $ophEPerusteetLevels = $this->ophEPerusteet->getEducationalLevels();
                return [$ophEPerusteetLevels[$ophLevelId]];
            }
        }
        return [];
    }

    /**
     * Is this level equivalent to an educational level with the specified ID?
     *
     * @param EducationalLevelInterface $educationalLevel
     *     Educational level
     * @param string $id
     *     Educational level ID
     *
     * @return bool
     *
     * @throws MissingValueException
     */
    public function isEquivalentToLevel(EducationalLevelInterface $educationalLevel, string $id): bool
    {
        if ($educationalLevel->getId() === $id) {
            return true;
        }
        foreach ($this->getEquivalentLevels($educationalLevel) as $equivalentLevel) {
            if ($equivalentLevel->getId() === $id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get educational subject by ID and URL.
     *
     * @param string $id
     * @param string $url
     *
     * @return EducationalSubjectInterface
     *
     * @throws MissingValueException
     * @throws NotFoundException
     * @throws NotSupportedException
     */
    public function getEducationalSubjectByIdAndUrl(string $id, string $url): EducationalSubjectInterface
    {
        if ($this->codeSets->isSupportedEducationalSubjectUrl($url)) {
            $subject = $this->codeSets->getEducationalSubjectByUrl($url);
            if ($subject->getId() !== $id) {
                $subject = Assert::educationalSubject($subject->getDescendant($id));
            }
        } elseif ($this->codeSets->isSupportedVocationalUnitUrl($url)) {
            $subject = $this->getVocationalCommonUnitById($id);
        } else {
            throw new NotFoundException($url);
        }
        return $subject;
    }

    /**
     * Get study contents or study objective by ID.
     *
     * @param string $id
     *     Study contents or study objective ID
     * @param EducationalSubjectInterface $educationalSubject
     *     Educational subject
     *
     * @return StudyContentsInterface|StudyObjectiveInterface
     *
     * @throws NotFoundException
     * @throws UnexpectedValueException
     * @throws ValueNotSetException
     */
    public function getStudyDataById(
        string $id,
        EducationalSubjectInterface $educationalSubject
    ): StudyContentsInterface|StudyObjectiveInterface {
        if (null !== ($contents = $educationalSubject->getStudyContents()->getDescendant($id))) {
            return Assert::studyContents($contents);
        }
        if (null !== ($objective = $educationalSubject->getStudyObjectives()->getDescendant($id))) {
            return Assert::studyObjective($objective);
        }
        throw (new NotFoundException($id))->setValue($educationalSubject);
    }

    /**
     * Get study contents or objective by ID and URL.
     *
     * @param string $id
     *     Study contents, study objective or transversal competence ID
     * @param string $url
     *     Educational subject or transversal competences URL
     *
     * @return StudyContentsInterface|StudyObjectiveInterface
     *
     * @throws NotFoundException
     * @throws NotSupportedException
     * @throws ValueNotSetException
     */
    public function getStudyDataByIdAndUrl(
        string $id,
        string $url
    ): StudyContentsInterface|StudyObjectiveInterface {
        if ($this->codeSets->isSupportedEducationalSubjectUrl($url)) {
            return $this->getStudyDataById(
                $id,
                $this->codeSets->getEducationalSubjectByUrl($url)
            );
        } else {
            // Transversal competence URLs are not expected to contain an ID.
            $urlWithId = $url . '/' . $id;
            if ($this->codeSets->isSupportedTransversalCompetenceUrl($urlWithId)) {
                return $this->codeSets->getTransversalCompetenceByUrl($urlWithId);
            }
        }
        throw new NotSupportedException($url);
    }

    /**
     * Get vocational common unit by ID.
     *
     * @param string $id
     *
     * @return VocationalUnitInterface
     *
     * @throws MissingValueException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     */
    public function getVocationalCommonUnitById(string $id): VocationalUnitInterface
    {
        $commonUnits = $this->codeSets->getVocationalCommonUnits();
        foreach ($commonUnits as $commonUnit) {
            if ($commonUnit->getId() === $id) {
                return $commonUnit;
            }
            foreach ($commonUnit->getChildren() as $childUnit) {
                if ($childUnit->getId() == $id) {
                    return Assert::vocationalUnit($childUnit);
                }
            }
        }
        throw new NotFoundException($id);
    }

    /**
     * Get educational data for values parsed from an LRMI record.
     *
     * @param array<string, string> $educationalLevels
     *     Educational levels array with level codes as keys and URLs as values,
     *     from learningResource->educationalLevel->termCode and
     *     learningResource->educationalLevel->inDefinedTermSet->url
     * @param array<string, string> $educationalSubjects
     *     Educational subjects array with IDs as keys and URLs as values,
     *     from learningResource->educationalAlignment->educationalSubject->identifier
     *     and learningResource->educationalAlignment->educationalSubject->targetUrl
     * @param array<string, string> $teaches
     *     Teaches array with IDs as keys and URLs as values, from
     *     learningResource->teaches->identifier and
     *     learningResource->teaches->inDefinedTermSet->url
     * @param bool $returnFullHierarchy
     *     Whether to return a full hierarchy by recursively adding any missing
     *     parent data (optional, defaults to true)
     * @param bool $filterBasicEducationRoot
     *     Whether to filter out possible basic education root level (optional,
     *     defaults to true)
     *
     * @return array<string, array<string, EducationalDataObjectInterface>>
     *     Array of educational data arrays, keyed by constants defined in this
     *     class. The educational data arrays are keyed by educational data
     *     object IDs.
     *
     * @throws MissingValueException
     * @throws NotFoundException
     * @throws NotSupportedException
     * @throws ValueNotSetException
     */
    public function getLrmiEducationalData(
        array $educationalLevels = [],
        array $educationalSubjects = [],
        array $teaches = [],
        bool $returnFullHierarchy = true,
        bool $filterBasicEducationRoot = true
    ): array {
        $educationalData = [];
        foreach ($educationalLevels as $codeValue => $url) {
            if (null !== ($level = $this->getEducationalLevelByCodeValue($codeValue))) {
                $educationalData[EducationalData::EDUCATIONAL_LEVELS][$level->getId()]
                    = $level;
            }
        }
        foreach ($educationalSubjects as $id => $url) {
            $subject = $this->getEducationalSubjectByIdAndUrl($id, $url);
            $educationalData[self::getKeyForInstance($subject)][$id] = $subject;
        }
        foreach ($teaches as $id => $url) {
            $contentsOrObjective
                = $this->getStudyDataByIdAndUrl($id, $url);
            $educationalData[self::getKeyForInstance($contentsOrObjective)][$id]
                = $contentsOrObjective;
        }
        if ($returnFullHierarchy) {
            $educationalData = self::addMissingParentData($educationalData);
        }
        if ($filterBasicEducationRoot) {
            foreach ($educationalData[EducationalData::EDUCATIONAL_LEVELS] ?? [] as $id => $level) {
                if ($level->getCodeValue() === EducationalLevelInterface::BASIC_EDUCATION) {
                    unset($educationalData[EducationalData::EDUCATIONAL_LEVELS][$id]);
                }
            }
        }
        return $educationalData;
    }

    /**
     * Maps educational level code values.
     *
     * Basic education levels are mapped to EducationalLevelInterface::PRIMARY_SCHOOL
     * and EducationalLevelInterface::LOWER_SECONDARY_SCHOOL.
     *
     * @param array<string> $levelCodeValues
     *     Educational level code values
     *
     * @return array<string>
     *     Mapped educational level code values
     */
    public static function mapEducationalLevelCodeValues(array $levelCodeValues): array
    {
        $mappedValues = [];
        foreach ($levelCodeValues as $levelCodeValue) {
            $addValues
                = self::PRIMARY_LOWER_SECONDARY_MAP[$levelCodeValue]
                    ?? [$levelCodeValue];
            // Ensure values are in array only once.
            foreach ($addValues as $addValue) {
                $mappedValues[$addValue] = $addValue;
            }
        }
        return $mappedValues;
    }

    /**
     * Unmaps an educational level code value.
     *
     * Note that unmapping is always to all possible values.
     *
     * @param string $levelCodeValue
     *     Educational level code value
     *
     * @return array<string>
     *     Unmapped educational level code values
     */
    public static function unmapEducationalLevelCodeValue(string $levelCodeValue): array
    {
        return match ($levelCodeValue) {
            EducationalLevelInterface::BASIC_EDUCATION => self::BASIC_EDUCATION_LEVELS,
            self::PRIMARY_SCHOOL => self::PRIMARY_SCHOOL_LEVELS,
            self::LOWER_SECONDARY_SCHOOL => self::LOWER_SECONDARY_SCHOOL_LEVELS,
            default => [$levelCodeValue],
        };
    }

    /**
     * Get mapped educational level code values from provided educational levels.
     *
     * @param array<EducationalLevelInterface> $educationalLevels
     *     Educational levels
     *
     * @return array<string>
     *     Mapped educational level code values
     *
     * @throws MissingValueException
     */
    public function getMappedLevelCodeValues(array $educationalLevels): array
    {
        $levelCodeValues = [];
        foreach ($educationalLevels as $educationalLevel) {
            $codeValue = $educationalLevel->getCodeValue();
            $equivalentAdded = false;
            if (!in_array($codeValue, self::ALL_EDUCATIONAL_LEVELS)) {
                foreach ($this->getEquivalentLevels($educationalLevel) as $equivalentLevel) {
                    if (in_array($equivalentLevel->getCodeValue(), self::ALL_EDUCATIONAL_LEVELS)) {
                        $codeValue = $equivalentLevel->getCodeValue();
                        $levelCodeValues[$codeValue] = $codeValue;
                        $equivalentAdded = true;
                    }
                }
            }
            if (!$equivalentAdded) {
                $levelCodeValues[$codeValue] = $codeValue;
            }
        }
        return self::mapEducationalLevelCodeValues($levelCodeValues);
    }

    /**
     * Get educational subjects of any subject level for the educational level from
     * the provided data.
     *
     * @param string $levelCodeValue
     *     Educational level code value
     * @param array<string, EducationalDataObjectInterface> $educationalSubjects
     *     Educational subjects
     *
     * @return array<string, EducationalSubjectInterface>
     *     Array of educational subjects keyed by subject IDs
     *
     * @throws UnexpectedValueException
     */
    public static function getEducationalSubjects(string $levelCodeValue, array $educationalSubjects): array
    {
        $subjects = [];
        foreach ($educationalSubjects as $educationalSubject) {
            $educationalSubject = Assert::educationalSubject($educationalSubject);
            foreach (self::unmapEducationalLevelCodeValue($levelCodeValue) as $unmappedValue) {
                if ($educationalSubject->isApplicableToEducationalLevel($unmappedValue)) {
                    $subjects[$educationalSubject->getId()] = $educationalSubject;
                    break;
                }
            }
        }
        return $subjects;
    }

    /**
     * Get educational level specific educational data from the provided data.
     *
     * @param string $levelCodeValue
     *     Educational level code value
     * @param array<string, array<string, EducationalDataObjectInterface>> $educationalData
     *     Educational data array
     * @param bool $filterSubjects
     *     Whether to filter out subjects that
     *         - have no other data for the specified level and
     *         - have other data for other levels
     *     (optional, defaults to false)
     *
     * @return array<string, array<string, EducationalDataObjectInterface>>
     *
     * @throws MissingValueException
     * @throws UnexpectedValueException
     */
    public static function getEducationalLevelData(
        string $levelCodeValue,
        array $educationalData,
        bool $filterSubjects = false
    ): array {
        $unmappedLevels = array_flip(self::unmapEducationalLevelCodeValue($levelCodeValue));

        $levelData = [];

        // Learning areas.
        if (
            EducationalLevelInterface::EARLY_CHILDHOOD_EDUCATION === $levelCodeValue
            && !empty($educationalData[EducationalData::LEARNING_AREAS])
        ) {
            $levelData[EducationalData::LEARNING_AREAS]
                = $educationalData[EducationalData::LEARNING_AREAS];
        }

        foreach (EducationalData::EDUCATIONAL_SUBJECT_LEVEL_KEYS as $subjectLevelKey) {
            // Educational subjects.
            if (!isset($educationalData[$subjectLevelKey])) {
                continue;
            }
            $subjects = self::getEducationalSubjects($levelCodeValue, $educationalData[$subjectLevelKey]);
            if (empty($subjects)) {
                continue;
            }
            $levelData[$subjectLevelKey] = $subjects;

            // Study contents and objectives.
            foreach ($subjects as $subject) {
                foreach (EducationalData::STUDY_DATA_KEYS as $key) {
                    $studyData = self::getStudyDataKeyedByEducationalLevel(
                        self::getStudyDataBySubject($subject, $educationalData[$key] ?? [])
                    );
                    $matchingLevels = array_intersect_key($studyData, $unmappedLevels);
                    foreach ($matchingLevels as $matchingLevel) {
                        foreach ($matchingLevel as $id => $data) {
                            $levelData[$key][$id] = $data;
                        }
                    }
                }
            }
        }

        // Transversal competences.
        $transversalCompetences = [];
        foreach ($educationalData[EducationalData::TRANSVERSAL_COMPETENCES] ?? [] as $transversalCompetence) {
            $transversalCompetence = Assert::studyContents($transversalCompetence);
            $unmappedValue = (
                self::PRIMARY_SCHOOL === $levelCodeValue
                || self::LOWER_SECONDARY_SCHOOL === $levelCodeValue
            )
                ? EducationalLevelInterface::BASIC_EDUCATION
                : $levelCodeValue;
            if ($transversalCompetence->getEducationalLevelCodeValue() === $unmappedValue) {
                $transversalCompetences[$transversalCompetence->getId()] = $transversalCompetence;
            }
        }
        if (!empty($transversalCompetences)) {
            $levelData[EducationalData::TRANSVERSAL_COMPETENCES] = $transversalCompetences;
        }

        // Vocational common units.
        if (
            EducationalLevelInterface::VOCATIONAL_EDUCATION === $levelCodeValue
            && !empty($educationalData[EducationalData::VOCATIONAL_COMMON_UNITS])
        ) {
            $levelData[EducationalData::VOCATIONAL_COMMON_UNITS]
                = $educationalData[EducationalData::VOCATIONAL_COMMON_UNITS];
        }

        if ($filterSubjects) {
            $levelData = self::filterLevelSubjects($levelData, $educationalData);
        }

        return $levelData;
    }

    /**
     * Get subject specific educational data from the provided data.
     *
     * @param EducationalSubjectInterface $educationalSubject
     *     Educational subject
     * @param array<string, array<string, EducationalDataObjectInterface>> $educationalData
     *     Educational data array
     *
     * @return HierarchicalProxyDataObjectInterface
     *     Hierarchical subject specific data
     *
     * @throws MissingValueException
     * @throws UnexpectedValueException
     */
    public static function getEducationalSubjectData(
        EducationalSubjectInterface $educationalSubject,
        array $educationalData
    ): HierarchicalProxyDataObjectInterface {
        // Crate proxy subject root for the data.
        $subjectProxy = self::getProxySubjectLevelWithStudyData(
            $educationalSubject,
            $educationalData
        );

        $levelCodeValue = $educationalSubject->getEducationalLevelCodeValue();
        switch ($levelCodeValue) {
            case EducationalLevelInterface::BASIC_EDUCATION:
            case self::PRIMARY_SCHOOL:
            case self::LOWER_SECONDARY_SCHOOL:
            case EducationalLevelInterface::UPPER_SECONDARY_SCHOOL:
                // Get subject syllabuses.
                $subjectSyllabuses = Data::getChildren(
                    $educationalSubject,
                    $educationalData[EducationalData::EDUCATIONAL_SYLLABUSES] ?? []
                );
                foreach ($subjectSyllabuses as $subjectSyllabus) {
                    $subjectSyllabusProxy = self::getProxySubjectLevelWithStudyData(
                        Assert::educationalSubject($subjectSyllabus),
                        $educationalData,
                        $subjectProxy
                    );

                    // Get subject syllabus modules.
                    $subjectSyllabusModules = Data::getChildren(
                        $subjectSyllabus,
                        $educationalData[EducationalData::EDUCATIONAL_MODULES] ?? []
                    );
                    foreach ($subjectSyllabusModules as $subjectSyllabusModule) {
                        self::getProxySubjectLevelWithStudyData(
                            Assert::educationalSubject($subjectSyllabusModule),
                            $educationalData,
                            $subjectSyllabusProxy
                        );
                    }
                }
                // Get subject modules.
                $subjectModules = Data::getChildren(
                    $educationalSubject,
                    $educationalData[EducationalData::EDUCATIONAL_MODULES] ?? []
                );
                foreach ($subjectModules as $subjectModule) {
                    self::getProxySubjectLevelWithStudyData(
                        Assert::educationalSubject($subjectModule),
                        $educationalData,
                        $subjectProxy
                    );
                }
                break;

            case EducationalLevelInterface::VOCATIONAL_EDUCATION:
                if ($educationalSubject instanceof VocationalUnitInterface) {
                    // Get vocational common unit subjects.
                    $vocationalUnits = Data::getChildren(
                        $educationalSubject,
                        $educationalData[EducationalData::VOCATIONAL_COMMON_UNIT_SUBJECTS] ?? []
                    );
                } else {
                    // Get vocational units.
                    $vocationalUnits = Data::getChildren(
                        $educationalSubject,
                        $educationalData[EducationalData::VOCATIONAL_UNITS] ?? []
                    );
                }
                foreach ($vocationalUnits as $vocationalUnit) {
                    $subjectProxy->addChild(
                        new HierarchicalProxyDataObject(Assert::vocationalUnit($vocationalUnit))
                    );
                }
                break;
        }

        return $subjectProxy;
    }

    /**
     * Get the provided study data in an array of arrays keyed by educational level
     * code value.
     *
     * @param array<string, EducationalDataObjectInterface|ProxyObjectInterface> $studyData
     *     Study contents or objectives, optionally proxied
     *
     * @return array<string, array<string, StudyDataObjectInterface>>
     *
     * @throws MissingValueException
     */
    public static function getStudyDataKeyedByEducationalLevel(array $studyData): array
    {
        $dataByLevel = [];
        foreach ($studyData as $contentsOrObjective) {
            $contentsOrObjective = Assert::studyDataObject(Data::deProxify($contentsOrObjective));
            $deProxied = Data::deProxify($contentsOrObjective->getParent());
            if ($deProxied instanceof OphEPerusteetEducationalLevel) {
                $levelCodeValue = array_search(
                    $deProxied->getId(),
                    self::DVV_KOODISTOT_OPH_PERUSTEET_MAP
                );
                if (false !== $levelCodeValue) {
                    $dataByLevel[$levelCodeValue][$contentsOrObjective->getId()] = $contentsOrObjective;
                }
            } else {
                $rootLevel = Assert::educationalDataObject(Data::deProxify($contentsOrObjective->getRoot()));
                $levelCodeValue = $rootLevel->getEducationalLevelCodeValue();
                $dataByLevel[$levelCodeValue][$contentsOrObjective->getId()] = $contentsOrObjective;
            }
        }
        return $dataByLevel;
    }

    /**
     * Add missing parent data.
     *
     * @param array<string, array<string, EducationalDataObjectInterface>> $educationalData
     *      Educational data array
     *
     * @return array<string, array<string, EducationalDataObjectInterface>>
     *      Educational data array
     *
     * @throws MissingValueException
     */
    protected function addMissingParentData(array $educationalData): array
    {
        foreach ($educationalData as $dataType) {
            foreach ($dataType as $dataObject) {
                // Iterate up to the root level, adding data.
                $dataObject = Assert::educationalDataObject($dataObject);
                $parent = $dataObject->getParent();
                while (null !== $parent) {
                    $deProxied = Assert::educationalDataObject(Data::deProxify($parent));
                    $id = $deProxied->getId();
                    $educationalData[self::getKeyForInstance($deProxied)][$id] = $deProxied;
                    $parent = $parent->getParent();
                }

                // Set educational level from the root level.
                $level = $root = Assert::educationalDataObject(Data::deProxify($dataObject->getRoot()));
                if (!$level instanceof EducationalLevelInterface) {
                    $level = self::getEducationalLevelByCodeValue($root->getEducationalLevelCodeValue());
                }
                if (null !== $level) {
                    $educationalData[self::getKeyForInstance($level)][$level->getId()] = $level;
                }
            }
        }
        return $educationalData;
    }

    /**
     * Get all study contents or objectives for the educational subject from the
     * provided data.
     *
     * @param EducationalSubjectInterface $educationalSubject
     *     Educational subject
     * @param array<EducationalDataObjectInterface> $studyData
     *     Study contents or objectives
     *
     * @return array<string, EducationalDataObjectInterface>
     *     Array keyed by object IDs
     *
     * @internal
     */
    protected static function getStudyDataBySubject(
        EducationalSubjectInterface $educationalSubject,
        array $studyData
    ): array {
        $subjectStudyData = [];
        foreach ($studyData as $contentsOrObjective) {
            Assert::studyDataObject($contentsOrObjective);
            $contentsOrObjectiveSubject = Assert::educationalSubject(
                Assert::proxyObject($contentsOrObjective->getRoot())->getProxiedObject()
            );
            if ($educationalSubject->getRoot()->getId() === $contentsOrObjectiveSubject->getId()) {
                $subjectStudyData[$contentsOrObjective->getId()] = $contentsOrObjective;
            }
        }
        return $subjectStudyData;
    }

    /**
     * Filter out subjects that
     *     - have no study data for the level and
     *     - have study data for other levels.
     *
     * @param array<string, array<string, EducationalDataObjectInterface>> $levelData
     *     Educational data array
     * @param array<string, array<string, EducationalDataObjectInterface>> $educationalData
     *      Educational data array
     *
     * @return array<string, array<string, EducationalDataObjectInterface>>
     *     Educational data array
     *
     * @internal
     */
    protected static function filterLevelSubjects(
        array $levelData,
        array $educationalData
    ): array {
        // Iterate through all educational subjects.
        foreach ($levelData[self::EDUCATIONAL_SUBJECTS] ?? [] as $id => $subject) {
            $subject = Assert::educationalSubject($subject);

            // Iterate through all study contents and objectives.
            foreach (self::STUDY_DATA_KEYS as $key) {
                if (!empty(self::getStudyDataBySubject($subject, $levelData[$key] ?? []))) {
                    // Study data for educational level found, continue with next subject.
                    continue 2;
                }
                if (!empty(self::getStudyDataBySubject($subject, $educationalData[$key] ?? []))) {
                    // Study data for other educational levels was found, remove subject.
                    unset($levelData[self::EDUCATIONAL_SUBJECTS][$id]);
                    continue 2;
                }
            }
        }

        return $levelData;
    }

    /**
     * Get all study contents or objectives for the educational subject level from
     * the provided data.
     *
     * @param EducationalSubjectInterface $educationalSubject
     *     Educational subject level
     * @param array<string, EducationalDataObjectInterface> $studyData
     *     Study contents or objectives
     *
     * @return array<string, EducationalDataObjectInterface>
     *
     * @internal
     */
    protected static function getStudyDataBySubjectLevel(
        EducationalSubjectInterface $educationalSubject,
        array $studyData
    ): array {
        $levelStudyData = [];
        foreach ($studyData as $contentsOrObjective) {
            $proxyParent = Assert::hierarchicalProxyDataObject($contentsOrObjective->getParent());
            $parent = $proxyParent->getProxiedObject();
            if ($parent instanceof EducationalLevelInterface && !$proxyParent->isRoot()) {
                $parent = Assert::proxyObject($proxyParent->getParent())->getProxiedObject();
            }
            if (
                ($parent instanceof EducationalSubjectInterface
                    && $parent->getId() === $educationalSubject->getId())
                || ($parent instanceof EducationalLevelInterface
                    && $proxyParent->isRoot())
            ) {
                $levelStudyData[$contentsOrObjective->getId()] = $contentsOrObjective;
            }
        }
        return $levelStudyData;
    }

    /**
     * Create a proxy item for the educational subject level and add possible
     * study contents and/or objectives to the proxy hierarchy.
     *
     * @param EducationalSubjectInterface $educationalSubject
     *     Educational subject level
     * @param array<string, array<string, EducationalDataObjectInterface>> $educationalData
     *     Educational data array
     * @param ?HierarchicalProxyDataObjectInterface $parent
     *      Parent educational subject level proxy object to add to (optional)
     *
     * @return HierarchicalProxyDataObject
     *     The added educational subject level proxy object
     *
     * @internal
     */
    protected static function getProxySubjectLevelWithStudyData(
        EducationalSubjectInterface $educationalSubject,
        array $educationalData,
        HierarchicalProxyDataObjectInterface $parent = null
    ): HierarchicalProxyDataObjectInterface {
        $subjectLevelProxy = new HierarchicalProxyDataObject($educationalSubject);
        $parent?->addChild($subjectLevelProxy);

        // Add study contents.
        if (!empty($educationalData[EducationalData::STUDY_CONTENTS])) {
            $levelStudyContents = self::getStudyDataBySubjectLevel(
                $educationalSubject,
                $educationalData[EducationalData::STUDY_CONTENTS]
            );
            foreach ($levelStudyContents as $studyContents) {
                $subjectLevelProxy->addChild(
                    new HierarchicalProxyDataObject(Assert::studyContents($studyContents))
                );
            }
        }

        // Add study objectives.
        if (!empty($educationalData[EducationalData::STUDY_OBJECTIVES])) {
            $levelStudyObjectives = self::getStudyDataBySubjectLevel(
                $educationalSubject,
                $educationalData[EducationalData::STUDY_OBJECTIVES]
            );
            foreach ($levelStudyObjectives as $studyObjective) {
                $subjectLevelProxy->addChild(
                    new HierarchicalProxyDataObject(Assert::studyObjective($studyObjective))
                );
            }
        }

        return $subjectLevelProxy;
    }
}
