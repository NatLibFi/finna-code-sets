<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalData;

use NatLibFi\FinnaCodeSets\Exception\UnexpectedValueException;
use NatLibFi\FinnaCodeSets\Model\AbstractHierarchicalDataObject;

abstract class AbstractStudyDataObject extends AbstractHierarchicalDataObject
{
    use EducationalDataObjectTrait;

    /**
     * AbstractStudyDataObject constructor.
     *
     * @param mixed $data
     *     Data from API
     * @param string $apiBaseUrl
     *     Base URL of source API
     * @param string $levelCodeValue
     *     Educational level code value
     *
     * @throws UnexpectedValueException if data is not an array
     */
    public function __construct(mixed $data, string $apiBaseUrl, string $levelCodeValue)
    {
        parent::__construct($data, $apiBaseUrl);
        $this->levelCodeValue = $levelCodeValue;
    }
}
