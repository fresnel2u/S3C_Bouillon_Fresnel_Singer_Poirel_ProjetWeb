<?php

namespace Whishlist\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Views\HomeView;

class HomeController extends BaseController
{
    /**
     * Crée une vue pour afficher la page d'accueil
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function home(Request $request, Response $response, array $args): Response
    {
        $v = new HomeView($this->container);
        $response->getBody()->write($v->render(0));
        return $response;
    }
}