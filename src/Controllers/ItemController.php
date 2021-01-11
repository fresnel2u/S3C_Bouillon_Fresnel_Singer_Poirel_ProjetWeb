<?php

namespace Whishlist\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Models\Item;
use Whishlist\Views\ItemView;
use Whishlist\Helpers\Auth;
use Whishlist\helpers\RedirectHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;
use Whishlist\Helpers\Flashes;

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
            Flashes::addFlash('Impossible de créer l\'item', 'error');
            return $response->withRedirect($this->container->router->pathFor('displayAllItems'));
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
            Flashes::addFlash("L'item " . $args['id'] . " n'a pas été trouvé", 'error');
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
            $response->getBody()->write($v->render(2));
            return $response;
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("L'item " . $args['id'] . " n'a pas été trouvé", 'error');
            return $response->withRedirect($this->container->router->pathFor('displayAllItems'));
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

            Flashes::addFlash("Item modifié avec succès", 'success');
            return $response->withRedirect($this->container->router->pathFor('displayAllItems'));
        } catch (Throwable $e) {
            Flashes::addFlash("Impossible de modifier l'item", 'error');
            return $response->withRedirect($this->container->router->pathFor('displayAllItems'));
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

            Flashes::addFlash("Item supprimé avec succès", 'success');
            return $response->withRedirect($this->container->router->pathFor('displayAllItems'));
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("Impossible de supprimer l'item", 'error');
            return $response->withRedirect($this->container->router->pathFor('displayAllItems'));
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

            if($item->user_id === null) {
                $item->user_id = $user['id'];
                $item->save();
                Flashes::addFlash("Item réservé avec succès", 'success');
            } else {
                Flashes::addFlash("Item déjà réservé", 'error');
            }
    
            return $response->withRedirect($redirectUrl);
        } catch (\Throwable $th) {
            Flashes::addFlash("Impossible de réservé l'item", 'error');
            return $response->withRedirect($this->container->router->pathFor('displayAllItems'));
        }
    }
}