<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalLevel;

use NatLibFi\FinnaCodeSets\Source\Oph\EPerusteet\OphEPerusteetInterface;

class OphEPerusteetEducationalLevel extends AbstractEducationalLevel
{
    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        return $this->apiBaseUrl
            . OphEPerusteetInterface::BASIC_EDUCATION_EDUCATIONAL_LEVELS_API_METHOD
            . '/' . $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeValue(): string
    {
        return (string)($this->data['codeValue'] ?? '');
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefLabels(): array
    {
        return array_filter(
            $this->data['nimi'] ?? [],
            function ($value, $key) {
                return !str_starts_with($key, '_');
            },
            ARRAY_FILTER_USE_BOTH
        );
    }
}
