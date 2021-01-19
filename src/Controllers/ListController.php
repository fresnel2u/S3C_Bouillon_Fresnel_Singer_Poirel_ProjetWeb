<?php

namespace Whishlist\Controllers;

use Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Helpers\Auth;
use Whishlist\Views\ListView;
use Whishlist\Helpers\Flashes;
use Whishlist\Models\WishList;
use Whishlist\Helpers\Validator;
use Whishlist\Models\ListMessage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;
use Whishlist\Models\User;
use Whishlist\Models\UsersLists;

class ListController extends BaseController
{
    /**
     * Crée une vue pour afficher le formulaire de creation d'une liste
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function newListPage(Request $request, Response $response, array $args): Response
    {
        $v = new ListView($this->container);
        $response->getBody()->write($v->render(0));
        return $response;
    }

    /**
     * Crée une nouvelle liste et Crée une vue qui affiche la liste des listes de souhaits
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response réponse à la requête
     */
    public function newList(Request $request, Response $response, array $args): Response
    {
        try {
            $body = $request->getParsedBody();
            $body = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $body);

            try {
                Validator::failIfEmptyOrNull($body, ['token', 'edit_token']);
            } catch (Exception $e) {
                Flashes::addFlash($e->getMessage(), 'error');
                return $response->withRedirect($this->pathFor('newListPage'));
            }

            $list = new WishList();
            $list->user_id = Auth::getUser()['id'];
            $list->title = $body['title'];
            $list->description = $body['description'];
            $list->expiration = $body['expiration'];
            $list->token = $body['token'];
            $list->modification_token = $body['edit_token'];
            $list->is_public = $body['is_public'] ? true : false;

            if ($list->token === '') {
                $list->token = bin2hex($list->id . random_bytes(10));
                $list->save();
            }

            if ($list->modification_token === '') {
                $list->modification_token = bin2hex($list->id . random_bytes(10));
                $list->save();
            }
            $list->save();

            return $response->withRedirect($this->pathFor('displayAllLists'));
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("Impossible d'ajouter la liste", 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }
    }

    /**
     * Page pour joindre une liste à l'utilisateur avec un token de modification
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function joinListPage(Request $request, Response $response, array $args) {
        $v = new ListView($this->container);
        $response->getBody()->write($v->render(7));      
    }

    /**
     * Page pour joindre une liste à l'utilisateur avec un token de modification
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function joinList(Request $request, Response $response, array $args) {
        $body = $request->getParsedBody();
        try {
            $token = $body['token'] ?? null;
            if($token === null)
                throw new Exception();
            $list = WishList::where('modification_token', $token)->firstOrFail();
            $user = Auth::getUser();
            $existing = UsersLists::where('list_id', $list->id)->where('user_id', $user['id'])->first();
            if($existing === null) {
                $invitation = new UsersLists();
                $invitation->user_id = $user['id'];
                $invitation->list_id = $list->id;
                $invitation->save();
                Flashes::addFlash('Liste ajoutée', 'success');
            } else {
                Flashes::addFlash('Vous avez déjà rejoint cette liste', 'warning');
            }
            return $response->withRedirect($this->pathFor('displayAllLists'));

        } catch(Throwable $throwable) {
            Flashes::addFlash('Token invalide', 'error');
            Flashes::addFlash($throwable->getMessage(), 'error');
            return $response->withRedirect($this->pathFor('joinListPage'));
        }
    }

    /**
     * Crée une vue pour afficher la liste des listes de souhaits dont on est le créateur / qu'on a été invité à modifier
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function displayAllLists(Request $request, Response $response, array $args): Response
    {
        $user = User::find(Auth::getUser()['id']);
        $lists = $user->lists()->get()->all();
        $invited = $user->invitedLists()->get()->all();

        if (count($lists) + count($invited) === 0) {
            $response->getBody()->write("<h1 style=\"text-align : center;\"> Aucune liste n'a été trouvée.</h1>");
        }
        $v = new ListView($this->container, ['lists' => $lists, 'invitedLists' => $invited]);
        $response->getBody()->write($v->render(1));
        return $response;
    }

    /**
     * Affichage des listes publiques
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function publicLists(Request $request, Response $response, array $args): Response
    {
        $lists = WishList::where('is_public', true)
            ->whereRaw('expiration > NOW()')
            ->orderBy('expiration')
            ->get();

        $v = new ListView($this->container, ['lists' => $lists]);
        $response->getBody()->write($v->render(2));
        return $response;
    }

    /**
     * Crée une vue pour afficher les items d'une liste
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function displayList(Request $request, Response $response, array $args): Response
    {
        $list = WishList::with('items')
            ->with('user')
            ->with('messages')
            ->where('token', $args['token'])
            ->first();

        if (!$list) {
            Flashes::addFlash("Liste introuvable", 'error');
            return $response->withRedirect($this->pathFor('home'));
        }

        $v = new ListView($this->container, [
            'list' => $list,
            'items' => $list->items,
            'messages' => $list->messages
        ]);
        $response->getBody()->write($v->render(3));
        return $response;
    }

    /**
     * Crée une vue pour afficher le formulaire d'édition d'une liste
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function editListPage(Request $request, Response $response, array $args): Response
    {
        try {
            $list = WishList::where('modification_token', $args['token'])->firstOrFail();
            $v = new ListView($this->container, ['list' => $list]);
            $response->getBody()->write($v->render(4));
            return $response;
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("La liste n'existe pas", 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }
    }

    /**
     * Modifie une liste existante
     * 
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response réponse à la requête
     */
    public function editList(Request $request, Response $response, $args): Response
    {
        try {
            $list = WishList::where('modification_token', $args['token'])->firstOrFail();
            $body = $request->getParsedBody();
            $body = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $body);

            try {
                Validator::failIfEmptyOrNull($body, ['token', 'edit_token']);
            } catch (Exception $e) {
                Flashes::addFlash($e->getMessage(), 'error');
                return $response->withRedirect($this->pathFor('editListPage', ['token' => $list->modification_token]));
            }

            $list->title = $body['title'];
            $list->description = $body['description'];
            $list->expiration = $body['expiration'];
            $list->token = $body['token'] !== '' ? $body['token'] : bin2hex($list->id . random_bytes(10));
            $list->modification_token = $body['edit_token'] !== '' ? $body['edit_token'] : bin2hex($list->id . random_bytes(10));
            $list->is_public = $body['is_public'] ? true : false;
            $list->save();

            Flashes::addFlash("Liste modifiée", 'success');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("Impossible de modifier la liste", 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }
    }

