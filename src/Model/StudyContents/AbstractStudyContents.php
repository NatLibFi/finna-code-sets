<?php

namespace NatLibFi\FinnaCodeSets\Model\StudyContents;

use NatLibFi\FinnaCodeSets\Model\EducationalData\AbstractStudyDataObject;
use NatLibFi\FinnaCodeSets\Utility\Data;

abstract class AbstractStudyContents extends AbstractStudyDataObject implements StudyContentsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        $root = Data::deProxify($this->getRoot());
        if ($root !== $this) {
            return $root->getUri();
        }
        return $this->apiBaseUrl ?? parent::getUri();
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeValue(): string
    {
        return (string)($this->data['codeValue'] ?? '');
    }
}
