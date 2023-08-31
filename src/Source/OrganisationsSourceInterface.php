<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Model\Organisation\OrganisationInterface;

interface OrganisationsSourceInterface
{
    /**
     * Get organisations.
     *
     * @return array<OrganisationInterface>
     *
     * @throws MissingValueException
     */
    public function getOrganisations(): array;
}
