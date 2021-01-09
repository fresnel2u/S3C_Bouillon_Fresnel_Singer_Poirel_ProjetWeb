<?php

namespace Whishlist\Controllers;

session_start();

use Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use \Whishlist\Views\AuthView;
use \Whishlist\helpers\Authentication;

class AuthController extends BaseController
{
    /**
     * Permet de gerer la connexion d'un utilisateur
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page renvoyee
     */
    public function login(Request $rq, Response $rs, array $args): Response
    {
        $username = filter_var($rq->getParsedBodyParam('email'), FILTER_SANITIZE_EMAIL);
        $password = filter_var($rq->getParsedBodyParam('password'), FILTER_SANITIZE_STRING);

        if ($username === "" || $password == "") {
            throw new Exception("Veuillez remplir tout les champs.");
        } else {
            Authentication::Authenticate($username, $password);
            if (session_status() == PHP_SESSION_NONE)
                session_start();
            if (isset($_SESSION['login_success_url']))
                $rs = $rs->withRedirect($_SESSION['login_success_url']);
            else
                $rs = $rs->withRedirect($this->container->router->pathFor('home'));
        }
        return $rs;
    }

    /**
     * Permet de gerer la deconnexion d'un utilisateur
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page renvoyee
     */
    public function logout(Request $rq, Response $rs, array $args): Response
    {
        if (isset($_SESSION['user'])) {
            $_SESSION['user'] = null;
        }
        $rs = $rs->withRedirect($this->container->router->pathFor('home'));

        return $rs;
    }

    /**
     * Permet de gerer l'inscription d'un utilisateur
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page renvoyee
     */
    public function register(Request $rq, Response $rs, array $args): Response
    {

        $firstname = filter_var($rq->getParsedBodyParam('firstname'), FILTER_SANITIZE_STRING);
        $lastname = filter_var($rq->getParsedBodyParam('lastname'), FILTER_SANITIZE_STRING);
        $email = filter_var($rq->getParsedBodyParam('email'), FILTER_SANITIZE_EMAIL);
        $password = filter_var($rq->getParsedBodyParam('password'), FILTER_SANITIZE_STRING);
        $passwordconfirm = filter_var($rq->getParsedBodyParam('password_confirm'), FILTER_SANITIZE_STRING);
        echo ($password);
        echo ($passwordconfirm);

        if ($firstname === "" || $lastname === "" || $email === "" || $password === "" || $passwordconfirm === "") {
            throw new Exception("Veuillez remplir tout les champs.");
        }
        Authentication::CheckData($email, $password, $passwordconfirm);
        Authentication::CreateUser($firstname, $lastname, $email, $password);

        return $rs->withRedirect($this->container->router->pathFor('loginPage'));
    }

    /**
     * creer une vue pour afficher la page de login
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function getLogin(Request $rq, Response $rs, array $args): Response
    {
        $v = new AuthView($this->container);
        $rs->getBody()->write($v->render(0));
        return $rs;
    }

    /**
     * creer une vue pour afficher la page de register
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function getRegister(Request $rq, Response $rs, array $args): Response
    {
        $v = new AuthView($this->container);
        $rs->getBody()->write($v->render(1));
        return $rs;
    }
}