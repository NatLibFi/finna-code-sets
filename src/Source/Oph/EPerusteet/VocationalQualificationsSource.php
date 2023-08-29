<?php

namespace NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet;

use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Exception\ValueNotSetException;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Model\VocationalQualification\VocationalQualification;
use NatLibFi\FinnaCodeSets\Model\VocationalQualification\VocationalQualificationInterface;
use NatLibFi\FinnaCodeSets\Model\VocationalUnit\VocationalUnit;
use NatLibFi\FinnaCodeSets\Source\AbstractApi;
use NatLibFi\FinnaCodeSets\Source\VocationalQualificationsSourceInterface;
use Psr\Http\Client\ClientExceptionInterface;

class VocationalQualificationsSource extends AbstractApi implements VocationalQualificationsSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getVocationalUpperSecondaryQualifications(bool $includeUnits = true): array
    {
        return $this->getQualifications(
            __METHOD__,
            OphEPerusteetInterface::VOCATIONAL_UPPER_SECONDARY_QUALIFICATIONS_API_PARAMETERS,
            $includeUnits
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFurtherVocationalQualifications(bool $includeUnits = true): array
    {
        return $this->getQualifications(
            __METHOD__,
            OphEPerusteetInterface::FURTHER_VOCATIONAL_QUALIFICATIONS_API_PARAMETERS,
            $includeUnits
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecialistVocationalQualifications(bool $includeUnits = true): array
    {
        return $this->getQualifications(
            __METHOD__,
            OphEPerusteetInterface::SPECIALIST_VOCATIONAL_QUALIFICATIONS_API_PARAMETERS,
            $includeUnits
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getVocationalCommonUnits(): array
    {
        $cacheKey = __METHOD__;
        if (!$this->cache->exists($cacheKey)) {
            $units = $this->processApiResponse(
                $this->apiGet(OphEPerusteet::VOCATIONAL_COMMON_UNITS_API_METHOD),
                true
            );
            $this->cache->set($cacheKey, $units);
        }
        return $this->cache->get($cacheKey);
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjects(string $levelCodeValue): array
    {
        if ($levelCodeValue !== EducationalLevelInterface::VOCATIONAL_EDUCATION) {
            throw NotSupportedException::forEducationalLevel($levelCodeValue);
        }
        $cacheKey = __METHOD__;
        if (!$this->cache->exists($cacheKey)) {
            $this->cache->set(
                $cacheKey,
                array_merge(
                    $this->getVocationalUpperSecondaryQualifications(),
                    $this->getFurtherVocationalQualifications(),
                    $this->getSpecialistVocationalQualifications(),
                    $this->getVocationalCommonUnits()
                )
            );
        }
        return $this->cache->get($cacheKey);
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjectByUrl(string $url): EducationalSubjectInterface
    {
        $this->assertBaseUrl($url);
        if (
            !$this->isVocationalQualificationUrl($url)
            && !$this->isVocationalCommonUnitsUrl($url)
        ) {
            throw new NotSupportedException($url);
        }
        $response = $this->apiGet(substr($url, strlen($this->apiBaseUrl)));
        $qualification = new VocationalQualification(
            $response,
            $this->apiBaseUrl,
            EducationalLevelInterface::VOCATIONAL_EDUCATION
        );
        $this->setVocationalQualificationUnits($qualification);
        return $qualification;
    }

    /**
     * Get qualifications from cache or API.
     *
     * @param string $cacheKey
     * @param array<mixed> $params
     * @param bool $includeUnits
     *
     * @return array<VocationalQualificationInterface>
     *
     * @throws ClientExceptionInterface
     * @throws ValueNotSetException
     */
    protected function getQualifications(string $cacheKey, array $params, bool $includeUnits): array
    {
        if (!$this->cache->exists($cacheKey)) {
            $params = array_merge(
                OphEPerusteetInterface::VOCATIONAL_QUALIFICATIONS_API_PARAMETERS,
                $params
            );
            $qualifications = [];
            while (!isset($response) || $params['sivu'] < (int)$response['sivuja']) {
                $response = $this->apiGet(
                    OphEPerusteetInterface::VOCATIONAL_QUALIFICATIONS_API_METHOD,
                    $params
                );
                foreach ($response['data'] as $result) {
                    $qualification = new VocationalQualification(
                        $result,
                        $this->apiBaseUrl,
                        EducationalLevelInterface::VOCATIONAL_EDUCATION
                    );
                    if ($includeUnits) {
                        $this->setVocationalQualificationUnits($qualification);
                    }
                    $qualifications[$qualification->getId()] = $qualification;
                }
                $params['sivu'] += 1;
            }
            $this->cache->set($cacheKey, $qualifications);
        }
        return $this->cache->get($cacheKey);
    }

    /**
     * Set vocational qualification units.
     *
     * @param VocationalQualification $qualification
     *
     * @return void
     *
     * @throws ClientExceptionInterface
     * @throws ValueNotSetException
     */
    protected function setVocationalQualificationUnits(VocationalQualification $qualification): void
    {
        $units = $this->processApiResponse(
            $this->apiGet(
                OphEPerusteetInterface::VOCATIONAL_QUALIFICATION_API_METHOD
                    . '/' . $qualification->getId()
            ),
            $this->isVocationalCommonUnitsUrl($qualification->getUri())
        );
        $qualification->addChildren($units);
    }

    /**
     * Process API response.
     *
     * @param array<mixed> $response
     * @param bool $commonUnits
     *
     * @return array<VocationalUnit>
     */
    protected function processApiResponse(array $response, bool $commonUnits = false): array
    {
        $units = [];
        foreach ($response['tutkinnonOsat'] as $data) {
            $unit = new VocationalUnit(
                $data,
                $this->apiBaseUrl,
                EducationalLevelInterface::VOCATIONAL_EDUCATION,
                [],
                $commonUnits
            );
            $unit->setSelectable(!$commonUnits);
            foreach ($data['osaAlueet'] as $childData) {
                $unit->addChild(
                    new VocationalUnit(
                        $childData,
                        $this->apiBaseUrl,
                        EducationalLevelInterface::VOCATIONAL_EDUCATION,
                        [],
                        $commonUnits
                    )
                );
            }
            $units[] = $unit;
        }
        return $units;
    }

    /**
     * Is the URL a vocational qualification URL?
     *
     * @param string $url
     *
     * @return bool
     */
    protected function isVocationalQualificationUrl(string $url): bool
    {
        return str_contains($url, OphEPerusteetInterface::VOCATIONAL_QUALIFICATION_API_METHOD);
    }

    /**
     * Is the URL a vocational common units URL?
     *
     * @param string $url
     *
     * @return bool
     */
    protected function isVocationalCommonUnitsUrl(string $url): bool
    {
        return str_contains($url, OphEPerusteetInterface::VOCATIONAL_COMMON_UNITS_API_METHOD);
    }
}