<?php

namespace Whishlist\Views;

use Whishlist\Views\Components\Menu;
use Whishlist\Views\Components\Header;

class HomeView extends BaseView
{
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
        return $html;
    }

    /**
     * @inheritdoc
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

        $html = Header::getHeader($title);
        $html .= Menu::getMenu();
        $html .= $content;
        $html .= "</body></html>";
        return $html;
    }
}
