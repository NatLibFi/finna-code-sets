<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalSubject;

use NatLibFi\FinnaCodeSets\Exception\UnexpectedValueException;
use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;
use NatLibFi\FinnaCodeSets\Model\EducationalData\EducationalDataObjectInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalObjectInterface;

interface EducationalSubjectInterface extends EducationalDataObjectInterface
{
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
