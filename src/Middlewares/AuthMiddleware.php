<?php

namespace Whishlist\Middlewares;

use Whishlist\Helpers\Auth;
use Whishlist\Helpers\Flashes;
use Whishlist\Helpers\RedirectHelper;

class AuthMiddleware extends BaseMiddleware
{
    /**
     * Vérifie si l'utilisateur est connecté
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        if (!Auth::isLogged()) {
            Flashes::addFlash('Vous devez être connecté pour accéder à cette page.', 'error');
            $uri = (string) $request->getUri();
            return RedirectHelper::loginAndRedirect($response, $uri);
        }

        return $next($request, $response);
    }
}