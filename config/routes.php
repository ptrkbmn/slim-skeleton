<?php

declare(strict_types=1);

use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    // Public routes
    $app->get('/', 'App\Controllers\HomeController:index')->setName('home');

    $app->get('/signup', 'App\Controllers\AuthController:signup')->setName('signup');
    $app->post('/signup', 'App\Controllers\AuthController:signup');

    $app->get('/login', 'App\Controllers\AuthController:login')->setName('login');
    $app->post('/login', 'App\Controllers\AuthController:login');

    $app->get('/logout', 'App\Controllers\AuthController:logout')->setName('logout');

    $app->get('/lang/{code}', 'App\Controllers\SystemController:lang')->setName('lang');

    // Administration routes
    // They have to start with /admin !
    $app->redirect('/admin', '/admin/dashboard')->setName('admin');
    $app->get('/admin/dashboard', 'App\Controllers\AdminController:dashboard')->setName('dashboard');
};
