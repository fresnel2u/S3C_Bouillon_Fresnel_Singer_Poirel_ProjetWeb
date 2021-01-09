<?php

namespace Whishlist\Views;

use Whishlist\Views\Components\Header;
use Whishlist\Views\Components\Menu;

class ConnectionView
{

    private $container;

    /**
     * Constructeur de la vue
     *
     * @param array $m - modeles pour recuperer les donnees de la bdd
     * @param \Slim\Container $c - container
     */
    public function __construct(array $m, \Slim\Container $c)
    {
        $this->modeles = $m;
        $this->container = $c;
    }

    /**
     * Construit le contenu de la page de login
     *
     * @return string l'HTML du login
     */
    private function getLogin(): string
    {
        $html = <<<HTML
            <div class="login">
                <h1>Connexion</h1>
                <form role="form" method="post" action="{$this->container->router->pathFor("login")}">
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
                <p>Pas de compte ? <a class="btn btn-light" href="{$this->container->router->pathFor('registerPage')}">Inscription</a></p>
            </div>
        HTML;

        return $html;
    }

    /**
     * construit le contenu de la page de register
     *
     * @return string l'HTML du register
     */
    private function getRegister(): string
    {
        $html = <<<HTML
            <div class="register">
                <h1>Inscription</h1>
                <form role="form" method="post" action="{$this->container->router->pathFor("register")}">
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
                <p>Déjà inscrit ? <a class="btn btn-light" href="{$this->container->router->pathFor("loginPage")}">Connexion</a></p>
            </div>
        HTML;

        return $html;
    }

    /**
     * construit la page entiere selon le parametre
     *
     * @param integer $selector - selon sa valeur, la methode execute une methode differente et renvoit une page adaptee a la demande
     * @return string l'HTML de la page complete
     */
    public function render(int $selector): string
    {
        $title = "MyWhishList | ";
        switch ($selector) {
            case 0: {
                    $content = $this->getLogin();
                    $title .= "Login";
                    break;
                }
            case 1: {
                    $content = $this->getRegister();
                    $title .= "Register";
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
