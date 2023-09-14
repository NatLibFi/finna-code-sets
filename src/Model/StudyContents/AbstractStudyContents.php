<?php

namespace NatLibFi\FinnaCodeSets\Model\StudyContents;

use NatLibFi\FinnaCodeSets\Model\AbstractHierarchicalDataObject;
use NatLibFi\FinnaCodeSets\Model\ProxyObjectInterface;

abstract class AbstractStudyContents extends AbstractHierarchicalDataObject implements StudyContentsInterface
{
    /**
     * Educational level code value.
     *
     * @var string
     */
    protected string $levelCodeValue;

    /**
     * AbstractStudyContents constructor.
     *
     * @param array<mixed> $data
     *     Data from API
     * @param string $apiBaseUrl
     *     Base URL of source API
     * @param string $levelCodeValue
     *     Educational level code value
     */
    public function __construct(array $data, string $apiBaseUrl, string $levelCodeValue)
    {
        parent::__construct($data, $apiBaseUrl);
        $this->levelCodeValue = $levelCodeValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getEducationalLevelCodeValue(): string
    {
        return $this->levelCodeValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        if (($root = $this->getRoot()) instanceof ProxyObjectInterface) {
            $root = $root->getProxiedObject();
        }
        if ($root !== $this) {
            return $root->getUri();
        }
        return $this->apiBaseUrl ?? parent::getUri();
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeValue(): string
    {
        return (string)($this->data['codeValue'] ?? '');
    }
}
