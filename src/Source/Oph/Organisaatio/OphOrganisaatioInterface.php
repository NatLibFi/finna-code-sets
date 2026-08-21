<?php

namespace NatLibFi\FinnaCodeSets\Source\Oph\Organisaatio;

use NatLibFi\FinnaCodeSets\Source\ConfigurableSourceInterface;
use NatLibFi\FinnaCodeSets\Source\OrganisationsSourceInterface;

interface OphOrganisaatioInterface extends ConfigurableSourceInterface, OrganisationsSourceInterface
{
    public const DEFAULT_API_BASE_URL = 'https://virkailija.opintopolku.fi/organisaatio-service/rest/organisaatio/v4';
}
