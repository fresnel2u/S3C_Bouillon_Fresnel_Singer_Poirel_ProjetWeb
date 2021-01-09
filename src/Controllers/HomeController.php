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
     * @param Request $request requete
     * @param Response $response reponse
     * @param array $args arguments
     * @return Response le contenu de la page
     */
    public function home(Request $request, Response $response, array $args): Response
    {
        $v = new HomeView($this->container);
        $response->getBody()->write($v->render(0));
        return $response;
    }
}