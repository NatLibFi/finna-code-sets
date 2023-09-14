<?php

namespace NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet;

use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\OphEperusteetEducationalSubject;
use NatLibFi\FinnaCodeSets\Source\AbstractApiSource;
use NatLibFi\FinnaCodeSets\Source\EducationalSubjectsSourceInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;

class EducationalSubjectsSource extends AbstractApiSource implements EducationalSubjectsSourceInterface
{
    /**
     * Educational levels.
     *
     * @var array<EducationalLevelInterface>
     */
    protected array $educationalLevels;

    public function __construct(
        ClientInterface $httpClient,
        CacheItemPoolInterface $cache,
        string $apiBaseUrl,
        EducationalLevelsSource $educationalLevelsSource
    ) {
        parent::__construct($httpClient, $cache, $apiBaseUrl);
        $this->educationalLevels = $educationalLevelsSource->getEducationalLevels();
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjects(string $levelCodeValue): array
    {
        $item = $this->cache->getItem(md5(__METHOD__ . '|' . $levelCodeValue));
        if (!$item->isHit()) {
            switch ($levelCodeValue) {
                case EducationalLevelInterface::BASIC_EDUCATION:
                    $educationalSubjects = $this->processApiResponse(
                        $this->apiGet(OphEPerusteetInterface::BASIC_EDUCATION_EDUCATIONAL_SUBJECTS_API_METHOD),
                        $levelCodeValue
                    );
                    break;

                case EducationalLevelInterface::UPPER_SECONDARY_SCHOOL:
                    $educationalSubjects = $this->processApiResponse(
                        $this->apiGet(OphEPerusteetInterface::UPPER_SECONDARY_SCHOOL_EDUCATIONAL_SUBJECTS_API_METHOD),
                        $levelCodeValue
                    );
                    break;

                default:
                    throw NotSupportedException::forEducationalLevel($levelCodeValue);
            }
            $this->cache->save($item->set($educationalSubjects));
        }
        return $item->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjectByUrl(string $url): EducationalSubjectInterface
    {
        if (!$this->isSupportedEducationalSubjectUrl($url)) {
            throw new NotSupportedException('API URL ' . $url);
        }
        $levelCodeValue = $this->getEducationalLevelCodeByUrl($url);
        return new OphEperusteetEducationalSubject(
            $this->apiGet(substr($url, strlen($this->getApiBaseUrl()))),
            $this->getApiBaseUrl(),
            $levelCodeValue,
            $this->educationalLevels
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedEducationalSubjectUrl(string $url): bool
    {
        return str_starts_with($url, $this->getApiBaseUrl())
            && $this->isEducationalSubjectUrl($url);
    }

    /**
     * Process API response.
     *
     * @param array<mixed> $response
     * @param string $levelCodeValue
     *
     * @return array<EducationalSubjectInterface>
     */
    protected function processApiResponse(array $response, string $levelCodeValue): array
    {
        $educationalLevels = $levelCodeValue === EducationalLevelInterface::BASIC_EDUCATION
            ? $this->educationalLevels
            : [];
        $educationalSubjects = [];
        foreach ($response as $result) {
            $parent = new OphEperusteetEducationalSubject(
                $result,
                $this->getApiBaseUrl(),
                $levelCodeValue,
                $educationalLevels
            );
            $educationalSubjects[$parent->getId()] = $parent;
        }
        return $educationalSubjects;
    }

    /**
     * Get educational level code by educational subject API URL.
     *
     * @param string $url
     *     Educational subject API URL
     *
     * @return string
     *
     * @throws NotSupportedException
     */
    protected function getEducationalLevelCodeByUrl(string $url): string
    {
        if ($this->isBasicEducationUrl($url)) {
            return EducationalLevelInterface::BASIC_EDUCATION;
        } elseif ($this->isUpperSecondarySchoolUrl($url)) {
            return EducationalLevelInterface::UPPER_SECONDARY_SCHOOL;
        }
        throw new NotSupportedException($url);
    }

    /**
     * Is the URL a basic education URL?
     *
     * @param string $url
     *
     * @return bool
     */
    protected function isBasicEducationUrl(string $url): bool
    {
        return str_contains($url, OphEPerusteetInterface::BASIC_EDUCATION_EDUCATIONAL_LEVELS_API_METHOD)
            || str_contains($url, OphEPerusteetInterface::BASIC_EDUCATION_EDUCATIONAL_SUBJECTS_API_METHOD)
            || str_contains($url, OphEPerusteetInterface::BASIC_EDUCATION_TRANSVERSAL_COMPETENCES_API_METHOD);
    }

    /**
     * Is the URL an upper secondary school URL?
     *
     * @param string $url
     *
     * @return bool
     */
    protected function isUpperSecondarySchoolUrl(string $url): bool
    {
        return str_contains($url, OphEPerusteetInterface::UPPER_SECONDARY_SCHOOL_EDUCATIONAL_SUBJECTS_API_METHOD)
            || str_contains($url, OphEPerusteetInterface::UPPER_SECONDARY_SCHOOL_EDUCATIONAL_SYLLABUS_API_METHOD)
            || str_contains($url, OphEPerusteetInterface::UPPER_SECONDARY_SCHOOL_TRANSVERSAL_COMPETENCES_API_METHOD)
            || $this->isEducationalModuleUrl($url);
    }

    /**
     * Is the URL an educational subject URL?
     *
     * @param string $url
     *
     * @return bool
     */
    protected function isEducationalSubjectUrl(string $url): bool
    {
        return (str_contains($url, OphEPerusteetInterface::BASIC_EDUCATION_EDUCATIONAL_SUBJECTS_API_METHOD)
            || str_contains($url, OphEPerusteetInterface::UPPER_SECONDARY_SCHOOL_EDUCATIONAL_SUBJECTS_API_METHOD))
            && !($this->isEducationalSyllabusUrl($url) || $this->isEducationalModuleUrl($url))
            && !(str_contains($url, OphEPerusteetInterface::BASIC_EDUCATION_TRANSVERSAL_COMPETENCES_API_METHOD)
            || str_contains($url, OphEPerusteetInterface::UPPER_SECONDARY_SCHOOL_TRANSVERSAL_COMPETENCES_API_METHOD));
    }

    /**
     * Is the URL an educational syllabus URL?
     *
     * @param string $url
     *
     * @return bool
     */
    protected function isEducationalSyllabusUrl(string $url): bool
    {
        return str_contains($url, OphEPerusteetInterface::UPPER_SECONDARY_SCHOOL_EDUCATIONAL_SYLLABUS_API_METHOD);
    }

    /**
     * Is the URL an educational module URL?
     *
     * @param string $url
     *
     * @return bool
     */
    protected function isEducationalModuleUrl(string $url): bool
    {
        // @todo
        return str_contains($url, '/moduulit/');
    }
}
