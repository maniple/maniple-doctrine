<?php

namespace ManipleDoctrine\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class AbstractEnum extends Type
{
    /**
     * Class used for enum type, must be provided by subclasses
     * @var string
     */
    protected $_enumClass;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        /** @var \SplEnum $enum */
        $enumClass = $this->_enumClass;
        $enum = new $enumClass($value, true);

        return $enum;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (is_object($value)) {
            $value = (string) $value;
        }

        return $value;
    }

    public function getName()
    {
        $pos = max(strrpos($this->_enumClass, '_'), strrpos($this->_enumClass, '\\'));
        return substr($this->_enumClass, $pos + 1);
    }
}
