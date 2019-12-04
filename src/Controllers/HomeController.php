<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class HomeController extends BaseController
{
    public function index(Request $request, Response $response, array $args = []): Response
    {
        debug()->start("home", "Rendering Home");
        $response = $this->render($request, $response, 'home.twig');
        debug()->stop("home");
        return $response;
    }

    public function admin(Request $request, Response $response, array $args = []): Response
    {
        $response = $this->render($request, $response, 'admintest.twig');
        return $response;
    }
}
