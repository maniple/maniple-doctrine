<?php

namespace ManipleDoctrine\Extensions;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Doctrine\ORM\Id\BigIntegerIdentityGenerator;
use Doctrine\ORM\Id\IdentityGenerator;
use Doctrine\ORM\Id\SequenceGenerator;
use Doctrine\ORM\Id\TableGenerator;

class TablePrefix
{
    protected $prefix = '';

    public function __construct($prefix)
    {
        $this->prefix = (string) $prefix;
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $classMetadata */
        $classMetadata = $eventArgs->getClassMetadata();

        if (!$classMetadata->isInheritanceTypeSingleTable() || $classMetadata->getName() === $classMetadata->rootEntityName) {
            $classMetadata->setTableName($this->prefix . $classMetadata->getTableName());
        }

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if ($mapping['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY && $mapping['isOwningSide']) {
                $mappedTableName = $mapping['joinTable']['name'];
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix . $mappedTableName;
            }
        }

        // add prefix to indexes
        $table = $classMetadata->table;

        if (isset($table['indexes'])) {
            $table['indexes'] = $this->prefixIndexes($classMetadata->getTableName(), $table['indexes'], 'idx');
        }

        // create uniqueConstraints from unique columns, so that properly prefixed
        // constraint names can be generated
        foreach ($classMetadata->fieldMappings as $field => &$mapping) {
            if ($mapping['unique']) {
                $table['uniqueConstraints'][] = array(
                    'columns' => array($mapping['columnName']),
                );
                $mapping['unique'] = false;
            }
        }

        if (isset($table['uniqueConstraints'])) {
            $table['uniqueConstraints'] = $this->prefixIndexes($classMetadata->getTableName(), $table['uniqueConstraints'], 'key');
        }

        // add prefix to sequence
        if ($classMetadata->sequenceGeneratorDefinition) {
            $sequenceName = $this->prefix . $classMetadata->sequenceGeneratorDefinition['sequenceName'];
            $classMetadata->sequenceGeneratorDefinition['sequenceName'] = $sequenceName;

            $this->_setIdGeneratorSequenceName($classMetadata->idGenerator, $sequenceName);
        }

        $classMetadata->table = $table;
    }

    protected function prefixIndexes($tableName, array $array, $typePrefix)
    {
        $prefixedArray = array();

        foreach ($array as $key => $value) {
            if (is_int($key)) {
                // auto generate index name when not provided
                $prefixedKey = $this->_generateIdentifierName($tableName, $value['columns'], $typePrefix);
            } else {
                $prefixedKey = $this->prefix . $key;
            }
            if (isset($array[$prefixedKey])) {
                throw new Exception(sprintf('Prefixed name conflict %s', $prefixedKey));
            }
            $prefixedArray[$prefixedKey] = $value;
        }

        return $prefixedArray;
    }

    protected function _generateIdentifierName($tableName, $columnNames, $prefix = '', $maxSize = 30)
    {
        $hash = implode("", array_map(function($column) {
            return dechex(crc32($column));
        }, array_merge(array($tableName), $columnNames)));

        return strtoupper(substr($prefix . "_" . $hash, 0, $maxSize));
    }

    protected function _setIdGeneratorSequenceName(AbstractIdGenerator $idGenerator, $sequenceName)
    {
        $refClass = new \ReflectionClass($idGenerator);
        $refProp = null;
        if ($refClass->hasProperty('sequenceName')) {
            $refProp = $refClass->getProperty('sequenceName');
        } elseif ($refClass->hasProperty('_sequenceName')) {
            $refProp = $refClass->getProperty('_sequenceName');
        }
        if ($refProp) {
            $refProp->setAccessible(true);
            $refProp->setValue($idGenerator, $sequenceName);
        }
        return $idGenerator;
    }
}
