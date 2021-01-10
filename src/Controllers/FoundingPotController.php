<?php

namespace Whishlist\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Models\WishList;
use Whishlist\Models\FoundingPot;

class FoundingPotController extends BaseController
{
    /**
     * Crée une cagnotte
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function create(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();
        $body = array_map(function ($field) {
            return filter_var($field, FILTER_SANITIZE_STRING);
        }, $body);

        try {
            $list = WishList::findOrFail($args); // vérifie si la liste existe

            $foundingPot = new FoundingPot();
            $foundingPot->list_id = $list->id;
            $foundingPot->amount = $body['amount'];
            $foundingPot->save();

            return $response->withRedirect($this->container->router->pathFor('editList', ['id' => $list->id]));
        } catch (\Throwable $th) {
            return $response->withRedirect($this->container->router->pathFor('displayAllList'));
        }
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
