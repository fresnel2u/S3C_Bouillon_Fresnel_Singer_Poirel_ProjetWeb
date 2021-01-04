<?php
namespace Whishlist\vues;

class CreationView 
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
     * construit le contenu d'un formulaire de creation de liste
     *
     * @return string l'HTML du formulaire de creation de liste
     */
    private function getFormList() : string
    {
        $urlNewList = $this->container->router->pathFor('formList');
        $html = '<form method="POST" action="' .$urlNewList . '">
                    <label> Titre : <br> <input type="text" name="list_title"/> </label> <br>
                    <label> Description : <br> <input type="text" name="list_description" /> </label> <br>
                    <button type="submit"> Créer la liste </button>
                </form>';
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
                $content = $this->getFormList();
                $title .= "Créer une liste";
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