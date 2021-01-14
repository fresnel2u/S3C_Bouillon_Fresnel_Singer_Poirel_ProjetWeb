<?php

namespace Whishlist\Views;

use Whishlist\Helpers\Auth;

class HomeView extends BaseView
{
    /**
     * Construit le contenu de la page d'accueil
     *
     * @return string l'HTML de la page d'accueil
     */
    private function getHome(): string
    {
        $html = <<<HTML
            <div class="home">
                <div class=home-header>
                    <div class="header-title">
                        <img src="/img/icons/wishlist_icon.svg" alt="wishlist">
                        <h2>MyWishList</h2>
                    </div>
                    <div class="header-log">
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
                <div class="home-intro">
                    <img src="/img/icons/online_whishlist.svg" alt="a whish list">
                    <div class="intro-container">
                        <h1> MyWishList </h1>
                        <p>
                            L'application web MyWishList vous permet de <strong>créer des listes de souhaits</strong> à l'occasion d'un événement particulier 
                            (anniversaire, fin d'année, mariage, retraite ...) et de <strong>les diffuser à un ensemble de personnes</strong> concernées que 
                            vous aurez choisi. Ces personnes peuvent ensuite <strong>consulter cette liste</strong>  et <strong>s'engager à offrir un élément</strong> de la liste ! Cet élément est alors marqué comme réservé dans la liste.
                            Bien d'autres possibilités s'offrent à vous, nous vous proposons de les découvrir maintenant, ou d'essayer par vous même dès à présent.
                            <div>
                                <a href="{$registerUrl}"><button class="btn btn-primary">Essayer maintenant</button></a> 
                                <a href="#see_more"><button class="btn btn-secondary">En savoir plus</button></a>
                            </div>
                        </p>
                    </div>
                </div>

                <div class="home-container">
                    <h1 id="see_more" class="home-container-title">Que propose MyWishList ?</h1>
                    <div class="home-strong-point white">
                        <div class="strong-point-container">
                            <h1>Créer</h1>
                            <p>Soyez sans limite ! Créez votre liste de souhaits en lui donnant un titre, une description, une date limite de validité et ajoutez-lui les éléments que vous souhaitez. Vous avez la possibilité de créer autant de liste que vous le voulez si cela est nécessaire. Cela permet de pouvoir couvrir plusieurs événements dans la même période. De la même manière le nombre d'éléments par liste n'est aucunement limité, n'hésitez pas à réaliser des listes bien founies ...</p>
                        </div>  
                        <img class="little-img" src="/img/icons/create_list.svg" alt="create list">  
                    </div>

                    <div class="home-strong-point ">
                        <img src="/img/icons/share_list.svg" alt="share list"> 
                        <div class="strong-point-container">
                            <h1>Partager</h1>
                            <p>Divisez les couts ! Lorsque que votre liste est créée, partagez la avec vos amis et votre famille. Une fois que les membres sont dans le groupe vous avez la possibilité de voir quels sont les éléments déjà sélectionnés (mais pas qui les a réservés, on vous garde la surprise...) sans avoir à vous occuper de quoique ce soit. Un gain de temps pour vous, et pour vos amis (eh oui, les idées ça manque parfois).</p>
                        </div>  
                    </div>

                    <div class="home-strong-point white">
                        <div class="strong-point-container">
                            <h1>Gérer / Modifier</h1>
                            <p>Gardez le contrôle ! Même si vous avez déjà partagé votre liste, vous avez toujours la possibilité d'ajouter des nouveaux éléments, mais aussi de modifier ou de supprimer ceux déjà existants. Cela permet d'avoir une application dynamique vous donnant un contrôle total sur vos listes de souhaits, dans le but de vous faciliter la démarche, c'est notre priorité. </p>
                        </div>  
                        <img src="/img/icons/edit.svg" alt="edit"> 
                    </div>

                    <div class="home-strong-point">
                        <img  src="/img/icons/community.svg" alt="participate"> 
                        <div class="strong-point-container">
                            <h1>Participer</h1>
                            <p>Soyez Généreux ! Bien sur, un ami peut vous inviter à rejoindre une liste pour un événement qui lui tient à coeur, mais ça ne s'arrête pas là. Notre équipe à conçu un système participatif permettant au créateur de la liste d'ouvrir une cagnotte sur les éléments souhaités. Grâce à cela, tous les membres peuvent participer en choisissant un montant qu'ils s'engagent à payer pour ce cadeau.  </p>
                        </div>   
                    </div>
                    
                    <div class="home-outro">
                        <h1 class="home-container-title">Essayez MyWishList maintenant</h1>
                        <a href="{$registerUrl}"><button class="btn btn-primary">Je m'inscris</button></a>
                    </div>
                </div>    
            </div>
        HTML;
    }

    /**
     * @inheritdoc
     */
    public function render(int $selector): string
    {
        $title = "MyWishList | ";
        switch ($selector) {
            case 0: {
                    $content = $this->getHome();
                    $title .= "Accueil";
                    break;
                }
            default: {
                    $content = '';
                    break;
                }
        }

        return $this->layout($content, $title, 0);
    }
}
