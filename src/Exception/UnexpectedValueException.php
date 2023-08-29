<?php

namespace NatLibFi\FinnaCodeSets\Exception;

class UnexpectedValueException extends \LogicException implements ExceptionInterface
{
    use ExceptionTrait;

    protected string $classMessage = 'Unexpected value: ';
}
