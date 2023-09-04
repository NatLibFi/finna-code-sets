<?php

namespace NatLibFi\FinnaCodeSets\Model;

use NatLibFi\FinnaCodeSets\Exception\HierarchyException;

/**
 * Abstract base class for hierarchical data objects.
 */
abstract class AbstractHierarchicalDataObject extends AbstractDataObject implements HierarchicalObjectInterface
{
    use HierarchicalObjectTrait {
        getHierarchyLevel as traitGetHierarchyLevel;
        setHierarchyLevel as traitSetHierarchyLevel;
    }

    /**
     * {@inheritdoc}
     */
    public function getHierarchyLevel(): int
    {
        return (int)($this->data['hierarchyLevel'] ?? $this->traitGetHierarchyLevel());
    }

    /**
     * {@inheritdoc}
     */
    public function setHierarchyLevel(int $level): void
    {
        if (isset($this->data['hierarchyLevel'])) {
            throw new HierarchyException('Fixed hierarchy level');
        }
        $this->traitSetHierarchyLevel($level);
    }
}
