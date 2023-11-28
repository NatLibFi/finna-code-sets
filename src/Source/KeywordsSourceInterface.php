<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\Model\Keyword\KeywordInterface;

interface KeywordsSourceInterface
{
    /**
     * Get list of the initial letters of keywords in the given language.
     *
     * @param string $langcode
     *     Language code
     *
     * @return array<string>
     */
    public function getKeywordsIndexLetters(string $langcode): array;

    /**
     * Get list of keywords starting with the given letter in the given language.
     *
     * @param string $langcode
     *     Language code
     * @param string $letter
     *     Letter
     *
     * @return array<KeywordInterface>
     */
    public function getKeywordsIndex(string $langcode, string $letter): array;
}
