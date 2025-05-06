<?php

namespace NatLibFi\FinnaCodeSets\Model\Concept;

class FintoIndexConcept extends AbstractConcept
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return (string)$this->data['localname'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefLabels(): array
    {
        return [$this->data['lang'] => $this->data['prefLabel']];
    }
}
