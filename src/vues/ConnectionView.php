<?php
namespace Whishlist\vues;

class ConnectionView 
{
    private $modeles;

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
     * construit le contenu de la page de login
     *
     * @return string l'HTML du login
     */
    private function getLogin() : string
    {
        $html = '<div class="login">
        <h1>Login</h1>
        <form>
          <div class="form-row">
            <label for="email">Email</label>
            <div class="input">
              <img src="/public/img/user.svg" alt="">
              <input type="text" id="email">
            </div>
            </div>
          <div class="form-row">
            <label for="password">Password</label>
            <div class="input">
              <img src="/public/img/password.svg" alt="">
              <input type="password" id="password">
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Sign in</button>
        </form>
        <p>Don\'t have an account ? <a class="btn btn-light" href="">Sign up</a></p>
      </div>
    </div>';

        return $html;
    }

     /**
     * construit le contenu de la page de register
     *
     * @return string l'HTML du register
     */
    private function getRegister() : string
    {
        $html = '<div>
        <div class="register">
          <h1>Sign up</h1>
          <form>
            <div class="form-row">
              <label for="fullname">Fullname</label>
              <div class="input">
                <img src="/public/img/user.svg" alt="user icon">
                <input type="text" id="fullname">
                </div>
            </div>
            <div class="form-row">
              <label for="email">Email</label>
              <div class="input">
                <img src="/public/img/user.svg" alt="user icon">
                <input type="text" id="email">
                </div>
            </div>
            <div class="form-row">
              <label for="password">Password</label>
              <div class="input">
                <img src="/public/img/password.svg" alt="password icon">
                <input type="password" id="password">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Continue</button>
          </form>
          <p>Already have an account ? <a class="btn btn-light" href="">Sign in</a></p>
        </div>
      </div>';

        return $html;
    }

    /**
     * ferme les balises du document
     *
     * @return string l'HTML fermant le document
     */
    private function closeHtml() : string
    {
        $html = "</body> </html>";

        return $html;
    }

    /**
     * construit la page entiere selon le parametre
     *
     * @param integer $selector - selon sa valeur, la methode execute une methode differente et renvoit une page adaptee a la demande
     * @return string l'HTML de la page complete
     */
    public function render(int $selector) : string
    {
        $title = "MyWhishList | ";
        switch ($selector){
            case 0 : {
                $content = $this->getLogin();
                $title .= "Login";
                break;
            }
            case 1 : {
                $content = $this->getRegister();
                $title .= "Register";
                break;
            }
            default : {
                $content = '';
                break;
            }
        }
        
        $html = composants\Header::getHeader($title);
        $html .= composants\Menu::getMenu();
        $html .= $content; 
        $html .= $this->closeHtml();
        return $html;
    }
}