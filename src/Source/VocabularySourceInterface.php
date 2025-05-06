<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\Concept\ConceptInterface;

interface VocabularySourceInterface
{
    /**
     * Get list of top concepts of the vocabulary.
     *
     * @param string $vocid
     *     Vocabulary ID
     * @param ?string $langcode
     *     Language code
     *
     * @return array<ConceptInterface>
     *
     * @throws NotSupportedException
     */
    public function getVocabularyTopConcepts(string $vocid, ?string $langcode = null): array;

    /**
     * Get data of a specific concept.
     *
     * @param string $vocid
     *     Vocabulary ID
     * @param string $uri
     *     URI of the desired concept
     * @param ?string $langcode
     *     Language code
     *
     * @return ConceptInterface
     *
     * @throws NotSupportedException
     */
    public function getVocabularyConceptData(string $vocid, string $uri, ?string $langcode = null): ConceptInterface;

    /**
     * Get children of a specific concept.
     *
     * @param string $vocid
     *     Vocabulary ID
     * @param string $uri
     *     URI of the desired concept
     * @param ?string $langcode
     *     Language code
     *
     * @return array<ConceptInterface>
     *
     * @throws NotSupportedException
     */
    public function getVocabularyConceptChildren(string $vocid, string $uri, ?string $langcode = null): array;

    /**
     * Get list of the initial letters of concepts in the given language.
     *
     * @param string $vocid
     *     Vocabulary ID
     * @param ?string $langcode
     *     Language code
     *
     * @return array<string>
     *
     * @throws NotSupportedException
     */
    public function getVocabularyIndexLetters(string $vocid, ?string $langcode = null): array;

    /**
     * Get list of concepts starting with the given letter in the given language.
     *
     * @param string $vocid
     *     Vocabulary ID
     * @param string $letter
     * *     Letter
     * @param ?string $langcode
     *     Language code
     *
     * @return array<ConceptInterface>
     *
     * @throws NotSupportedException
     */
    public function getVocabularyIndex(string $vocid, string $letter, ?string $langcode = null): array;
}
