<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalSubject;

use NatLibFi\FinnaCodeSets\Exception\NotFoundException;
use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;
use NatLibFi\FinnaCodeSets\Model\AbstractHierarchicalDataObject;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalObjectInterface;

abstract class AbstractEducationalSubject extends AbstractHierarchicalDataObject implements
    EducationalSubjectInterface
{
    /**
     * Educational level code value.
     *
     * @var string
     */
    protected string $levelCodeValue;

    /**
     * Educational levels.
     *
     * @var array<EducationalLevelInterface>
     */
    protected array $educationalLevels;

    /**
     * Study contents.
     *
     * @var ?HierarchicalObjectInterface
     */
    protected ?HierarchicalObjectInterface $studyContents = null;

    /**
     * Study objectives.
     *
     * @var ?HierarchicalObjectInterface
     */
    protected ?HierarchicalObjectInterface $studyObjectives = null;

    /**
     * AbstractEducationalSubject constructor.
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
    public function __construct(
        array $data,
        string $apiBaseUrl,
        string $levelCodeValue,
        array $educationalLevels = []
    ) {
        parent::__construct($data, $apiBaseUrl);
        $this->levelCodeValue = $levelCodeValue;
        $this->educationalLevels = $educationalLevels;
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalLevelCodeValue(): string
    {
        return $this->levelCodeValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalLevelsApplicableTo(): array
    {
        $ids = [];
        foreach ($this->data['vuosiluokkakokonaisuudet'] ?? [] as $level) {
            $ids[] = (string)$level['_vuosiluokkaKokonaisuus'];
        }
        return $ids;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicableToEducationalLevel(string $levelCodeValue): bool
    {
        if ($this->getEducationalLevelCodeValue() === $levelCodeValue) {
            return true;
        }
        if ($levelId = EducationalLevelInterface::DVV_KOODISTOT_OPH_PERUSTEET_MAP[$levelCodeValue] ?? false) {
            foreach ($this->getEducationalLevelsApplicableTo() as $id) {
                if ($id === $levelId) {
                    return true;
                }
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
            throw new ValueNotSetException('Study contents');
        }
        return $this->studyContents;
    }

    /**
     * {@inheritdoc}
     */
    public function getStudyObjectives(): HierarchicalObjectInterface
    {
        if (null === $this->studyObjectives) {
            throw new ValueNotSetException('Study objectives');
        }
        return $this->studyObjectives;
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
