<?php

namespace NatLibFi\FinnaCodeSets\Model;

/**
 * A hierarchical proxy data object.
 *
 * - Can be placed in separate hierarchy from the proxied object.
 * - Has a separate automatically generated ID based on hierarchy position.
 *   (ID will change if hierarchy position changes.)
 * - Has a selectable property.
 */
class HierarchicalProxyDataObject implements
    DataObjectInterface,
    HierarchicalObjectInterface,
    ProxyObjectInterface,
    SelectableInterface
{
    use HierarchicalObjectTrait {
        setParent as traitSetParent;
    }
    use ProxiedDataObjectTrait;
    use SelectableTrait;

    protected string $id;

    /**
     * HierarchicalProxyDataObject constructor.
     *
     * @param DataObjectInterface $proxiedObject
     * @param bool $selectable
     */
    public function __construct(
        DataObjectInterface $proxiedObject,
        bool $selectable = true
    ) {
        $this->proxiedObject = $proxiedObject;
        $this->selectable = $selectable;
        $this->updateId();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(?HierarchicalObjectInterface $parent): void
    {
        $this->traitSetParent($parent);
        $this->updateId();
    }

    /**
     * Update automatically generated ID.
     *
     * @return void
     */
    public function updateId(): void
    {
        $this->id = 'proxy__' . md5(
            $this->proxiedObject->getId()
            . '__'
            . ($this->parent ? $this->parent->getId() : 'root')
        );

        // A changed ID will also change child hierarchical proxy data object IDs.
        foreach ($this->getChildren() as $child) {
            if ($child instanceof HierarchicalProxyDataObject) {
                $child->updateId();
            }
        }
    }
}
