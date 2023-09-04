<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalSubject;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Exception\UnexpectedValueException;
use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;
use NatLibFi\FinnaCodeSets\Model\DataObjectInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalObjectInterface;

interface EducationalSubjectInterface extends DataObjectInterface, HierarchicalObjectInterface
{
    /**
     * Get educational level code value.
     *
     * @return string
     *
     * @throws MissingValueException
     */
    public function getEducationalLevelCodeValue(): string;

    /**
     * Get IDs of educational levels this educational subject is applicable to.
     *
     * @return array<string>
     *     Educational level IDs
     */
    public function getEducationalLevelsApplicableTo(): array;

    /**
     * Is this educational subject applicable to the educational level?
     *
     * @param string $levelCodeValue
     *     Educational level code value
     *
     * @return bool
     */
    public function isApplicableToEducationalLevel(string $levelCodeValue): bool;

    /**
     * Get study contents.
     *
     * @return HierarchicalObjectInterface
     *     Study contents hierarchy with this level as root.
     *
     * @throws UnexpectedValueException
     * @throws ValueNotSetException
     */
    public function getStudyContents(): HierarchicalObjectInterface;

    /**
     * Get study objectives.
     *
     * @return HierarchicalObjectInterface
     *     Study objectives hierarchy with this level as root
     *
     * @throws UnexpectedValueException
     * @throws ValueNotSetException
     */
    public function getStudyObjectives(): HierarchicalObjectInterface;
}
