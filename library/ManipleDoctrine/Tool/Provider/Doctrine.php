<?php

namespace ManipleDoctrine\Tool\Provider;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

class Doctrine extends \Maniple_Tool_Provider_Abstract
{
    public function runAction($command = null, $a1 = null, $b1 = null)
    {
        $application = $this->_getApplication()->bootstrap();

        /** @var EntityManager $entityManager */
        $entityManager = $application->getBootstrap()->getResource('EntityManager');

        $helperSet = ConsoleRunner::createHelperSet($entityManager);

        $_SERVER['argv'] = array_merge(array('doctrine'), func_get_args());
        $_SERVER['argc'] = count($_SERVER['argv']);

        ConsoleRunner::run($helperSet, array());
    }
}
