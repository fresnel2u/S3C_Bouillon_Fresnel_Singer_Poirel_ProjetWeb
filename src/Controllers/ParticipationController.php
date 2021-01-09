<?php

namespace Whishlist\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Models\Item;
use Whishlist\Models\User;
use Whishlist\Views\HomeView;
use Whishlist\Models\WishList;
use Whishlist\helpers\Authentication;
use Whishlist\helpers\RedirectHelper;
use Whishlist\Views\ParticipationView;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Ce controleur permet de creer de gerer les actions concernant les fonctionnalites de consultation.
 */
class ParticipationController
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
     * creer une vue pour afficher la page d'accueil
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function home(Request $rq, Response $rs, array $args): Response
    {
        $v = new HomeView(array(), $this->container);
        $rs->getBody()->write($v->render(0));
        return $rs;
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
        $v = new ParticipationView($lists->toArray(), $this->container);
        $rs->getBody()->write($v->render(0));
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

        $items = Item::select('*')->where('list_id', '=', $args['id'])->get();
        if (!$items->count()) {
            $rs->getBody()->write("<h1 style=\"text-align : center;\"> La liste " . $args['id'] . " n'a pas été trouvé.</h1>");
        } else {
            $v = new ParticipationView($items->toArray(), $this->container);
            $rs->getBody()->write($v->render(1));
        }

        return $rs;
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

            $v = new ParticipationView($items->toArray(), $this->container);
            $rs->getBody()->write($v->render(2));
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

            $v = new ParticipationView($item->toArray(), $this->container);
            $rs->getBody()->write($v->render(3));
            return $rs;
        } catch (ModelNotFoundException $e) {
            $rs->getBody()->write("<h1 style=\"text-align : center;\"> L'item " . $args['id'] . " n'a pas été trouvé.</h1>");
            return $rs;
        }
    }

    /**
     * creer une vue pour afficher les informations du compte utilisateur
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function displayAccount(Request $rq, Response $rs, array $args): Response
    {
        $v = new ParticipationView(array(), $this->container);
        $rs->getBody()->write($v->render(4));
        return $rs;
    }

    /**
     * creer une vue pour afficher l'edition du compte utilisateur
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function displayEditAccount(Request $rq, Response $rs, array $args): Response
    {
        $v = new ParticipationView(array(), $this->container);
        $rs->getBody()->write($v->render(5));
        return $rs;
    }

    /**
     * sauvegarde les changements effectues lors de l'edition d'un compte
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function editAccount(Request $rq, Response $rs, array $args): Response
    {
        try {
            $user_session = Authentication::getUser();

            $user = User::findOrFail($user_session->id);
            $post = $rq->getParsedBody();

            $post = array_map(function ($field) {
                return filter_var($field, FILTER_SANITIZE_STRING);
            }, $post);

            $user->firstname = $post['firstname'];
            $user_session->firstname = $post['firstname'];

            $user->lastname = $post['lastname'];
            $user_session->lastname = $post['lastname'];

            $user->email = $post['mail'];
            $user_session->email = $post['mail'];

            $user->save();

            return $rs->withRedirect($this->container->router->pathFor('editAccountPage'));
        } catch (ModelNotFoundException $e) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('editAccountPage'));
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
