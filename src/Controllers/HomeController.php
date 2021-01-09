<?php

namespace Whishlist\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Views\HomeView;

class HomeController extends BaseController
{
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
        $v = new HomeView($this->container);
        $rs->getBody()->write($v->render(0));
        return $rs;
    }
}