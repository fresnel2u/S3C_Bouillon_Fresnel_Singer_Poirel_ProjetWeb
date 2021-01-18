<?php

namespace Whishlist\Views;

class AccountView extends BaseView
{
    /**
     * Construit le contenu de la page de compte
     *
     * @return string l'HTML des informations de compte
     */
    private function getAccount(): string
    {
        $editAccount = $this->pathFor('editAccountPage');
        $deleteAccount = $this->pathFor('deleteAccount');
        $logoutUrl = $this->pathFor('logout');
        $user = $this->params['user'];

        return <<<HTML
            <div class="account">
                <h1> Mon compte - Informations </h1>
                <div class="account-container">
                    <div class="account-informations">
                        <p> Nom : {$user['lastname']}  </p>
                        <p> Prénom : {$user['firstname']} </p>
                        <p> Email : {$user['email']}</p>
                    </div>
                    <div class="account-actions">
                        <form method="GET" action="{$editAccount}">
                            <button class="btn btn-primary">Éditer</button>
                        </form>
                        <form method="POST" action="{$logoutUrl}" class="logout">
                            <div>
                                <button class="btn btn-secondary">Déconnexion</button>
                            </div>
                        </form>
                        <form method="POST" action="{$deleteAccount}" onsubmit="return confirm('Voulez vous supprimer votre compte ?');">
                            <button type="submit" class="btn btn-danger">Supprimer mon compte</button>
                        </form>
                    </div>
                </div>
            </div>
        HTML;
    }

    /**
     * Construit le contenu d'un formulaire d'edition de compte
     *
     * @return string
     */
    private function editAccountPage(): string
    {
        $editAccount = $this->pathFor('editAccount');
        $user = $this->params['user'];

        return <<<HTML
            <div class="container">
                <h1>Éditer mon compte</h1>
                <form method="POST" action="{$editAccount}">
                    <div class="form-group">
                        <label for="lastname">Nom</label>
                        <input type="text" name="lastname" id="lastname" value="{$user['lastname']}" autocomplete="off" value="">
                    </div>        
                    <div class="form-group">
                        <label for="firstname">Prénom</label>
                        <input type="text" name="firstname" id="firstname" value="{$user['firstname']}" autocomplete="off">

                    </div>        
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" name="password" id="password" placeholder="Confidentiel" value="">
                    </div>   

                    <div class="form-group">
                        <label for="password_confirm">Confirmez le nouveau mot de passe</label>
                        <input type="password" name="password_confirm" id="password_confirm" placeholder="Confidentiel" value="">
                    </div>     
                     
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>  
                </form>
            </div>
        HTML;
    }

    /**
     * Affiche une liste de créateurs
     */
    private function allCreators(): string
    {
        $html = <<<HTML
            <div class="container">
                <h1>Créateurs publiques</h1>
        HTML;
        $creators = $this->params['creators'];
        foreach($creators as $creator)
            $html .= "<p>{$creator->firstname} {$creator->lastname}</p>";
        if(count($creators) === 0)
            $html .= "<p>Aucun créateur n'a de liste publique</p>";
        return $html . '</div>';
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
            case 2: {
                    $content = $this->allCreators();
                    $title .= "Lite des créateurs";
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