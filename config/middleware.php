<?php

declare(strict_types=1);

use App\Helpers\DebugbarWrapper;
use Slim\App;
use Slim\Views\TwigMiddleware;

return function (App $app, DebugbarWrapper $debug) {
    // Keep in mind that those middleware which
    // are added last are executed first (LIFO)!

    $container = $app->getContainer();
    $settings = $container->get('settings');

    if ($settings["debug"] === true) {
        // Make sure that this middleware is (more or less) the inner most middleware.
        $app->add(new \App\Middlewares\CoreExecutionTimeMiddleware($debug));

        if ($container->has("em")) {
            $debugstack = $container->get("em")->getConfiguration()->getSQLLogger();
            $debug->addCollector(new \DebugBar\Bridge\DoctrineCollector($debugstack));
        }

        // Make sure that this middleware is added before the Twig middleware.
        $app->add((new \App\Middlewares\DebugBarMiddleware($debug->getDebugBar()))
                ->inline()
                ->captureAjax()
        );
    }

    $app->add(TwigMiddleware::createFromContainer($app));
    $app->add($container->get('session'));
};
