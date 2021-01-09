<?php

namespace Whishlist\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Models\User;
use Whishlist\Helpers\Auth;
use Whishlist\Views\AccountView;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AccountController extends BaseController
{
    /**
     * Crée une vue pour afficher les informations du compte utilisateur
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function displayAccount(Request $request, Response $response, array $args): Response
    {
        $user = Auth::getUser();

        $v = new AccountView($this->container, ['user' => $user]);
        $response->getBody()->write($v->render(0));
        return $response;
    }

    /**
     * Crée une vue pour afficher l'edition du compte utilisateur
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function displayEditAccount(Request $request, Response $response, array $args): Response
    {
        $user = Auth::getUser();

        $v = new AccountView($this->container, ['user' => $user]);
        $response->getBody()->write($v->render(1));
        return $response;
    }

    /**
     * Sauvegarde les changements effectues lors de l'edition d'un compte
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function editAccount(Request $request, Response $response, array $args): Response
    {
        try {
            $user_session = Auth::getUser();
            $user = User::findOrFail($user_session['id']);
            
            $body = $request->getParsedBody();
            $body = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $body);

            $user->firstname = $body['firstname'];
            $user_session->firstname = $body['firstname'];

            $user->lastname = $body['lastname'];
            $user_session->lastname = $body['lastname'];

            $user->email = $body['email'];
            $user_session->email = $body['email'];

            $user->save();

            return $response->withRedirect($this->container->router->pathFor('editAccountPage'));
        } catch (ModelNotFoundException $e) {
            $response->withStatus(400);
            $response->withRedirect($this->container->router->pathFor('editAccountPage'));
            return $response;
        }
    }
    
    /**
     * Supprimer un utilisateur
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args 
     * @return Response
     */
    public function deleteAccount(Request $request, Response $response, array $args): Response
    {
        try {
            $user = User::findOrFail($_SESSION['user']->id);
            $user->delete();
            $_SESSION['user'] = null;

            return $response->withRedirect($this->container->router->pathFor('home'));
        } catch (ModelNotFoundException $e) {
            $response->withStatus(400);
            $response->withRedirect($this->container->router->pathFor('home'));
            return $response;
        }
    }
}
