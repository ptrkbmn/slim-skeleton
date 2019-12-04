<?php

namespace App\Controllers;

use App\Entities\BaseEntity;
use App\Entities\IUserEntity;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
     * \Slim\Interfaces\RouteParserInterface RouteParserInterface
     */
    private $routeParser;

    /**
     * @var \App\Entities\User User
     */
    protected $user;

    public function __construct(ContainerInterface $container)
    {
        $this->view = $container->get('view');
        $this->logger = $container->get('logger');
        $this->flash = $container->get('flash');
        $this->em = $container->get('em');
        $this->routeParser = $container->get('routeParser');
        if (auth()->check()) {
            $this->user = auth()->user();
        }
    }

    protected function renderDummy(Request $request, Response $response)
    {
        return $this->render($request, $response, "admin/dummy.twig");
    }

    protected function render(Request $request, Response $response, string $template, array $params = []): Response
    {
        $params['flash'] = $this->flash;
        return $this->view->render($response, $template, $params);
    }

    protected function authorize(IUserEntity $entity): bool
    {
        if ($this->user) {
            return $this->user == $entity->getUser();
        }
        return false;
    }

    protected function urlFor(string $routeName, array $data = [], array $queryParams = []): string
    {
        return $this->routeParser->urlFor($routeName, $data, $queryParams);
    }

    protected function redirect(Response $response, string $routeName, int $status = 200)
    {
        return $this->redirectWithParams($response, $routeName, [], $status);
    }

    protected function redirectWithParams(Response $response, string $routeName, array $routeData, int $status = 200)
    {
        if ($status == 401) {
            $this->flash->addMessage('danger', gettext('Unauthorized'));
        } else if ($status == 403) {
            $this->flash->addMessage('danger', gettext('Access denied'));
        }
        return $response
            ->withHeader('Location', $this->urlFor($routeName, $routeData))
            ->withStatus($status);
    }

    public function paginate(string $dql, $page = 1, $limit = 15)
    {
        return $this->paginateParams($dql, [], $page, $limit);
    }

    public function paginateParams(string $dql, array $parameters, $page = 1, $limit = 15, $fetchJoinCollection = false)
    {
        $query = $this->em->createQuery($dql)
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        if (!empty($parameters)) {
            $query->setParameters($parameters);
        }

        $paginator = new Paginator($query,  $fetchJoinCollection);
        $paginator->current = $page;
        $paginator->pagecount = ceil($paginator->count() / $limit);
        return $paginator;
    }

    protected function confirmRouteParams(string $routeName, BaseEntity $entity)
    {
        return ['name' => $routeName, 'data' => ['id' => $entity->id()]];
    }
}
