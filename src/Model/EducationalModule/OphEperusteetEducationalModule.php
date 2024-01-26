<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalModule;

use NatLibFi\FinnaCodeSets\Model\EducationalSubject\OphEperusteetEducationalSubject;
use NatLibFi\FinnaCodeSets\Model\HierarchicalObjectInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalProxyDataObject;
use NatLibFi\FinnaCodeSets\Model\StudyContents\UpperSecondarySchoolStudyContents;
use NatLibFi\FinnaCodeSets\Model\StudyObjective\UpperSecondarySchoolStudyObjective;
use NatLibFi\FinnaCodeSets\Utility\Assert;

class OphEperusteetEducationalModule extends OphEperusteetEducationalSubject implements EducationalModuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        return Assert::educationalSubject($this->getRoot())->getUri();
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeValue(): string
    {
        return (string)($this->data['koodi']['arvo'] ?? '');
    }

    /**
     * {@inheritdoc}
     */
    public function getStudyContents(): HierarchicalObjectInterface
    {
        if (null === $this->studyContents) {
            $proxy = new HierarchicalProxyDataObject($this, false);
            foreach ($this->data['sisallot'] as $targetsData) {
                foreach ($targetsData['sisallot'] as $contentsData) {
                    $proxy->addChild(new UpperSecondarySchoolStudyContents(
                        $contentsData,
                        $this->apiBaseUrl,
                        $this->levelCodeValue
                    ));
                }
            }
            $this->studyContents = $proxy;
        }
        return $this->studyContents;
    }

    /**
     * {@inheritdoc}
     */
    public function getStudyObjectives(): HierarchicalObjectInterface
    {
        if (null === $this->studyObjectives) {
            $proxy = new HierarchicalProxyDataObject($this, false);
            foreach ($this->data['tavoitteet']['tavoitteet'] as $objectiveData) {
                $proxy->addChild(new UpperSecondarySchoolStudyObjective(
                    $objectiveData,
                    $this->apiBaseUrl,
                    $this->levelCodeValue
                ));
            }
            $this->studyObjectives = $proxy;
        }
        return $this->studyObjectives;
    }
}
