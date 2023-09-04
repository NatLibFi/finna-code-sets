<?php

namespace NatLibFi\FinnaCodeSets\Model;

/**
 * Trait for objects proxying a data object.
 *
 * @see DataObjectInterface
 * @see ProxyObjectInterface
 */
trait ProxiedDataObjectTrait
{
    use ProxyObjectTrait;

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->proxiedObject->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        return $this->proxiedObject->getUri();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder(): int
    {
        return $this->proxiedObject->getOrder();
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeValue(): string
    {
        return $this->proxiedObject->getCodeValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefLabel(
        string $langcode,
        ?string $fallback = DataObjectInterface::LANGUAGE_FINNISH
    ): string {
        return $this->proxiedObject->getPrefLabel($langcode, $fallback);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefLabels(): array
    {
        return $this->proxiedObject->getPrefLabels();
    }

    /**
     * {@inheritdoc}
     */
    public function getRawData(): array
    {
        return $this->proxiedObject->getRawData();
    }
}
