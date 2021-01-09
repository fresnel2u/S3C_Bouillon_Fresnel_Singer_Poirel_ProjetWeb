<?php

namespace Whishlist\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Models\Item;
use Whishlist\Views\ItemView;
use Whishlist\Helpers\Auth;
use Whishlist\helpers\RedirectHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ItemController extends BaseController
{
    /**
     * Ajouter un item (page)
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response réponse à la requête
     */
    public function newItemPage(Request $request, Response $response, array $args): Response
    {
        $v = new ItemView($this->container);
        $response->getBody()->write($v->render(0));
        return $response;
    }

    /**
     * Ajouter un item
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response
     */
    public function newItem(Request $request, Response $response, array $args): Response
    {
        try {
            $body = $request->getParsedBody();
            $body = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $body);

            $item = new Item();
            $item->list_id = $body['list_id'];
            $item->name = $body['name'];
            $item->description = $body['description'];
            $item->image = $body['image'];
            $item->url = $body['url'];
            $item->price = $body['price'];
            $item->save();

            return $response->withRedirect($this->container->router->pathFor('displayAllItems'));
        } catch (ModelNotFoundException $e) {
            $response->withStatus(400);
            $response->withRedirect($this->container->router->pathFor('displayAllItems'));
            return $response;
        }
    }
    
    /**
     * Crée une vue pour afficher les items
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function displayAllItems(Request $request, Response $response, array $args): Response
    {
        try {
            $items = Item::all();

            $v = new ItemView($this->container, ['items' => $items]);
            $response->getBody()->write($v->render(1));
            return $response;
        } catch (ModelNotFoundException $e) {
            $response->getBody()->write("<h1 style=\"text-align : center;\"> L'item " . $args['id'] . " n'a pas été trouvé.</h1>");
            return $response;
        }
    }

    /**
     * Crée une vue pour afficher un item
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function displayItem(Request $request, Response $response, array $args): Response
    {
        try {
            $item = Item::findOrFail($args['id']);

            $v = new ItemView($this->container, ['item' => $item]);
            $response->getBody()->write($v->render(2));
            return $response;
        } catch (ModelNotFoundException $e) {
            $response->getBody()->write("<h1 style=\"text-align : center;\"> L'item " . $args['id'] . " n'a pas été trouvé.</h1>");
            return $response;
        }
    }

    /**
     * Éditer un item (page)
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response réponse à la requête
     */
    public function editItemPage(Request $request, Response $response, array $args): Response
    {
        try {
            $item = Item::findOrFail($args['id']);

            $v = new ItemView($this->container, ['item' => $item]);
            $response->getBody()->write($v->render(3));
            return $response;
        } catch (ModelNotFoundException $e) {
            $response->withStatus(400);
            $response->withRedirect($this->container->router->pathFor('displayAllItems'));
            return $response;
        }
    }

    /**
     * Éditer un item
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response
     */
    public function editItem(Request $request, Response $response, array $args): Response
    {
        try {
            $item = Item::findOrFail($args['id']);

            $body = $request->getParsedBody();
            $body = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $body);

            $item->list_id = $body['list_id'];
            $item->name = $body['name'];
            $item->description = $body['description'];
            $item->image = $body['image'];
            $item->url = $body['url'];
            $item->price = $body['price'];
            $item->save();

            return $response->withRedirect($this->container->router->pathFor('displayAllItems'));
        } catch (ModelNotFoundException $e) {
            $response->withStatus(400);
            $response->withRedirect($this->container->router->pathFor('displayAllItems'));
            return $response;
        }
    }

    /**
     * Supprimer un item
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response
     */
    public function deleteItem(Request $request, Response $response, array $args): Response
    {
        try {
            $item = Item::findOrFail($args['id']);
            $item->delete();

            return $response->withRedirect($this->container->router->pathFor('displayAllItems'));
        } catch (ModelNotFoundException $e) {
            $response->withStatus(400);
            $response->withRedirect($this->container->router->pathFor('displayAllItems'));
            return $response;
        }
    }

    /**
     * Réservation d'un item
     * 
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function lockItem(Request $request, Response $response, array $args): Response
    {
        try {
            $item = Item::findOrFail($args['id']);
            $user = Auth::getUser();
            $redirectUrl = $this->container->router->pathFor('displayList', ['id' => $item->list_id]);
    
            if ($user === null) {
                return RedirectHelper::loginAndRedirect($response, $redirectUrl);
            }
    
            $item->user_id = $user['id'];
            $item->save();
            return $response->withRedirect($redirectUrl);
        } catch (\Throwable $th) {
            $response->withStatus(400);
            $response->withRedirect($this->container->router->pathFor('displayAllItems'));
            return $response;
        }
    }
}