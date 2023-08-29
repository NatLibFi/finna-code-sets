<?php

namespace NatLibFi\FinnaCodeSets\Source\Oph\Organisaatio;

use NatLibFi\FinnaCodeSets\Source\OrganisationsSourceInterface;

interface OphOrganisaatioInterface extends OrganisationsSourceInterface
{
    public const DEFAULT_API_BASE_URL = 'https://virkailija.opintopolku.fi/organisaatio-service/rest/organisaatio/v4';
}
