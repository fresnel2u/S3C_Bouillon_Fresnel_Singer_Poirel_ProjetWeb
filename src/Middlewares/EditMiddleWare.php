<?php

namespace Whishlist\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Helpers\Auth;
use Whishlist\Helpers\Flashes;
use Whishlist\Models\UsersLists;
use Whishlist\Models\WishList;

class EditMiddleWare extends BaseMiddleWare
{
    public function __invoke(Request $rq, Response $rs, callable $next): Response
    {
        $route = $rq->getAttribute('route');
        $list = WishList::where('token', $route->getArgument('token'));
        if($list !== null)
            return $next($rq, $rs);
        Flashes::addFlash('Token de modification invalide', 'error');
        return $rq->withRedirect($this->container->router->pathFor('displayAllLists')); 
    }
}