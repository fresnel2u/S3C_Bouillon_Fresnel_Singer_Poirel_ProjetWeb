<?php
namespace Whishlist\controleur;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Whishlist\vues\ConnectionView;

/**
 * Ce controleur permet de creer de gerer les actions concernant les fonctionnalites de connection/inscription.
 */
class ConnectionController
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
     * creer une vue pour afficher la page de login
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function getLogin(Request $rq, Response $rs, array $args) : Response
    {
        $v = new ConnectionView(array(), $this->container );
        $rs->getBody()->write($v->render(0));
        return $rs;        
    }

    /**
     * creer une vue pour afficher la page de register
     *
     * @param Request $rq requete
     * @param Response $rs reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function getRegister(Request $rq, Response $rs, array $args) : Response
    {
        $v = new ConnectionView(array(), $this->container );
        $rs->getBody()->write($v->render(1));
        return $rs;        
    }

}