<?php

namespace NatLibFi\FinnaCodeSets\Model;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Exception\UnexpectedValueException;
use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;

/**
 * Interface for data objects.
 */
interface DataObjectInterface
{
    public const LANGUAGE_FINNISH = 'fi';
    public const LANGUAGE_SWEDISH = 'sv';
    public const LANGUAGE_ENGLISH = 'en';

    /**
     * Get ID.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get URI.
     *
     * @return string
     *
     * @throws MissingValueException
     * @throws NotSupportedException
     * @throws UnexpectedValueException
     */
    public function getUri(): string;

    /**
     * Get order.
     *
     * @return int
     */
    public function getOrder(): int;

    /**
     * Get code value.
     *
     * @return string
     *
     * @throws MissingValueException
     */
    public function getCodeValue(): string;

    /**
     * Get preferred label in specified language.
     *
     * @param string $langcode
     *     Language code
     * @param ?string $fallback
     *     Fallback language code (optional, defaults to Finnish).
     *     Set to null for no fallback.
     *
     * @return string
     *
     * @throws MissingValueException
     * @throws ValueNotSetException
     */
    public function getPrefLabel(
        string $langcode,
        ?string $fallback = DataObjectInterface::LANGUAGE_FINNISH
    ): string;

    /**
     * Get preferred labels in array keyed by language code.
     *
     * @return array<string>
     *
     * @throws MissingValueException
     */
    public function getPrefLabels(): array;

    /**
     * Get raw data received from an API call.
     *
     * @return array<mixed>
     */
    public function getRawData(): array;
}
