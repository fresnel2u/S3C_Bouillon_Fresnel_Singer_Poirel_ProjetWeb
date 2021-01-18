<?php

namespace Whishlist\Views;

class AuthView extends BaseView
{
    /**
     * Construit le contenu de la page de login
     *
     * @return string l'HTML du login
     */
    private function getLogin(): string
    {
        $loginUrl = $this->pathFor('login');
        $registerUrl = $this->pathFor('registerPage');
        $homeUrl = $this->pathFor('home');

        return <<<HTML
            <div class="back-home">
                <a href="{$homeUrl}"><img class="back-home-arrow" src="/img/icons/left-arrow.svg"></a>
                <a href="{$homeUrl}"><img src="/img/icons/home.svg"></a>
            </div>
            <div class="login">
                <h1>Connexion</h1>
                <form role="form" method="post" action="{$loginUrl}">
                    <div class="form-row">
                        <label for="email">Email</label>
                        <div class="input">
                            <img src="/img/user.svg" alt="User icon">
                            <input type="text" name="email" id="email">
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="password">Mot de passe</label>
                        <div class="input">
                            <img src="/img/password.svg" alt="Password icon">
                            <input type="password" name="password" id="password">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </form>
                <p>Pas de compte ? <a class="btn btn-light" href="{$registerUrl}">Inscription</a></p>
            </div>
        HTML;
    }

    /**
     * Construit le contenu de la page de register
     *
     * @return string l'HTML du register
     */
    private function getRegister(): string
    {
        $loginUrl = $this->pathFor('loginPage');
        $registerUrl = $this->pathFor('register');
        $homeUrl = $this->pathFor('home');

        return <<<HTML
            <div class="back-home">
                <a href="{$homeUrl}"><img class="back-home-arrow" src="/img/icons/left-arrow.svg"></a>
                <a href="{$homeUrl}"><img src="/img/icons/home.svg"></a>
            </div>
            <div class="register">
                <h1>Inscription</h1>
                <form role="form" method="post" action="{$registerUrl}">
                    <div class="form-row">
                        <label for="firstname">Prénom</label>
                        <div class="input">
                            <img src="/img/user.svg" alt="User icon">
                            <input type="text" name="firstname" id="firstname">
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="lastname">Nom</label>
                        <div class="input">
                            <img src="/img/user.svg" alt="User icon">
                            <input type="text" name="lastname" id="lastname">
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="email">Email</label>
                        <div class="input">
                            <img src="/img/user.svg" alt="User icon">
                            <input type="text" name="email" id="email">
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="password">Mot de passe</label>
                        <div class="input">
                            <img src="/img/password.svg" alt="Password icon">
                            <input type="password" name="password" id="password">
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="password_confirm">Confirmer le mot de passe</label>
                        <div class="input">
                            <img src="/img/password.svg" alt="Password icon">
                            <input type="password" name="password_confirm" id="password_confirm">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Continuer</button>
                </form>
                <p>Déjà inscrit ? <a class="btn btn-light" href="{$loginUrl}">Connexion</a></p>
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
                    $content = $this->getLogin();
                    $title .= "Connexion";
                    break;
                }
            case 1: {
                    $content = $this->getRegister();
                    $title .= "Inscription";
                    break;
                }
            default: {
                    $content = '';
                    break;
                }
        }

        return $this->layout($content, $title,0);
    }
}
