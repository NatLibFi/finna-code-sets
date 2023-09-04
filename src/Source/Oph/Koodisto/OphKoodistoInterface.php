<?php

namespace NatLibFi\FinnaCodeSets\Source\Oph\Koodisto;

use NatLibFi\FinnaCodeSets\Source\EducationalSubjectsSourceInterface;

interface OphKoodistoInterface extends EducationalSubjectsSourceInterface
{
    public const DEFAULT_API_BASE_URL = 'https://virkailija.opintopolku.fi/koodisto-service/rest/json';
}
