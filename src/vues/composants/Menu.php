<?php

namespace Whishlist\vues\composants;

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
            <ul style="display : flex; justify-content : space-between;">
                <li><a href="/">Accueil</a></li>
                <li><a href="/login">Connexion</a></li>
                <li><a href="/register">Inscription</a></li>
                <li><a href="/allList">Listes de souhaits</a></li>
                <li><a href="/list/1">Liste</a></li>
                <li><a href="/items">Items</a></li>
                <li><a href="/newList">Nouvelle liste</a></li>
            </ul>
        HTML;
    }
}
