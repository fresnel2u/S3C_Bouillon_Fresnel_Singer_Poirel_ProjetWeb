<?php

namespace Whishlist\controleur;

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\modele\Item;
use Whishlist\modele\Liste;
use Whishlist\vues\HomeView;
use Whishlist\vues\ParticipationView;
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
    public function home(Request $rq, Response $rs, array $args) : Response
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
    public function displayAllList(Request $rq, Response $rs, array $args) : Response
    {
        $listes = Liste::select('*')->get();
        if(! $listes->count()){
            $rs->getBody()->write("<h1 style=\"text-align : center;\"> Aucune liste n'a été trouvée.</h1>");
        }
        $v = new ParticipationView($listes->toArray(), $this->container );
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
    public function displayList(Request $rq, Response $rs, array $args) : Response
    {
        
        $items = Item::select('*')->where('liste_id', '=', $args['id'])->get();
        if(! $items->count()){
            $rs->getBody()->write("<h1 style=\"text-align : center;\"> La liste ". $args['id'] . " n'a pas été trouvé.</h1>");
        } else {
            $v = new ParticipationView($items->toArray(), $this->container);
            $rs->getBody()->write($v->render(1));
        }
              
        return $rs;  
    }

    /**
     * creer une vue pour afficher un item
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function displayItem(Request $rq, Response $rs, array $args) : Response
    {
        try {
            $item = Item::select('*')->where('id', '=', $args['id'])->firstOrFail();

            $v = new ParticipationView($item->toArray(), $this->container);
            $rs->getBody()->write($v->render(2));
            return $rs;  
        } catch(ModelNotFoundException $e) {
            $rs->getBody()->write("<h1 style=\"text-align : center;\"> L'item ". $args['id'] . " n'a pas été trouvé.</h1>");
            return $rs;  
        }
    }
}