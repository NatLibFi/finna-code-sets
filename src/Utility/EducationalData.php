<?php

namespace NatLibFi\FinnaCodeSets\Utility;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Exception\NotFoundException;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Exception\UnexpectedValueException;
use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;
use NatLibFi\FinnaCodeSets\FinnaCodeSets;
use NatLibFi\FinnaCodeSets\Model\DataObjectInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalModule\EducationalModuleInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSyllabus\EducationalSyllabusInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalObjectInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalProxyDataObject;
use NatLibFi\FinnaCodeSets\Model\ProxyObjectInterface;
use NatLibFi\FinnaCodeSets\Model\StudyContents\StudyContentsInterface;
use NatLibFi\FinnaCodeSets\Model\StudyObjective\StudyObjectiveInterface;
use NatLibFi\FinnaCodeSets\Model\VocationalQualification\VocationalQualificationInterface;
use NatLibFi\FinnaCodeSets\Model\VocationalUnit\VocationalUnitInterface;

/**
 * Utility methods for working with educational data.
 */
class EducationalData
{
    // Used as educational data array keys.
    public const EDUCATIONAL_LEVELS = 'educationalLevels';
    public const EDUCATIONAL_SUBJECTS = 'educationalSubjects';
    public const EDUCATIONAL_SYLLABUSES = 'educationalSyllabuses';
    public const EDUCATIONAL_MODULES = 'educationalModules';
    public const STUDY_CONTENTS = 'studyContents';
    public const STUDY_OBJECTIVES = 'studyObjectives';
    public const TRANSVERSAL_COMPETENCES = 'transversalCompetences';
    public const VOCATIONAL_QUALIFICATIONS = 'vocationalQualifications';
    public const VOCATIONAL_UNITS = 'vocationalUnits';
    public const VOCATIONAL_COMMON_UNITS = 'vocationalCommonUnits';

    public const EDUCATIONAL_SUBJECT_LEVEL_KEYS = [
        self::EDUCATIONAL_SUBJECTS,
        self::EDUCATIONAL_SYLLABUSES,
        self::EDUCATIONAL_MODULES,
        self::VOCATIONAL_QUALIFICATIONS,
    ];
    public const STUDY_CONTENTS_OR_OBJECTIVES_KEYS = [
        self::STUDY_CONTENTS,
        self::STUDY_OBJECTIVES,
        self::VOCATIONAL_UNITS,
    ];

    protected FinnaCodeSets $codeSets;

