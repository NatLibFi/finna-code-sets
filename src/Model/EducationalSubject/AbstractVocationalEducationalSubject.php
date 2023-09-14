<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalSubject;

use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalObjectInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalProxyDataObject;

abstract class AbstractVocationalEducationalSubject extends AbstractEducationalSubject
{
    /**
     * {@inheritdoc}
     */
    public function getPrefLabels(): array
    {
        return array_filter(
            $this->data['nimi'] ?? $this->data,
            function ($value, $key) {
                return !str_starts_with($key, '_');
            },
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicableToEducationalLevel(string $levelCodeValue): bool
    {
        return $levelCodeValue === EducationalLevelInterface::VOCATIONAL_EDUCATION;
    }

    /**
     * {@inheritdoc}
     */
    public function getStudyContents(): HierarchicalObjectInterface
    {
        if (null === $this->studyContents) {
            $proxyRoot = new HierarchicalProxyDataObject($this, false);
            $proxyRoot->addChildren($this->getChildren());
            $this->studyContents = $proxyRoot;
        }
        return $this->studyContents;
    }

    /**
     * {@inheritdoc}
     */
    public function getStudyObjectives(): HierarchicalObjectInterface
    {
        if (null === $this->studyContents) {
            $this->studyContents = new HierarchicalProxyDataObject($this, false);
        }
        return $this->studyContents;
    }
}
