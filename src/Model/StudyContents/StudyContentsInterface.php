<?php

namespace NatLibFi\FinnaCodeSets\Model\StudyContents;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Model\DataObjectInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalObjectInterface;

interface StudyContentsInterface extends DataObjectInterface, HierarchicalObjectInterface
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
