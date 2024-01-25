<?php

namespace NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet;

use NatLibFi\FinnaCodeSets\Model\EducationalLevel\OphEPerusteetEducationalLevel;
use NatLibFi\FinnaCodeSets\Source\AbstractApiSource;
use NatLibFi\FinnaCodeSets\Source\EducationalLevelsSourceInterface;

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
}
