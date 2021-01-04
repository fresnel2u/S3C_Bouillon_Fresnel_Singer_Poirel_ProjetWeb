<?php

namespace Whishlist\vues;

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
    private function newList(): string
    {
        return <<<HTML
            <div class="container">
                <h1>Créer une liste</h1>
                <form method="POST" action="{$this->container->router->pathFor('newList')}">
                    <div class="form-group">
                        <label for="titre">Titre</label>
                        <input type="text" name="titre" id="titre">
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
                        <label for="liste_id">ID de la liste</label>
                        <input type="text" name="liste_id" id="liste_id">
                    </div>        
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" name="nom" id="nom">
                    </div>        
                    <div class="form-group">
                        <label for="descr">Description</label>
                        <textarea name="descr" id="descr" cols="30" rows="10"></textarea>
                    </div>        
                    <div class="form-group">
                        <label for="img">Image</label>
                        <input type="text" name="img" id="img">
                    </div>        
                    <div class="form-group">
                        <label for="url">URL</label>
                        <input type="text" name="url" id="url">
                    </div>      
                    <div class="form-group">
                        <label for="tarif">Tarif</label>
                        <input type="text" name="tarif" id="tarif">
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

        return <<<HTML
            <div class="container">
                <h1>Éditer un item</h1>
                <form method="POST">
                    <div class="form-group">
                        <label for="liste_id">ID de la liste</label>
                        <input type="text" name="liste_id" id="liste_id" value="{$item['liste_id']}">
                    </div>        
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" name="nom" id="nom" value="{$item['nom']}">
                    </div>        
                    <div class="form-group">
                        <label for="descr">Description</label>
                        <textarea name="descr" id="descr" cols="30" rows="10">{$item['descr']}</textarea>
                    </div>        
                    <div class="form-group">
                        <label for="img">Image</label>
                        <input type="text" name="img" id="img" value="{$item['img']}">
                    </div>        
                    <div class="form-group">
                        <label for="url">URL</label>
                        <input type="text" name="url" id="url" value="{$item['url']}">
                    </div>      
                    <div class="form-group">
                        <label for="tarif">Tarif</label>
                        <input type="text" name="tarif" id="tarif" value="{$item['tarif']}">
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
                    $content = $this->newList();
                    $title .= "Créer une liste";
                    break;
                }
            case 1: {
                    $content = $this->newItemPage();
                    $title .= "Ajouter un item";
                    break;
                }
            case 2: {
                    $content = $this->editItemPage();
                    $title .= "Éditer un item";
                    break;
                }
            default: {
                    $content = '';
                    break;
                }
        }

        $html = composants\Header::getHeader($title);
        $html .= composants\Menu::getMenu();
        $html .= $content;
        $html .= "</body></html>";
        return $html;
    }
}
