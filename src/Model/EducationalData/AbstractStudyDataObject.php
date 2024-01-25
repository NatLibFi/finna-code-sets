<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalData;

use NatLibFi\FinnaCodeSets\Model\AbstractHierarchicalDataObject;

abstract class AbstractStudyDataObject extends AbstractHierarchicalDataObject
{
    use EducationalDataObjectTrait;

    /**
     * AbstractStudyDataObject constructor.
     *
     * @param array<mixed> $data
     *     Data from API
     * @param string $apiBaseUrl
     *     Base URL of source API
     * @param string $levelCodeValue
     *     Educational level code value
     */
    public function __construct(array $data, string $apiBaseUrl, string $levelCodeValue)
    {
        parent::__construct($data, $apiBaseUrl);
        $this->levelCodeValue = $levelCodeValue;
    }
}
