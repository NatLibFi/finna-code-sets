<?php

namespace NatLibFi\FinnaCodeSets\Model\Keyword;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Model\AbstractHierarchicalDataObject;

class Keyword extends AbstractHierarchicalDataObject implements KeywordInterface
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
        if (!isset($this->data['prefLabel'])) {
            throw new MissingValueException('Preferred labels');
        }
        return [$this->data['lang'] => $this->data['prefLabel']];
    }
}
