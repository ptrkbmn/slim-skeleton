<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        'logger' => function (ContainerInterface $container) {
            $settings = $container->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        'em' => function (ContainerInterface $container) {
            $settings = $container->get('settings');
            $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
                $settings['doctrine']['meta']['entity_path'],
                $settings['doctrine']['meta']['auto_generate_proxies'],
                $settings['doctrine']['meta']['proxy_dir'],
                $settings['doctrine']['meta']['cache'],
                false
            );
            $em = EntityManager::create($settings['doctrine']['connection'], $config);

            if ($settings["debug"] === true) {
                $em->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\DebugStack());
            }
            return $em;
        },
        'view' => function (ContainerInterface $container) {
            $settings = $container->get('settings');

            $view = new Twig($settings['view']['template_path'], $settings['view']['twig']);

            // Add extensions
            $view->addExtension(new Twig_Extension_Debug());
            $view->addExtension(new Twig_Extensions_Extension_I18n());
            $view->addExtension(new Twig_Extensions_Extension_Intl());

            $environment = $view->getEnvironment();
            $environment->addGlobal('TODAY', new DateTime());

            return $view;
        },
        'session' => function (ContainerInterface $container) {
            return new \App\Middlewares\SessionMiddleware;
        },
        'flash' => function (ContainerInterface $container) {
            $session = $container->get('session');
            return new \Slim\Flash\Messages($session);
        }
    ]);
};
