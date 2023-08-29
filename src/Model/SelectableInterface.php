<?php

namespace NatLibFi\FinnaCodeSets\Model;

/**
 * Interface for selectable objects.
 *
 * @see SelectableTrait
 */
interface SelectableInterface
{
    /**
     * Get selectability.
     *
     * @return bool
     */
    public function isSelectable(): bool;

    /**
     * Set selectability.
     *
     * @param bool $selectable
     */
    public function setSelectable(bool $selectable): void;
}
