<?php

use DI\ContainerBuilder;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require 'vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();
$settings = require('config/settings.php');
$settings($containerBuilder);
$container = $containerBuilder->build();

$doctrineSettings = $container->get("settings")["doctrine"];

$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
    $doctrineSettings['meta']['entity_path'],
    $doctrineSettings['meta']['auto_generate_proxies'],
    $doctrineSettings['meta']['proxy_dir'],
    $doctrineSettings['meta']['cache'],
    false
);

$em = \Doctrine\ORM\EntityManager::create($doctrineSettings['connection'], $config);

return ConsoleRunner::createHelperSet($em);
