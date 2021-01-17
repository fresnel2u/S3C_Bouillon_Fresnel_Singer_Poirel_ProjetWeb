<?php

namespace Whishlist\Views;

use Whishlist\Models\User;
use Whishlist\Helpers\Auth;
use Whishlist\Models\WishList;
use Whishlist\Models\ListMessage;

class ListView extends BaseView
{
    /**
     * Construit le contenu d'un formulaire de creation de liste
     *
     * @return string l'HTML du formulaire de creation de liste
     */
    private function newListPage(): string
    {
        $newListUrl = $this->pathFor('newList');

        return <<<HTML
            <div class="container">
                <h1>Créer une liste</h1>
                <form method="POST" action="{$newListUrl}">
                    <div class="form-group">
                        <label for="title">Titre</label>
                        <input type="text" name="title" id="title">
                    </div>        
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" cols="30" rows="10"></textarea>
                    </div>        
                    <div class="form-group">
                        <label for="expiration">Expiration</label>
                        <input type="date" name="expiration" id="expiration">
                    </div>        
                    <div class="form-group">
                        <label for="token">Token</label>
                        <input type="text" name="token" id="token">
                    </div>      
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>  
                </form>
            </div>
        HTML;
    }

    /**
     * Construit le contenu des listes de souhaits
     *
     * @return string l'HTML des listes de souhaits
     */
    private function getAllList(): string
    {
        $lists = $this->params['lists'];
        $addUrl = $this->pathFor('newListPage');

        $html = <<<HTML
            <div class="container container-full">
                <h1>Mes listes de souhaits</h1>
                <a href="{$addUrl}" class="btn btn-primary">Ajouter une liste</a>
                <div class="table-wrapper">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>URL Publique</th>
                                <th>Expiration</th>
                                <th class="table-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
        HTML;

        foreach ($lists as $list) {
            $publicUrl = $this->pathFor('displayList', ['token' => $list->token]);
            $editUrl = $this->pathFor('editListPage', ['list_id' => $list->id]);
            $itemsUrl = $this->pathFor('displayAllItems', ['list_id' => $list->id]);
            $deleteUrl = $this->pathFor('deleteList', ['list_id' => $list->id]);
            $resultsUrl = $this->pathFor('displayListResults', ['list_id' => $list->id]);

            $html .= <<<HTML
                <tr>
                    <td>{$list->id}</td>
                    <td>{$list->title}</td>
                    <td>{$list->description}</td>
                    <td><a href="{$publicUrl}" target="_blank">{$publicUrl}</a></td>
                    <td>{$list->expiration->format('d/m/Y')}</td>
                    <td class="table-actions">
                        <div>
                            <a href="{$editUrl}" class="btn btn-light">Éditer</a>
                            <a href="{$itemsUrl}" class="btn btn-light">Items</a>
            HTML;

            if ($list->isExpired()) {
                $html .= <<<HTML
                    <a href="{$resultsUrl}" class="btn btn-light">Bilan</a>    
                HTML;
            }

            $html .= <<<HTML
                            <form method="POST" action="{$deleteUrl}" onsubmit="return confirm('Voulez-vous vraiment supprimer cette liste ?');">
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            HTML;
        }

        return $html . <<<HTML
                        </tbody>
                    </table>
                </div>
            </div>
        HTML;
    }

