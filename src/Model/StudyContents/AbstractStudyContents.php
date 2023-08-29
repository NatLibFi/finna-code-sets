<?php

namespace NatLibFi\FinnaCodeSets\Model\StudyContents;

use NatLibFi\FinnaCodeSets\Exception\UnexpectedValueException;
use NatLibFi\FinnaCodeSets\Model\AbstractHierarchicalDataObject;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Utility\Assert;

abstract class AbstractStudyContents extends AbstractHierarchicalDataObject implements StudyContentsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        $root = Assert::proxyObject($this->getRoot())->getProxiedObject();
        if ($root instanceof EducationalLevelInterface || $root instanceof EducationalSubjectInterface) {
            return $root->getUri();
        }
        throw (new UnexpectedValueException('Not an educational level or subject'))->setValue($root);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeValue(): string
    {
        return (string)($this->data['codeValue'] ?? '');
    }
}
