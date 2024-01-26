<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalLevel;

use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;
use NatLibFi\FinnaCodeSets\Model\AbstractHierarchicalDataObject;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Model\StudyContents\StudyContentsInterface;

abstract class AbstractEducationalLevel extends AbstractHierarchicalDataObject implements EducationalLevelInterface
{
    /**
     * Educational subjects.
     *
     * @var ?array<EducationalSubjectInterface>
     */
    protected ?array $educationalSubjects = null;

    /**
     * Transversal competences.
     *
     * @var ?array<StudyContentsInterface>
     */
    protected ?array $transversalCompetences = null;

    /**
     * {@inheritdoc}
     */
    public function getEducationalLevelCodeValue(): string
    {
        return $this->getCodeValue();
    }

    /**
     * {@inheritdoc}
     */
    public function educationalSubjectsSet(): bool
    {
        return null !== $this->educationalSubjects;
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjects(): array
    {
        if (null === $this->educationalSubjects) {
            throw new ValueNotSetException('Educational subjects');
        }
        return $this->educationalSubjects;
    }

    /**
     * {@inheritdoc}
     */
    public function setEducationalSubjects(array $educationalSubjects): void
    {
        $this->educationalSubjects = $educationalSubjects;
    }

    /**
     * {@inheritdoc}
     */
    public function transversalCompetencesSet(): bool
    {
        return null !== $this->transversalCompetences;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransversalCompetences(): array
    {
        if (null === $this->transversalCompetences) {
            throw new ValueNotSetException('Transversal competences');
        }
        return $this->transversalCompetences;
    }

    /**
     * {@inheritdoc}
     */
    public function setTransversalCompetences(array $transversalCompetences): void
    {
        $this->transversalCompetences = $transversalCompetences;
    }
}