    /**
     * Construit le contenu d'une liste de souhaits
     *
     * @return string l'HTML d'une liste de souhaits
     */
    private function getList(): string
    {
        $list = $this->params['list'];
        $items = $this->params['items'];
        $messages = $this->params['messages'];

        $html = <<<HTML
            <div class="container page-list-show">
                <h1>{$list->title}</h1>
                <p>{$list->description}</p>
                <br><br>
                <p><i>Expiration : {$list->expiration->format('d/m/Y')}</i></p> 
                <p><i>Créée par : {$list->user->firstname} {$list->user->lastname}</i></p> 

                <h2>Items de la liste</h2>
                <div class="items-list">
        HTML;

        foreach ($items as $item) {
            $itemUrl = $this->pathFor('displayItem', [
                'token' => $list->token,
                'item_id' => $item->id
            ]);
            $html .= <<<HTML
                <div class="item">
                    <img src="/img/{$item->image}" alt="Image de l'item" />
                    <h3>{$item->name}</h3> 
            HTML;

            // Réservation
            if ($item->reservation) {
                if (!($list->user_id === Auth::getUser()['id'])) {
                    $html .= <<<HTML
                        <p><i>Réservé par {$item->reservation->user->getFullname()}.</i></p>
                    HTML;
                } else {
                    $html .= <<<HTML
                        <p><i>Réservé.</i></p>
                    HTML;
                }
            } else {
                $html .= <<<HTML
                    <p><i>Non réservé.</i></p>
                HTML;
            }

            $html .= <<<HTML
                <a href="{$itemUrl}" class="btn btn-primary"> Afficher </a>
                </div>
            HTML;
        }

        $html .= <<<HTML
                </div>
        HTML;

        $addListMessageUrl = $this->pathFor('newListMessage', ['token' => $list->token]);

        $html .= <<<HTML
        <div class="messages">
        
            <form method="POST" action="{$addListMessageUrl}">
                <label for="descr"><br><h2>Messages publics : </h2></label>
                <div class="form-group" >
                    <input type="text" name ="message" id="message" placeholder="Contenu du message">
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
            
        <br>
        HTML;

        foreach ($messages as $message) {
            $deleteListMessageUrl = $this->container->router->pathFor('deleteListMessage', [
                'id' => $message->id,
                'token' => $list->token
            ]);

            $editListMessagePageUrl = $this->container->router->pathFor('editListMessagePage', ['token' => $list->token]);

            $html .= <<<HTML
            <div class="message">
                <p><i> Ecrit par {$message->user->firstname} {$message->user->lastname} :</i></p>
                <p>$message->message</p>
                <form method="POST" action="{$deleteListMessageUrl}">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
                <form method="GET" action="{$editListMessagePageUrl}">
                    <button type="submit" class="btn btn-secondary">Modifier</button>
                </form>
                <br>
            </div>

            HTML;
        }

        return $html . <<<HTML
            </div>
        HTML;
    }

    /**
     * Construit le contenu d'un formulaire pour modifier un message public sur une liste
     *
     * @return string l'HTML d'une liste de souhaits
     */
    private function getEditMessage(): string
    {
        $list = $this->params['list'];
        $items = $this->params['items'];
        $messages = $this->params['messages'];

        $html = <<<HTML
            <div class="container page-list-show">
                <h1>{$list->title}</h1>
                <p>{$list->description}</p>
                <br><br>
                <p><i>Expiration : {$list->expiration->format('d/m/Y')}</i></p> 
                <p><i>Créée par : {$list->user->firstname} {$list->user->lastname}</i></p> 

                <h2>Items de la liste</h2>
                <div class="items-list">
        HTML;

        foreach ($items as $item) {
            $itemUrl = $this->pathFor('displayItem', [
                'token' => $list->token,
                'item_id' => $item->id
            ]);
            $html .= <<<HTML
                <div class="item">
                    <img src="/img/{$item->image}" alt="Image de l'item" />
                    <h3>{$item->name}</h3> 
            HTML;

            // Réservation
            if ($item->reservation) {
                if (!($list->user_id === Auth::getUser()['id'])) {
                    $html .= <<<HTML
                        <p><i>Réservé par {$item->reservation->user->getFullname()}.</i></p>
                    HTML;
                } else {
                    $html .= <<<HTML
                        <p><i>Réservé.</i></p>
                    HTML;
                }
            } else {
                $html .= <<<HTML
                    <p><i>Non réservé.</i></p>
                HTML;
            }

            $html .= <<<HTML
                <a href="{$itemUrl}" class="btn btn-primary">Afficher</a>
                </div>
            HTML;
        }

        $html .= <<<HTML
                </div>
        HTML;

        $html .= <<<HTML
        <div class="messages">
        <br>
        HTML;

        foreach ($messages as $message) {
            $editListMessageUrl = $this->container->router->pathFor('editListMessage', [
                'id' => $message->id,
                'token' => $list->token
            ]);
            $html .= <<<HTML
            <div class="message">
                <p><i> Ecrit par {$message->user->firstname} {$message->user->lastname} :</i></p>
                <form method="POST" action="{$editListMessageUrl}">
                    <input type="text" name ="message" id="editmessage" value="$message->message">
                    <button type="submit" class="btn btn-secondary">Sauvegarder</button>
                </form>
                <br>
            </div>

            HTML;
        }

        return $html . <<<HTML
            </div>
        HTML;
    }

