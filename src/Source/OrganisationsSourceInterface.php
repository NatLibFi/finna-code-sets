<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\Organisation\OrganisationInterface;

interface OrganisationsSourceInterface extends SourceInterface
{
    /**
     * Get organisations.
     *
     * @return array<OrganisationInterface>
     *
     * @throws MissingValueException
     * @throws NotSupportedException
     */
    public function getOrganisations(): array;

    /**
     * Get organisation.
     *
     * @param string $id
     *     Organisation ID
     *
     * @return OrganisationInterface|null
     */
    public function getOrganisation(string $id): ?OrganisationInterface;
}
