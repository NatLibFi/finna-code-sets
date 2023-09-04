<?php

namespace NatLibFi\FinnaCodeSets\Exception;

use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;

class NotSupportedException extends \Exception implements ExceptionInterface
{
    use ExceptionTrait;

    protected string $classMessage = 'Not supported: ';

    public static function forEducationalLevel(
        EducationalLevelInterface|string $educationalLevel
    ): NotSupportedException {
        if ($educationalLevel instanceof EducationalLevelInterface) {
            $codeValue = $educationalLevel->getCodeValue();
        } else {
            $codeValue = $educationalLevel;
        }
        $exception = new NotSupportedException($codeValue);
        $exception->setValue($educationalLevel);
        return $exception;
    }
}
