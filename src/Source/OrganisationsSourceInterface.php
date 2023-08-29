<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\Model\Organisation\OrganisationInterface;

interface OrganisationsSourceInterface
{
    /**
     * Get organisations.
     *
     * @return array<OrganisationInterface>
     */
    public function getOrganisations(): array;
}
