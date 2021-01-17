<?php

namespace Whishlist\Views\Components;

use Whishlist\Helpers\Auth;

class Menu extends BaseComponent
{
    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $homeUrl = $this->pathFor('home');

        $html = <<<HTML
            <div class="nav">
                <nav>
                    <a href="{$homeUrl}">Accueil</a>
        HTML;

        if (Auth::isLogged()) {
            $listsUrl = $this->pathFor('displayAllLists');
            $accountUrl = $this->pathFor('displayAccount');

            $html .= <<<HTML
                <a href="{$listsUrl}">Mes listes</a>
                <a href="{$accountUrl}">Mon compte</a>
            HTML;
        } else {
            $loginUrl = $this->pathFor('loginPage');
            $registerUrl = $this->pathFor('registerPage');

            $html .= <<<HTML
                <a href="{$loginUrl}">Connexion</a>
                <a href="{$registerUrl}">Inscription</a>
            HTML;
        }

        return $html . <<<HTML
                </nav>
            </div>
        HTML;
    }
}
