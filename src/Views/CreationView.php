<?php

namespace Whishlist\Views;

use Whishlist\Views\Components\Menu;
use Whishlist\Views\Components\Header;

class CreationView
{
    private $container;

    private $model;

    /**
     * Constructeur de la vue
     *
     * @param mixed $m - model pour recuperer les donnees de la bdd
     * @param \Slim\Container $c - container
     */
    public function __construct($m, \Slim\Container $c)
    {
        $this->model = $m;
        $this->container = $c;
    }

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
     * Construit le contenu d'un formulaire d'edition de liste
     *
     * @return string
     */
    private function editListPage(): string
    {
        $list = $this->model;
        $editUrl = $this->container->router->pathFor('editList', [
            'id' => $list['id']
        ]);

        return <<<HTML
            <div class="container">
                <h1>Éditer une liste</h1>
                <form method="POST" action="{$editUrl}">
                    <div class="form-group">
                        <label for="title">Titre</label>
                        <input type="text" name="title" id="title" value="{$list['title']}">
                    </div>        
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" cols="30" rows="10">{$list['description']}</textarea>
                    </div>        
                    <div class="form-group">
                        <label for="expiration">Expiration</label>
                        <input type="date" name="expiration" id="expiration" value="{$list['expiration']}">
                    </div>        
                    <div class="form-group">
                        <label for="token">Token</label>
                        <input type="text" name="token" id="token" value="{$list['token']}">
                    </div>      
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>  
                </form>
            </div>
        HTML;
    }

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
     * Construit la page d'édition d'un item
     *
     * @return string
     */
    private function editItemPage(): string
    {
        $item = $this->model;
        $editUrl = $this->container->router->pathFor('editItem', [
            'id' => $item['id']
        ]);

        return <<<HTML
            <div class="container">
                <h1>Éditer un item</h1>
                <form method="POST" action="{$editUrl}">
                    <div class="form-group">
                        <label for="list_id">ID de la liste</label>
                        <input type="text" name="list_id" id="list_id" value="{$item['list_id']}">
                    </div>        
                    <div class="form-group">
                        <label for="name">Nom</label>
                        <input type="text" name="name" id="name" value="{$item['name']}">
                    </div>        
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" cols="30" rows="10">{$item['description']}</textarea>
                    </div>        
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="text" name="image" id="image" value="{$item['image']}">
                    </div>        
                    <div class="form-group">
                        <label for="url">URL</label>
                        <input type="text" name="url" id="url" value="{$item['url']}">
                    </div>      
                    <div class="form-group">
                        <label for="price">Tarif</label>
                        <input type="text" name="price" id="price" value="{$item['price']}">
                    </div>
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>  
                </form>
            </div>
        HTML;
    }

    /**
     * Construit la page entiere selon le parametre
     *
     * @param integer $selector - selon sa valeur, la methode execute une methode differente et renvoit une page adaptee a la demande
     * @return string l'HTML de la page complete
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
                    $content = $this->editListPage();
                    $title .= "Éditer une liste";
                    break;
                }
            case 2: {
                    $content = $this->newItemPage();
                    $title .= "Ajouter un item";
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