    /**
     * EducationalData constructor.
     *
     * @param FinnaCodeSets $codeSets
     *     Library instance used by utility methods
     */
    public function __construct(FinnaCodeSets $codeSets)
    {
        $this->codeSets = $codeSets;
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
     * @return EducationalLevelInterface
     *
     * @throws NotFoundException
     * @throws UnexpectedValueException
     */
    public function getEducationalLevelByCodeValue(
        string $levelCodeValue,
        ?array $educationalLevels = null
    ): EducationalLevelInterface {
        if (null === $educationalLevels) {
            $educationalLevels = $this->codeSets->getEducationalLevels();
        }
        foreach ($educationalLevels as $level) {
            $level = Assert::educationalLevel($level);
            try {
                if ($level->getCodeValue() === $levelCodeValue) {
                    return $level;
                }
            } catch (MissingValueException) {
            }
            try {
                return $this->getEducationalLevelByCodeValue($levelCodeValue, $level->getChildren());
            } catch (NotFoundException) {
            }
            try {
                return $this->getEducationalLevelByCodeValue($levelCodeValue, $level->getEquivalentLevels());
            } catch (NotFoundException) {
            }
        }
        throw new NotFoundException($levelCodeValue);
    }

    /**
     * Find and return an educational level from the provided array or all
     * educational levels.
     *
     * @param string $url
     *     Educational level URL
     * @param ?array<HierarchicalObjectInterface> $educationalLevels
     *     Array of educational levels to search from
     *
     * @return EducationalLevelInterface
     *
     * @throws MissingValueException
     * @throws NotFoundException
     * @throws NotSupportedException
     * @throws UnexpectedValueException
     */
    public function getEducationalLevelByUrl(
        string $url,
        ?array $educationalLevels = null
    ): EducationalLevelInterface {
        if (null === $educationalLevels) {
            $educationalLevels = $this->codeSets->getEducationalLevels();
        }
        foreach ($educationalLevels as $level) {
            $level = Assert::educationalLevel($level);
            if ($level->getUri() === $url) {
                return $level;
            }
            try {
                return $this->getEducationalLevelByUrl($url, $level->getChildren());
            } catch (NotFoundException) {
            }
            try {
                return $this->getEducationalLevelByUrl($url, $level->getEquivalentLevels());
            } catch (NotFoundException) {
            }
        }
        throw new NotFoundException($url);
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
     * Get transversal competences for an educational level.
     *
     * @param EducationalLevelInterface $educationalLevel
     *     Educational level
     *
     * @return HierarchicalObjectInterface
     *     Transversal competences hierarchy with the educational level as root.
     *
     * @throws MissingValueException
     * @throws NotFoundException
     * @throws NotSupportedException
     * @throws ValueNotSetException
     */
    public function getTransversalCompetences(EducationalLevelInterface $educationalLevel): HierarchicalObjectInterface
    {
        if (!$educationalLevel->transversalCompetencesSet()) {
            $educationalLevel->setTransversalCompetences(
                $this->codeSets->getTransversalCompetences($educationalLevel->getCodeValue())
            );
        }
        $root = new HierarchicalProxyDataObject($educationalLevel, false);
        foreach ($educationalLevel->getTransversalCompetences() as $transversalCompetence) {
            $root->addChild($transversalCompetence);
        }
        return $root;
    }

    /**
     * Get educational subject by ID and URL.
     *
     * @param string $id
     * @param string $url
     *
     * @return EducationalSubjectInterface
     *
     * @throws NotFoundException
     * @throws NotSupportedException
     */
    public function getEducationalSubjectByIdAndUrl(string $id, string $url): EducationalSubjectInterface
    {
        $subject = $this->codeSets->getEducationalSubjectByUrl($url);
        if ($subject->getId() !== $id) {
            $subject = Assert::educationalSubject($subject->getDescendant($id));
        }
        return $subject;
    }

    /**
     * Get study contents or study objective by ID.
     *
     * @param string $id
     *     Study contents or study objective ID
     * @param EducationalSubjectInterface|EducationalLevelInterface $subjectOrLevel
     *     Educational subject or level
     *
     * @return StudyContentsInterface|StudyObjectiveInterface
     *
     * @throws MissingValueException
     * @throws NotFoundException
     * @throws NotSupportedException
     * @throws UnexpectedValueException
     * @throws ValueNotSetException
     */
    public function getStudyContentsOrObjectiveById(
        string $id,
        EducationalSubjectInterface|EducationalLevelInterface $subjectOrLevel
    ): StudyContentsInterface|StudyObjectiveInterface {
        if ($subjectOrLevel instanceof EducationalSubjectInterface) {
            try {
                return Assert::studyContents(
                    $subjectOrLevel->getStudyContents()->getDescendant($id)
                );
            } catch (NotFoundException) {
            }
            return Assert::studyObjective(
                $subjectOrLevel->getStudyObjectives()->getDescendant($id)
            );
        }
        return Assert::studyContents(
            $this->getTransversalCompetences($subjectOrLevel)->getDescendant($id)
        );
    }

    /**
     * Get study contents or objective by ID and educational subject or level URL.
     *
     * @param string $id
     *     Study contents or objective ID
     * @param string $url
     *     Educational subject or level URL
     *
     * @return StudyContentsInterface|StudyObjectiveInterface
     *
     * @throws MissingValueException
     * @throws NotFoundException
     * @throws NotSupportedException
     * @throws ValueNotSetException
     */
    public function getStudyContentsOrObjectiveByIdAndUrl(
        string $id,
        string $url
    ): StudyContentsInterface|StudyObjectiveInterface {
        try {
            // Try to get educational subject.
            $subjectOrLevel = $this->codeSets->getEducationalSubjectByUrl($url);
        } catch (NotSupportedException) {
            // Try to get educational level.
            $subjectOrLevel = $this->getEducationalLevelByUrl($url);
        }
        return $this->getStudyContentsOrObjectiveById($id, $subjectOrLevel);
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
     *
     * @return array<string, array<EducationalLevelInterface|EducationalSubjectInterface|StudyContentsInterface|StudyObjectiveInterface>>
     *     Educational data array, keyed by constants defined in this class
     *
     * @throws MissingValueException
     * @throws NotFoundException
     * @throws NotSupportedException
     * @throws ValueNotSetException
     */
    public function getLrmiEducationalData(
        array $educationalLevels = [],
        array $educationalSubjects = [],
        array $teaches = []
    ): array {
        $data = [];
        foreach ($educationalLevels as $codeValue => $url) {
            $data[EducationalData::EDUCATIONAL_LEVELS][]
                = $this->getEducationalLevelByCodeValue($codeValue);
        }
        foreach ($educationalSubjects as $id => $url) {
            $subject = $this->getEducationalSubjectByIdAndUrl($id, $url);
            if ($subject instanceof EducationalSyllabusInterface) {
                $data[EducationalData::EDUCATIONAL_SYLLABUSES][] = $subject;
            } elseif ($subject instanceof EducationalModuleInterface) {
                $data[EducationalData::EDUCATIONAL_MODULES][] = $subject;
            } elseif ($subject instanceof  VocationalQualificationInterface) {
                $data[EducationalData::VOCATIONAL_QUALIFICATIONS][] = $subject;
            } else {
                $data[EducationalData::EDUCATIONAL_SUBJECTS][] = $subject;
            }
        }
        foreach ($teaches as $id => $url) {
            $contentsOrObjective
                = $this->getStudyContentsOrObjectiveByIdAndUrl($id, $url);
            if ($contentsOrObjective instanceof VocationalUnitInterface) {
                if ($contentsOrObjective->isCommonUnit()) {
                    $data[EducationalData::VOCATIONAL_COMMON_UNITS][] = $contentsOrObjective;
                } else {
                    $data[EducationalData::VOCATIONAL_UNITS][] = $contentsOrObjective;
                }
            } elseif ($contentsOrObjective instanceof StudyContentsInterface) {
                $levelOrSubject = Assert::proxyObject($contentsOrObjective->getRoot())->getProxiedObject();
                if ($levelOrSubject instanceof EducationalLevelInterface) {
                    $data[EducationalData::TRANSVERSAL_COMPETENCES][] = $contentsOrObjective;
                } elseif ($levelOrSubject instanceof EducationalSubjectInterface) {
                    $data[EducationalData::STUDY_CONTENTS][] = $contentsOrObjective;
                } else {
                    throw new UnexpectedValueException();
                }
            } elseif ($contentsOrObjective instanceof StudyObjectiveInterface) {
                $data[EducationalData::STUDY_OBJECTIVES][] = $contentsOrObjective;
            } else {
                throw new UnexpectedValueException();
            }
        }
        return $data;
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
            $mappedValue
                = EducationalLevelInterface::PRIMARY_LOWER_SECONDARY_MAP[$levelCodeValue]
                    ?? $levelCodeValue;
            // Ensure mapped values are in array only once.
            $mappedValues[$mappedValue] = $mappedValue;
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
        switch ($levelCodeValue) {
            case EducationalLevelInterface::PRIMARY_SCHOOL:
                return EducationalLevelInterface::PRIMARY_SCHOOL_LEVELS;

            case EducationalLevelInterface::LOWER_SECONDARY_SCHOOL:
                return EducationalLevelInterface::LOWER_SECONDARY_SCHOOL_LEVELS;

            default:
                return [$levelCodeValue];
        }
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
    public static function getMappedLevelCodeValues(array $educationalLevels): array
    {
        $levelCodeValues = [];
        foreach ($educationalLevels as $educationalLevel) {
            $levelCodeValues[] = $educationalLevel->getCodeValue();
        }
        return self::mapEducationalLevelCodeValues($levelCodeValues);
    }

    /**
     * Get subjects for the educational level from the provided data.
     *
     * @param string $levelCodeValue
     *     Educational level code value
     * @param array<EducationalSubjectInterface> $educationalSubjects
     *     Educational subjects
     *
     * @return array<EducationalSubjectInterface>
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
     * Get educational level specific data from the provided data.
     *
     * @param string $levelCodeValue
     *     Educational level code value
     * @param array<mixed> $educationalData
     *     Educational data array
     *
     * @return array<mixed>
     *
     * @throws UnexpectedValueException
     */
    public static function getEducationalLevelData(string $levelCodeValue, array $educationalData): array
    {
        $levelData = [];

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
                foreach (EducationalData::STUDY_CONTENTS_OR_OBJECTIVES_KEYS as $contentsOrObjectivesKey) {
                    $subjectContentsOrObjectives
                        = self::getStudyContentsOrObjectives(
                            $subject,
                            $educationalData[$contentsOrObjectivesKey] ?? []
                        );
                    $levelData[$contentsOrObjectivesKey] = array_merge(
                        $levelData[$contentsOrObjectivesKey] ?? [],
                        $subjectContentsOrObjectives
                    );
                }
            }
        }

        // Transversal competences.
        $transversalCompetences = [];
        foreach ($educationalData[EducationalData::TRANSVERSAL_COMPETENCES] ?? [] as $transversalCompetence) {
            $transversalCompetence = Assert::studyContents($transversalCompetence);
            if (
                ($root = $transversalCompetence->getRoot()) instanceof ProxyObjectInterface
                && ($level = $root->getProxiedObject()) instanceof EducationalLevelInterface
            ) {
                $unmappedValue = (
                    EducationalLevelInterface::PRIMARY_SCHOOL === $levelCodeValue
                    || EducationalLevelInterface::LOWER_SECONDARY_SCHOOL === $levelCodeValue
                )
                    ? EducationalLevelInterface::BASIC_EDUCATION
                    : $levelCodeValue;
                if ($level->getCodeValue() === $unmappedValue) {
                    $transversalCompetences[$transversalCompetence->getId()] = $transversalCompetence;
                    break;
                }
            } else {
                throw new UnexpectedValueException();
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

        return $levelData;
    }

    /**
     * Get study contents or objectives for the educational subject from the provided
     * data.
     *
     * @param EducationalSubjectInterface $educationalSubject
     *     Educational subject
     * @param array<StudyContentsInterface|StudyObjectiveInterface> $studyContentsOrObjectives
     *     Study contents or objectives
     *
     * @return array<StudyContentsInterface|StudyObjectiveInterface>
     */
    public static function getStudyContentsOrObjectives(
        EducationalSubjectInterface $educationalSubject,
        array $studyContentsOrObjectives
    ): array {
        $contents = [];
        foreach ($studyContentsOrObjectives as $studyContentsOrObjective) {
            $subjectLevel = Assert::educationalSubject(
                Assert::proxyObject($studyContentsOrObjective->getRoot())->getProxiedObject()
            );
            if ($educationalSubject->getId() === $subjectLevel->getId()) {
                $contents[$studyContentsOrObjective->getId()] = $studyContentsOrObjective;
            }
        }
        return $contents;
    }

    /**
     * Get data object preferred labels in specified language.
     *
     * @param array<DataObjectInterface> $dataObjects
     *     Data objects
     * @param string $langcode
     *     Language code
     * @param ?string $fallback
     *     Fallback language code (optional, defaults to Finnish).
     *     Set to null for no fallback.
     * @param bool $deduplicate
     *     Deduplicate labels (optional, defaults to true)
     *
     * @return array<string>
     *
     * @throws MissingValueException
     * @throws ValueNotSetException
     */
    public static function getPrefLabels(
        array $dataObjects,
        string $langcode,
        ?string $fallback = DataObjectInterface::LANGUAGE_FINNISH,
        bool $deduplicate = true
    ): array {
        $labels = [];
        foreach ($dataObjects as $dataObject) {
            $label = $dataObject->getPrefLabel($langcode, $fallback);
            if (!$deduplicate || !in_array($label, $labels)) {
                $labels[] = $label;
            }
        }
        return $labels;
    }

    /**
     * Find a descendant from an array of hierarchical objects.
     *
     * @param string $id ID
     *     Descendant ID
     * @param array<HierarchicalObjectInterface> $objects
     *     Array of hierarchical objects to search from
     *
     * @return HierarchicalObjectInterface
     *
     * @throws NotFoundException
     */
    public static function findDescendant(string $id, array $objects): HierarchicalObjectInterface
    {
        foreach ($objects as $object) {
            try {
                return $object->getDescendant($id);
            } catch (NotFoundException) {
            }
        }
        throw new NotFoundException($id);
    }
}
