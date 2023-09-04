<?php

namespace NatLibFi\FinnaCodeSets\Model\EducationalSubject;

class OphKoodistoEducationalSubject extends AbstractEducationalSubject
{
    /**
     * Preferred labels processed from raw data.
     *
     * @var ?array<string>
     */
    protected ?array $prefLabels = null;

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->data['koodiArvo'];
    }

    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        return $this->data['koodiUri'];
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeValue(): string
    {
        return (string)$this->data['koodiArvo'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefLabels(): array
    {
        if (null === $this->prefLabels) {
            $this->prefLabels = [];
            foreach ($this->data['metadata'] as $metadata) {
                $this->prefLabels[strtolower($metadata['kieli'])] = $metadata['nimi'];
            }
        }
        return $this->prefLabels;
    }
}
