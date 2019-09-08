<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    // Controller factories
    $containerBuilder->addDefinitions([
        App\Controllers\HomeController::class => function (ContainerInterface $c) {
            return new App\Controllers\HomeController($c);
        }
    ]);
};
