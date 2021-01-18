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
        $list = UsersLists::where('list_id', $route->getArgument($this->idParamName))
            ->where('user_id', Auth::getUser()['id']);
        if($list !== null)
            return $next($rq, $rs);
        Flashes::addFlash('Vous devez être le propriétaire ou avoir été invité à la liste pour accéder à cet URL', 'error');
        return $rq->withRedirect($this->container->router->pathFor('displayAllLists')); 
    }
}