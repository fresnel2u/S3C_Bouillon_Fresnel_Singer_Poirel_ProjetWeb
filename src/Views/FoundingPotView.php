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
        $list = $this->params['list'];
        $item = $this->params['item'];

        $createFoundingPotUrl = $this->pathFor('createFoundingPot', [
            'list_id' => $list->id,
            'item_id' => $item->id
        ]);

        return <<<HTML
            <div class="container">
                <h1>Créer une cagnotte</h1>
                <form method="POST" action="{$createFoundingPotUrl}">
                    <p><strong> Prix de l'item : </strong> {$item->price} € </p>
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
     * Page de participation à une cagnotte
     *
     * @return string page HTML
     */
    public function participatePage(): string
    {
        $list = $this->params['list'];
        $item = $this->params['item'];

        $participateUrl = $this->pathFor('participateFoundingPot', [
            'list_id' => $list->id,
            'item_id' => $item->id
        ]);
        $cancelUrl = $this->pathFor('displayItem', [
            'token' => $list->token,
            'item_id' => $item->id
        ]);

        $amount = number_format($item->foundingPot->amount, 2);
        $rest = number_format($item->foundingPot->getRest(), 2);

        return <<<HTML
            <div class="container">
                <h1>Participer à la cagnotte de {$amount} €</h1>
                <p>Montant actuellement manquant : {$rest} €</p>
                <br>
                <form method="POST" action="{$participateUrl}">
                    <div class="form-group">
                        <label for="amount">Montant de votre participation</label>
                        <input type="text" name="amount" id="amount">
                    </div>             
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>  
                    <a href="{$cancelUrl}" class="btn btn-secondary">Annuler</a>
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
            case 1: {
                    $content = $this->participatePage();
                    $title .= "Participer à une cagnotte";
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
