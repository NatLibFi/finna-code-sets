<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;

interface EducationalLevelsSourceInterface
{
    /**
     * Get educational levels.
     *
     * @return array<EducationalLevelInterface>
     */
    public function getEducationalLevels(): array;

    /**
     * Recursively add equivalent educational levels.
     *
     * @param EducationalLevelInterface $educationalLevel
     *     Root educational level.
     */
    public function addEquivalentEducationalLevels(EducationalLevelInterface $educationalLevel): void;
}
