<?php

namespace Whishlist\Views;

use Whishlist\Helpers\ViewHelpers;
use Whishlist\Views\Components\Menu;
use Whishlist\Views\Components\Header;

class AccountView extends BaseView
{
    /**
     * Construit le contenu de la page de compte
     *
     * @return string l'HTML des informations de compte
     */
    private function getAccount(): string
    {
        $editAccount = $this->container->router->pathFor('editAccountPage');
        $deleteAccount = $this->container->router->pathFor('deleteAccount');
        $logOut = $this->container->router->pathFor('logout');
        $user = $this->params['user'];

        $html = ViewHelpers::generateLogOut($logOut);
        $html .= <<<HTML
        <div class="account">
            <h1> Mon compte - Informations </h1>
            <div class="account-container">
                <div class="account-informations">
                    <p> Nom : {$user->lastname}  </p>
                    <p> Prénom : {$user->firstname} </p>
                    <p> Email : {$user->email}</p>
                </div>
                <div class="account-actions">
                    <form method="GET" action="{$editAccount}">
                        <button class="btn btn-primary">Éditer</button>
                    </form>
                    <form method="POST" action="{$deleteAccount}" onsubmit="return confirm('Voulez vous supprimer votre compte ?');">
                        <button type="submit" class="btn btn-danger">Supprimer mon compte</button>
                    </form>
                </div>
            </div>
        </div>
        HTML;

        return $html;
    }

    /**
     * Construit le contenu d'un formulaire d'edition de compte
     *
     * @return string
     */
    private function editAccountPage(): string
    {
        $editAccount = $this->container->router->pathFor('editAccount');
        $user = $this->params['user'];

        return <<<HTML
            <div class="container">
                <h1>Éditer mon compte</h1>
                <form method="POST" action="{$editAccount}" onsubmit="return confirm('Voulez-vous sauvegarder les changements effectués ?');">
                    <div class="form-group">
                        <label for="lastname">Nom</label>
                        <input type="text" name="lastname" id="lastname" value="{$user->lastname}">
                    </div>        
                    <div class="form-group">
                        <label for="firstname">Prénom</label>
                        <input type="text" name="firstname" id="firstname" value="{$user->firstname}">

                    </div>        
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email" value="{$user->email}">
                    </div>        
                     
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>  
                </form>
            </div>
        HTML;
    }

    /**
     * @inheritdoc
     */
    public function render(int $selector): string
    {
        $title = "MyWhishList | ";
        switch ($selector) {
            case 0: {
                    $content = $this->getAccount();
                    $title .= "Mon compte";
                    break;
                }
            case 1: {
                    $content = $this->editAccountPage();
                    $title .= "Modifier mon compte";
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