<?php

namespace NatLibFi\FinnaCodeSets\Model\VocationalUnit;

use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Model\StudyContents\StudyContentsInterface;

interface VocationalUnitInterface extends EducationalSubjectInterface, StudyContentsInterface
{
    /**
     * Is this a common unit?
     *
     * @return bool
     */
    public function isCommonUnit(): bool;
}
