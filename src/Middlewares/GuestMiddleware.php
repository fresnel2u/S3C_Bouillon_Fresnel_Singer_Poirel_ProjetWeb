<?php

namespace Whishlist\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Helpers\Auth;
use Whishlist\Helpers\Flashes;
use Whishlist\Helpers\RedirectHelper;

class GuestMiddleware extends BaseMiddleware
{
    /**
     * Vérifie si l'utilisateur est bien déconnecté
     *
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if (Auth::isLogged()) {
            Flashes::addFlash('Vous devez être déconnecté pour accéder à cette page.', 'error');
            $uri = $this->container->router->pathFor('displayAccount');
            return $response->withRedirect($uri);
        }

        return $next($request, $response);
    }
}