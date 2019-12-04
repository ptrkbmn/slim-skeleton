<?php

namespace App\Controllers;

use App\Entities\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class AuthController extends BaseController
{

    private function auth(string $email, string $pswd)
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::Class)->findOneBy(array('email' => $email));
        if ($user == null) return null;
        if (!password_verify($pswd, $user->getPassword())) return null;

        return $user;
    }

    public function login(Request $request, Response $response, array $args = []): Response
    {
        if ($request->getMethod() == 'POST') {
            $data = $request->getParsedBody();

            if (empty($data["email"]) || empty($data["pswd"])) {
                $this->flash->addMessage('danger', gettext('Empty value in login/password'));
                return $this->redirect($response, 'login', 302);
            }

            // Check the user username / pass
            $user = $this->auth($data["email"], $data['pswd']);
            if ($user == null) {
                $this->flash->addMessage('danger', gettext('Invalid login/password'));
                return $this->redirect($response, 'login', 302);
            }

            $session = $request->getAttribute('session');
            $session['signedin'] = true;
            $session['userid'] = $user->id();

            $this->flash->addMessage('info', gettext('Login successful'));
            return $this->redirect($response, 'admin');
        }

        if (auth()->check()) {
            return $this->redirect($response, 'home', 302);
        }

        return $this->render($request, $response, 'public/auth/login.twig', [
            'user' => $request->getAttribute('user')
        ]);
    }

    public function logout(Request $request, Response $response, array $args = []): Response
    {
        $session = $request->getAttribute('session');
        $session['signedin'] = false;
        unset($session['userid']);
        return $response->withStatus(302)->withHeader('Location', '/');
    }

    public function signup(Request $request, Response $response, array $args = []): Response
    {
        if ($request->getMethod() == 'POST') {
            $data = $request->getParsedBody();

            // Validate user input...
            if (empty($data["email"]) || empty($data["pswd"])) {
                $this->flash->addMessage('danger', gettext('Empty value in login/password'));
                return $this->redirect($response, 'login', 302);
            }

            $user = new User();
            $user->setEmail($data["email"]);
            $user->setPassword($data["pswd"]);
            $this->em->persist($user);
            $this->em->flush();

            $this->flash->addMessage('info', gettext('Signed up successfully'));
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        return $this->render($request, $response, 'public/auth/signup.twig', [
            'user' => $request->getAttribute('user')
        ]);
    }
}
