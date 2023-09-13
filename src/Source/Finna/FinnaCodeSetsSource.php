<?php

namespace NatLibFi\FinnaCodeSets\Source\Finna;

use NatLibFi\FinnaCodeSets\Exception\NotFoundException;
use NatLibFi\FinnaCodeSets\Exception\NotSupportedException;
use NatLibFi\FinnaCodeSets\Exception\UnexpectedValueException;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Model\LearningArea\LearningArea;
use NatLibFi\FinnaCodeSets\Model\StudyContents\EarlyChildhoodEducationStudyContents;
use NatLibFi\FinnaCodeSets\Model\StudyContents\StudyContentsInterface;
use NatLibFi\FinnaCodeSets\Source\AbstractApiSource;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;

class FinnaCodeSetsSource extends AbstractApiSource implements FinnaCodeSetsSourceInterface
{
    /**
     * FinnaCodeSetsSource constructor.
     */
    public function __construct(
        ClientInterface $httpClient,
        CacheItemPoolInterface $cache,
        string $apiBaseUrl = FinnaCodeSetsSourceInterface::DEFAULT_API_BASE_URL
    ) {
        parent::__construct($httpClient, $cache, $apiBaseUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalSubjects(string $levelCodeValue): array
    {
        if ($levelCodeValue !== EducationalLevelInterface::EARLY_CHILDHOOD_EDUCATION) {
            throw NotSupportedException::forEducationalLevel($levelCodeValue);
        }
        $item = $this->cache->getItem(md5(__METHOD__));
        if (!$item->isHit()) {
            $educationalSubjects = [];
            $response = $this->apiGet(
                FinnaCodeSetsSourceInterface::EARLY_CHILDHOOD_EDUCATION_LEARNING_AREAS_API_METHOD
            );
            foreach ($response as $result) {
                $learningArea = new LearningArea(
                    $result,
                    $this->getApiBaseUrl(),
                    EducationalLevelInterface::EARLY_CHILDHOOD_EDUCATION
                );
                $educationalSubjects[$learningArea->getId()] = $learningArea;
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
        $id = substr(
            $url,
            strlen(
                $this->getApiBaseUrl()
                    . FinnaCodeSetsSourceInterface::EARLY_CHILDHOOD_EDUCATION_LEARNING_AREAS_API_METHOD
                    . '/'
            )
        );
        $educationalSubjects = $this->getEducationalSubjects(EducationalLevelInterface::EARLY_CHILDHOOD_EDUCATION);
        if (isset($educationalSubjects[$id])) {
            return $educationalSubjects[$id];
        }
        throw new NotFoundException($url);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedEducationalSubjectUrl(string $url): bool
    {
        return str_starts_with(
            $url,
            $this->getApiBaseUrl()
                . FinnaCodeSetsSourceInterface::EARLY_CHILDHOOD_EDUCATION_LEARNING_AREAS_API_METHOD
                . '/'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTransversalCompetences(string $levelCodeValue): array
    {
        if ($levelCodeValue !== EducationalLevelInterface::EARLY_CHILDHOOD_EDUCATION) {
            throw NotSupportedException::forEducationalLevel($levelCodeValue);
        }
        $item = $this->cache->getItem(md5(__METHOD__));
        if (!$item->isHit()) {
            $competences = [];
            $response = $this->apiGet(
                FinnaCodeSetsSourceInterface::EARLY_CHILDHOOD_EDUCATION_TRANSVERSAL_COMPETENCES_API_METHOD
            );
            foreach ($response as $result) {
                $studyContents = new EarlyChildhoodEducationStudyContents(
                    $result,
                    $this->getApiBaseUrl(),
                    EducationalLevelInterface::EARLY_CHILDHOOD_EDUCATION
                );
                $competences[$studyContents->getId()] = $studyContents;
            }
            $this->cache->save($item->set($competences));
        }
        return $item->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getTransversalCompetenceByUrl(string $url): StudyContentsInterface
    {
        if (!$this->isSupportedTransversalCompetenceUrl($url)) {
            throw new NotSupportedException('API URL ' . $url);
        }
        $id = substr(
            $url,
            strlen(
                $this->getApiBaseUrl()
                . FinnaCodeSetsSourceInterface::EARLY_CHILDHOOD_EDUCATION_TRANSVERSAL_COMPETENCES_API_METHOD
                . '/'
            )
        );
        $competences = $this->getTransversalCompetences(EducationalLevelInterface::EARLY_CHILDHOOD_EDUCATION);
        if (isset($competences[$id])) {
            return $competences[$id];
        }
        throw new NotFoundException($url);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportedTransversalCompetenceUrl(string $url): bool
    {
        return str_starts_with(
            $url,
            $this->getApiBaseUrl()
            . FinnaCodeSetsSourceInterface::EARLY_CHILDHOOD_EDUCATION_TRANSVERSAL_COMPETENCES_API_METHOD
            . '/'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function apiGet(string $method, array|object $query = []): array
    {
        // Emulate a real API call.
        switch ($method) {
            case FinnaCodeSetsSourceInterface::EARLY_CHILDHOOD_EDUCATION_LEARNING_AREAS_API_METHOD:
                return [
                    [
                        'id' => 'OA-2022-1',
                        'prefLabel' => ['fi' => 'Kielten rikas maailma'],
                    ],
                    [
                        'id' => 'OA-2022-2',
                        'prefLabel' => ['fi' => 'Ilmaisun monet muodot'],
                    ],
                    [
                        'id' => 'OA-2022-3',
                        'prefLabel' => ['fi' => 'Minä ja meidän yhteisömme'],
                    ],
                    [
                        'id' => 'OA-2022-4',
                        'prefLabel' => ['fi' => 'Tutkin ja toimin ympäristössäni'],
                    ],
                    [
                        'id' => 'OA-2022-5',
                        'prefLabel' => ['fi' => 'Kasvan, liikun ja kehityn'],
                    ],
                ];

            case FinnaCodeSetsSourceInterface::EARLY_CHILDHOOD_EDUCATION_TRANSVERSAL_COMPETENCES_API_METHOD:
                return [
                    [
                        'id' => 'LO-2022-1',
                        'prefLabel' => ['fi' => 'Ajattelu ja oppiminen'],
                    ],
                    [
                        'id' => 'LO-2022-2',
                        'prefLabel' => ['fi' => 'Kulttuurinen osaaminen, vuorovaikutus ja ilmaisu'],
                    ],
                    [
                        'id' => 'LO-2022-3',
                        'prefLabel' => ['fi' => 'Itsestä huolehtiminen ja arjen taidot'],
                    ],
                    [
                        'id' => 'LO-2022-4',
                        'prefLabel' => ['fi' => 'Monilukutaito'],
                    ],
                    [
                        'id' => 'LO-2022-5',
                        'prefLabel' => ['fi' => 'Digitaalinen osaaminen'],
                    ],
                    [
                        'id' => 'LO-2022-6',
                        'prefLabel' => ['fi' => 'Osallistuminen ja vaikuttaminen'],
                    ],
                ];
        }
        throw new UnexpectedValueException('API method: ' . $method);
    }
}
