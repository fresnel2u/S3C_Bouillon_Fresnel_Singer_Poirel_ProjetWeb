<?php

namespace Whishlist\controleur;
session_start();

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\helpers\Authentication;
use Whishlist\modele\Item;
use Whishlist\modele\Liste;
use Whishlist\modele\User;
use \Whishlist\vues\CreationView;

/**
 * Ce controleur permet de gerer les actions concernant les fonctionnalites de consultation.
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
            $list = Liste::select('*')->where('no', '=', $args['id'])->firstOrFail();

            $v = new CreationView($list, $this->container);
            $rs->getBody()->write($v->render(1));
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
    public function editList(Request $rq, Response $rs, $args): Response {
        try {
            $list = Liste::select('*')->where('no', '=', $args['id'])->firstOrFail();

            $post = $rq->getParsedBody();
            $post = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $post);

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
        $rs->getBody()->write($v->render(2));
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
            $list = Liste::select('*')->where('no', '=', $args['no'])->firstOrFail();
            $list->delete();

            return $rs->withRedirect($this->container->router->pathFor('displayAllList'));
        } catch (ModelNotFoundException $e) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('displayAllList'));
            return $rs;
        }
    }

    /**
     * Supprimer un utilisateur
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args 
     * @return Response
     */
    public function deleteAccount(Request $rq, Response $rs, array $args): Response
    {
        try {
            $user = User::select('*')->where('id', '=', $_SESSION['user']->id)->firstOrFail();
            $user->delete();
            $_SESSION['user'] = null;
            
            return $rs->withRedirect($this->container->router->pathFor('home'));
        } catch (ModelNotFoundException $e) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('home'));
            return $rs;
        }
    }
}
