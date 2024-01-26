<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalSubject;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Exception\NotFoundException;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Exception\UnexpectedValueException;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalModule\OphEperusteetEducationalModule;
use NatLibFi\FinnaCodeSets\Model\EducationalSyllabus\OphEperusteetEducationalSyllabus;
use NatLibFi\FinnaCodeSets\Model\HierarchicalObjectInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalProxyDataObject;
use NatLibFi\FinnaCodeSets\Model\StudyContents\BasicEducationStudyContents;
use NatLibFi\FinnaCodeSets\Model\StudyContents\UpperSecondarySchoolStudyContents;
use NatLibFi\FinnaCodeSets\Model\StudyObjective\BasicEducationStudyObjective;
use NatLibFi\FinnaCodeSets\Model\StudyObjective\UpperSecondarySchoolStudyObjective;
use NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet\OphEPerusteetInterface;
use NatLibFi\FinnaCodeSets\Utility\Assert;
use NatLibFi\FinnaCodeSets\Utility\EducationalData;

class OphEperusteetEducationalSubject extends AbstractEducationalSubject
{
    /**
     * Educational levels.
     *
     * @var array<EducationalLevelInterface>
     */
    protected array $educationalLevels;

    /**
     * OphEperusteetEducationalSubject constructor.
     *
     * @param array<mixed> $data
     *     Data from API
     * @param string $apiBaseUrl
     *     Base URL of source API
     * @param string $levelCodeValue
     *     Educational level code value
     * @param array<EducationalLevelInterface> $educationalLevels
     *     Educational levels
     */
    public function __construct(array $data, string $apiBaseUrl, string $levelCodeValue, array $educationalLevels = [])
    {
        parent::__construct($data, $apiBaseUrl, $levelCodeValue);
        $this->educationalLevels = $educationalLevels;
        // Add possible syllabus data as second level in hierarchy.
        if (!empty($syllabus = $data['oppimaarat'] ?? [])) {
            foreach ($syllabus as $syllabusData) {
                $child = new OphEperusteetEducationalSyllabus(
                    $syllabusData,
                    $apiBaseUrl,
                    $levelCodeValue,
                    $educationalLevels
                );
                $this->addChild($child);
                // Add possible upper secondary school module data as third level
                // in hierarchy.
                if (!empty($modules = $syllabusData['moduulit'] ?? [])) {
                    foreach ($modules as $moduleData) {
                        $grandChild = new OphEperusteetEducationalModule(
                            $moduleData,
                            $apiBaseUrl,
                            $levelCodeValue,
                            $educationalLevels
                        );
                        $child->addChild($grandChild);
                    }
                }
            }
        }
        // Add possible upper secondary school module data as second level in
        // hierarchy.
        if (!empty($modules = $data['moduulit'] ?? [])) {
            foreach ($modules as $moduleData) {
                $child = new OphEperusteetEducationalModule(
                    $moduleData,
                    $apiBaseUrl,
                    $levelCodeValue,
                    $educationalLevels
                );
                $this->addChild($child);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        switch ($this->getEducationalLevelCodeValue()) {
            case EducationalLevelInterface::BASIC_EDUCATION:
                return $this->apiBaseUrl
                    . OphEPerusteetInterface::BASIC_EDUCATION_EDUCATIONAL_SUBJECTS_API_METHOD
                    . '/' . $this->getId();

            case EducationalLevelInterface::UPPER_SECONDARY_SCHOOL:
                return $this->apiBaseUrl
                    . OphEPerusteetInterface::UPPER_SECONDARY_SCHOOL_EDUCATIONAL_SUBJECTS_API_METHOD
                    . '/' . $this->getId();
        }
        throw new NotSupportedException($this->getEducationalLevelCodeValue());
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder(): int
    {
        switch ($this->getEducationalLevelCodeValue()) {
            case EducationalLevelInterface::BASIC_EDUCATION:
                return (int)$this->data['jnro'];
        }
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeValue(): string
    {
        switch ($this->getEducationalLevelCodeValue()) {
            case EducationalLevelInterface::BASIC_EDUCATION:
                $codeValue = $this->data['koodiArvo'] ?? null;
                break;

            case EducationalLevelInterface::UPPER_SECONDARY_SCHOOL:
                $codeValue = $this->data['koodi']['arvo'] ?? null;
                break;

            default:
                throw new NotSupportedException($this->getEducationalLevelCodeValue());
        }
        if (!isset($codeValue)) {
            throw new MissingValueException('Code value');
        }
        return (string)$codeValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefLabels(): array
    {
        return array_filter(
            $this->data['nimi'] ?? [],
            function ($value, $key) {
                return !str_starts_with($key, '_');
            },
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicableToEducationalLevel(string $levelCodeValue): bool
    {
        if ($this->getEducationalLevelCodeValue() === $levelCodeValue) {
            return true;
        }
        if ($levelId = EducationalData::DVV_KOODISTOT_OPH_PERUSTEET_MAP[$levelCodeValue] ?? false) {
            if (in_array($levelId, $this->getEducationalLevelsApplicableTo())) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getStudyContents(): HierarchicalObjectInterface
    {
        if (null === $this->studyContents) {
            $proxyRoot = new HierarchicalProxyDataObject($this, false);
            foreach ($this->data['vuosiluokkakokonaisuudet'] ?? [] as $levelData) {
                if (empty($levelData['sisaltoalueet'])) {
                    continue;
                }
                $proxyLevel = new HierarchicalProxyDataObject(
                    $this->getEducationalLevel($levelData['_vuosiluokkaKokonaisuus']),
                    false
                );
                foreach ($levelData['sisaltoalueet'] as $contentsData) {
                    switch ($this->getEducationalLevelCodeValue()) {
                        case EducationalLevelInterface::BASIC_EDUCATION:
                            $proxyLevel->addChild(new BasicEducationStudyContents(
                                $contentsData,
                                $this->apiBaseUrl,
                                $this->levelCodeValue
                            ));
                            break;

                        case EducationalLevelInterface::UPPER_SECONDARY_SCHOOL:
                            $proxyLevel->addChild(new UpperSecondarySchoolStudyContents(
                                $contentsData,
                                $this->apiBaseUrl,
                                $this->levelCodeValue
                            ));
                            break;

                        default:
                            throw new UnexpectedValueException($this->getEducationalLevelCodeValue());
                    }
                }
                $proxyRoot->addChild($proxyLevel);
            }
            foreach ($this->getChildren() as $child) {
                $child = Assert::educationalSubject($child);
                $proxyRoot->addChild($child->getStudyContents());
            }
            $this->studyContents = $proxyRoot;
        }
        return $this->studyContents;
    }

    /**
     * {@inheritdoc}
     */
    public function getStudyObjectives(): HierarchicalObjectInterface
    {
        if (null === $this->studyObjectives) {
            $proxyRoot = new HierarchicalProxyDataObject($this, false);
            foreach ($this->data['vuosiluokkakokonaisuudet'] ?? [] as $levelData) {
                if (empty($levelData['tavoitteet'])) {
                    continue;
                }
                $proxyLevel = new HierarchicalProxyDataObject(
                    $this->getEducationalLevel($levelData['_vuosiluokkaKokonaisuus']),
                    false
                );
                foreach ($levelData['tavoitteet'] as $objectiveData) {
                    switch ($this->getEducationalLevelCodeValue()) {
                        case EducationalLevelInterface::BASIC_EDUCATION:
                            $proxyLevel->addChild(new BasicEducationStudyObjective(
                                $objectiveData,
                                $this->apiBaseUrl,
                                $this->levelCodeValue
                            ));
                            break;

                        case EducationalLevelInterface::UPPER_SECONDARY_SCHOOL:
                            $proxyLevel->addChild(new UpperSecondarySchoolStudyObjective(
                                $objectiveData,
                                $this->apiBaseUrl,
                                $this->levelCodeValue
                            ));
                            break;

                        default:
                            throw new UnexpectedValueException($this->getEducationalLevelCodeValue());
                    }
                }
                $proxyRoot->addChild($proxyLevel);
            }
            foreach ($this->getChildren() as $child) {
                $child = Assert::educationalSubject($child);
                $proxyRoot->addChild($child->getStudyObjectives());
            }
            $this->studyObjectives = $proxyRoot;
        }
        return $this->studyObjectives;
    }

    /**
     * Get IDs of educational levels this subject is applicable to.
     *
     * @return array<string>
     */
    protected function getEducationalLevelsApplicableTo(): array
    {
        $ids = [];
        foreach ($this->data['vuosiluokkakokonaisuudet'] ?? [] as $level) {
            $ids[] = (string)$level['_vuosiluokkaKokonaisuus'];
        }
        return $ids;
    }

    /**
     * Get educational level.
     *
     * @param string $id
     *
     * @return EducationalLevelInterface
     *
     * @throws NotFoundException
     */
    protected function getEducationalLevel(string $id): EducationalLevelInterface
    {
        foreach ($this->educationalLevels as $educationalLevel) {
            if ($educationalLevel->getId() === $id) {
                return $educationalLevel;
            }
        }
        throw new NotFoundException($id);
    }
}
