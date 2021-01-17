<?php

namespace Whishlist\Views\Components;

use Whishlist\Helpers\Auth;

class Menu
{
    /**
     * Construit le menu de la page
     *
     * @return string l'HTML de la navigation
     */
    public static function getMenu(): string
    {
        return <<<HTML
            <div class="nav">
                <nav>
                    <a href="/">Accueil</a>
                    <a href="/lists">Mes listes</a>
                    <a href="/account">Mon compte</a>
                </nav>
            </div>
        HTML;
    }
}
