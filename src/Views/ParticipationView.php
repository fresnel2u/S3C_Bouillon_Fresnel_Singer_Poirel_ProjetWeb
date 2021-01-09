<?php

namespace Whishlist\Views;

session_start();

use Whishlist\helpers\Authentication;
use \Whishlist\helpers\ViewHelpers;
use Whishlist\Views\Components\Header;
use Whishlist\Views\Components\Menu;

class ParticipationView
{
    private $model;

    /**
     * Constructeur de la vue
     *
     * @param array $m - model pour recuperer les donnees de la bdd
     * @param \Slim\Container $c - container
     */
    public function __construct(array $m, \Slim\Container $c)
    {
        $this->model = $m;
        $this->container = $c;
    }

    /**
     * Construit le contenu des listes de souhaits
     *
     * @return string l'HTML des listes de souhaits
     */
    private function getAllList(): string
    {
        $logOut = $this->container->router->pathFor('logout');
        $html = ViewHelpers::generateLogOut($logOut);

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
        foreach ($this->model as $list) {
            $html .= "<tr>";
            foreach ($list as $row) {
                $html .= "<td>$row</td>";
            }
            $editUrl = $this->container->router->pathFor('editListPage', [
                'id' => $list['id']
            ]);

            $deleteUrl = $this->container->router->pathFor('deleteList', [
                'id' => $list['id']
            ]);

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
        foreach ($this->model as $item) {
            $html .= "<tr>";
            $i = 0;
            foreach ($item as $row) {
                if ($i === 4) {
                    $url = "/img/{$row}";
                    $html .= "<td><img src=\"{$url}\" width=\"150\"/></td>";
                } else if ($i !== 7) { // Don't show user_id column
                    $html .= "<td>{$row}</td>";
                }
                $i += 1;
            }
            $html .= "<td><form action='/items/" . $item['id'] . "/lock' method='POST'><button type='submit'>Reserver</button></form></td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>
            </table>
        </div>";

        return $html;
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


        foreach ($this->model as $modele) {
            $html .= "<tr>";
            $i = 0;
            foreach ($modele as $col) {
                if ($i === 4) {
                    $url = "/img/{$col}";
                    $html .= "<td><img src=\"{$url}\" width=\"150\"/></td>";
                } else {
                    $html .= "<td>{$col}</td>";
                }
                $i++;
            }
            $editUrl = $this->container->router->pathFor('editItem', [
                'id' => $modele['id']
            ]);
            $deleteUrl = $this->container->router->pathFor('deleteItem', [
                'id' => $modele['id']
            ]);
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
        foreach ($this->model as $col) {
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
     * Construit le contenu de la page de compte
     *
     * @return string l'HTML des informations de compte
     */
    private function getAccount(): string
    {
        $editAccount = $this->container->router->pathFor('editAccountPage');
        $deleteAccount = $this->container->router->pathFor('deleteAccount');
        $logOut = $this->container->router->pathFor('logout');
        $user = Authentication::getUser();

        $html = ViewHelpers::generateLogOut($logOut);
        $html .= <<<HTML
        <div class="account">
            <h1> Mon compte - Informations </h1>
            <div class="account-container">
                <div class="account-informations">
                    <p> Nom : {$user->lastname}  </p>
                    <p> Prénom : {$user->firstname} </p>
                    <p> Email : {$user->email}</p>
                </div>
                <div class="account-actions">
                    <form method="GET" action="{$editAccount}">
                        <button class="btn btn-primary">Éditer</button>
                    </form>
                    <form method="POST" action="{$deleteAccount}" onsubmit="return confirm('Voulez vous supprimer votre compte ?');">
                        <button type="submit" class="btn btn-danger">Supprimer mon compte</button>
                    </form>
                </div>
            </div>
        </div>
        HTML;

        return $html;
    }

    /**
     * Construit le contenu d'un formulaire d'edition de compte
     *
     * @return string
     */
    private function editAccountPage(): string
    {
        $editAccount = $this->container->router->pathFor('editAccount');
        $user = Authentication::getUser();
        return <<<HTML
            <div class="container">
                <h1>Éditer mon compte</h1>
                <form method="POST" action="{$editAccount}" onsubmit="return confirm('Voulez-vous sauvegarder les changements effectués ?');">
                    <div class="form-group">
                        <label for="lastname">Nom</label>
                        <input type="text" name="lastname" id="lastname" value="{$user->lastname}">
                    </div>        
                    <div class="form-group">
                        <label for="firstname">Prénom</label>
                        <input type="text" name="firstname" id="firstname" value="{$user->firstname}">

                    </div>        
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email" value="{$user->email}">
                    </div>        
                     
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>  
                </form>
            </div>
        HTML;
    }


    /** 
     * Construit la page entiere selon le selecteur
     *
     * @param integer $selector - selon sa valeur, la methode execute une methode differente et renvoit une page adaptee a la demande
     * @return string l'HTML de la page complete
     */
    public function render(int $selector): string
    {
        $title = "MyWishList | ";
        switch ($selector) {
            case 0: {
                    $content = $this->getAllList();
                    $title .= "Listes de souhaits";
                    break;
                }
            case 1: {
                    $content = $this->getList();
                    $title .= "Liste";
                    break;
                }
            case 2: {
                    $content = $this->getAllItems();
                    $title .= "Liste des items";
                    break;
                }
            case 3: {
                    $content = $this->getItem();
                    $title .= "Item";
                    break;
                }
            case 4: {
                    $content = $this->getAccount();
                    $title .= "Mon compte";
                    break;
                }
            case 5: {
                    $content = $this->editAccountPage();
                    $title .= "Editer mon compte";
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
