<?php

namespace ManipleDoctrine\Service;

use ManipleDoctrine\Extensions\TablePrefix;
use ManipleDoctrine\Types\BoolintType;
use ManipleDoctrine\Types\EpochType;

abstract class EntityManagerFactory
{
    public static function factory(\Maniple_Di_Container $container)
    {
        /** @var \ManipleDoctrine\Config $config */
        $config = $container->getResource('EntityManager.config');

        /** @var $db \Zefram_Db */
        $db = $container->getResource('Zefram_Db');

        $evm = new \Doctrine\Common\EventManager();
        $conn = \Doctrine\DBAL\DriverManager::getConnection(array('pdo' => $db->getAdapter()->getConnection()), null, $evm);

        $logger = new \Doctrine\DBAL\Logging\DebugStack();
        $conn->getConfiguration()->setSQLLogger($logger);

        $paths = $config->getPaths();
        $isDevMode = true;
        if(0)$config = \Doctrine\ORM\Tools\Setup::createYAMLMetadataConfiguration(
            $paths,
            $isDevMode
        );
        $metadataConfig = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $metadataConfig->setProxyDir(APPLICATION_PATH . '/../data/doctrine/Proxies');
        $metadataConfig->setAutoGenerateProxyClasses(true);

        // setup table prefix
        $tablePrefix = new TablePrefix($db->getTablePrefix());
        $evm->addEventListener(\Doctrine\ORM\Events::loadClassMetadata, $tablePrefix);

        // setup custom types
        \Doctrine\DBAL\Types\Type::addType('epoch', EpochType::className);
        \Doctrine\DBAL\Types\Type::addType('boolint', BoolintType::className);

        foreach ($config->getTypes() as $name => $class) {
            \Doctrine\DBAL\Types\Type::addType($name, $class);
        }

        $entityManager = \Doctrine\ORM\EntityManager::create($conn, $metadataConfig, $evm);
        return $entityManager;
    }
}
