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
    public function formList(Request $rq, Response $rs, array $args) : Response
    {
        $v = new CreationView([], $this->container);
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
    public function newList(Request $rq, Response $rs, array $args) : Response
    {
        $post = $rq->getParsedBody();
        $titre = filter_var($post['list_title'], FILTER_SANITIZE_STRING) ;
		$description = filter_var($post['list_description'], FILTER_SANITIZE_STRING) ;
        $l = new Liste();
        $l->user_id = 2;
		$l->titre = $titre;
		$l->description = $description;
		$l->save();
		$url_listes = $this->container->router->pathFor('displayAllList') ;		
		return $rs->withRedirect($url_listes);  
    }

    /**
     * Créer un nouvel item
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response le contenu de la page
     */
    public function editItemPage(Request $rq, Response $rs, array $args) : Response
    {
        try {
            $item = Item::select('*')->where('id', '=', $args['id'])->firstOrFail();

            $v = new CreationView($item->toArray(), $this->container);
            $rs->getBody()->write($v->render(2));
            return $rs;  
        } catch(ModelNotFoundException $e) {
            $rs->getBody()->write("<h1 style=\"text-align : center;\"> L'item ". $args['id'] . " n'a pas été trouvé.</h1>");
            return $rs;  
        }
    }
}