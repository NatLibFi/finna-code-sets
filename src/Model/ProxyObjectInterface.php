<?php

namespace NatLibFi\FinnaCodeSets\Model;

/**
 * Interface for proxy objects.
 *
 * @see ProxyObjectTrait
 */
interface ProxyObjectInterface
{
    /**
     * Get proxied object.
     *
     * @return mixed
     */
    public function getProxiedObject(): mixed;
}
