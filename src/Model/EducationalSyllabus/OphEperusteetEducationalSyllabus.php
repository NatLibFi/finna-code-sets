<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalSyllabus;

use NatLibFi\FinnaCodeSets\Model\EducationalSubject\OphEperusteetEducationalSubject;
use NatLibFi\FinnaCodeSets\Utility\Assert;

class OphEperusteetEducationalSyllabus extends OphEperusteetEducationalSubject implements EducationalSyllabusInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        return Assert::educationalSubject($this->getRoot())->getUri();
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeValue(): string
    {
        return (string)($this->data['koodiArvo'] ?? '');
    }
}
