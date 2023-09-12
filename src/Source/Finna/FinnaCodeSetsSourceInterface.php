<?php

namespace NatLibFi\FinnaCodeSets\Source\Finna;

use NatLibFi\FinnaCodeSets\Source\EducationalSubjectsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\TransversalCompetencesSourceInterface;

interface FinnaCodeSetsSourceInterface extends
    EducationalSubjectsSourceInterface,
    TransversalCompetencesSourceInterface
{
    public const DEFAULT_API_BASE_URL = 'https://github.com/NatLibFi/finna-code-sets/api';

    public const EARLY_CHILDHOOD_EDUCATION_LEARNING_AREAS_API_METHOD
        = '/varhaisopetus/osaamisalueet';

    public const EARLY_CHILDHOOD_EDUCATION_TRANSVERSAL_COMPETENCES_API_METHOD
        = '/varhaisopetus/laajaalaisetosaamiset';
}
