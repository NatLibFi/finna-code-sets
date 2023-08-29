<?php

namespace NatLibFi\FinnaCodeSets\Exception;

/**
 * Thrown when a value requested from the library was not found.
 */
class NotFoundException extends \Exception implements ExceptionInterface
{
    use ExceptionTrait;

    protected string $classMessage = 'Not found: ';
}
