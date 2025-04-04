<?php

namespace NatLibFi\FinnaCodeSets\Model\Concept;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;

class FintoChildConcept extends AbstractConcept
{
    /**
     * {@inheritdoc}
     */
    public function getPrefLabels(): array
    {
        if (null === $this->langcode) {
            throw new MissingValueException('langcode');
        }
        return [$this->langcode => $this->data['prefLabel']];
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren(): bool
    {
        return (bool)$this->data['hasChildren'];
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren(): array
    {
        if ($this->hasChildren() && empty($this->children)) {
            throw new MissingValueException('children');
        }
        return parent::getChildren();
    }
}
