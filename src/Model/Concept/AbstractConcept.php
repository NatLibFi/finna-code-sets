<?php

namespace NatLibFi\FinnaCodeSets\Model\Concept;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Exception\UnexpectedValueException;
use NatLibFi\FinnaCodeSets\Model\AbstractHierarchicalDataObject;

abstract class AbstractConcept extends AbstractHierarchicalDataObject implements ConceptInterface
{
    /**
     * Vocabulary ID.
     *
     * @var string
     */
    protected string $vocid;

    /**
     * Langcode used in data request.
     *
     * @var ?string
     */
    protected ?string $langcode;

    /**
     * AbstractConcept constructor.
     *
     * @param mixed $data
     *     Data from API
     * @param string $apiBaseUrl
     *     Base URL of source API
     * @param string $vocid
     *     Vocabulary ID
     * @param ?string $langcode
     *     Langcode used in data request
     *
     * @throws UnexpectedValueException if data is not an array
     */
    public function __construct(
        mixed $data,
        string $apiBaseUrl,
        string $vocid,
        ?string $langcode = null
    ) {
        parent::__construct($data, $apiBaseUrl);
        $this->vocid = $vocid;
        $this->langcode = $langcode;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        $path = parse_url($this->getUri(), PHP_URL_PATH);
        if (!is_string($path)) {
            throw new UnexpectedValueException();
        }
        $parts = explode('/', $path);
        return end($parts);
    }

    /**
     * {@inheritdoc}
     */
    public function getVocabularyId(): string
    {
        return $this->vocid;
    }

    /**
     * Get notation.
     *
     * @return string
     *
     * @throws MissingValueException
     */
    public function getNotation(): string
    {
        if (!isset($this->data['notation'])) {
            throw new MissingValueException('Notation');
        }
        return $this->data['notation'];
    }
}
