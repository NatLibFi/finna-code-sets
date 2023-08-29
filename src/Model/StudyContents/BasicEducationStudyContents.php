<?php

namespace NatLibFi\FinnaCodeSets\Model\StudyContents;

class BasicEducationStudyContents extends AbstractStudyContents
{
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
