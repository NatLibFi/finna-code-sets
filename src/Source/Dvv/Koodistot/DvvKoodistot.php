<?php

namespace NatLibFi\FinnaCodeSets\Source\Dvv\Koodistot;

use NatLibFi\FinnaCodeSets\Model\EducationalLevel\DvvKoodistotEducationalLevel;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\Licence\Licence;
use NatLibFi\FinnaCodeSets\Source\AbstractApi;

class DvvKoodistot extends AbstractApi implements DvvKoodistotInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLicences(): array
    {
        $response = $this->apiGet('/coderegistries/edtech/codeschemes/Licence/codes');
        $licences = [];
        foreach ($response['results'] as $result) {
            $licences[$result['id']] = new Licence($result);
        }
        return $licences;
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalLevels(): array
    {
        $response = $this->apiGet('/coderegistries/edtech/codeschemes/Koulutusaste/codes');
        // Create objects from response.
        $educationalLevels = [];
        foreach ($response['results'] as $result) {
            $educationalLevel = new DvvKoodistotEducationalLevel($result);
            $educationalLevels[$educationalLevel->getId()] = $educationalLevel;
        }
        // Build object hierarchy.
        foreach ($educationalLevels as $id => $educationalLevel) {
            $broaderCodeId = $educationalLevel->getBroaderCodeId();
            if ($broaderCodeId && isset($educationalLevels[$broaderCodeId])) {
                $educationalLevels[$broaderCodeId]->addChild($educationalLevel);
                // Leave only the first hierarchy level in the array.
                unset($educationalLevels[$id]);
            }
        }
        return $educationalLevels;
    }

    /**
     * {@inheritdoc}
     */
    public function addEquivalentEducationalLevels(EducationalLevelInterface $educationalLevel): void
    {
        // @todo Implement addEquivalentEducationalLevels() method.
    }
}
