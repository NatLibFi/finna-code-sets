<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\Keyword\KeywordInterface;

/**
 * Keywords source interface.
 *
 * @deprecated Replaced by VocabularySourceInterface.
 */
interface KeywordsSourceInterface extends SourceInterface
{
    /**
     * Get list of the initial letters of keywords in the given language.
     *
     * @param string $langcode
     *     Language code
     *
     * @return array<string>
     *
     * @throws NotSupportedException
     *
     * @deprecated Use VocabularySourceInterface::getVocabularyIndexLetters() instead.
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
     *
     * @throws NotSupportedException
     *
     * @deprecated Use VocabularySourceInterface::getVocabularyIndex() instead.
     */
    public function getKeywordsIndex(string $langcode, string $letter): array;
}
