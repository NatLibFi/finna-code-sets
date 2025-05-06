<?php

namespace NatLibFi\FinnaCodeSets\Model\Keyword;

use NatLibFi\FinnaCodeSets\Model\Concept\FintoIndexConcept;

/**
 * Keyword data object.
 *
 * @deprecated
 */
class Keyword extends FintoIndexConcept implements KeywordInterface
{
    public function __construct(mixed $data, string $apiBaseUrl, string $vocid = '', ?string $langcode = null)
    {
        parent::__construct($data, $apiBaseUrl, $vocid, $langcode);
    }
}
