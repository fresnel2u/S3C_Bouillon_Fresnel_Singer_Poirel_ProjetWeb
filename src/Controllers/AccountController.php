<?php

namespace Whishlist\Controllers;

use Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Models\User;
use Whishlist\Helpers\Auth;
use Whishlist\Helpers\Flashes;
use Whishlist\Views\AccountView;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Whishlist\Helpers\Validator;
use Whishlist\Models\WishList;

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

            try {
                Validator::failIfEmptyOrNull($body, ['password', 'password_confirm']);
            } catch(Exception $e) {
                Flashes::addFlash($e->getMessage(), 'error');
                return $response->withRedirect($this->pathFor('editAccountPage'));
            }
            
            $user->firstname = $body['firstname'];
            $user->lastname = $body['lastname'];
        
            $pass = $body['password'];
            $password_confirm = $body['password_confirm'];
            if ($pass !== "" || $password_confirm !== "") {
                if ($pass !== $password_confirm) {
                    throw new Exception('les mots de passe ne correspondent pas');
                }
                $user->password = password_hash($pass, PASSWORD_DEFAULT);
                Auth::setUser(null);
                Flashes::addFlash('Mot de passe changé, déconnexion', 'success');
                $response = $response->withRedirect($this->pathFor('home'));
                
            } else {
                Flashes::addFlash('Informations modifiées', 'success');
                $response = $response->withRedirect($this->pathFor('displayAccount'));
                Auth::setUser($user);
            }
            $user->save();

            return $response;
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("Impossible d'éditer le compte.", 'error');
            return $response->withRedirect($this->pathFor('editAccountPage'));
        } catch (Exception $e) {
            Flashes::addFlash($e->getMessage(), 'error');
            return $response->withRedirect($this->pathFor('editAccountPage'));
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
            $user = User::findOrFail(Auth::getUser()['id']);
            $user->delete();
            Auth::setUser(null);
            Flashes::addFlash('Compte supprimé.', 'success');
            return $response->withRedirect($this->pathFor('home'));
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash('Impossible de supprimer le compte', 'error');
            return $response->withRedirect($this->pathFor('home'));
        }
    }

    /**
     * Liste de tous les createur d'au moins une liste publique
     */
    public function allPublicAccounts(Request $request, Response $response, array $args): Response
    {
        $accounts = WishList::where('is_public', true)->join('users', 'lists.user_id', '=', 'users.id')->distinct('users.id')->get()->all();
        $v = new AccountView($this->container, ['creators' => $accounts]);
        $response->getBody()->write($v->render(2));
        return $response;
    }
}
