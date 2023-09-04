<?php

namespace NatLibFi\FinnaCodeSets\Exception;

trait ExceptionTrait
{
    /**
     * Value causing or related to the exception.
     *
     * @var mixed
     */
    protected mixed $value = null;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(($this->classMessage ?? '') . $message, $code, $previous);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }
}
