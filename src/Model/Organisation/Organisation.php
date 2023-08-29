<?php

namespace NatLibFi\FinnaCodeSets\Model\Organisation;

use NatLibFi\FinnaCodeSets\Exception\MissingValueException;
use NatLibFi\FinnaCodeSets\Model\AbstractHierarchicalDataObject;

class Organisation extends AbstractHierarchicalDataObject implements OrganisationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->data['oid'];
    }

    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        return $this->apiBaseUrl . '/' . $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefLabels(): array
    {
        if (!isset($this->data['nimi'])) {
            throw new MissingValueException('Preferred labels');
        }
        return $this->data['nimi'];
    }

    /**
     * Get parent organisation ID path.
     *
     * @return array<string>
     */
    public function getParentOidPath(): array
    {
        return explode('/', $this->data['parentOidPath']);
    }
}
