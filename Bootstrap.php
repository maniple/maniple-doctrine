<?php

class ManipleDoctrine_Bootstrap extends Maniple_Application_Module_Bootstrap
{
    public function getModuleDependencies()
    {
        return array();
    }

    public function getResourcesConfig()
    {
        return require __DIR__ . '/configs/resources.config.php';
    }

    /**
     * Register autoloader paths
     */
    protected function _initAutoloader()
    {
        Zend_Loader_AutoloaderFactory::factory(array(
            'Zend_Loader_StandardAutoloader' => array(
                'prefixes' => array(
                    'ManipleDoctrine_' => __DIR__ . '/library/ManipleDoctrine/',
                ),
            ),
        ));
    }
}
