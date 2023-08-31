<?php

namespace NatLibFi\FinnaCodeSets\Source;

use NatLibFi\FinnaCodeSets\Exception\NotFoundException;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;

interface EducationalSubjectsSourceInterface
{
    /**
     * Get educational subjects.
     *
     * @param string $levelCodeValue
     *     Educational level code value
     *
     * @return array<EducationalSubjectInterface>
     *
     * @throws NotFoundException
     * @throws NotSupportedException
     */
    public function getEducationalSubjects(string $levelCodeValue): array;

    /**
     * Get educational subject by URL.
     *
     * @param string $url
     *     Educational subject API URL
     *
     * @return EducationalSubjectInterface
     *
     * @throws NotFoundException
     * @throws NotSupportedException
     */
    public function getEducationalSubjectByUrl(string $url): EducationalSubjectInterface;

    /**
     * Is this an educational subject URL supported by this source?
     *
     * @param string $url
     *
     * @return bool
     */
    public function isSupportedEducationalSubjectUrl(string $url): bool;
}
