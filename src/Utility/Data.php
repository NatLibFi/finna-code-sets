<?php

namespace NatLibFi\FinnaCodeSets\Utility;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;
use NatLibFi\FinnaCodeSets\Model\DataObjectInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalObjectInterface;
use NatLibFi\FinnaCodeSets\Model\ProxyObjectInterface;

/**
 * Utility methods for working with all data objects.
 */
class Data
{
    /**
     * Get data object preferred labels in specified language.
     *
     * @param array<DataObjectInterface> $dataObjects
     *     Data objects
     * @param string $langcode
     *     Language code
     * @param ?string $fallback
     *     Fallback language code (optional, defaults to Finnish).
     *     Set to null for no fallback.
     * @param bool $deduplicate
     *     Deduplicate labels (optional, defaults to true)
     *
     * @return array<string>
     *
     * @throws MissingValueException
     * @throws ValueNotSetException
     */
    public static function getPrefLabels(
        array $dataObjects,
        string $langcode,
        ?string $fallback = DataObjectInterface::LANGUAGE_FINNISH,
        bool $deduplicate = true
    ): array {
        $labels = [];
        foreach ($dataObjects as $dataObject) {
            $label = $dataObject->getPrefLabel($langcode, $fallback);
            if (!$deduplicate || !in_array($label, $labels)) {
                $labels[] = $label;
            }
        }
        return $labels;
    }

    /**
     * Get children of the specified parent from the provided objects.
     *
     * @param HierarchicalObjectInterface $parent
     *     Parent hierarchical object
     * @param array<HierarchicalObjectInterface> $hierarchicalObjects
     *     Hierarchical objects
     *
     * @return array<HierarchicalObjectInterface>
     */
    public static function getChildren(
        HierarchicalObjectInterface $parent,
        array $hierarchicalObjects
    ): array {
        return array_filter(
            $hierarchicalObjects,
            function ($hierarchicalObject) use ($parent) {
                return $parent->hasChild($hierarchicalObject->getId());
            }
        );
    }

    /**
     * Get descendants of the specified parent from the provided objects.
     *
     * @param HierarchicalObjectInterface $parent
     *     Parent hierarchical object
     * @param array<HierarchicalObjectInterface> $hierarchicalObjects
     *     Hierarchical objects
     *
     * @return array<HierarchicalObjectInterface>
     */
    public static function getDescendants(
        HierarchicalObjectInterface $parent,
        array $hierarchicalObjects
    ): array {
        return array_filter(
            $hierarchicalObjects,
            function ($hierarchicalObject) use ($parent) {
                return $parent->hasDescendant($hierarchicalObject->getId());
            }
        );
    }

    /**
     * Conditionally deproxify the object.
     *
     * @param mixed $object Object
     *
     * @return mixed
     */
    public static function deProxify(mixed $object): mixed
    {
        if ($object instanceof ProxyObjectInterface) {
            return $object->getProxiedObject();
        }
        return $object;
    }
}
