<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\Licence\LicenceInterface;

interface LicencesSourceInterface extends SourceInterface
{
    /**
     * Get licences.
     *
     * @return array<LicenceInterface>
     *
     * @throws NotSupportedException
     */
    public function getLicences(): array;
}
