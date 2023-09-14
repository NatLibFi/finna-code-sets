<?php

namespace NatLibFi\FinnaCodeSets\Source\Finna;

use NatLibFi\FinnaCodeSets\Source\EducationalSubjectsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\TransversalCompetencesSourceInterface;

interface FinnaCodeSetsSourceInterface extends
    EducationalSubjectsSourceInterface,
    TransversalCompetencesSourceInterface
{
    public const DEFAULT_API_BASE_URL = 'https://api.finna.fi/api/v1/code-sets';

    public const EARLY_CHILDHOOD_EDUCATION_LEARNING_AREAS_API_METHOD
        = '/varhaiskasvatus/osaamisalueet';

    public const EARLY_CHILDHOOD_EDUCATION_TRANSVERSAL_COMPETENCES_API_METHOD
        = '/varhaiskasvatus/laajaalaisetosaamiset';
}
