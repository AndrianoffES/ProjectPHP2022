<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(__DIR__);
$baseDir = dirname($vendorDir);

return array(
    'project\\App\\' => array($baseDir . '/src'),
    'Symfony\\Polyfill\\Uuid\\' => array($vendorDir . '/symfony/polyfill-uuid'),
    'Psr\\Container\\' => array($vendorDir . '/psr/container/src'),
    'PhpParser\\' => array($vendorDir . '/nikic/php-parser/lib/PhpParser'),
    'Faker\\' => array($vendorDir . '/fakerphp/faker/src/Faker'),
    'Doctrine\\Instantiator\\' => array($vendorDir . '/doctrine/instantiator/src/Doctrine/Instantiator'),
    'DeepCopy\\' => array($vendorDir . '/myclabs/deep-copy/src/DeepCopy'),
    'App\\Blog\\UnitTests\\' => array($baseDir . '/tests'),
);
