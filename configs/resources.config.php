<?php

return array(
    'EntityManager' => array(
        'callback' => 'ManipleDoctrine\Service\EntityManagerFactory::factory',
    ),

    'EntityManager.config' => array(
        'class' => 'ManipleDoctrine\Config',
    ),
);
