<?php

namespace NatLibFi\FinnaCodeSets\Model\StudyObjective;

class BasicEducationStudyObjective extends AbstractStudyObjective
{
    /**
     * {@inheritdoc}
     */
    public function getPrefLabels(): array
    {
        return array_filter(
            $this->data['tavoite'] ?? [],
            function ($value, $key) {
                return !str_starts_with($key, '_');
            },
            ARRAY_FILTER_USE_BOTH
        );
    }
}
