<?php

namespace NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet;

use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\StudyContents\BasicEducationStudyContents;
use NatLibFi\FinnaCodeSets\Model\StudyContents\UpperSecondarySchoolStudyContents;
use NatLibFi\FinnaCodeSets\Source\AbstractSource;
use NatLibFi\FinnaCodeSets\Source\TransversalCompetencesSourceInterface;

class TransversalCompetencesSource extends AbstractSource implements TransversalCompetencesSourceInterface
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
            $studyContents = new $class($result);
            $competences[$studyContents->getId()] = $studyContents;
        }
        return $competences;
    }
}
