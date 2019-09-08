<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class BaseController
{
    /**
     * @var \Slim\Views\Twig Twig View
     */
    protected $view;

    /**
     * @var \Monolog\Logger Logger
     */
    protected $logger;

    /**
     * @var \Slim\Flash\Messages Flash Messages
     */
    protected $flash;

    /**
     * @var \Doctrine\ORM\EntityManager EntityManager
     */
    protected $em;

    /**
     * @var \App\Helpers\DebugBarWrapper Debugbar Wrapper
     */
    protected $debug;

    public function __construct(ContainerInterface $container)
    {
        $this->view = $container->get('view');
        $this->logger = $container->get('logger');
        $this->flash = $container->get('flash');
        $this->em = $container->get('em');
        $this->debug = $container->get('debugbar');
    }

    protected function render(Request $request, Response $response, string $template, array $params = []): Response
    {
        $params['flash'] = $this->flash->getMessage('info');
        return $this->view->render($response, $template, $params);
    }
}
