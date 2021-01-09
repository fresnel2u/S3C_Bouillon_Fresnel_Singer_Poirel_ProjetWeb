<?php

namespace Whishlist\Views;

class HomeView extends BaseView
{
    /**
     * Construit le contenu de la page d'accueil
     *
     * @return string l'HTML de la page d'accueil
     */
    private function getHome(): string
    {
        return <<<HTML
            <h1 style="text-align : center;">Page d'accueil | TODO</h1>
        HTML;
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

        return $this->layout($content, $title);
    }
}
