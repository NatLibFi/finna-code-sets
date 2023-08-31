<?php

namespace NatLibFi\FinnaCodeSets;

use NatLibFi\FinnaCodeSets\Source\EducationalLevelsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\EducationalSubjectsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\LicencesSourceInterface;
use NatLibFi\FinnaCodeSets\Source\OrganisationsSourceInterface;
use NatLibFi\FinnaCodeSets\Source\TransversalCompetencesSourceInterface;
use NatLibFi\FinnaCodeSets\Source\VocationalQualificationsSourceInterface;
use NatLibFi\FinnaCodeSets\Utility\EducationalData;
use Psr\Cache\CacheItemPoolInterface;

interface FinnaCodeSetsInterface extends
    EducationalLevelsSourceInterface,
    EducationalSubjectsSourceInterface,
    LicencesSourceInterface,
    OrganisationsSourceInterface,
    TransversalCompetencesSourceInterface,
    VocationalQualificationsSourceInterface
{
    /**
     * Get cache.
     *
     * @return CacheItemPoolInterface
     */
    public function getCache(): CacheItemPoolInterface;

    /**
     * Return educational data utility class instance.
     *
     * @return EducationalData
     */
    public function getEducationalData(): EducationalData;
}
