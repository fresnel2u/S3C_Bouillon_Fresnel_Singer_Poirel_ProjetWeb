<?php

namespace Whishlist\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Models\Item;
use Whishlist\Views\ListView;
use Whishlist\Models\WishList;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Whishlist\Helpers\Auth;
use Whishlist\Helpers\Flashes;

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

            $list = new WishList();
            $list->user_id = Auth::getUser()['id'];
            $list->title = $body['title'];
            $list->description = $body['description'];
            $list->expiration = $body['expiration'];
            $list->token = $body['token'];
            $list->save();

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
        try {
            $list = WishList::with('items')->findOrFail($args['id']);
            
            $v = new ListView($this->container, [
                'list' => $list,
                'items' => $list->items
            ]);
            $response->getBody()->write($v->render(2));
            return $response;
        } catch (\Throwable $th) {
            Flashes::addFlash("Impossible de consulter la liste.", 'error');
            return $response->withRedirect($this->container->router->pathFor('displayAllList'));
        }
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

            $list->title = $body['title'];
            $list->description = $body['description'];
            $list->expiration = $body['expiration'];
            $list->token = $body['token'];
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
}