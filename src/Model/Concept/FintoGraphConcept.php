<?php

namespace NatLibFi\FinnaCodeSets\Model\Concept;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;

class FintoGraphConcept extends AbstractConcept
{
    /**
     * Creates a FintoGraphConcept from Finto concept data.
     *
     * @param mixed $data
     *     Data from API
     * @param string $apiBaseUrl
     *     Base URL of source API
     * @param string $vocid
     *     Vocabulary ID
     * @param string $uri
     *     URI of the concept
     *
     * @return FintoGraphConcept
     *
     * @throws MissingValueException
     */
    public static function fromConceptData(
        mixed $data,
        string $apiBaseUrl,
        string $vocid,
        string $uri
    ): FintoGraphConcept {
        foreach ($data['graph'] as $item) {
            if (isset($item['uri']) && $item['uri'] === $uri) {
                $fullData = $data;
                $data = $item;
                break;
            }
        }
        if (!isset($fullData)) {
            throw new MissingValueException();
        }

        $graphConcept = new FintoGraphConcept($data, $apiBaseUrl, $vocid);
        foreach ($fullData['graph'] as $item) {
            if (
                is_array($item['type'] ?? null)
                && reset($item['type']) === 'http://www.yso.fi/onto/yso-meta/Concept'
            ) {
                if ($item['uri'] !== $uri) {
                    $graphConcept->addChild(new FintoGraphConcept($item, $apiBaseUrl, $vocid));
                }
            }
        }
        return $graphConcept;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefLabels(): array
    {
        $prefLabels = [];
        foreach ($this->data['prefLabel'] as $prefLabel) {
            $prefLabels[$prefLabel['lang']] = $prefLabel['value'];
        }
        return $prefLabels;
    }
}
