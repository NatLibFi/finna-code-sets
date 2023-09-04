<?php

namespace NatLibFi\FinnaCodeSets\Model;

/**
 * Trait for proxy objects.
 *
 * @see ProxyObjectInterface
 */
trait ProxyObjectTrait
{
    protected mixed $proxiedObject;

    /**
     * {@inheritdoc}
     */
    public function getProxiedObject(): mixed
    {
        $proxiedObject = $this->proxiedObject;
        while ($proxiedObject instanceof ProxyObjectInterface) {
            $proxiedObject = $proxiedObject->getProxiedObject();
        }
        return $proxiedObject;
    }
}
