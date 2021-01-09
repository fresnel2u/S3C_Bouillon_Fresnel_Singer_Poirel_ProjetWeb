<?php

namespace Whishlist\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Models\Item;
use Whishlist\Views\ItemView;
use Whishlist\helpers\Authentication;
use Whishlist\helpers\RedirectHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ItemController extends BaseController
{
    /**
     * Ajouter un item (page)
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response le contenu de la page
     */
    public function newItemPage(Request $rq, Response $rs, array $args): Response
    {
        $v = new ItemView($this->container);
        $rs->getBody()->write($v->render(0));
        return $rs;
    }

    /**
     * Ajouter un item
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response
     */
    public function newItem(Request $rq, Response $rs, array $args): Response
    {
        try {
            $post = $rq->getParsedBody();
            $post = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $post);

            $item = new Item();
            $item->list_id = $post['list_id'];
            $item->name = $post['name'];
            $item->description = $post['description'];
            $item->image = $post['image'];
            $item->url = $post['url'];
            $item->price = $post['price'];
            $item->save();

            return $rs->withRedirect($this->container->router->pathFor('displayAllItems'));
        } catch (ModelNotFoundException $e) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('displayAllItems'));
            return $rs;
        }
    }
    
    /**
     * creer une vue pour afficher les items
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function displayAllItems(Request $rq, Response $rs, array $args): Response
    {
        try {
            $items = Item::all();

            $v = new ItemView($this->container, ['items' => $items]);
            $rs->getBody()->write($v->render(1));
            return $rs;
        } catch (ModelNotFoundException $e) {
            $rs->getBody()->write("<h1 style=\"text-align : center;\"> L'item " . $args['id'] . " n'a pas été trouvé.</h1>");
            return $rs;
        }
    }

    /**
     * creer une vue pour afficher un item
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function displayItem(Request $rq, Response $rs, array $args): Response
    {
        try {
            $item = Item::findOrFail($args['id']);

            $v = new ItemView($this->container, ['item' => $item]);
            $rs->getBody()->write($v->render(2));
            return $rs;
        } catch (ModelNotFoundException $e) {
            $rs->getBody()->write("<h1 style=\"text-align : center;\"> L'item " . $args['id'] . " n'a pas été trouvé.</h1>");
            return $rs;
        }
    }

    /**
     * Editer un item (page)
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response le contenu de la page
     */
    public function editItemPage(Request $rq, Response $rs, array $args): Response
    {
        try {
            $item = Item::findOrFail($args['id']);

            $v = new ItemView($this->container, ['item' => $item]);
            $rs->getBody()->write($v->render(3));
            return $rs;
        } catch (ModelNotFoundException $e) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('displayAllItems'));
            return $rs;
        }
    }

    /**
     * Editer un item
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response
     */
    public function editItem(Request $rq, Response $rs, array $args): Response
    {
        try {
            $item = Item::findOrFail($args['id']);

            $post = $rq->getParsedBody();
            $post = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $post);

            $item->list_id = $post['list_id'];
            $item->name = $post['name'];
            $item->description = $post['description'];
            $item->image = $post['image'];
            $item->url = $post['url'];
            $item->price = $post['price'];
            $item->save();

            return $rs->withRedirect($this->container->router->pathFor('displayAllItems'));
        } catch (ModelNotFoundException $e) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('displayAllItems'));
            return $rs;
        }
    }

    /**
     * Supprimer un item
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response
     */
    public function deleteItem(Request $rq, Response $rs, array $args): Response
    {
        try {
            $item = Item::findOrFail($args['id']);
            $item->delete();

            return $rs->withRedirect($this->container->router->pathFor('displayAllItems'));
        } catch (ModelNotFoundException $e) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('displayAllItems'));
            return $rs;
        }
    }

    /**
     * Reservation d'un item
     * 
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function lockItem(Request $rq, Response $rs, array $args): Response
    {
        $item = Item::find($args['id']);
        $user = Authentication::getUser();
        if ($user === null) {
            return RedirectHelper::loginAndRedirect($rs, "/lists/" . $item->list_id);
        }
        $item->user_id = $user->id;
        $item->save();
        return $rs->withRedirect("/lists/" . $item->list_id);
    }
}