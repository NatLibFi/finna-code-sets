<?php

namespace NatLibFi\FinnaCodeSets\Model\VocationalUnit;

use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;

interface VocationalUnitInterface extends EducationalSubjectInterface
{
    /**
     * Is this a common unit?
     *
     * @return bool
     */
    public function isCommonUnit(): bool;
}
