<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalData;

trait EducationalDataObjectTrait
{
    /**
     * Educational level code value.
     *
     * @var string
     */
    protected string $levelCodeValue;

    /**
     * {@inheritdoc}
     */
    public function getEducationalLevelCodeValue(): string
    {
        return $this->levelCodeValue;
    }
}
