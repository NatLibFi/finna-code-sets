<?php

namespace NatLibFi\FinnaCodeSets\Model\Organisation;

use NatLibFi\FinnaCodeSets\Model\AbstractHierarchicalDataObject;
use NatLibFi\FinnaCodeSets\Model\DataObjectInterface;

class FinnaAdminOrganisation extends AbstractHierarchicalDataObject implements FinnaAdminOrganisationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        return $this->apiBaseUrl . '/organization/organization/' . $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeValue(): string
    {
        return $this->data['name'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefLabels(): array
    {
        return [DataObjectInterface::LANGUAGE_FINNISH => $this->data['nameReadable']];
    }

    /**
     * {@inheritdoc}
     */
    public function getFeedbackEmail(): ?string
    {
        return $this->data['feedbackEmail'] ?? null;
    }
}
