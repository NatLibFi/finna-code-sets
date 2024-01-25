<?php

namespace NatLibFi\FinnaCodeSets\Utility;

use NatLibFi\FinnaCodeSets\Exception\UnexpectedValueException;
use NatLibFi\FinnaCodeSets\Model\EducationalData\EducationalDataObjectInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalData\StudyDataObjectInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalLevel\EducationalLevelInterface;
use NatLibFi\FinnaCodeSets\Model\EducationalSubject\EducationalSubjectInterface;
use NatLibFi\FinnaCodeSets\Model\HierarchicalProxyDataObject;
use NatLibFi\FinnaCodeSets\Model\HierarchicalProxyDataObjectInterface;
use NatLibFi\FinnaCodeSets\Model\ProxyObjectInterface;
use NatLibFi\FinnaCodeSets\Model\StudyContents\StudyContentsInterface;
use NatLibFi\FinnaCodeSets\Model\StudyObjective\StudyObjectiveInterface;
use NatLibFi\FinnaCodeSets\Model\VocationalUnit\VocationalUnitInterface;

class Assert
{
    /**
     * Assert that the object is an instance of EducationalDataObjectInterface.
     *
     * @param mixed $object
     *
     * @return EducationalDataObjectInterface
     *
     * @throws UnexpectedValueException
     */
    public static function educationalDataObject(mixed $object): EducationalDataObjectInterface
    {
        if (!$object instanceof EducationalDataObjectInterface) {
            throw (new UnexpectedValueException('Not an educational object'))->setValue($object);
        }
        return $object;
    }

    /**
     * Assert that the object is an instance of StudyDataObjectInterface.
     *
     * @param mixed $object
     *
     * @return StudyDataObjectInterface
     *
     * @throws UnexpectedValueException
     */
    public static function studyDataObject(mixed $object): StudyDataObjectInterface
    {
        if (!($object instanceof StudyDataObjectInterface)) {
            throw (new UnexpectedValueException('Not a study data object'))
                ->setValue($object);
        }
        return $object;
    }

    /**
     * Assert that the object is an instance of EducationalLevelInterface.
     *
     * @param mixed $object
     *
     * @return EducationalLevelInterface
     *
     * @throws UnexpectedValueException
     */
    public static function educationalLevel(mixed $object): EducationalLevelInterface
    {
        if (!$object instanceof EducationalLevelInterface) {
            throw (new UnexpectedValueException('Not an educational level'))->setValue($object);
        }
        return $object;
    }

    /**
     * Assert that the object is an instance of EducationalSubjectInterface.
     *
     * @param mixed $object
     *
     * @return EducationalSubjectInterface
     *
     * @throws UnexpectedValueException
     */
    public static function educationalSubject(mixed $object): EducationalSubjectInterface
    {
        if (!$object instanceof EducationalSubjectInterface) {
            throw (new UnexpectedValueException('Not an educational subject'))->setValue($object);
        }
        return $object;
    }

    /**
     * Assert that the object is an instance of ProxyObjectInterface.
     *
     * @param mixed $object
     *
     * @return ProxyObjectInterface
     *
     * @throws UnexpectedValueException
     */
    public static function proxyObject(mixed $object): ProxyObjectInterface
    {
        if (!$object instanceof ProxyObjectInterface) {
            throw (new UnexpectedValueException('Not a proxy object'))->setValue($object);
        }
        return $object;
    }

    /**
     * Assert that the object is an instance of HierarchicalProxyDataObjectInterface.
     *
     * @param mixed $object
     *
     * @return HierarchicalProxyDataObject
     *
     * @throws UnexpectedValueException
     */
    public static function hierarchicalProxyDataObject(mixed $object): HierarchicalProxyDataObjectInterface
    {
        if (!$object instanceof HierarchicalProxyDataObject) {
            throw (new UnexpectedValueException('Not a hierarchical proxy data object'))->setValue($object);
        }
        return $object;
    }

    /**
     * Assert that the object is an instance of StudyContentsInterface.
     *
     * @param mixed $object
     *
     * @return StudyContentsInterface
     *
     * @throws UnexpectedValueException
     */
    public static function studyContents(mixed $object): StudyContentsInterface
    {
        if (!$object instanceof StudyContentsInterface) {
            throw (new UnexpectedValueException('Not a study contents object'))->setValue($object);
        }
        return $object;
    }

    /**
     * Assert that the object is an instance of StudyObjectiveInterface.
     *
     * @param mixed $object
     *
     * @return StudyObjectiveInterface
     *
     * @throws UnexpectedValueException
     */
    public static function studyObjective(mixed $object): StudyObjectiveInterface
    {
        if (!$object instanceof StudyObjectiveInterface) {
            throw (new UnexpectedValueException('Not a study objective'))->setValue($object);
        }
        return $object;
    }

    /**
     * Assert that the object is an instance of VocationalUnitInterface.
     *
     * @param mixed $object
     *
     * @return VocationalUnitInterface
     *
     * @throws UnexpectedValueException
     */
    public static function vocationalUnit(mixed $object): VocationalUnitInterface
    {
        if (!$object instanceof VocationalUnitInterface) {
            throw (new UnexpectedValueException('Not a vocational unit'))->setValue($object);
        }
        return $object;
    }
}
