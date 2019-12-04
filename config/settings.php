<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            // Debug mode
            'debug' => (getenv('APPLICATION_ENV') != 'production'),

            // Temprorary directory
            'temporary_path' => ROOT_PATH . '/storage/tmp',

            // Route cache
            'route_cache' => ROOT_PATH . '/cache/routes',

            // View (Twig)
            'view' => [
                'template_path' => ROOT_PATH . '/templates',
                'twig' => [
                    'cache' => ROOT_PATH . '/cache/twig',
                    'debug' => (getenv('APPLICATION_ENV') != 'production'),
                    'auto_reload' => true,
                ],
            ],

            // Doctrine
            'doctrine' => [
                'meta' => [
                    'entity_path' => [ROOT_PATH . '/src/Entities'],
                    'auto_generate_proxies' => true,
                    'proxy_dir' => ROOT_PATH . '/cache/proxies',
                    'cache' => null,
                ],
                'connection' => [
                    'driver' => 'pdo_sqlite',
                    'path' => ROOT_PATH . '/storage/database.sqlite'
                ]
            ],

            // ACL
            'acl' => [
                'routes' => [
                    // route pattern -> roles, first "starts-with" match is used
                    '/admin/users' => ['admin'],
                    '/admin' => ['user', 'admin'],
                ],
                'redirect_url' => '/login',
            ],

            // Monolog
            'logger' => [
                'name' => 'app',
                'path' =>  getenv('docker') ? 'php://stdout' : ROOT_PATH . '/storage/log/app.log',
                'level' => (getenv('APPLICATION_ENV') != 'production') ? Logger::DEBUG : Logger::INFO,
            ]
        ],
    ]);

    if (getenv('APPLICATION_ENV') == 'production') {
        $containerBuilder->enableCompilation(ROOT_PATH . '/cache');
    }
};
