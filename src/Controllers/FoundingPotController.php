<?php

namespace Whishlist\Controllers;

use Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Models\Item;
use Whishlist\Models\WishList;
use Whishlist\Models\FoundingPot;
use Whishlist\Views\FoundingPotView;

class FoundingPotController extends BaseController
{
    /**
     * Crée une cagnotte
     *
     * @param Request $request
     * @param array $args
     * @param Response $response
     * @return Response
     */
    public function create(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();
        $body = array_map(function ($field) {
            return filter_var($field, FILTER_SANITIZE_STRING);
        }, $body);

        try {
            $item = Item::findOrFail($args['item_id']); // vérifie si l'item existe

            $alreadyExists = FoundingPot::where('item_id', $item->id)->exists();
            if ($alreadyExists) {
                throw new Exception('L\'item possède déjà une cagnotte.');
            }

            $foundingPot = new FoundingPot();
            $foundingPot->item_id = $item->id;
            $foundingPot->amount = $body['amount'];
            $foundingPot->save();

            return $response->withRedirect($this->container->router->pathFor('displayAllItems'));
        } catch (\Throwable $th) {
            return $response->withRedirect($this->container->router->pathFor('displayAllList'));
        }
    }

    /**
     * Page de création d'une cagnotte
     *
     * @param Request $request
     * @param array $args
     * @param Response $response
     * @return Response
     */
    public function createPage(Request $request, Response $response, array $args): Response
    {
        $alreadyExists = FoundingPot::where('item_id', $args['item_id'])->exists();
        
        if ($alreadyExists) {
            return $response->withRedirect($this->container->router->pathFor('displayAllItems'));
        }

        $v = new FoundingPotView($this->container, ['item_id' => $args['item_id']]);
        $response->getBody()->write($v->render(0));
        return $response;
    }

    /**
     * Met à jour une cagnotte
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();
        $body = array_map(function ($field) {
            return filter_var($field, FILTER_SANITIZE_STRING);
        }, $body);

        try {
            $list = WishList::findOrFail($args); // vérifie si la liste existe

            $foundingPot = FoundingPot::findOrFail($args['founding_pot_id']);
            $foundingPot->list_id = $list->id;
            $foundingPot->amount = $body['amount'];
            $foundingPot->save();

            return $response->withRedirect($this->container->router->pathFor('editList', ['id' => $list->id]));
        } catch (\Throwable $th) {
            return $response->withRedirect($this->container->router->pathFor('displayAllList'));
        }
    }

    /**
     * Supprime une cagnotte
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $list = WishList::findOrFail($args); // vérifie si la liste existe

            $foundingPot = FoundingPot::findOrFail($args['founding_pot_id']);
            $foundingPot->delete();

            return $response->withRedirect($this->container->router->pathFor('editList', ['id' => $list->id]));
        } catch (\Throwable $th) {
            return $response->withRedirect($this->container->router->pathFor('displayAllList'));
        }

        return $response;
    }
}
