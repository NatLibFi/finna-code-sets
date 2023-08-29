<?php

namespace NatLibFi\FinnaCodeSets\Exception;

/**
 * Thrown when a value expected by the library to exist is missing.
 */
class MissingValueException extends \Exception implements ExceptionInterface
{
    use ExceptionTrait;

    protected string $classMessage = 'Missing value: ';
}
