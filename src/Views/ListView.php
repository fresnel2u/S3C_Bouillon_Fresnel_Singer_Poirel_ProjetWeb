<?php

namespace Whishlist\Views;

use Whishlist\Helpers\Auth;

class ListView extends BaseView
{
    /**
     * Construit le contenu d'un formulaire de creation de liste
     *
     * @return string l'HTML du formulaire de creation de liste
     */
    private function newListPage(): string
    {
        $newListUrl = $this->container->router->pathFor('newList');

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
        $newListUrl = $this->container->router->pathFor('newListPage');

        $html = <<<HTML
            <h1>Listes de souhaits</h1>
            <a href="{$newListUrl}" class="btn btn-primary">Ajouter une liste</a>
            <div>
                <table class="table">
                <thead>
                    <tr>
                        <th scope="col">id</th>
                        <th scope="col">user_id</th>
                        <th scope="col">titre</th>
                        <th scope="col">description</th>
                        <th scope="col">expiration</th>
                        <th scope="col">token</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                        <tbody> 
        HTML;

        foreach ($this->params['lists'] as $list) {
            $html .= "<tr>";

            foreach ($list->toArray() as $row) {
                $html .= "<td>$row</td>";
            }
            
            $showUrl = $this->container->router->pathFor('displayList', ['id' => $list->id]);
            $editUrl = $this->container->router->pathFor('editListPage', ['id' => $list->id]);
            $deleteUrl = $this->container->router->pathFor('deleteList', ['id' => $list->id]);

            $html .= <<<HTML
                    <td>
                        <a href="{$showUrl}" class="btn btn-light">Aperçu</a>
                        <a href="{$editUrl}" class="btn btn-light">Éditer</a>
                        <form method="POST" action="{$deleteUrl}">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            HTML;
        }
        $html .= '</tbody>
            </table>
        </div>';
        return $html;
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

        $html = <<<HTML
            <h1>Items de la liste "{$list->title}":</h1>
            <div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nom</th>
                            <th scope="col">Description</th>
                            <th scope="col">Image</th>
                            <th scope="col">URL</th>
                            <th scope="col">Tarif</th>
                            <th scope="col">Cagnotte</th>
                            <th scope="col">Réservation</th>
                        </tr>
                    </thead>
                    <tbody>
        HTML;
        foreach ($items as $item) {
            $html .= <<<HTML
                <tr>
                    <td>{$item->id}</td>
                    <td>{$item->name}</td>
                    <td>{$item->description}</td>
                    <td><img src="/img/{$item->image}" alt="Image de l'item {$item->id}" width="150" /></td>
                    <td>{$item->url}</td>
                    <td>{$item->price} €</td>
            HTML;

            // Cagnotte
            if ($item->foundingPot) {
                $foundingPotUrl = $this->container->router->pathFor('participateFoundingPotPage', [
                    'item_id' => $item->id
                ]);
                $html .= <<<HTML
                    <td>
                        <a href="{$foundingPotUrl}" class="btn btn-light">Participer à la cagnotte</a>
                    </td>
                HTML;
            } else {
                $html .= <<<HTML
                    <td>Pas de cagnotte.</td>
                HTML;
            }

            // Réservation
            if ($item->reservation) {
                $html .= <<<HTML
                    <td>Réservé par {$item->reservation->user->firstname} {$item->reservation->user->lastname}</td>
                HTML;
            } else if (Auth::isLogged()) {
                $lockUrl = $this->container->router->pathFor('lockItemPage', ['id' => $item->id]);
                $html .= <<<HTML
                    <td><a href="{$lockUrl}" class="btn btn-light">Réserver</button></td>
                HTML;
            } else {
                $html .= <<<HTML
                    <td>Vous devez être connecté pour réserver.</td>
                HTML;
            }

            $html .= '<tr>';
        }

        return $html . <<<HTML
                    </tbody>
                </table>
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
        $editUrl = $this->container->router->pathFor('editList', ['id' => $list->id]);

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
                        <input type="date" name="expiration" id="expiration" value="{$list->expiration}">
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
            default: {
                    $content = '';
                    break;
                }
        }

        return $this->layout($content, $title);
    }
}