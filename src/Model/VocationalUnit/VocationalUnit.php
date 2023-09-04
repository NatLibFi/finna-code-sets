<?php

namespace NatLibFi\FinnaCodeSets\Model\VocationalUnit;

use NatLibFi\FinnaCodeSets\Model\EducationalSubject\AbstractVocationalEducationalSubject;
use NatLibFi\FinnaCodeSets\Model\SelectableTrait;
use NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet\OphEPerusteetInterface;
use NatLibFi\FinnaCodeSets\Utility\Assert;

class VocationalUnit extends AbstractVocationalEducationalSubject implements VocationalUnitInterface
{
    use SelectableTrait;

    protected bool $isCommonUnit;

    public function __construct(
        array $data,
        string $apiBaseUrl,
        string $levelCodeValue,
        array $educationalLevels = [],
        bool $isCommonUnit = false
    ) {
        parent::__construct($data, $apiBaseUrl, $levelCodeValue, $educationalLevels);
        $this->isCommonUnit = $isCommonUnit;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        $root = Assert::educationalSubject($this->getRoot());
        if ($root !== $this) {
            return $root->getUri();
        }
        return $this->apiBaseUrl
            . OphEPerusteetInterface::VOCATIONAL_COMMON_UNITS_API_METHOD;
    }

    /**
     * {@inheritdoc}
     */
    public function isCommonUnit(): bool
    {
        return $this->isCommonUnit;
    }
}
