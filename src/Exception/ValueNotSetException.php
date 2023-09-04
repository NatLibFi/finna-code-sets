<?php

namespace NatLibFi\FinnaCodeSets\Exception;

/**
 * Thrown when a value that can be set using the library has not been set.
 */
class ValueNotSetException extends \Exception implements ExceptionInterface
{
    use ExceptionTrait;

    protected string $classMessage = 'Value not set: ';
}
