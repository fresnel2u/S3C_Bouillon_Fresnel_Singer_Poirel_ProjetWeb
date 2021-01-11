<?php

namespace Whishlist\Controllers;

use Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use Throwable;
use Whishlist\Helpers\Auth;
use Whishlist\Helpers\Flashes;
use Whishlist\Views\AuthView;

class AuthController extends BaseController
{
    /**
     * Connecte un utilisateur
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête renvoyee
     */
    public function login(Request $request, Response $response, array $args): Response
    {
        $username = filter_var($request->getParsedBodyParam('email'), FILTER_SANITIZE_EMAIL);
        $password = filter_var($request->getParsedBodyParam('password'), FILTER_SANITIZE_STRING);

        try {
            if ($username === "" || $password == "") {
                throw new Exception("Veuillez remplir tout les champs.");
            } else {
                Auth::attempt($username, $password);
                if (isset($_SESSION['login_success_url'])) {
                    $response = $response->withRedirect($_SESSION['login_success_url']);
                    $_SESSION['login_success_url'] = null;
                } else {
                    $response = $response->withRedirect($this->container->router->pathFor('displayAccount'));
                }
                Flashes::addFlash('Vous vous êtes bien connecté.', 'success');
            }
        } catch (Throwable $throwable) {
            Flashes::addFlash($throwable->getMessage(), 'error');
            $response = $response->withRedirect($this->container->router->pathFor('loginPage'));
        }
        return $response;
    }

    /**
     * Inscription d'un utilisateur
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête renvoyee
     */
    public function register(Request $request, Response $response, array $args): Response
    {
        $authorizedFields = ['firstname', 'lastname', 'email', 'password', 'password_confirm'];
        $body = $request->getParsedBody();

        // Supprime les champs indésirés
        $body = array_filter($body, function ($field) use ($authorizedFields) {
            return in_array($field, $authorizedFields);
        }, ARRAY_FILTER_USE_KEY);

        // Protection injection
        $body = array_map(function ($field) {
            return filter_var($field, FILTER_SANITIZE_STRING);
        }, $body);

        // Chercher si un champ est vide
        $searchEmpty = array_search(function ($field) {
            return $field === '';
        }, $body);

        // Validation
        try {
            if (count($body) !== count($authorizedFields) || $searchEmpty) {
                throw new Exception("Veuillez remplir tout les champs.");
            }
    
            Auth::checkData($body['email'], $body['password'], $body['password_confirm']);
            Auth::createUser($body['firstname'], $body['lastname'], $body['email'], $body['password']);
        } catch (Throwable $throwable) {
            Flashes::addFlash($throwable->getMessage(), 'error');
            return $response->withRedirect($this->container->router->pathFor('registerPage'));
        }
        
        return $response->withRedirect($this->container->router->pathFor('loginPage'));
    }

    /**
     * Crée une vue pour afficher la page de connection
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function getLogin(Request $request, Response $response, array $args): Response
    {
        $v = new AuthView($this->container);
        $response->getBody()->write($v->render(0));
        return $response;
    }

    /**
     * Crée une vue pour afficher la page d'inscription
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function getRegister(Request $request, Response $response, array $args): Response
    {
        $v = new AuthView($this->container);
        $response->getBody()->write($v->render(1));
        return $response;
    }

    /**
     * Déconnecte d'un utilisateur
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête renvoyee
     */
    public function logout(Request $request, Response $response, array $args): Response
    {
        if (isset($_SESSION['user'])) {
            $_SESSION['user'] = null;
            Flashes::addFlash('Déconnecté', 'success');
        }
        
        $response = $response->withRedirect($this->container->router->pathFor('login'));
        return $response;
    }
}
