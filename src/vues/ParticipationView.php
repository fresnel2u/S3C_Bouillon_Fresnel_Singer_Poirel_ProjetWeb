<?php

namespace Whishlist\vues;

class ParticipationView
{
    private $modeles;

    /**
     * Constructeur de la vue
     *
     * @param array $m - modeles pour recuperer les donnees de la bdd
     * @param \Slim\Container $c - container
     */
    public function __construct(array $m, \Slim\Container $c)
    {
        $this->modeles = $m;
        $this->container = $c;
    }

    /**
     * Construit le contenu des listes de souhaits
     *
     * @return string l'HTML des listes de souhaits
     */
    private function getAllList(): string
    {
        $html = <<<HTML
            <h1 style="text-align : center;"> Liste des listes de souhaits : </h1>
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
                    </tr>
                </thead>
                        <tbody> 
        HTML;
        foreach ($this->modeles as $liste) {
            $html .= "<tr>";
            foreach ($liste as $row) {
                $html .= "<td>$row</td>";
            }
            $html .= "</tr>";
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

        $html = <<<HTML
            <h1> Résultat de l'affichage de la liste : </h1>"
            <div>
                <table class="table">
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
        HTML;
        foreach ($this->modeles as $it) {
            $html .= "<tr>";
            $i = 0;
            foreach ($it as $row) {
                if ($i === 4) {
                    $url = "/img/{$row}";
                    $html .= "<td><img src=\"{$url}\" width=\"150\"/></td>";
                } else {
                    $html .= "<td>{$row}</td>";
                }
                $i += 1;
            }
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
        $html = <<<HTML
            <h1>Résultat de l'affichage de l'item :</h1>'
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
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
        HTML;
        
        
        foreach ($this->modeles as $modele) {
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
            $edit = $this->container->router->pathFor('editItem', [
                'id' => $modele['id']
            ]);
            $html .= "<td><a href=\"{$edit}\">Éditer</a></td>";
            $html .= "</tr>";
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
        $html = <<<HTML
            <h1>Tous les items :</h1>'
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
        foreach ($this->modeles as $col) {
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
