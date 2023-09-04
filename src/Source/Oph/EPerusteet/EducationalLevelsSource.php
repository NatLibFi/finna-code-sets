<?php

namespace NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet;

use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\OphEPerusteetEducationalLevel;
use NatLibFi\FinnaCodeSets\Source\AbstractApiSource;
use NatLibFi\FinnaCodeSets\Source\EducationalLevelsSourceInterface;
use NatLibFi\FinnaCodeSets\Utility\Assert;

class EducationalLevelsSource extends AbstractApiSource implements EducationalLevelsSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEducationalLevels(): array
    {
        $item = $this->cache->getItem(md5(__METHOD__));
        if (!$item->isHit()) {
            $educationalLevels = [];
            $response = $this->apiGet(OphEPerusteetInterface::BASIC_EDUCATION_EDUCATIONAL_LEVELS_API_METHOD);
            foreach ($response as $result) {
                $educationalLevel = new OphEPerusteetEducationalLevel($result, $this->getApiBaseUrl());
                $educationalLevels[$educationalLevel->getId()] = $educationalLevel;
            }
            $this->cache->save($item->set($educationalLevels));
        }
        return $item->get();
    }

    /**
     * {@inheritdoc}
     */
    public function addEquivalentEducationalLevels(EducationalLevelInterface $educationalLevel): void
    {
        switch ($educationalLevel->getCodeValue()) {
            case EducationalLevelInterface::BASIC_EDUCATION:
                $equivalentLevels = $this->getEducationalLevels();
                foreach ($educationalLevel->getChildren() as $childLevel) {
                    $childLevel = Assert::educationalLevel($childLevel);
                    $ophLevelId
                        = EducationalLevelInterface::DVV_KOODISTOT_OPH_PERUSTEET_MAP[$childLevel->getCodeValue()]
                            ?? null;
                    if ($ophLevelId && array_key_exists($ophLevelId, $equivalentLevels)) {
                        $childLevel->addEquivalentLevel($equivalentLevels[$ophLevelId]);
                    }
                }
                break;
        }
    }
}
