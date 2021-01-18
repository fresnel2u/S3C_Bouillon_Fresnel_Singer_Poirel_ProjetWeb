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
        $list = $this->params['list'];
        $items = $this->params['items'];

        $newItemUrl = $this->pathFor('newItemPage', ['list_id' => $list->id]);
        $backUrl = $this->pathFor('displayAllLists');
        
        $html = <<<HTML
            <div class="container container-full">
                <a href="{$backUrl}">Retour</a><br><br>
                <h1>Items de la liste "{$list->title}"</h1>
                <a href="{$newItemUrl}" class="btn btn-primary">Ajouter un item</a>
                <div class="table-wrapper">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Nom</th>
                                <th class="table-center">Lien externe</th>
                                <th>Prix</th>
                                <th>Cagnotte</th>
                                <th class="table-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
        HTML;

        foreach ($items as $item) {
            $img = '';
            if ($item->image && $item->image !== '') {
                $img = <<<HTML
                    <img src="/img/{$item->image}" alt="Image de l'item {$item->id}" width="50" />
                HTML;
            }
            $extern = '/';
            if ($item->url && $item->url !== '') {
                $extern = <<<HTML
                    <a href="{$item->url}" target="_blank">{$item->url}</a>
                HTML;
            }

            $price = number_format($item->price, 2);

            $html .= <<<HTML
                <tr>
                    <td>{$item->id}</td>
                    <td>{$img}</td>
                    <td>{$item->name}</td>
                    <td class="table-center">{$extern}</td>
                    <td>{$price} €</td>
            HTML;

            if ($item->foundingPot) {
                $goal = number_format($item->foundingPot->amount, 2);
                $current = number_format($goal - $item->foundingPot->getRest(), 2);

                $html .= <<<HTML
                    <td>
                      {$current}  / {$goal} €
                    </td>
                HTML;
            } else {
                $newFoundingPotUrl = $this->pathFor('createFoundingPotPage', ['list_id' => $list->id, 'item_id' => $item->id]);
                $html .= <<<HTML
                    <td>
                      <a href="{$newFoundingPotUrl}" class="btn btn-light">Créer une cagnotte</a>
                    </td>
                HTML;
            }

            $editUrl = $this->pathFor('editItemPage', ['list_id' => $list->id, 'item_id' => $item->id]);
            $deleteUrl = $this->pathFor('deleteItem', ['list_id' => $list->id, 'item_id' => $item->id]);

            $html .= <<<HTML
                    <td class="table-actions">
                        <div>
                            <a href="{$editUrl}" class="btn btn-light">Éditer</a>
                            <form method="POST" action="{$deleteUrl}" onsubmit=" return confirm('Êtes-vous sûr de vouloir supprimer cet item ?')">
                                <button class="btn btn-danger">Supprimer</button>
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
     * Affichage d'un item
     *
     * @return string
     */
    private function displayItem(): string
    {
        $item = $this->params['item'];
        $list = $this->params['list'];
        $user = Auth::getUser();

        $listUrl = $this->pathFor('displayList', ['token' => $list->token]);

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
                $foundingPotUrl = $this->pathFor('participateFoundingPotPage', ['token' => $list->token, 'item_id' => $item->id]);
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
            if ($list->isExpired() && $list->user_id != Auth::getLastUserId()) {
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
                $cancelLockItem = $this->pathFor('cancelLockItem', ['list_id' => $list->id, 'item_id' => $item->id]);
                $html .= <<<HTML
                    <form method="POST" action="{$cancelLockItem}" onsubmit=" return confirm('Êtes-vous sûr de vouloir annuler votre réservation ?')">
                        <button class="btn btn-danger">Annuler la réservation</button>
                    </form>
                HTML;
            }
        } else {
            $lockUrl = $this->pathFor('lockItemPage', ['list_id' => $list->id, 'item_id' => $item->id]);
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
        $list = $this->params['list'];
        $item = $this->params['item'];
        $editUrl = $this->pathFor('editItem', ['list_id' => $list->id, 'item_id' => $item->id]);

        return <<<HTML
            <div class="container">
                <h1>Éditer un item</h1>
                <form method="POST" action="{$editUrl}" enctype="multipart/form-data">   
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
        $lockUrl = $this->pathFor('lockItem', ['list_id' => $list->id, 'item_id' => $item->id]);
        $cancelUrl = $this->pathFor('displayItem', [
            'token' => $list->token,
            'item_id' => $item->id
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
