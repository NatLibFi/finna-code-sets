<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\Exception\NotFoundException;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\StudyContents\StudyContentsInterface;

interface TransversalCompetencesSourceInterface
{
    /**
     * Get transversal competences.
     *
     * @param string $levelCodeValue
     *     Educational level code value
     *
     * @return array<StudyContentsInterface>
     *
     * @throws NotFoundException
     * @throws NotSupportedException
     */
    public function getTransversalCompetences(string $levelCodeValue): array;
}
