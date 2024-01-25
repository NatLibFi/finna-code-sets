<?php

namespace NatLibFi\FinnaCodeSets\Model;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Exception\NotFoundException;

/**
 * Trait for hierarchical objects.
 *
 * @see HierarchicalObjectInterface
 */
trait HierarchicalObjectTrait
{
    /**
     * Level.
     *
     * @var ?int
     */
    protected ?int $level = null;

    /**
     * Parent.
     *
     * @var ?HierarchicalObjectInterface
     */
    protected ?HierarchicalObjectInterface $parent = null;

    /**
     * Children.
     *
     * @var array<HierarchicalObjectInterface>
     */
    protected array $children = [];

    /**
     * {@inheritdoc}
     */
    public function getHierarchyLevel(): int
    {
        // Only set the value here as the trait method could be overridden.
        if (null === $this->level) {
            $this->level = 1;
        }
        return $this->level;
    }

    /**
     * {@inheritdoc}
     */
    public function setHierarchyLevel(int $level): void
    {
        $this->level = $level;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?HierarchicalObjectInterface
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(?HierarchicalObjectInterface $parent): void
    {
        // Early exit.
        if ($parent === $this->parent) {
            return;
        }

        // If there is a previous parent, remove this object from its children.
        if ($previousParent = $this->getParent()) {
            try {
                $previousParent->removeChild($this->getId());
            } catch (NotFoundException) {
                throw (new MissingValueException("Object missing from parent's children"))->setValue($this);
            }
        }

        // Set the parent.
        $this->parent = $parent;

        if (null === $parent) {
            // An exception will be thrown if the level is fixed.
            $this->setHierarchyLevel(1);
        } else {
            // Verify that this object's hierarchy level is correct in relation to the
            // parent. An exception will be thrown if the level is fixed.
            if ($this->getHierarchyLevel() !== $parent->getHierarchyLevel() + 1) {
                $this->setHierarchyLevel($parent->getHierarchyLevel() + 1);
            }

            // Add this object to the new parent's children.
            $parent->addChild($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren(): bool
    {
        return !empty($this->getChildren());
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChild(string $id): bool
    {
        return isset($this->children[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getChild(string $id): HierarchicalObjectInterface
    {
        if (!isset($this->children[$id])) {
            throw new NotFoundException('Hierarchical object child ' . $id);
        }
        return $this->children[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function hasDescendant(string $id): bool
    {
        return null !== $this->getDescendant($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescendant(string $id): ?HierarchicalObjectInterface
    {
        if ($this->hasChild($id)) {
            return $this->getChild($id);
        }
        foreach ($this->children as $child) {
            if (null !== ($descendant = $child->getDescendant($id))) {
                return $descendant;
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(HierarchicalObjectInterface $child): void
    {
        if ($this->hasChild($child->getId()) && $child === $this->getChild($child->getId())) {
            // Already a child.
            return;
        }

        // Verify that the child's hierarchy level is correct in relation to this
        // object. An exception will be thrown if the level is fixed.
        if ($child->getHierarchyLevel() !== $this->getHierarchyLevel() + 1) {
            $child->setHierarchyLevel($this->getHierarchyLevel() + 1);
        }

        // Add the child.
        $this->children[$child->getId()] = $child;

        // Set the child's parent.
        $child->setParent($this);
    }

    /**
     * {@inheritdoc}
     */
    public function addChildren(array $children): void
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(string $id): void
    {
        unset($this->children[$this->getChild($id)->getId()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot(): HierarchicalObjectInterface
    {
        $root = $this;
        while (null !== ($parent = $root->getParent())) {
            $root = $parent;
        }
        return $root;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot(): bool
    {
        return $this === $this->getRoot();
    }
}
