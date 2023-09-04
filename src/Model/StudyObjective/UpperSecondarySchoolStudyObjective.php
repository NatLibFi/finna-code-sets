<?php

namespace NatLibFi\FinnaCodeSets\Model\StudyObjective;

class UpperSecondarySchoolStudyObjective extends AbstractStudyObjective
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->data['_id'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefLabels(): array
    {
        return array_filter(
            $this->data,
            function ($value, $key) {
                return !str_starts_with($key, '_');
            },
            ARRAY_FILTER_USE_BOTH
        );
    }
}
