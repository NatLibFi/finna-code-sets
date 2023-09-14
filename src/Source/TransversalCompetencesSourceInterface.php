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

    /**
     * Get transversal competence by URL.
     *
     * @param string $url
     *     Transversal competence API URL
     *
     * @return StudyContentsInterface
     *
     * @throws NotFoundException
     * @throws NotSupportedException
     */
    public function getTransversalCompetenceByUrl(string $url): StudyContentsInterface;

    /**
     * Is this a transversal competence URL supported by this source?
     *
     * @param string $url
     *
     * @return bool
     */
    public function isSupportedTransversalCompetenceUrl(string $url): bool;
}
