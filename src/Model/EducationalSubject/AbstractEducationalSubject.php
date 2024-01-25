<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalSubject;

use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;
use NatLibFi\FinnaCodeSets\Model\AbstractHierarchicalDataObject;
use NatLibFi\FinnaCodeSets\Model\EducationalData\EducationalDataObjectTrait;
use NatLibFi\FinnaCodeSets\Model\HierarchicalObjectInterface;

abstract class AbstractEducationalSubject extends AbstractHierarchicalDataObject implements
    EducationalSubjectInterface
{
    use EducationalDataObjectTrait;

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
     */
    public function __construct(array $data, string $apiBaseUrl, string $levelCodeValue)
    {
        parent::__construct($data, $apiBaseUrl);
        $this->levelCodeValue = $levelCodeValue;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicableToEducationalLevel(string $levelCodeValue): bool
    {
        if ($this->getEducationalLevelCodeValue() === $levelCodeValue) {
            return true;
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
}