    /**
     * Supprime une liste
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args 
     * @return Response
     */
    public function deleteList(Request $request, Response $response, array $args): Response
    {
        try {
            $list = WishList::find($args['list_id']);
            $list->delete();

            Flashes::addFlash("Liste supprimée", 'success');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("Impossible de supprimer la liste", 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }
    }

    /**
     * Crée une vue pour afficher les reservations et les messages d'une liste après échéance.
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function displayListResults(Request $request, Response $response, array $args): Response
    {
        try {
            $list = WishList::with('items')->findOrFail($args['list_id']);
            if (!$list->isExpired()) {
                Flashes::addFlash("Impossible de consulter les réservations d'une liste avant échéance", 'error');
                return $response->withRedirect($this->pathFor('displayAllLists'));
            }

            $v = new ListView($this->container, [
                'list' => $list,
                'items' => $list->items
            ]);
            $response->getBody()->write($v->render(5));
            return $response;
        } catch (\Throwable $th) {
            Flashes::addFlash("Impossible de consulter les réservations de la liste.", 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }
    }

    /**
     * Ajoute un message public sur la page d'une liste 
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function addListMessage(Request $request, Response $response, array $args): Response
    {
        try {
            $list = WishList::select('id')->where('token', '=', $args['token'])->firstOrFail();

            $body = $request->getParsedBody();
            $body = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $body);

            if($body['message'] === "") {
                Flashes::addFlash("Veuillez mettre du contenu dans votre message", 'error');
            } else {
                $message = new ListMessage();
                $message->list_id = $list->id;
                $message->user_id = Auth::getUser()['id'];
                $message->message = $body['message'];
                $message->save();
                Flashes::addFlash("Message ajouté", 'success');
            }
            
            return $response->withRedirect($this->container->router->pathFor('displayList', [
                'token' => $args['token']
            ]));
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("Impossible d'ajouter le message", 'error');
            return $response->withRedirect($this->container->router->pathFor('displayList', [
                'token' => $args['token']
            ]));
        }
    }

    /**
     * Supprime un message public sur la page d'une list
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function deleteListMessage(Request $request, Response $response, array $args): Response
    {
        try {
            $message = ListMessage::where('id', $args['id'])->first();
            $message->delete();

            Flashes::addFlash("Message supprimé", 'success');
            return $response->withRedirect($this->container->router->pathFor('displayList', [
                'token' => $args['token']
            ]));
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("Impossible de supprimer le message", 'error');
            return $response->withRedirect($this->container->router->pathFor('displayList', [
                'token' => $args['token']
            ]));
        }
    }
    
    /**
     * Crée une vue pour pouvoir modifier un message public d'une liste
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function editListMessagePage(Request $request, Response $response, array $args): Response
    {
        try {
            $list = WishList::with('items')
            ->with('user')
            ->with('messages')
            ->where('token', $args['token'])
            ->first();
            $v = new ListView($this->container, [
                'list' => $list,
                'items' => $list->items,
                'messages' => $list->messages
            ]);
            $response->getBody()->write($v->render(6));
            return $response;
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("Impossible de modifier le message", 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }
    } 

    /**
     * Modifie le message public d'une list
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function editListMessage(Request $request, Response $response, array $args): Response
    {
        try {
            $message = ListMessage::where('id', $args['id'])->first();

            $body = $request->getParsedBody();
            $body = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $body);

            try {
                Validator::failIfEmptyOrNull($body, ['token']);
            } catch (Exception $e) {
                Flashes::addFlash($e->getMessage(), 'error');
                return $response->withRedirect($this->pathFor('editListPage', ['token' => $list->modification_token]));
            }

            if($body['message'] === "") {
                Flashes::addFlash("Veuillez mettre du contenu dans votre message", 'error');
            } else {
                $message->message = $body['message'];
                $message->save();
                Flashes::addFlash("Message modifié", 'success');
            }

            return $response->withRedirect($this->container->router->pathFor('displayList', [
                'token' => $args['token']
            ]));
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("Impossible de modifier le message", 'error');
            return $response->withRedirect($this->container->router->pathFor('displayList', [
                'token' => $args['token']
            ]));
        }
    }
}
