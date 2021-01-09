<?php

namespace Whishlist\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Models\Item;
use Whishlist\Views\ListView;
use Whishlist\Models\WishList;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ListController extends BaseController
{
    /**
     * creer une vue pour afficher le formulaire de creation d'une liste
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function newListPage(Request $rq, Response $rs, array $args): Response
    {
        $v = new ListView($this->container);
        $rs->getBody()->write($v->render(0));
        return $rs;
    }

    /**
     * creer une nouvelle liste et creer une vue qui affiche la liste des listes de souhaits
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response le contenu de la page
     */
    public function newList(Request $rq, Response $rs, array $args): Response
    {
        try {
            $post = $rq->getParsedBody();
            $post = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $post);

            $list = new WishList();
            $list->user_id = 1; // TODO: Change to current user id
            $list->title = $post['title'];
            $list->description = $post['description'];
            $list->expiration = $post['expiration'];
            $list->token = $post['token'];
            $list->save();

            return $rs->withRedirect($this->container->router->pathFor('displayAllList'));
        } catch (ModelNotFoundException $e) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('displayAllList'));
            return $rs;
        }
    }

    /**
     * creer une vue pour afficher la liste des listes de souhaits
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function displayAllList(Request $rq, Response $rs, array $args): Response
    {
        $lists = WishList::all();
        if (!$lists->count()) {
            $rs->getBody()->write("<h1 style=\"text-align : center;\"> Aucune liste n'a été trouvée.</h1>");
        }
        $v = new ListView($this->container, ['lists' => $lists]);
        $rs->getBody()->write($v->render(1));
        return $rs;
    }

    /**
     * creer une vue pour afficher les items d'une liste
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function displayList(Request $rq, Response $rs, array $args): Response
    {
        // TODO: Logique fausse par rapport au message d'erreur
        $items = Item::select('*')->where('list_id', '=', $args['id'])->get();
        if (!$items->count()) {
            $rs->getBody()->write("<h1 style=\"text-align : center;\"> La liste " . $args['id'] . " n'a pas été trouvé.</h1>");
        } else {
            $v = new ListView($this->container, ['items' => $items]);
            $rs->getBody()->write($v->render(2));
        }

        return $rs;
    }

    /**
     * Crée une vue pour afficher le formulaire d'édition d'une liste
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function editListPage(Request $rq, Response $rs, array $args): Response
    {
        try {
            $list = WishList::findOrFail($args['id']);

            $v = new ListView($this->container, ['list' => $list]);
            $rs->getBody()->write($v->render(3));
            return $rs;
        } catch (ModelNotFoundException $e) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('displayAllList'));
            return $rs;
        }
    }

    /**
     * Modifie une liste existante
     * 
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response le contenu de la page
     */
    public function editList(Request $rq, Response $rs, $args): Response
    {
        try {
            $list = WishList::findOrFail($args['id']);

            $post = $rq->getParsedBody();
            $post = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $post);

            $list->title = $post['title'];
            $list->description = $post['description'];
            $list->expiration = $post['expiration'];
            $list->token = $post['token'];
            $list->save();

            return $rs->withRedirect($this->container->router->pathFor('displayAllList'));
        } catch (ModelNotFoundException $e) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('displayAllList'));
            return $rs;
        }
    }

    /**
     * Supprimer une liste
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args 
     * @return Response
     */
    public function deleteList(Request $rq, Response $rs, array $args): Response
    {
        try {
            $list = WishList::find($args['id']);
            $list->delete();

            return $rs->withRedirect($this->container->router->pathFor('displayAllList'));
        } catch (ModelNotFoundException $e) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('displayAllList'));
            return $rs;
        }
    }
}