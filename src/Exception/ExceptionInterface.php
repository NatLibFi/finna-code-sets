<?php

namespace NatLibFi\FinnaCodeSets\Exception;

interface ExceptionInterface extends \Throwable
{
    /**
     * Get value causing or related to the exception.
     *
     * @return mixed
     */
    public function getValue(): mixed;

    /**
     * Set value causing or related to the exception.
     *
     * @param mixed $value Value
     *
     * @return ExceptionInterface instance for method chaining
     */
    public function setValue(mixed $value): ExceptionInterface;
}
