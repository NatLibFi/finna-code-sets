<?php

namespace NatLibFi\FinnaCodeSets\Model\Concept;

use NatLibFi\FinnaCodeSets\Model\DataObjectInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalObjectInterface;

interface ConceptInterface extends DataObjectInterface, HierarchicalObjectInterface
{
    /**
     * Get vocabulary ID.
     *
     * @return string
     */
    public function getVocabularyId(): string;
}
