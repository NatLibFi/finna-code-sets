<?php

namespace NatLibFi\FinnaCodeSets\Model\StudyContents;

use NatLibFi\FinnaCodeSets\Model\AbstractHierarchicalDataObject;
use NatLibFi\FinnaCodeSets\Model\ProxyObjectInterface;

abstract class AbstractStudyContents extends AbstractHierarchicalDataObject implements StudyContentsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        if (($root = $this->getRoot()) instanceof ProxyObjectInterface) {
            $root = $root->getProxiedObject();
        }
        if ($root !== $this) {
            return $root->getUri();
        }
        return parent::getUri();
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeValue(): string
    {
        return (string)($this->data['codeValue'] ?? '');
    }
}
