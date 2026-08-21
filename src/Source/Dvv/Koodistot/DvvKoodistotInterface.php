<?php

namespace NatLibFi\FinnaCodeSets\Source\Dvv\Koodistot;

use NatLibFi\FinnaCodeSets\Source\ConfigurableSourceInterface;
use NatLibFi\FinnaCodeSets\Source\EducationalLevelsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\LicencesSourceInterface;

interface DvvKoodistotInterface extends
    ConfigurableSourceInterface,
    EducationalLevelsSourceInterface,
    LicencesSourceInterface
{
    public const DEFAULT_API_BASE_URL = 'https://koodistot.suomi.fi/codelist-api/api/v1';
}
