<?php

namespace ManipleDoctrine\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Boolean implemented by integer, for backwards compatibility and portability
 */
class BoolintType extends Type
{
    const className = __CLASS__;

    public function getName()
    {
        return 'boolint';
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $sqlDeclaration = $platform->getBooleanTypeDeclarationSQL($fieldDeclaration);

        if (false === stripos($sqlDeclaration, 'INT')) {
            $sqlDeclaration = $platform->getSmallIntTypeDeclarationSQL($fieldDeclaration);
        }

        return $sqlDeclaration;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        return (bool) +$value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        return +$value ? 1 : 0;
    }
}
