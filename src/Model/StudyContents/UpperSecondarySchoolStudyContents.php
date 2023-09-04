<?php

namespace NatLibFi\FinnaCodeSets\Model\StudyContents;

class UpperSecondarySchoolStudyContents extends AbstractStudyContents
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return (string)($this->data['id'] ?? $this->data['_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefLabels(): array
    {
        return array_filter(
            $this->data['nimi'] ?? $this->data,
            function ($value, $key) {
                return !str_starts_with($key, '_');
            },
            ARRAY_FILTER_USE_BOTH
        );
    }
}
