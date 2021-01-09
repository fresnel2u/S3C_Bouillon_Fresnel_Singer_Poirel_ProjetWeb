<?php

namespace Whishlist\Views;

use Whishlist\Helpers\ViewHelpers;
use Whishlist\Views\Components\Menu;
use Whishlist\Views\Components\Header;

session_start();

class ListView extends BaseView
{
    /**
     * Construit le contenu d'un formulaire de creation de liste
     *
     * @return string l'HTML du formulaire de creation de liste
     */
    private function newListPage(): string
    {
        return <<<HTML
            <div class="container">
                <h1>Créer une liste</h1>
                <form method="POST" action="{$this->container->router->pathFor('newList')}">
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
        $logoutUrl = $this->container->router->pathFor('logout');
        $html = ViewHelpers::generateLogOut($logoutUrl);

        $html .= <<<HTML
            <h1>Listes de souhaits</h1>
            <a href="{$this->container->router->pathFor('newListPage')}" class="btn btn-primary">Ajouter une liste</a>
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
            
            $editUrl = $this->container->router->pathFor('editListPage', ['id' => $list->id]);
            $deleteUrl = $this->container->router->pathFor('deleteList', ['id' => $list->id]);

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
     * Construit le contenu d'une liste de souhaits
     *
     * @return string l'HTML d'une liste de souhaits
     */
    private function getList(): string
    {
        $logOut = $this->container->router->pathFor('logout');
        $html = ViewHelpers::generateLogOut($logOut);

        $html .= <<<HTML
            <h1>Items de la liste :</h1>
            <div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">item id</th>
                            <th scope="col">liste id</th>
                            <th scope="col">nom</th>
                            <th scope="col">description</th>
                            <th scope="col">image</th>
                            <th scope="col">url</th>
                            <th scope="col">tarif</th>
                            <th scope="col">action</th>
                        </tr>
                    </thead>
                    <tbody>
        HTML;
        foreach ($this->params['items'] as $item) {
            $html .= "<tr>";
            $i = 0;

            foreach ($item->toArray() as $row) {
                if ($i === 4) {
                    $url = "/img/{$row}";
                    $html .= "<td><img src=\"{$url}\" width=\"150\"/></td>";
                } else if ($i !== 7) { // Don't show user_id column
                    $html .= "<td>{$row}</td>";
                }
                $i += 1;
            }

            $lockUrl = $this->container->router->pathFor('lockItem', ['id' => $item->id]);

            $html .= <<<HTML
                    <td>
                        <form action="{$lockUrl}" method="POST">
                            <button type="submit">Reserver</button>
                        </form>
                    </td>
                </tr>
            HTML;
        }
        $html .= "</tbody>
            </table>
        </div>";

        return $html;
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

        $html = Header::getHeader($title);
        $html .= Menu::getMenu();
        $html .= $content;
        $html .= "</body></html>";
        return $html;
    }
}