<?php

namespace Whishlist\Middlewares;

use Error;
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
    const WISHLIST = 'wishlist';
    const ITEM = 'item';

    /**
     * Type of was the owner is supposed to own
     * @var string
     */
    private $checkType;

    /**
     * Name of the argument in the Request to read the id from
     * @var string
     */
    private $idParamName;

    /**
     * Construct a owner middleware
     *
     * @param Container $container 
     * @param string $checkType Type of was the owner is supposed to own
     * @param string $idParamName Name of the argument in the Request to read the id from
     */
    public function __construct(Container $container, string $checkType, string $idParamName = 'id') 
    {
        parent::__construct($container);
        $this->checkType = $checkType;
        $this->idParamName = $idParamName;
    }

    /**
     * Handle request and check if the user is the owner of the ressouce
     *
     * @return void
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        $rs = null;
        switch($this->checkType)
        {
            case self::WISHLIST:
                $rs = $this->userOwnsList($request, $response, $next);
                break;
            case self::ITEM:
                $rs = $this->userOwnsItem($request, $response, $next);
                break;
            default:
                throw new Error("OwnerMiddle can't haddle check type : '{$this->checkType}'");
        }
        return $rs;
    }

    /**
     * Vérifie si l'utilisateur courant possède bien la liste avec l'id en argument dans la requete
     * 
     * @param listIdParam nom de l'argument pour l'id de la liste
     */
    public function userOwnsList(Request $request, Response $response, callable $next): Response
    {
        $route = $request->getAttribute('route');
        $list = WishList::find($route->getArgument($this->idParamName));
        if(Auth::getUser()['id'] === $list->user_id)
            return $next($request, $response);
        Flashes::addFlash('Vous devez être le propriétaire de la liste pour accéder à cet URL', 'error');
        return $response->withRedirect($this->container->router->pathFor('displayAllLists')); 
    }

    /**
     * Vérifie si l'utilisateur courant possède bien la liste avec l'id en argument dans la requete
     * 
     * @param itemIdParam nom de l'argument pour l'id de l'item
     */
    public function userOwnsItem(Request $request, Response $response, callable $next): Response
    {
        $route = $request->getAttribute('route');
        $item = Item::find($route->getArgument($this->idParamName));
        if($item) {
            $list = WishList::find($item->list_id);
            if(Auth::getUser()['id'] === $list->user_id)
                return $next($request, $response);
            Flashes::addFlash('Vous devez être le propriétaire de la liste de cet item pour accéder à cet URL', 'error');
            return $response->withRedirect($this->container->router->pathFor('displayAllItems', ['list_id' => $list->id])); 
        } else {
            Flashes::addFlash('Item inaccessible', 'error');
            return $response->withRedirect($this->container->router->pathFor('displayAllLists')); 
        }
    }
}