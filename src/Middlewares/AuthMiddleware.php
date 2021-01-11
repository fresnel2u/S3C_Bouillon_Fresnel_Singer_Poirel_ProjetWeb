<?php

namespace Whishlist\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Helpers\Auth;
use Whishlist\Helpers\Flashes;
use Whishlist\Helpers\RedirectHelper;

class AuthMiddleware extends BaseMiddleware
{
    /**
     * Vérifie si l'utilisateur est bien connecté
     *
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if (!Auth::isLogged()) {
            Flashes::addFlash('Vous devez être connecté pour accéder à cette page.', 'error');
            $uri = (string) $request->getUri();
            return RedirectHelper::loginAndRedirect($response, $uri);
        }

        return $next($request, $response);
    }
}