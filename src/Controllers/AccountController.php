<?php

namespace Whishlist\Controllers;

session_start();

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Models\User;
use Whishlist\Views\AccountView;
use Whishlist\helpers\Authentication;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AccountController extends BaseController
{
    /**
     * creer une vue pour afficher les informations du compte utilisateur
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function displayAccount(Request $rq, Response $rs, array $args): Response
    {
        $user = Authentication::getUser();

        $v = new AccountView($this->container, ['user' => $user]);
        $rs->getBody()->write($v->render(0));
        return $rs;
    }

    /**
     * creer une vue pour afficher l'edition du compte utilisateur
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function displayEditAccount(Request $rq, Response $rs, array $args): Response
    {
        $user = Authentication::getUser();

        $v = new AccountView($this->container, ['user' => $user]);
        $rs->getBody()->write($v->render(1));
        return $rs;
    }

    /**
     * sauvegarde les changements effectues lors de l'edition d'un compte
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function editAccount(Request $rq, Response $rs, array $args): Response
    {
        try {
            $user_session = Authentication::getUser();

            $user = User::findOrFail($user_session->id);
            $post = $rq->getParsedBody();

            $post = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $post);

            $user->firstname = $post['firstname'];
            $user_session->firstname = $post['firstname'];

            $user->lastname = $post['lastname'];
            $user_session->lastname = $post['lastname'];

            $user->email = $post['email'];
            $user_session->email = $post['email'];

            $user->save();

            return $rs->withRedirect($this->container->router->pathFor('editAccountPage'));
        } catch (ModelNotFoundException $e) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('editAccountPage'));
            return $rs;
        }
    }
    
    /**
     * Supprimer un utilisateur
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args 
     * @return Response
     */
    public function deleteAccount(Request $rq, Response $rs, array $args): Response
    {
        try {
            $user = User::findOrFail($_SESSION['user']->id);
            $user->delete();
            $_SESSION['user'] = null;

            return $rs->withRedirect($this->container->router->pathFor('home'));
        } catch (ModelNotFoundException $e) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('home'));
            return $rs;
        }
    }
}
