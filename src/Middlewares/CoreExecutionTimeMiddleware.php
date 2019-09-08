<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Helpers\DebugbarWrapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class CoreExecutionTimeMiddleware implements Middleware
{

    /**
     * @var DebugbarWrapper|null The debugbar wrapper
     */
    private $debug;

    public function __construct(DebugbarWrapper $debug)
    {
        $this->debug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $this->debug->start("appcore", "Core");
        $response = $handler->handle($request);
        $this->debug->stop("appcore");
        return $response;
    }
}
