<?php

namespace Whishlist\Controllers;

use Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Helpers\Auth;
use Whishlist\Views\ListView;
use Whishlist\Helpers\Flashes;
use Whishlist\Models\WishList;
use Whishlist\Models\ListMessage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Whishlist\Helpers\Validator;

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
                Validator::failIfEmptyOrNull($body);
            } catch(Exception $e) {
                Flashes::addFlash($e->getMessage(), 'error');
                return $response->withRedirect($this->container->router->pathFor('newListPage'));
            }

            $list = new WishList();
            $list->user_id = Auth::getUser()['id'];
            $list->title = $body['title'];
            $list->description = $body['description'];
            $list->expiration = $body['expiration'];
            $list->token = $body['token'];
            $list->save();

            if ($list->token === '') {
                $list->token = bin2hex($list->id . random_bytes(10));
                $list->save();
            }

            return $response->withRedirect($this->container->router->pathFor('displayAllList'));
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("Impossible d'ajouter la liste", 'error');
            return $response->withRedirect($this->container->router->pathFor('displayAllList'));
        }
    }

    /**
     * Crée une vue pour afficher la liste des listes de souhaits
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function displayAllList(Request $request, Response $response, array $args): Response
    {
        $lists = WishList::all();
        if ($lists->count() === 0) {
            $response->getBody()->write("<h1 style=\"text-align : center;\"> Aucune liste n'a été trouvée.</h1>");
        }
        $v = new ListView($this->container, ['lists' => $lists]);
        $response->getBody()->write($v->render(1));
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
            return $response->withRedirect($this->container->router->pathFor('home'));
        }
        
        $v = new ListView($this->container, [
            'list' => $list,
            'items' => $list->items,
            'messages' => $list->messages
        ]);
        $response->getBody()->write($v->render(2));
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
            $list = WishList::findOrFail($args['id']);
            $v = new ListView($this->container, ['list' => $list]);
            $response->getBody()->write($v->render(3));
            return $response;
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("La liste n'existe pas", 'error');
            return $response->withRedirect($this->container->router->pathFor('displayAllList'));
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
            $list = WishList::findOrFail($args['id']);

            $body = $request->getParsedBody();
            $body = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $body);

            try {
                Validator::failIfEmptyOrNull($body);
            } catch(Exception $e) {
                Flashes::addFlash($e->getMessage(), 'error');
                return $response->withRedirect($this->container->router->pathFor('editListPage', ['id' => $args['id']]));
            }

            $list->title = $body['title'];
            $list->description = $body['description'];
            $list->expiration = $body['expiration'];
            $list->token = $body['token'] !== '' ? $body['token'] : bin2hex($list->id . random_bytes(10));
            $list->save();

            Flashes::addFlash("Liste modifiée", 'success');
            return $response->withRedirect($this->container->router->pathFor('displayAllList'));
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("Impossible de modifier la liste", 'error');
            return $response->withRedirect($this->container->router->pathFor('displayAllList'));
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
            $list = WishList::find($args['id']);
            $list->delete();

            Flashes::addFlash("Liste supprimée", 'success');
            return $response->withRedirect($this->container->router->pathFor('displayAllList'));
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("Impossible de supprimer la liste", 'error');
            return $response->withRedirect($this->container->router->pathFor('displayAllList'));
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
            $list = WishList::with('items')->findOrFail($args['id']);
            if(!$list->isExpired()) {
                Flashes::addFlash("Impossible de consulter les réservations d'une liste avant échéance", 'error');
                return $response->withRedirect($this->container->router->pathFor('displayAllList'));
            }

            $v = new ListView($this->container, [
                'list' => $list,
                'items' => $list->items
            ]);
            $response->getBody()->write($v->render(4));
            return $response;
        } catch (\Throwable $th) {
            Flashes::addFlash("Impossible de consulter les réservations de la liste.", 'error');
            return $response->withRedirect($this->container->router->pathFor('displayAllList'));
        }
    }

    public function addListMessage(Request $request, Response $response, array $args): Response
    {
        try {
            $list = WishList::select('id')->where('token', '=', $args['token'])->firstOrFail();

            $body = $request->getParsedBody();
            $body = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $body);

            $message = new ListMessage();
            $message->list_id = $list->id;
            $message->user_id = Auth::getUser()['id']; 
            $message->message = $body['message'];
            $message->save();

            return $response->withRedirect($this->container->router->pathFor('displayList', [
                'token' => $args['token']
            ]));
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("Impossible d'ajouter la liste", 'error');
            return $response->withRedirect($this->container->router->pathFor('displayList', [
                'token' => $args['token']
            ]));
        }
    }
}