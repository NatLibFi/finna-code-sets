<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalLevel;

class DvvKoodistotEducationalLevel extends AbstractEducationalLevel
{
    public function getBroaderCodeId(): ?string
    {
        return $this->data['broaderCode']['id'] ?? null;
    }
}
