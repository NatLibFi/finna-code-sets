<?php

namespace NatLibFi\FinnaCodeSets\Model;

use NatLibFi\FinnaCodeSets\Exception\HierarchyException;
use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Exception\NotFoundException;

/**
 * Interface for hierarchical objects.
 *
 * @see HierarchicalObjectTrait
 */
interface HierarchicalObjectInterface
{
    /**
     * Get ID.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get hierarchy level.
     *
     * @return int
     */
    public function getHierarchyLevel(): int;

    /**
     * Set hierarchy level.
     *
     * @param int $level
     *
     * @throws HierarchyException if the hierarchy level is fixed
     */
    public function setHierarchyLevel(int $level): void;

    /**
     * Get parent, or null if none.
     *
     * @return ?HierarchicalObjectInterface
     */
    public function getParent(): ?HierarchicalObjectInterface;

    /**
     * Set parent, or null for none.
     *
     * @param ?HierarchicalObjectInterface $parent
     *
     * @throws HierarchyException if this object's hierarchy level is fixed and there
     *     is a level mismatch
     * @throws MissingValueException if there was a previous parent and the object
     *     was missing from its children
     */
    public function setParent(?HierarchicalObjectInterface $parent): void;

    /**
     * Get children.
     *
     * @return array<HierarchicalObjectInterface>
     */
    public function getChildren(): array;

    /**
     * Is there a child with this id?
     *
     * @param string $id
     *     Child ID
     *
     * @return bool
     */
    public function hasChild(string $id): bool;

    /**
     * Get child.
     *
     * @param string $id
     *     Child ID
     *
     * @return HierarchicalObjectInterface
     *
     * @throws NotFoundException if the child does not exist
     */
    public function getChild(string $id): HierarchicalObjectInterface;

    /**
     * Get descendant.
     *
     * @param string $id
     *     Descendant ID
     *
     * @return HierarchicalObjectInterface
     *     Returns null if the descendant does not exist
     */
    public function getDescendant(string $id): ?HierarchicalObjectInterface;

    /**
     * Add child.
     *
     * @param HierarchicalObjectInterface $child
     *
     * @throws HierarchyException if the child's hierarchy level is fixed and there
     *     is a level mismatch
     */
    public function addChild(HierarchicalObjectInterface $child): void;

    /**
     * Add children.
     *
     * @param array<HierarchicalObjectInterface> $children
     *
     * @throws HierarchyException if a child's hierarchy level is fixed and there
     *     is a level mismatch
     */
    public function addChildren(array $children): void;

    /**
     * Remove child.
     *
     * @param string $id
     *
     * @throws NotFoundException if the child does not exist
     */
    public function removeChild(string $id): void;

    /**
     * Get root.
     *
     * @return HierarchicalObjectInterface
     */
    public function getRoot(): HierarchicalObjectInterface;

    /**
     * Is this a root object?
     *
     * @return bool
     */
    public function isRoot(): bool;
}
