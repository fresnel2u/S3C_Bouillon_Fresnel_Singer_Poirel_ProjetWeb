<?php

namespace Whishlist\Controllers;

use Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Helpers\Auth;
use Whishlist\Views\AuthView;

class AuthController extends BaseController
{
    /**
     * Permet de gerer la connexion d'un utilisateur
     *
     * @param Request $request requete
     * @param Response $response reponse
     * @param array $args arguments
     * @return Response le contenu de la page renvoyee
     */
    public function login(Request $request, Response $response, array $args): Response
    {
        $username = filter_var($request->getParsedBodyParam('email'), FILTER_SANITIZE_EMAIL);
        $password = filter_var($request->getParsedBodyParam('password'), FILTER_SANITIZE_STRING);

        if ($username === "" || $password == "") {
            throw new Exception("Veuillez remplir tout les champs.");
        } else {
            Auth::attempt($username, $password);

            if (isset($_SESSION['login_success_url'])) {
                $response = $response->withRedirect($_SESSION['login_success_url']);
            } else {
                $response = $response->withRedirect($this->container->router->pathFor('home'));
            }
        }
        return $response;
    }

    /**
     * Permet de gerer la deconnexion d'un utilisateur
     *
     * @param Request $request requete
     * @param Response $response reponse
     * @param array $args arguments
     * @return Response le contenu de la page renvoyee
     */
    public function logout(Request $request, Response $response, array $args): Response
    {
        if (isset($_SESSION['user'])) {
            $_SESSION['user'] = null;
        }
        
        $response = $response->withRedirect($this->container->router->pathFor('home'));
        return $response;
    }

    /**
     * Permet de gerer l'inscription d'un utilisateur
     *
     * @param Request $request requete
     * @param Response $response reponse
     * @param array $args arguments
     * @return Response le contenu de la page renvoyee
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
        if (count($body) !== count($authorizedFields) || $searchEmpty) {
            throw new Exception("Veuillez remplir tout les champs.");
        }

        Auth::checkData($body['email'], $body['password'], $body['password_confirm']);
        Auth::createUser($body['firstname'], $body['lastname'], $body['email'], $body['password']);

        return $response->withRedirect($this->container->router->pathFor('loginPage'));
    }

    /**
     * creer une vue pour afficher la page de login
     *
     * @param Request $request requete
     * @param Response $response reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function getLogin(Request $request, Response $response, array $args): Response
    {
        $v = new AuthView($this->container);
        $response->getBody()->write($v->render(0));
        return $response;
    }

    /**
     * creer une vue pour afficher la page de register
     *
     * @param Request $request requete
     * @param Response $response reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function getRegister(Request $request, Response $response, array $args): Response
    {
        $v = new AuthView($this->container);
        $response->getBody()->write($v->render(1));
        return $response;
    }
}
