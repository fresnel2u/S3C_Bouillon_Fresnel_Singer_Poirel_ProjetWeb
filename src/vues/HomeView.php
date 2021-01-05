<?php
namespace Whishlist\vues;

session_start();
class HomeView
{
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
     * Construit le contenu de la page d'accueil
     *
     * @return string l'HTML de la page d'accueil
     */
    private function getHome(): string
    {
        $html = <<<HTML
            <h1 style="text-align : center;">Page d'accueil | TODO</h1>
        HTML;
        $html .= "<p> ". $_SESSION['user']. "</p>" ;
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
                    $content = $this->getHome();
                    $title .= "Accueil";
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
