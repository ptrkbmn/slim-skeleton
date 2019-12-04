<?php

declare(strict_types=1);

// Global helper function

use App\Helpers\Auth;
use App\Helpers\DebugBarWrapper;
use Slim\Routing\RouteParser;

$auth = $container->get('auth');

/**
 * Return the DebugBar wrapper object
 */
function debug(): DebugBarWrapper
{
    global $debug;
    return $debug;
}

/**
 * Returns the Auth-Helper object
 */
function auth(): Auth
{
    global $auth;
    return $auth;
}
