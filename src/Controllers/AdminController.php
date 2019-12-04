<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class AdminController extends BaseController
{
    public function dashboard(Request $request, Response $response, array $args = []): Response
    {
        $response = $this->render($request, $response, 'admin/dashboard.twig');
        return $response;
    }
}
