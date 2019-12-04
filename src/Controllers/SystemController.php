<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class SystemController extends BaseController
{
	public function lang(Request $request, Response $response, array $args = []): Response
	{
		$session = $request->getAttribute('session');
		$session['lang'] = $args['code'];
    $referer = filter_input(INPUT_SERVER, 'HTTP_REFERER');
		return $response->withHeader('Location', $referer);
	}
}
