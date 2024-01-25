<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalData;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Model\DataObjectInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalObjectInterface;

/**
 * Base interface for all educational data objects.
 */
interface EducationalDataObjectInterface extends DataObjectInterface, HierarchicalObjectInterface
{
    /**
     * Get educational level code value.
     *
     * @return string
     *
     * @throws MissingValueException
     */
    public function getEducationalLevelCodeValue(): string;
}
