<?php

namespace Whishlist\vues\composants;

class Menu
{
       
    /**
     * construit le menu de la page
     *
     * @return string l'HTML de la navigation
     */
    public static function getMenu() : string
    {
        $html = ' 
        <ul style="display : flex; justify-content : space-between;">
        <li><a href="/"> Accueil </a></li>
        <li><a href="/allList"> Listes de souhaits </a></li>
        <li><a href="/list/1"> Liste </a></li>
        <li><a href="/item/1"> Item </a></li>
        <li><a href="/newList"> Nouvelle liste </a></li>
        </ul>
        ';

        return $html;
    }
}