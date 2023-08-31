<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Model\VocationalQualification\VocationalQualificationInterface;
use NatLibFi\FinnaCodeSets\Model\VocationalUnit\VocationalUnitInterface;

interface VocationalQualificationsSourceInterface extends EducationalSubjectsSourceInterface
{
    /**
     * Get vocational upper secondary qualifications.
     *
     * @param bool $includeUnits
     *     Whether to include qualification units
     *
     * @return array<VocationalQualificationInterface>
     */
    public function getVocationalUpperSecondaryQualifications(bool $includeUnits = true): array;

    /**
     * Get further vocational qualifications.
     *
     * @param bool $includeUnits
     *    Whether to include qualification units
     *
     * @return array<VocationalQualificationInterface>
     */
    public function getFurtherVocationalQualifications(bool $includeUnits = true): array;

    /**
     * Get specialist vocational qualifications.
     *
     * @param bool $includeUnits
     *   Whether to include qualification units
     *
     * @return array<VocationalQualificationInterface>
     */
    public function getSpecialistVocationalQualifications(bool $includeUnits = true): array;

    /**
     * Get vocational common units.
     *
     * @return array<VocationalUnitInterface>
     *
     * @throws MissingValueException
     */
    public function getVocationalCommonUnits(): array;
}
