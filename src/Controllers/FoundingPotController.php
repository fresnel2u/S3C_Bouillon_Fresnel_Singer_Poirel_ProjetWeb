<?php

namespace Whishlist\Controllers;

use Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Models\Item;
use Whishlist\Helpers\Auth;
use Whishlist\Helpers\Flashes;
use Whishlist\Models\WishList;
use Whishlist\Models\FoundingPot;
use Whishlist\Views\FoundingPotView;
use Whishlist\Models\FoundingPotParticipation;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        $list = WishList::where('id', $args['list_id'])->first();
        if (!$list) {
            Flashes::addFlash("Liste introuvable.", 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }

        $item = Item::find($args['item_id']);
        if (!$item) {
            Flashes::addFlash("Item introuvable.", 'error');
            return $response->withRedirect($this->pathFor('displayAllItems', ['list_id' => $list->id]));
        }

        if ($list->id !== $item->list_id) {
            Flashes::addFlash("L'item ne correspond pas à la liste.", 'error');
            return $response->withRedirect($this->pathFor('displayAllItems', ['list_id' => $list->id]));
        }

        $user = Auth::getUser();
        if ($user['id'] !== $list->user_id) {
            Flashes::addFlash('Vous devez être le propriétaire de la liste pour créer une cagnotte.', 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }
        $alreadyExists = FoundingPot::where('item_id', $item->id)->exists();
        if ($alreadyExists) {
            Flashes::addFlash('L\'item possède déjà une cagnotte.', 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }

        $body = $request->getParsedBody();
        $body = array_map(function ($field) {
            return filter_var($field, FILTER_SANITIZE_STRING);
        }, $body);

        if ($body['amount'] > $item->price) {
            Flashes::addFlash("Vous ne pouvez pas demander plus d'argent que le montant de l'item.", 'error');
            return $response->withRedirect($this->pathFor('createFoundingPotPage', [
                'item_id' => $item->id
            ]));
        }
        if ($body['amount'] === '0' || $body['amount'] === '') {
            Flashes::addFlash("Vous ne pouvez pas créer une cagnotte avec un montant nul.", 'error');
            return $response->withRedirect($this->pathFor('createFoundingPotPage', [
                'item_id' => $item->id
            ]));
        }

        $foundingPot = new FoundingPot();
        $foundingPot->item_id = $item->id;
        $foundingPot->amount = $body['amount'];
        $foundingPot->save();

        return $response->withRedirect($this->pathFor('displayAllItems', ['list_id' => $list->id]));
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
        $list = WishList::where('id', $args['list_id'])->first();
        if (!$list) {
            Flashes::addFlash("Liste introuvable.", 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }

        $item = Item::find($args['item_id']);
        if (!$item) {
            Flashes::addFlash("Item introuvable.", 'error');
            return $response->withRedirect($this->pathFor('displayAllItems', ['list_id' => $list->id]));
        }

        if ($list->id !== $item->list_id) {
            Flashes::addFlash("L'item ne correspond pas à la liste.", 'error');
            return $response->withRedirect($this->pathFor('displayAllItems', ['list_id' => $list->id]));
        }

        $alreadyExists = FoundingPot::where('item_id', $item->id)->exists();
        if ($alreadyExists) {
            Flashes::addFlash('L\'item possède déjà une cagnotte.', 'error');
            throw new Exception('L\'item possède déjà une cagnotte.');
        }

        $v = new FoundingPotView($this->container, ['list' => $list, 'item' => $item]);
        $response->getBody()->write($v->render(0));
        return $response;
    }

    /**
     * Participation à une cagnotte
     *
     * @param Request $request
     * @param array $args
     * @param Response $response
     * @return Response
     */
    public function participate(Request $request, Response $response, array $args): Response
    {
        try {
            $item = Item::with('foundingPot')->findOrFail($args['item_id']);

            $amount = round(abs(floatval($request->getParsedBodyParam('amount'))), 2);
            $rest = $item->foundingPot->getRest();

            if ($amount > $rest) {
                Flashes::addFlash("Vous ne pouvez pas mettre plus d'argent que le reste à payer.", 'error');
                return $response->withRedirect($this->pathFor('participateFoundingPotPage', [
                    'item_id' => $item->id
                ]));
            }

            $participation = new FoundingPotParticipation();
            $participation->amount = $amount;
            $participation->user_id = Auth::getUser()['id'];
            $participation->founding_pot_id = $item->foundingPot->id;
            $participation->save();

            Flashes::addFlash("Vous avez bien ajouté {$amount} € à la cagnotte.", 'success');
            return $response->withRedirect($this->pathFor('displayList', [
                'token' => $item->list->token
            ]));
        } catch (ModelNotFoundException $e) {
            Flashes::addFlash("Le model n'existe pas.", 'error');
        } catch (\Throwable $th) {
            Flashes::addFlash($th->getMessage(), 'error');
        }
        return $response->withRedirect($this->pathFor('displayAllLists'));
    }

    /**
     * Affichage d'une cagnotte
     *
     * @param Request $request
     * @param array $args
     * @param Response $response
     * @return Response
     */
    public function participatePage(Request $request, Response $response, array $args): Response
    {
        try {
            $item = Item::with('foundingPot')->with('list')->findOrFail($args['item_id']);

            $v = new FoundingPotView($this->container, [
                'founding_pot' => $item->foundingPot,
                'list' => $item->list,
                'item' => $item
            ]);
            $response->getBody()->write($v->render(1));
            return $response;
        } catch (\Throwable $th) {
            Flashes::addFlash('Impossible d\'afficher la cagnotte.', 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
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
            $foundingPot->amount = $body['amount'];
            $foundingPot->save();

            return $response->withRedirect($this->pathFor('displayAllItems', ['list_id' => $list->id]));
        } catch (\Throwable $th) {
            Flashes::addFlash('Impossible de mettre à jour la cagnotte', 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
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

            Flashes::addFlash('Cagnotte supprimée', 'success');
            return $response->withRedirect($this->pathFor('displayAllItems', ['list_id' => $list->id]));
        } catch (\Throwable $th) {
            Flashes::addFlash('Impossible de supprimer la cagnotte', 'erorr');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }

        return $response;
    }
}
