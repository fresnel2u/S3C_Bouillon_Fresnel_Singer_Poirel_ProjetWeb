<?php

namespace Whishlist\Controllers;

use Exception;
use Throwable;
use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\Models\Item;
use Whishlist\Helpers\Auth;
use Whishlist\Views\ItemView;
use Whishlist\Helpers\Flashes;
use Whishlist\Models\WishList;
use Whishlist\Helpers\Validator;
use Whishlist\Helpers\UploadFile;
use Whishlist\Models\ItemReservation;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ItemController extends BaseController
{
    /**
     * Ajouter un item (page)
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response réponse à la requête
     */
    public function newItemPage(Request $request, Response $response, array $args): Response
    {
        $v = new ItemView($this->container);
        $response->getBody()->write($v->render(0));
        return $response;
    }

    /**
     * Ajouter un item
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response
     */
    public function newItem(Request $request, Response $response, array $args): Response
    {
        $list = WishList::find($args['list_id']);
        if (!$list) {
            Flashes::addFlash('Impossible de créer l\'item', 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }

        $body = $request->getParsedBody();
        $body = array_map(function ($field) {
            return filter_var($field, FILTER_SANITIZE_STRING);
        }, $body);

        try {
            Validator::failIfEmptyOrNull($body, ['image', 'url']);
        } catch (Exception $e) {
            Flashes::addFlash($e->getMessage(), 'error');
            return $response->withRedirect($this->pathFor('newItemPage', ['list_id' => $list->id]));
        }

        $files = $request->getUploadedFiles();
        $file = $files['image'];
        if ($file->getError() === UPLOAD_ERR_OK) {
            $directory = ROUTE . '\img\\';
            $filename = UploadFile::moveUploadedFile($directory, $file);
        }

        $item = new Item();
        $item->list_id = $list->id;
        $item->name = $body['name'];
        $item->description = $body['description'];
        $item->image = $filename;
        $item->url = $body['url'];
        $item->price = $body['price'];
        $item->save();

        return $response->withRedirect($this->pathFor('displayAllItems', ['list_id' => $list->id]));
    }

    /**
     * Crée une vue pour afficher les items
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function displayAllItems(Request $request, Response $response, array $args): Response
    {
        $list = WishList::with('items')->find($args['list_id']);
        if (!$list) {
            Flashes::addFlash("Liste introuvable.", 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }

        $v = new ItemView($this->container, [
            'list' => $list,
            'items' => $list->items
        ]);
        $response->getBody()->write($v->render(1));
        return $response;
    }

    /**
     * Affichage public d'un item
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function displayItem(Request $request, Response $response, array $args): Response
    {
        $list = WishList::where('token', $args['token'])->first();
        if (!$list) {
            Flashes::addFlash("Liste introuvable.", 'error');
            return $response->withRedirect($this->pathFor('home'));
        }

        $item = Item::find($args['item_id']);
        if (!$item) {
            Flashes::addFlash("Item introuvable.", 'error');
            return $response->withRedirect($this->pathFor('home'));
        }

        if ($list->id !== $item->list_id) {
            Flashes::addFlash("L'item ne correspond pas à la liste.", 'error');
            return $response->withRedirect($this->pathFor('home'));
        }

        $v = new ItemView($this->container, [
            'list' => $list,
            'item' => $item
        ]);
        $response->getBody()->write($v->render(2));
        return $response;
    }

    /**
     * Éditer un item (page)
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response réponse à la requête
     */
    public function editItemPage(Request $request, Response $response, array $args): Response
    {
        $list = WishList::find($args['list_id']);
        if (!$list) {
            Flashes::addFlash("Liste introuvable.", 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }

        $item = Item::find($args['item_id']);
        if (!$item || $list->id !== $item->list_id) {
            Flashes::addFlash("L'item n'a pas été trouvé", 'error');
            return $response->withRedirect($this->pathFor('displayAllItems', ['list_id' => $list->id]));
        }

        $v = new ItemView($this->container, ['list' => $list, 'item' => $item]);
        $response->getBody()->write($v->render(3));
        return $response;
    }

    /**
     * Éditer un item
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response
     */
    public function editItem(Request $request, Response $response, array $args): Response
    {
        $list = WishList::find($args['list_id']);
        if (!$list) {
            Flashes::addFlash("Liste introuvable.", 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }

        $item = Item::find($args['item_id']);
        if (!$item || $list->id !== $item->list_id) {
            Flashes::addFlash("L'item n'a pas été trouvé", 'error');
            return $response->withRedirect($this->pathFor('displayAllItems', ['list_id' => $list->id]));
        }

        $body = $request->getParsedBody();
        $body = array_map(function ($field) {
            return filter_var($field, FILTER_SANITIZE_STRING);
        }, $body);

        try {
            Validator::failIfEmptyOrNull($body, ['image', 'url']);
        } catch (Exception $e) {
            Flashes::addFlash($e->getMessage(), 'error');
            return $response->withRedirect($this->pathFor('editItemPage', [
                'list_id' => $list->id,
                'item_id' => $item->id
            ]));
        }

        $files = $request->getUploadedFiles();
        $file = $files['image'];
        $filename = $item->image;
        if ($file !== null) {
            if ($file->getError() === UPLOAD_ERR_OK) {
                $directory = ROUTE . '\img\\';
                unlink($directory . $item->image);
                $filename = UploadFile::moveUploadedFile($directory, $file);
            }
        }

        $item->name = $body['name'];
        $item->description = $body['description'];
        $item->image = $filename;
        $item->url = $body['url'];
        $item->price = $body['price'];
        $item->save();

        Flashes::addFlash("Item modifié avec succès", 'success');
        return $response->withRedirect($this->pathFor('displayAllItems', ['list_id' => $list->id]));
    }

    /**
     * Supprimer un item
     *
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments donnes par le createur de la liste
     * @return Response
     */
    public function deleteItem(Request $request, Response $response, array $args): Response
    {
        $list = WishList::find($args['list_id']);
        if (!$list) {
            Flashes::addFlash("Liste introuvable.", 'error');
            return $response->withRedirect($this->pathFor('displayAllLists'));
        }

        $item = Item::find($args['item_id']);
        if (!$item || $list->id !== $item->list_id) {
            Flashes::addFlash("L'item n'a pas été trouvé", 'error');
            return $response->withRedirect($this->pathFor('displayAllItems', ['list_id' => $list->id]));
        }

        $directory = ROUTE . '\img\\';
        unlink($directory . $item->image);
        $item->delete();

        Flashes::addFlash("Item supprimé avec succès", 'success');
        return $response->withRedirect($this->pathFor('displayAllItems', ['list_id' => $list->id]));
    }

    /**
     * Formulaire de réservation d'un item
     * 
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function lockItemPage(Request $request, Response $response, array $args): Response
    {
        $list = WishList::find($args['list_id']);
        if (!$list) {
            Flashes::addFlash("Liste introuvable.", 'error');
            return $response->withRedirect($this->pathFor('home'));
        }

        $item = Item::find($args['item_id']);
        if (!$item || $list->id !== $item->list_id) {
            Flashes::addFlash("L'item n'a pas été trouvé", 'error');
            return $response->withRedirect($this->pathFor('displayList', ['list_id' => $list->id]));
        }

        $v = new ItemView($this->container, [
            'item' => $item,
            'list' => $item->list
        ]);
        $response->getBody()->write($v->render(4));
        return $response;
    }

    /**
     * Réservation d'un item
     * 
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function lockItem(Request $request, Response $response, array $args): Response
    {
        $list = WishList::find($args['list_id']);
        if (!$list) {
            Flashes::addFlash("Liste introuvable.", 'error');
            return $response->withRedirect($this->pathFor('home'));
        }

        $item = Item::find($args['item_id']);
        if (!$item || $list->id !== $item->list_id) {
            Flashes::addFlash("L'item n'a pas été trouvé", 'error');
            return $response->withRedirect($this->pathFor('displayList', ['list_id' => $list->id]));
        }

        if (!$item->reservation) {
            $user = Auth::getUser();

            $body = $request->getParsedBody();
            $message = filter_var($body['message'], FILTER_SANITIZE_STRING);

            $reservation = new ItemReservation();
            $reservation->item_id = $item->id;
            $reservation->user_id = $user['id'];
            $reservation->message = $message === '' ? null : $message;
            $reservation->save();

            Flashes::addFlash("Item réservé avec succès.", 'success');
        } else {
            Flashes::addFlash("Item déjà réservé.", 'error');
        }

        $redirectUrl = $this->pathFor('displayItem', [
            'token' => $item->list->token,
            'item_id' => $item->id
        ]);
        return $response->withRedirect($redirectUrl);
}

    /**
     * Annulation de la réservation d'un item
     * 
     * @param Request $request requête
     * @param Response $response réponse
     * @param array $args arguments
     * @return Response réponse à la requête
     */
    public function cancelLockItem(Request $request, Response $response, array $args): Response
    {
        $list = WishList::find($args['list_id']);
        if (!$list) {
            Flashes::addFlash("Liste introuvable.", 'error');
            return $response->withRedirect($this->pathFor('home'));
        }

        $item = Item::find($args['item_id']);
        if (!$item || $list->id !== $item->list_id) {
            Flashes::addFlash("L'item n'a pas été trouvé", 'error');
            return $response->withRedirect($this->pathFor('displayList', ['list_id' => $list->id]));
        }

        $lock_message = ItemReservation::select('*')->where('item_id', $item->reservation->item_id)->firstOrFail();
        $lock_message->delete();

        Flashes::addFlash("Réservation annulée avec succès", 'success');

        $redirectUrl = $this->pathFor('displayItem', [
            'token' => $item->list->token,
            'item_id' => $item->id
        ]);
        return $response->withRedirect($redirectUrl);
    }
}
