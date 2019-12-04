<?php

declare(strict_types=1);

use App\Helpers\CsrfExtension;
use App\Helpers\DebugbarWrapper;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\LangMiddleware;
use Slim\App;
use Slim\Csrf\Guard;
use Slim\Views\TwigMiddleware;
use Tkhamez\Slim\RoleAuth\RoleMiddleware;
use Tkhamez\Slim\RoleAuth\SecureRouteMiddleware;

return function (App $app, DebugbarWrapper $debug) {
    // Keep in mind that those middleware which
    // are added last are executed first (LIFO)!

    $container = $app->getContainer();
    $settings = $container->get('settings');
    $sessionMiddleware = $container->get('session'); // Initializes the session!

    $twig = $container->get("view");
    $em = $container->get("em");
    $auth = $container->get('auth');

    if ($settings["debug"] === true) {
        // Make sure that this middleware is (more or less) the inner most middleware.
        $app->add(new \App\Middlewares\CoreExecutionTimeMiddleware($debug));

        $debugstack = $em->getConfiguration()->getSQLLogger();
        $debug->addCollector(new \DebugBar\Bridge\DoctrineCollector($debugstack));

        // Make sure that this middleware is added before the Twig middleware.
        $app->add((new \App\Middlewares\DebugBarMiddleware($debug->getDebugBar()))
                ->cssJsInPublicFolder(true, ROOT_PATH . DIRECTORY_SEPARATOR . "public")
                ->captureAjax()
        );
    }

    // Deny access if a required role is missing
    $app->add(new SecureRouteMiddleware(
        new Slim\Psr7\Factory\ResponseFactory(),
        $settings['acl']['routes'],
        ['redirect_url' => $settings['acl']['redirect_url']]
    ));

    // Add roles to request attribute
    $app->add(new RoleMiddleware(new \App\Helpers\RoleProvider()));

    // CSRF Protection
    $guard = new Guard($app->getResponseFactory());
    $app->add($guard);

    // Language
    $app->add(new LangMiddleware());

    // Twig
    $app->add(new TwigMiddleware($twig, $app->getRouteCollector()->getRouteParser(), $app->getBasePath(), 'twig'));

    // Make sure that the CSRF values are available in twig
    $twig->addExtension(new CsrfExtension($guard));

    // Authentication
    $app->add(new AuthMiddleware($auth, $em, $twig));

    // Make sure that the session middleware is added last. This way you can access
    // the session variable within each other middleware.
    $app->add($sessionMiddleware);
};
