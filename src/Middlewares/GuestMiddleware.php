<?php

namespace Whishlist\Middlewares;

use Whishlist\Helpers\Auth;
use Whishlist\Helpers\Flashes;
use Whishlist\Helpers\RedirectHelper;

class GuestMiddleware extends BaseMiddleware
{
    /**
     * Vérifie si l'utilisateur est n'est pas connecté
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        if (Auth::isLogged()) {
            Flashes::addFlash('Vous devez être déconnecté pour accéder à cette page.', 'error');
            $uri = $this->container->router->pathFor('displayAccount');
            return RedirectHelper::loginAndRedirect($response, $uri);
        }

        return $next($request, $response);
    }
}