<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;

interface EducationalLevelsSourceInterface extends SourceInterface
{
    /**
     * Get educational levels.
     *
     * @return array<EducationalLevelInterface>
     *
     * @throws NotSupportedException
     */
    public function getEducationalLevels(): array;
}
