<?php

namespace NatLibFi\FinnaCodeSets\Model;

use NatLibFi\FinnaCodeSets\Exception\ExceptionInterface;
use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;

/**
 * Abstract base class for data objects.
 */
abstract class AbstractDataObject implements DataObjectInterface
{
    /**
     * Raw data from API response.
     *
     * @var array<mixed>
     */
    protected array $data;

    /**
     * API base URL.
     *
     * @var ?string
     */
    protected ?string $apiBaseUrl;

    /**
     * AbstractDataObject constructor.
     *
     * @param array<mixed> $data
     *     Data from API
     * @param ?string $apiBaseUrl
     *     Base URL of source API
     */
    public function __construct(array $data = [], ?string $apiBaseUrl = null)
    {
        $this->data = $data;
        $this->apiBaseUrl = $apiBaseUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return (string)$this->data['id'];
    }

    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        if (!isset($this->data['uri'])) {
            throw new MissingValueException('URI');
        }
        return (string)$this->data['uri'];
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder(): int
    {
        return (int)($this->data['order'] ?? 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeValue(): string
    {
        if (!isset($this->data['codeValue'])) {
            throw new MissingValueException('Code value');
        }
        return (string)$this->data['codeValue'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefLabel(
        string $langcode,
        ?string $fallback = DataObjectInterface::LANGUAGE_FINNISH
    ): string {
        $labels = $this->getPrefLabels();
        if (isset($labels[$langcode])) {
            return (string)$labels[$langcode];
        }
        $message = 'Preferred label for language ' . $langcode;
        if (null !== $fallback && $langcode !== $fallback) {
            if (isset($labels[$fallback])) {
                return (string)$labels[$fallback];
            }
            $message = ' or fallback language ' . $fallback;
        }
        throw new ValueNotSetException($message);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefLabels(): array
    {
        if (!isset($this->data['prefLabel'])) {
            throw new MissingValueException('Preferred labels');
        }
        return $this->data['prefLabel'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRawData(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        try {
            return $this->getCodeValue();
        } catch (MissingValueException) {
        }
        try {
            return $this->getPrefLabel(DataObjectInterface::LANGUAGE_FINNISH);
        } catch (ExceptionInterface) {
        }
        return $this->getId();
    }
}
