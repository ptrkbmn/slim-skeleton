<?php

declare(strict_types=1);

namespace App\Renderers;

use Psr\Container\ContainerInterface;
use Slim\Interfaces\ErrorRendererInterface;

class HtmlErrorRenderer implements ErrorRendererInterface
{
    protected $view;

    public function __construct(ContainerInterface $container)
    {
        $this->view = $container->get('view');
    }

    public function __invoke(\Throwable $exception, bool $displayErrorDetails): string
    {
        $title = '500 - ' .  get_class($exception);
        if (is_a($exception, '\Slim\Exception\HttpException')) {
            $title = $exception->getTitle();
        }

        return $this->view->fetch('error/default.twig', [
            'title' => $title,
            'debug' => $displayErrorDetails,
            'type' => get_class($exception),
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' =>  $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
