<?php

namespace Whishlist\Views;

use Whishlist\Helpers\Auth;

class ItemView extends BaseView
{
    /**
     * Construit la page d'ajout d'un item
     *
     * @return string
     */
    private function newItemPage(): string
    {
        return <<<HTML
            <div class="container">
                <h1>Ajouter un item</h1>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="list_id">ID de la liste</label>
                        <input type="text" name="list_id" id="list_id">
                    </div>        
                    <div class="form-group">
                        <label for="name">Nom</label>
                        <input type="text" name="name" id="name">
                    </div>        
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" cols="30" rows="10"></textarea>
                    </div>        
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" name="image" id="image" accept=".jpg, .png">
                    </div>        
                    <div class="form-group">
                        <label for="url">URL</label>
                        <input type="text" name="url" id="url">
                    </div>      
                    <div class="form-group">
                        <label for="price">Tarif</label>
                        <input type="text" name="price" id="price">
                    </div>
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>  
                </form>
            </div>
        HTML;
    }

    /**
     * Construit le contenu de la liste d'items
     *
     * @return string
     */
    private function getAllItems(): string
    {
        $newItemUrl = $this->pathFor('newItemPage');

        $html = <<<HTML
            <h1>Résultat de l'affichage des items :</h1>
            <a href="{$newItemUrl}" class="btn btn-primary">Ajouter un item</a>
            <div>
                <table class="table table-bordered table-dark">
                    <thead>
                        <tr>
                            <th scope="col">id</th>
                            <th scope="col">liste id</th>
                            <th scope="col">nom</th>
                            <th scope="col">description</th>
                            <th scope="col">image</th>
                            <th scope="col">url</th>
                            <th scope="col">tarif</th>
                            <th scope="col">Cagnotte</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
        HTML;


        foreach ($this->params['items'] as $item) {
            $html .= "<tr>";
            $i = 0;
            foreach ($item->toArray() as $col) {
                if ($i === 4) {
                    $url = "/img/{$col}";
                    $html .= "<td><img src=\"{$url}\" width=\"150\"/></td>";
                } else {
                    $html .= "<td>{$col}</td>";
                }
                $i++;
            }

            if ($item->foundingPot) {
                $html .= <<<HTML
                    <td>
                        {$item->foundingPot->amount} €
                    </td>
                HTML;
            } else {
                $foundingPotUrl = $this->pathFor('createFoundingPot', ['item_id' => $item->id]);
                $html .= <<<HTML
                    <td>
                        <a href="{$foundingPotUrl}" class="btn btn-light">Créer une cagnotte</a>
                    </td>
                HTML;
            }

            $editUrl = $this->pathFor('editItem', ['id' => $item->id]);
            $deleteUrl = $this->pathFor('deleteItem', ['id' => $item->id]);

            $html .= <<<HTML
                    <td>
                        <a href="{$editUrl}" class="btn btn-light">Éditer</a>
                        <form method="POST" action="{$deleteUrl}">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            HTML;
        }
        $html .= <<<HTML
                    </tbody>
                </table>
            </div>
        HTML;

        return $html;
    }

    /**
     * Affichage d'un item
     *
     * @return string
     */
    private function displayItem(): string
    {
        $item = $this->params['item'];
        $list = $this->params['list'];
        $user = Auth::getUser();

        $listUrl = $this->pathFor('displayList', [
            'token' => $list->token
        ]);

        $html = <<<HTML
            <div class="container page-item-show">
                <a href="{$listUrl}">Retour à la liste</a><br><br>
                <h1>{$item->name}</h1>
                <p>{$item->description}</p>
                <img src="/img/{$item->image}" alt="Image de l'item" width="250">
                <br>
                <p><strong>Prix : </strong>{$item->price} €</p>
        HTML;

        // URL
        if ($item->url && $item->url !== '') {
            $html .= <<<HTML
                <p><strong>Lien externe : </strong><a href="{$item->url}" target="_blank">{$item->url}</a></p>
            HTML;
        }

        // Cagnotte
        if ($item->foundingPot) {
            $rest = $item->foundingPot->getRest();

            if ($rest > 0) {
                $foundingPotUrl = $this->pathFor('participateFoundingPotPage', [
                    'item_id' => $item->id
                ]);
                $html .= <<<HTML
                    <p><strong>Cagnotte : </strong> {$item->foundingPot->getRest()} € restant à payer</p>
                    <a href="{$foundingPotUrl}" class="btn btn-secondary">Participer à la cagnotte</a>
                HTML;
            } else {
                $html .= <<<HTML
                    <p><strong>Cagnotte : </strong> complétée.</p>
                HTML;
            }
        }

        // Réservation
        if ($item->reservation) {
            if ($list->isExpired() && $user && $list->user_id !== $user['id']) {
                $html .= <<<HTML
                    <br><br><hr><br>
                    <p><i>Réservé par {$item->reservation->user->getFullname()}.</i></p>
                HTML;
            } else {
                $html .= <<<HTML
                    <br><hr><br>
                    <p><i>Réservé.</i></p>
                HTML;
            }

            // Annuler la réservation
            if ($user && $item->reservation->user_id === $user['id']) {
                $cancelLockItem = $this->pathFor('cancelLockItem', ['id' => $item->id]);
                $html .= <<<HTML
                    <form method="POST" action="{$cancelLockItem}" onsubmit=" return confirm('Êtes-vous sûr de vouloir annuler votre réservation ?')">
                        <button class="btn btn-danger">Annuler la réservation</button>
                    </form>
                HTML;
            }
        } else {
            $lockUrl = $this->pathFor('lockItemPage', ['id' => $item->id]);
            $html .= <<<HTML
                <a href="{$lockUrl}" class="btn btn-primary">Réserver</a>
            HTML;
        }

        return $html . <<<HTML
            </div>
        HTML;
    }

