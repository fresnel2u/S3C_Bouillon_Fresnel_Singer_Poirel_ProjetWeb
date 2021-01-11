<?php

namespace Whishlist\Helpers;

use Slim\Http\Response;

class RedirectHelper
{
    /**
     * Redirige l'utilisateur à la page de connection avec une URL de redirection après la connection
     *
     * @param Response $response
     * @param string $target url à suivre après la connection
     * 
     * @return Response
     */
    public static function loginAndRedirect(Response $response, string $target): Response
    {
        $_SESSION['login_success_url'] = $target;
        return $response->withRedirect('/login');
    }
}
