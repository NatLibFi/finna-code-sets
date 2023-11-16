<?php

namespace NatLibFi\FinnaCodeSets\Source\NatLibFi\Finto;

use NatLibFi\FinnaCodeSets\Source\KeywordsSourceInterface;

interface FintoSourceInterface extends KeywordsSourceInterface
{
    public const DEFAULT_API_BASE_URL = 'https://api.finto.fi/rest/v1';
}
