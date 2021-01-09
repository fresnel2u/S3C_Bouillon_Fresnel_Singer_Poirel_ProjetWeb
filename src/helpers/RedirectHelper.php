<?php

namespace Whishlist\Helpers;

if (session_status() == PHP_SESSION_NONE)
    session_start();

use Slim\Http\Response;

/**
 * Helper for redirecting response
 */
class RedirectHelper
{
    /**
     * Redirect a user to the login page and set in session the target url to go after login success
     *
     * @param Response $rs response to redirect to login
     * @param string $target url to go after login success
     * 
     * @return Response redirect response
     */
    public static function loginAndRedirect(Response $rs, string $target): Response
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        $_SESSION['login_success_url'] = $target;
        return $rs->withRedirect('/login');
    }
}
