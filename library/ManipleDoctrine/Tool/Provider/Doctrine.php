<?php

namespace ManipleDoctrine\Tool\Provider;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

/**
 * Run doctrine cli, e.g.:
 *
 * maniple run doctrine orm:generate-proxies
 *
 * To pass additional options you have to separate them from command arg
 * with '--', e.g.:
 *
 * maniple run doctrine orm:schema-tool:create -- --dump-sql
 */
class Doctrine extends \Maniple_Tool_Provider_Abstract
{
    public function runAction($command = null)
    {
        /** @var \Zefram_Tool_Framework_Client_Console $client */
        $client = $this->_registry->getClient();
        if (!$client instanceof \Zefram_Tool_Framework_Client_Console) {
            throw new \Zend_Tool_Framework_Provider_Exception(sprintf(
                'Client is expected to be an instance of Zefram_Tool_Framework_Client_Console, %s was provided.',
                is_object($client) ? get_class($client) : gettype($client)
            ));
        }

        $application = $this->_getApplication()->bootstrap();

        /** @var EntityManager $entityManager */
        $entityManager = $application->getBootstrap()->getResource('EntityManager');

        $helperSet = ConsoleRunner::createHelperSet($entityManager);

        $_SERVER['argv'] = array_merge(
            array('doctrine', $command),
            $client->getArgumentParser()->getRemainingArgs()
        );
        $_SERVER['argc'] = count($_SERVER['argv']);

        ConsoleRunner::run($helperSet, array());
    }
}
