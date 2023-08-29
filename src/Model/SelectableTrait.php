<?php

namespace NatLibFi\FinnaCodeSets\Model;

/**
 * Trait for selectable objects.
 *
 * @see SelectableInterface
 */
trait SelectableTrait
{
    protected bool $selectable = true;

    /**
     * {@inheritdoc}
     */
    public function isSelectable(): bool
    {
        return $this->selectable;
    }

    /**
     * {@inheritdoc}
     */
    public function setSelectable(bool $selectable): void
    {
        $this->selectable = $selectable;
    }
}
