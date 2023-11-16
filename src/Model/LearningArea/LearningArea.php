<?php

namespace NatLibFi\FinnaCodeSets\Model\LearningArea;

use NatLibFi\FinnaCodeSets\Model\EducationalSubject\AbstractEducationalSubject;
use NatLibFi\FinnaCodeSets\Source\NatLibFi\Finna\FinnaCodeSetsSourceInterface;

class LearningArea extends AbstractEducationalSubject implements LearningAreaInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        return $this->apiBaseUrl
            . FinnaCodeSetsSourceInterface::EARLY_CHILDHOOD_EDUCATION_LEARNING_AREAS_API_METHOD
            . '/' . $this->getId();
    }
}