    /**
     * Construit la page d'édition d'un item
     *
     * @return string
     */
    private function editItemPage(): string
    {
        $item = $this->params['item'];
        $editUrl = $this->pathFor('editItem', ['id' => $item->id]);

        return <<<HTML
            <div class="container">
                <h1>Éditer un item</h1>
                <form method="POST" action="{$editUrl}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="list_id">ID de la liste</label>
                        <input type="text" name="list_id" id="list_id" value="{$item->list_id}">
                    </div>        
                    <div class="form-group">
                        <label for="name">Nom</label>
                        <input type="text" name="name" id="name" value="{$item->name}">
                    </div>        
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" cols="30" rows="10">{$item->description}</textarea>
                    </div>        
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" name="image" id="image" value="{$item->image}" accept=".png, .jpg">
                    </div>        
                    <div class="form-group">
                        <label for="url">URL</label>
                        <input type="text" name="url" id="url" value="{$item->url}">
                    </div>      
                    <div class="form-group">
                        <label for="price">Tarif</label>
                        <input type="text" name="price" id="price" value="{$item->price}">
                    </div>
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>  
                </form>
            </div>
        HTML;
    }

    /**
     * Construit la page de formulaire pour la reservation d'un item
     *
     * @return string
     */
    public function lockItem(): string
    {
        $list = $this->params['list'];
        $item = $this->params['item'];
        $imgUrl = "/img/{$item->image}";
        $lockUrl = $this->pathFor('lockItem', ['id' => $item->id]);
        $cancelUrl = $this->pathFor('displayItem', [
            'token' => $list->token,
            'id' => $item->id
        ]);

        return <<<HTML
            <div class="container lock-item">
                <h1>Réserver un item</h1>
                <div class="item-recap">
                    <h2>Récapitulatif de l'item à réserver : </h2>
                    <img src="{$imgUrl}" alt="Image de l'item">
                    <p><strong>Nom :</strong> {$item->name}</p>
                    <p><strong>Description :</strong> {$item->description}</p>
                    <p><strong>Prix :</strong> {$item->price} €</p>
                </div> 
                <br>
                <div class="item-message">
                    <form method="POST" action="{$lockUrl}">
                        <div class="form-group">
                            <label for="message">Message (optionnel)</label>
                            <input type="text" name="message" id="message" placeholder="Ecrivez votre message">
                        </div>        
                    
                        <button type="submit" class="btn btn-primary">Confirmer la réservation</button>  
                        <a href="{$cancelUrl}" class="btn btn-secondary">Annuler</a>
                    </form>
                </div>
               
            </div>
        HTML;
    }

    /**
     * @inheritdoc
     */
    public function render(int $selector): string
    {
        $title = "MyWhishList | ";
        switch ($selector) {
            case 0: {
                    $content = $this->newItemPage();
                    $title .= "Ajouter un item";
                    break;
                }
            case 1: {
                    $content = $this->getAllItems();
                    $title .= "Liste des items";
                    break;
                }
            case 2: {
                    $content = $this->displayItem();
                    $title .= "Affichage d'un item";
                    break;
                }
            case 3: {
                    $content = $this->editItemPage();
                    $title .= "Éditer un item";
                    break;
                }
            case 4: {
                    $content = $this->lockItem();
                    $title .= "Réserver un item";
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
