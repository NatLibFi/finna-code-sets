<?php

namespace NatLibFi\FinnaCodeSets\Model\Organisation;

interface FinnaAdminOrganisationInterface extends OrganisationInterface
{
    /**
     * Get feedback email.
     *
     * @return string|null
     */
    public function getFeedbackEmail(): ?string;
}
