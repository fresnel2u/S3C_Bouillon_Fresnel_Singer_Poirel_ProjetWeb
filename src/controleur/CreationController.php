<?php

namespace Whishlist\controleur;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\modele\Item;
use Whishlist\modele\Liste;
use \Whishlist\vues\CreationView;

/**
 * Ce controleur permet de creer de gerer les actions concernant les fonctionnalites de creation.
 */
class CreationController
{
    private $container;

    /**
     * Constructeur du controleur
     *
     * @param \Slim\Container $c
     */
    function __construct(\Slim\Container $c)
    {
        $this->container = $c;
    }

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
        $v = new CreationView(null, $this->container);
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

            $list = new Liste();
            $list->user_id = 1; // TODO: Change to current user id
            $list->titre = $post['titre'];
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
     * Ajouter un item (page)
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response le contenu de la page
     */
    public function newItemPage(Request $rq, Response $rs, array $args): Response
    {
        $v = new CreationView(null, $this->container);
        $rs->getBody()->write($v->render(1));
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
            $item->liste_id = $post['liste_id'];
            $item->nom = $post['nom'];
            $item->descr = $post['descr'];
            $item->img = $post['img'];
            $item->url = $post['url'];
            $item->tarif = $post['tarif'];
            $item->save();

            return $rs->withRedirect($this->container->router->pathFor('displayAllItems'));
        } catch (ModelNotFoundException $e) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('displayAllItems'));
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
            $item = Item::select('*')->where('id', '=', $args['id'])->firstOrFail();

            $v = new CreationView($item->toArray(), $this->container);
            $rs->getBody()->write($v->render(2));
            return $rs;
        } catch (ModelNotFoundException $e) {
            $rs->getBody()->write("<h1 style=\"text-align : center;\"> L'item " . $args['id'] . " n'a pas été trouvé.</h1>");
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
            $item = Item::select('*')->where('id', '=', $args['id'])->firstOrFail();

            $post = $rq->getParsedBody();
            $post = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $post);

            $item->liste_id = $post['liste_id'];
            $item->nom = $post['nom'];
            $item->descr = $post['descr'];
            $item->img = $post['img'];
            $item->url = $post['url'];
            $item->tarif = $post['tarif'];
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
            $item = Item::select('*')->where('id', '=', $args['id'])->firstOrFail();
            $item->delete();

            return $rs->withRedirect($this->container->router->pathFor('displayAllItems'));
        } catch (ModelNotFoundException $e) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('displayAllItems'));
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
    public function saveList(Request $rq, Response $rs, $args): Response {
        $id = $args['id'];
        $list = Liste::find($id);
        if($list === null) {
            $rs->getBody()->write("Unable to find list with id '$id'");
            return $rs->withStatus(400, "Unable to find list with id '$id'");
        }
        $newVals = $rq->getParsedBody();
        $list->description = htmlentities($newVals['list_description'], ENT_QUOTES);
        $list->titre = htmlentities($newVals['list_title'], ENT_QUOTES);
        $list->save();
        $rs->getBody()->write((new CreationView($list, $this->container))->render(0));
        return $rs;     
    }
}