    /**
     * Construit le contenu d'un formulaire d'edition de liste
     *
     * @return string
     */
    private function editListPage(): string
    {
        $list = $this->params['list'];
        $editUrl = $this->pathFor('editList', ['list_id' => $list->id]);

        return <<<HTML
            <div class="container">
                <h1>Éditer une liste</h1>
                <form method="POST" action="{$editUrl}">
                    <div class="form-group">
                        <label for="title">Titre</label>
                        <input type="text" name="title" id="title" value="{$list->title}">
                    </div>        
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" cols="30" rows="10">{$list->description}</textarea>
                    </div>        
                    <div class="form-group">
                        <label for="expiration">Expiration</label>
                        <input type="date" name="expiration" id="expiration" value="{$list->expiration->format('Y-m-d')}">
                    </div>        
                    <div class="form-group">
                        <label for="token">Token</label>
                        <input type="text" name="token" id="token" value="{$list->token}">
                    </div>      
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>  
                </form>
            </div>
        HTML;
    }

    /**
     * Construit le contenu du bilan de reservations d'une liste
     *
     * @return string
     */
    public function getListResults(): string
    {
        $list = $this->params['list'];
        $items = $this->params['items'];
        $html = <<<HTML
            <div class="container big list-results">
                <h1>Bilan des réservations de votre liste : <br> $list->title</h1>
                <div class="results-container">
        HTML;

        $hasContent = false;
        foreach ($items as $item) {
            if (!is_null($item->reservation) || !is_null($item->foundingPot)) {
                $hasContent = true;
                $imgUrl = "/img/{$item->image}";
                $reservation = $item->reservation;
                $foundingPot = $item->foundingPot;

                if (!is_null($foundingPot)) {
                    $type = 'Cagnotte';
                    $content = '<div class="item-participants">
                    <p> <strong>Participants :</strong> </p>';

                    foreach ($foundingPot->participations as $participant) {
                        $content .= <<<HTML
                            <div>
                                <p><strong>-</strong>  {$participant->user->firstname} {$participant->user->lastname} 
                                   | {$participant->amount} €</p>
                            </div>
                        HTML;
                    }
                    $content .= '</div>
                    <p> <strong> Montant restant : </strong>' . $foundingPot->getRest() . '</p>';
                } else {
                    $type = 'Réservation';
                    $content = <<<HTML
                        <p><strong>auteur de la réservation : </strong> {$reservation->user->firstname} {$reservation->user->lastname}</p>
                    HTML;
                    if ($reservation->message != '') {
                        $content .= <<<HTML
                            <p><strong>message de l'expéditeur :</strong> {$reservation->message} </p>
                        HTML;
                    }
                }
                $html .= <<<HTML
                    <div class="item-result">
                        <img src="{$imgUrl}"/>
                        <div>
                            <h3><strong>Nom : </strong> {$item->name} </h3>
                            <p><strong>Type :</strong> {$type}</p>
                            <p><strong>prix :</strong> {$item->price} €</p>
                            {$content}
                        </div>
                    </div>
                HTML;
            }
        }

        if ($hasContent === false) {
            $html .= <<<HTML
                    <p> Aucune réservation n'a été effectué sur cette liste. </p>
                HTML;
        }

        $html .= <<<HTML
                </div>
            </div>
        HTML;

        return $html;
    }

    /**
     * @inheritdoc
     */
    public function render(int $selector): string
    {
        $title = "MyWhishList | ";
        switch ($selector) {
            case 0: {
                    $content = $this->newListPage();
                    $title .= "Créer une liste";
                    break;
                }
            case 1: {
                    $content = $this->getAllList();
                    $title .= "Listes de souhaits";
                    break;
                }
            case 2: {
                    $content = $this->getList();
                    $title .= "Liste";
                    break;
                }
            case 3: {
                    $content = $this->editListPage();
                    $title .= "Éditer une liste";
                    break;
                }
            case 4: {
                    $content = $this->getListResults();
                    $title .= "Bilan réservations";
                    break;
                }
            case 5: {
                    $content = $this->getEditMessage();
                    $title .= "Modifier un message";
                    break;
                }
            default: {
                    $content = '';
                    break;
                }
        }

        return $this->layout($content, $title);
    }
}
