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
        $accountLink = Auth::isLogged() ? '<li><a href="/account">Compte</a></li>' : '';
        
        return <<<HTML
            <ul style="display : flex; justify-content : space-between;">
                <li><a href="/">Accueil</a></li>
                <li><a href="/login">Connexion</a></li>
                <li><a href="/register">Inscription</a></li>
                <li><a href="/lists">Listes</a></li>
                <li><a href="/items">Items</a></li>
                {$accountLink}
            </ul>
        HTML;
    }
}
