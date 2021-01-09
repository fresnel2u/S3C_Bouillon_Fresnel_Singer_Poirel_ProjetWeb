<?php

namespace Whishlist\Helpers;

use Slim\Http\Response;

/**
 * Helper for redirecting response
 */
class RedirectHelper
{
    /**
     * Redirect a user to the login page and set in session the target url to go after login success
     *
     * @param Response $response response to redirect to login
     * @param string $target url to go after login success
     * 
     * @return Response redirect response
     */
    public static function loginAndRedirect(Response $response, string $target): Response
    {
        $_SESSION['login_success_url'] = $target;
        return $response->withRedirect('/login');
    }
}
