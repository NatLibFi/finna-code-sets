<?php

namespace NatLibFi\FinnaCodeSets\Model\VocationalQualification;

use NatLibFi\FinnaCodeSets\Model\EducationalSubject\AbstractVocationalEducationalSubject;
use NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet\OphEPerusteetInterface;

class VocationalQualification extends AbstractVocationalEducationalSubject implements VocationalQualificationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        return $this->apiBaseUrl
            . OphEPerusteetInterface::VOCATIONAL_QUALIFICATION_API_METHOD
            . '/' . $this->getId();
    }
}
