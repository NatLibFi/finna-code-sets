<?php

namespace NatLibFi\FinnaCodeSets\Source\NatLibFi\Finto;

use NatLibFi\FinnaCodeSets\Source\ConfigurableSourceInterface;
use NatLibFi\FinnaCodeSets\Source\KeywordsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\VocabularySourceInterface;

interface FintoSourceInterface extends
    ConfigurableSourceInterface,
    KeywordsSourceInterface,
    VocabularySourceInterface
{
    public const DEFAULT_API_BASE_URL = 'https://api.finto.fi/rest/v1';

    public const VOCABULARY_FINTO_YSO = 'yso';
    public const VOCABULARY_FINTO_YSO_PLACES = 'yso-paikat';
    public const VOCABULARY_FINTO_YSO_TIME = 'yso-aika';
}
