<?php

namespace Whishlist\Middlewares;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Helpers\Auth;
use Whishlist\Helpers\Flashes;
use Whishlist\Models\FoundingPot;
use Whishlist\Models\Item;
use Whishlist\Models\WishList;

class OwnerMiddleware extends BaseMiddleware
{
    public function __construct(Container $container) {
        parent::__construct($container);
    }

    /**
     * Vérifie si l'utilisateur courant possède bien la liste avec l'id en argument dans la requete
     * 
     * @param listIdParam nom de l'argument pour l'id de la liste
     */
    public function userOwnsList(string $listIdParam = 'id'): callable
    {
        $container = $this->container;
        return function(Request $request, Response $response, callable $next) use ($container, $listIdParam)
        {
            $route = $request->getAttribute('route');
            if(Auth::getUser()['id'] === $route->getArgument($listIdParam))
                return $next($request, $response);
            Flashes::addFlash('Vous devez être le propriétaire de la liste pour accéder à cet URL', 'error');
            return $response->withRedirect($container->router->pathFor('displayAllList')); 
        };
    }

    /**
     * Vérifie si l'utilisateur courant possède bien la liste avec l'id en argument dans la requete
     * 
     * @param itemIdParam nom de l'argument pour l'id de l'item
     */
    public function userOwnsItem(string $itemIdParam = 'id'): callable
    {
        $container = $this->container;
        return function(Request $request, Response $response, callable $next) use ($container, $itemIdParam)
        {
            $route = $request->getAttribute('route');
            $item = Item::find($route->getArgument($itemIdParam));
            if($item !== null) {
                $list = WishList::find($item->list_id);
                if(Auth::getUser()['id'] === $list->user_id)
                    return $next($request, $response);
                Flashes::addFlash('Vous devez être le propriétaire de la liste de cet item pour accéder à cet URL', 'error');
            } else {
                Flashes::addFlash('Item inaccessible', 'error');
            }
            return $response->withRedirect($container->router->pathFor('displayAllItems')); 
        };
    }
}