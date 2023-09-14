<?php

namespace NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet;

use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\StudyContents\BasicEducationStudyContents;
use NatLibFi\FinnaCodeSets\Model\StudyContents\StudyContentsInterface;
use NatLibFi\FinnaCodeSets\Model\StudyContents\UpperSecondarySchoolStudyContents;
use NatLibFi\FinnaCodeSets\Source\AbstractApiSource;
use NatLibFi\FinnaCodeSets\Source\TransversalCompetencesSourceInterface;

class TransversalCompetencesSource extends AbstractApiSource implements TransversalCompetencesSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTransversalCompetences(string $levelCodeValue): array
    {
        switch ($levelCodeValue) {
            case EducationalLevelInterface::BASIC_EDUCATION:
                $method = OphEPerusteetInterface::BASIC_EDUCATION_TRANSVERSAL_COMPETENCES_API_METHOD;
                $class = BasicEducationStudyContents::class;
                break;

            case EducationalLevelInterface::UPPER_SECONDARY_SCHOOL:
                $method = OphEPerusteetInterface::UPPER_SECONDARY_SCHOOL_TRANSVERSAL_COMPETENCES_API_METHOD;
                $class = UpperSecondarySchoolStudyContents::class;
                break;

            default:
                throw NotSupportedException::forEducationalLevel($levelCodeValue);
        }
        $competences = [];
        $response = $this->apiGet($method);
        foreach ($response as $result) {
            $studyContents = new $class(
                $result,
                $this->getApiBaseUrl() . $method,
                $levelCodeValue
            );
            $competences[$studyContents->getId()] = $studyContents;
        }
        return $competences;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransversalCompetenceByUrl(string $url): StudyContentsInterface
    {
        $this->assertApiBaseUrl($url);
        if ($this->isBasicEducationTransversalCompetenceUrl($url)) {
            $method = OphEPerusteetInterface::BASIC_EDUCATION_TRANSVERSAL_COMPETENCES_API_METHOD;
            $class = BasicEducationStudyContents::class;
            $levelCodeValue = EducationalLevelInterface::BASIC_EDUCATION;
        } elseif ($this->isUpperSecondarySchoolTransversalCompetenceUrl($url)) {
            $method = OphEPerusteetInterface::UPPER_SECONDARY_SCHOOL_TRANSVERSAL_COMPETENCES_API_METHOD;
            $class = UpperSecondarySchoolStudyContents::class;
            $levelCodeValue = EducationalLevelInterface::UPPER_SECONDARY_SCHOOL;
        } else {
            throw new NotSupportedException('API URL ' . $url);
        }
        return new $class(
            $this->apiGet(substr($url, strlen($this->getApiBaseUrl()))),
            $this->getApiBaseUrl() . $method,
            $levelCodeValue
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedTransversalCompetenceUrl(string $url): bool
    {
        return str_starts_with($url, $this->getApiBaseUrl())
            && ($this->isBasicEducationTransversalCompetenceUrl($url)
            || $this->isUpperSecondarySchoolTransversalCompetenceUrl($url));
    }

    /**
     * Is the URL a basic education transversal competences URL?
     *
     * @param string $url
     *
     * @return bool
     */
    protected function isBasicEducationTransversalCompetenceUrl(string $url): bool
    {
        return str_contains($url, OphEPerusteetInterface::BASIC_EDUCATION_TRANSVERSAL_COMPETENCES_API_METHOD);
    }

    /**
     * Is the URL an upper secondary school transversal competences URL?
     *
     * @param string $url
     *
     * @return bool
     */
    protected function isUpperSecondarySchoolTransversalCompetenceUrl(string $url): bool
    {
        return str_contains($url, OphEPerusteetInterface::UPPER_SECONDARY_SCHOOL_TRANSVERSAL_COMPETENCES_API_METHOD);
    }
}
