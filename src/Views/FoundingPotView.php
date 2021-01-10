<?php

namespace Whishlist\Views;

class FoundingPotView extends BaseView
{
    /**
     * Page de création d'une cagnotte
     *
     * @return string page HTML
     */
    public function createPage(): string
    {
        $createFoundingPotUrl = $this->container->router->pathFor('createFoundingPot', [
            'item_id' => $this->params['item_id']
        ]);

        return <<<HTML
            <div class="container">
                <h1>Créer une cagnotte</h1>
                <form method="POST" action="{$createFoundingPotUrl}">
                    <div class="form-group">
                        <label for="amount">Montant de la cagnotte</label>
                        <input type="text" name="amount" id="amount">
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
        $title = "MyWishList | ";
        switch ($selector) {
            case 0: {
                    $content = $this->createPage();
                    $title .= "Créer une cagnotte";
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