<?php

namespace NatLibFi\FinnaCodeSets\Source\Oph\Koodisto;

use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\OphKoodistoEducationalSubject;
use NatLibFi\FinnaCodeSets\Source\AbstractApi;

class OphKoodisto extends AbstractApi implements OphKoodistoInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjects(string $levelCodeValue): array
    {
        if (EducationalLevelInterface::HIGHER_EDUCATION === $levelCodeValue) {
            return $this->processApiResponse(
                $this->apiGet('/tieteenala/koodi'),
                $levelCodeValue
            );
        }
        throw NotSupportedException::forEducationalLevel($levelCodeValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjectByUrl(string $url): EducationalSubjectInterface
    {
        // @todo Implement getEducationalSubjectByUrl() method.
        throw new NotSupportedException($url);
    }

    /**
     * Process API response.
     *
     * @param array<mixed> $response
     *
     * @return array<OphKoodistoEducationalSubject>
     */
    protected function processApiResponse(array $response, string $levelCodeValue): array
    {
        $educationalSubjects = [];
        foreach ($response as $result) {
            $educationalSubject = new OphKoodistoEducationalSubject($result, $this->apiBaseUrl, $levelCodeValue);
            $educationalSubjects[$educationalSubject->getId()] = $educationalSubject;
        }
        // @todo Hierarchy
        return $educationalSubjects;
    }
}
