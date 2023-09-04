<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\Model\Licence\LicenceInterface;

interface LicencesSourceInterface
{
    /**
     * Get licences.
     *
     * @return array<LicenceInterface>
     */
    public function getLicences(): array;
}
