<?php

namespace NatLibFi\FinnaCodeSets\Exception;

class HierarchyException extends \LogicException implements ExceptionInterface
{
    use ExceptionTrait;

    protected string $classMessage = 'Hierarchy exception: ';
}
