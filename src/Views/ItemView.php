<?php

namespace Whishlist\Views;

use Whishlist\Helpers\ViewHelpers;
use Whishlist\Views\Components\Menu;
use Whishlist\Views\Components\Header;

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
                <form method="POST">
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
                        <input type="text" name="image" id="image">
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
        $logOut = $this->container->router->pathFor('logout');
        $html = ViewHelpers::generateLogOut($logOut);

        $html .= <<<HTML
            <h1>Résultat de l'affichage de l'item :</h1>
            <a href="{$this->container->router->pathFor('newItemPage')}" class="btn btn-primary">Ajouter un item</a>
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
                            <th scope="col">user id</th>
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

            $editUrl = $this->container->router->pathFor('editItem', ['id' => $item->id]);
            $deleteUrl = $this->container->router->pathFor('deleteItem', ['id' => $item->id]);

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
        $html .= '</tbody>
            </table>
        </div>';

        return $html;
    }

    /**
     * Construit le contenu d'un item
     *
     * @return string l'HTML d'un item
     */
    private function getItem(): string
    {
        $logOut = $this->container->router->pathFor('logout');
        $html = ViewHelpers::generateLogOut($logOut);

        $html .= <<<HTML
            <h1>Tous les items :</h1>
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
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
        HTML;
        $i = 0;
        foreach ($this->params['item']->toArray() as $col) {
            if ($i === 4) {
                $url = "/img/{$col}";
                $html .= "<td><img src=\"{$url}\" width=\"150\"/></td>";
            } else {
                $html .= "<td>{$col}</td>";
            }
            $i++;
        }
        $html .= "</tr>;
                </tbody>
            </table>
        </div>";

        return $html;
    }

    /**
     * Construit la page d'édition d'un item
     *
     * @return string
     */
    private function editItemPage(): string
    {
        $item = $this->params['item'];
        $editUrl = $this->container->router->pathFor('editItem', ['id' => $item->id]);

        return <<<HTML
            <div class="container">
                <h1>Éditer un item</h1>
                <form method="POST" action="{$editUrl}">
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
                        <input type="text" name="image" id="image" value="{$item->image}">
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
                    $content = $this->getItem();
                    $title .= "Item";
                    break;
                }
            case 3: {
                    $content = $this->editItemPage();
                    $title .= "Éditer un item";
                    break;
                }
            default: {
                    $content = '';
                    break;
                }
        }

        $html = Header::getHeader($title);
        $html .= Menu::getMenu();
        $html .= $content;
        $html .= "</body></html>";
        return $html;
    }
}