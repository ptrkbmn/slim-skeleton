<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    $rootPath = realpath(__DIR__ . '/..');

    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            // Debug mode
            'debug' => (getenv('APPLICATION_ENV') != 'production'),

            // Temprorary directory
            'temporary_path' => $rootPath . '/storage/tmp',

            // Route cache
            'route_cache' => $rootPath . '/cache/routes',

            // View (Twig)
            'view' => [
                'template_path' => $rootPath . '/templates',
                'twig' => [
                    'cache' => $rootPath . '/cache/twig',
                    'debug' => (getenv('APPLICATION_ENV') != 'production'),
                    'auto_reload' => true,
                ],
            ],

            // Doctrine
            'doctrine' => [
                'meta' => [
                    'entity_path' => [$rootPath . '/src/Entities'],
                    'auto_generate_proxies' => true,
                    'proxy_dir' => $rootPath . '/cache/proxies',
                    'cache' => null,
                ],
                'connection' => [
                    'driver' => 'pdo_sqlite',
                    'path' => $rootPath . '/storage/database.sqlite'
                ]
            ],

            // Monolog
            'logger' => [
                'name' => 'app',
                'path' =>  getenv('docker') ? 'php://stdout' : $rootPath . '/storage/log/app.log',
                'level' => (getenv('APPLICATION_ENV') != 'production') ? Logger::DEBUG : Logger::INFO,
            ]
        ],
    ]);

    if (getenv('APPLICATION_ENV') == 'production') {
        $containerBuilder->enableCompilation($rootPath . '/cache');
    }
};
