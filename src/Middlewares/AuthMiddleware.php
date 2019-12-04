<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Entities\User;
use App\Helpers\Auth;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Views\Twig;

class AuthMiddleware implements Middleware
{
    private $em;
    private $twig;
    private $auth;

    public function __construct(Auth $auth, EntityManagerInterface $em, Twig $twig)
    {
        $this->auth = $auth;
        $this->em = $em;
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $session = $request->getAttribute('session');
        $user = null;
        if ($session['signedin']) {
            $query = $this->em->createQuery('SELECT u, r FROM App\Entities\User u '
                . 'JOIN u.role r WHERE u.id = ?1');
            $query->setParameter(1, intval($session['userid']));
            $user = $query->getSingleResult();
        }

        $this->auth->init($session['signedin'], $user);

        $env = $this->twig->getEnvironment();
        $env->addGlobal("auth", $this->auth);
        return $handler->handle($request);
    }
}
