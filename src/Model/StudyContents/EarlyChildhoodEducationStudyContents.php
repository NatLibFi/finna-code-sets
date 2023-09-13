<?php

namespace NatLibFi\FinnaCodeSets\Model\StudyContents;

use NatLibFi\FinnaCodeSets\Source\Finna\FinnaCodeSetsSourceInterface;

class EarlyChildhoodEducationStudyContents extends AbstractStudyContents
{
    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        return $this->apiBaseUrl
            . FinnaCodeSetsSourceInterface::EARLY_CHILDHOOD_EDUCATION_TRANSVERSAL_COMPETENCES_API_METHOD;
    }
}
