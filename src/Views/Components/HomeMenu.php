<?php

namespace Whishlist\Views\Components;

use Whishlist\Helpers\Auth;

class HomeMenu extends BaseComponent
{
    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $publicListsUrl = $this->pathFor('publicLists');

        $html = <<<HTML
            <div class="home-header">
                <div class="header-title">
                    <img src="/img/icons/wishlist_icon.svg" alt="wishlist">
                    <h2>MyWishList</h2>
                </div>
                <div class="header-log">
                    <a href="{$publicListsUrl}">Listes publiques</a>
        HTML;
        $registerUrl = $this->pathFor('registerPage');
        if (Auth::isLogged()) {
            $accountUrl = $this->pathFor('displayAccount');
            $html .= <<<HTML
                <a href="{$accountUrl}"><button class="btn btn-primary">Mon compte</button></a>
            HTML;
        } else {

            $loginUrl = $this->pathFor('loginPage');
            $html .= <<<HTML
                <a href="{$loginUrl}"><button class="btn btn-secondary">Connexion</button></a>
                <a href="{$registerUrl}"><button class="btn btn-primary">Inscription</button></a>
            HTML;
        }

        return $html . <<<HTML
                </div>
            </div>
        HTML;
    }
}
