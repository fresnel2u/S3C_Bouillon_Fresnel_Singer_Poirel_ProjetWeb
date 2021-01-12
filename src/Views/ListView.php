<?php

namespace Whishlist\Views;

use Whishlist\Helpers\Auth;
use Whishlist\Models\WishList;

class ListView extends BaseView
{
    /**
     * Construit le contenu d'un formulaire de creation de liste
     *
     * @return string l'HTML du formulaire de creation de liste
     */
    private function newListPage(): string
    {
        $newListUrl = $this->container->router->pathFor('newList');

        return <<<HTML
            <div class="container">
                <h1>Créer une liste</h1>
                <form method="POST" action="{$newListUrl}">
                    <div class="form-group">
                        <label for="title">Titre</label>
                        <input type="text" name="title" id="title">
                    </div>        
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" cols="30" rows="10"></textarea>
                    </div>        
                    <div class="form-group">
                        <label for="expiration">Expiration</label>
                        <input type="date" name="expiration" id="expiration">
                    </div>        
                    <div class="form-group">
                        <label for="token">Token</label>
                        <input type="text" name="token" id="token">
                    </div>      
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>  
                </form>
            </div>
        HTML;
    }

    /**
     * Construit le contenu des listes de souhaits
     *
     * @return string l'HTML des listes de souhaits
     */
    private function getAllList(): string
    {
        $newListUrl = $this->container->router->pathFor('newListPage');

        $html = <<<HTML
            <h1>Listes de souhaits</h1>
            <a href="{$newListUrl}" class="btn btn-primary">Ajouter une liste</a>
            <div>
                <table class="table">
                <thead>
                    <tr>
                        <th scope="col">id</th>
                        <th scope="col">user_id</th>
                        <th scope="col">titre</th>
                        <th scope="col">description</th>
                        <th scope="col">expiration</th>
                        <th scope="col">token</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                        <tbody> 
        HTML;

        foreach ($this->params['lists'] as $list) {
            $html .= "<tr>";

            foreach ($list->toArray() as $row) {
                $html .= "<td>$row</td>";
            }
            
            $showUrl = $this->container->router->pathFor('displayList', ['token' => $list->token]);
            $editUrl = $this->container->router->pathFor('editListPage', ['id' => $list->id]);
            $deleteUrl = $this->container->router->pathFor('deleteList', ['id' => $list->id]);
            $resultsUrl = $this->container->router->pathFor('displayListResults', ['id' => $list->id]);

            $html .= <<<HTML
                    <td>
                        <a href="{$showUrl}" class="btn btn-light">Aperçu</a>
                        <form method="POST" action="{$deleteUrl}">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                        <a href="{$editUrl}" class="btn btn-light">Éditer</a>
            HTML;

           
            if($list->isExpired()) {
                $html .= <<<HTML
                 <a href="{$resultsUrl}" class="btn btn-light">Bilan</a>    
                HTML;
            }
            $html .= '</td> </tr>';
        }
        $html .= '</tbody>
            </table>
        </div>';
        return $html;
    }

    /**
     * Construit le contenu d'une liste de souhaits
     *
     * @return string l'HTML d'une liste de souhaits
     */
    private function getList(): string
    {
        $list = $this->params['list'];
        $items = $this->params['items'];
        $user = Auth::getUser();

        $html = <<<HTML
            <div class="container page-list-show">
                <h1>{$list->title}</h1>
                <p>{$list->description}</p>
                <br><br>
                <p><i>Expiration : {$list->expiration->format('d/m/Y')}</i></p> 
                <p><i>Créée par : {$list->user->firstname} {$list->user->lastname}</i></p> 

                <h2>Items de la liste</h2>
                <div class="items-list">
        HTML;
        
        foreach ($items as $item) {
            $itemUrl = $this->container->router->pathFor('displayItem', [
                'token' => $list->token,
                'id' => $item->id
            ]);
            $html .= <<<HTML
                <div class="item">
                    <a href="{$itemUrl}">
                        <img src="/img/{$item->image}" alt="Image de l'item" />
                        <h3>{$item->name}</h3>
                    </a>
            HTML;

            // Réservation
            if ($item->reservation) {
                if ($list->expiration->lessThan(new \DateTime())) {
                    $reservationMessage = $item->reservation->message ? trim($item->reservation->message) : '';
                    $message = ($reservationMessage !== '') ? $reservationMessage : 'Pas de message.';
                    $html .= <<<HTML
                        <p><i>Réservé par {$item->reservation->user->getFullname()}.</i></p>
                        <p><strong>Message : </strong><i>{$message}</i></p>
                    HTML;
                } else {
                    $html .= <<<HTML
                        <p><i>Réservé.</i></p>
                    HTML;
                }
            } else {
                $html .= <<<HTML
                    <p><i>Non réservé.</i></p>
                HTML;
            }

            $html .= <<<HTML
                </div>
            HTML;
        }

        $html .= <<<HTML
                </div>
            </div>
        HTML;

        return $html;

        // $html = <<<HTML
        //     <h1>Items de la liste "{$list->title}":</h1>
        //     <div>
        //         <table class="table">
                    // <thead>
                    //     <tr>
                    //         <th scope="col">ID</th>
                    //         <th scope="col">Nom</th>
                    //         <th scope="col">Description</th>
                    //         <th scope="col">Image</th>
                    //         <th scope="col">URL</th>
                    //         <th scope="col">Tarif</th>
                    //         <th scope="col">Cagnotte</th>
                    //         <th scope="col">Réservation</th>
                    //     </tr>
                    // </thead>
                    // <tbody>
        // HTML;
        // foreach ($items as $item) {
        //     $html .= <<<HTML
        //         <tr>
        //             <td>{$item->id}</td>
        //             <td>{$item->name}</td>
        //             <td>{$item->description}</td>
        //             <td><img src="/img/{$item->image}" alt="Image de l'item {$item->id}" width="150" /></td>
        //             <td>{$item->url}</td>
        //             <td>{$item->price} €</td>
        //     HTML;

        //     // Cagnotte
        //     if ($item->foundingPot) {
                // $foundingPotUrl = $this->container->router->pathFor('participateFoundingPotPage', [
                //     'item_id' => $item->id
                // ]);
        //         $html .= <<<HTML
        //             <td>
        //                 <a href="{$foundingPotUrl}" class="btn btn-light">Participer à la cagnotte</a>
        //             </td>
        //         HTML;
        //     } else {
        //         $html .= <<<HTML
        //             <td>Pas de cagnotte.</td>
        //         HTML;
        //     }

        //     // Réservation
        //     if ($item->reservation) {
        //         $html .= <<<HTML
        //             <td>Réservé par {$item->reservation->user->firstname} {$item->reservation->user->lastname}
        //         HTML;
                
        //         // annuler la réservation
        //         if ($item->reservation->user_id === $user['id']) {
        //             $cancelLockItem = $this->container->router->pathFor('cancelLockItem', ['id' => $item->id]);
        //             $html .= <<<HTML
        //                 <br> 
        //                 <form method="POST" action="{$cancelLockItem}" onsubmit=" return confirm('Êtes-vous sûr de vouloir annuler votre réservation ?')">
        //                     <button class="btn btn-danger">Annuler</button>
        //                 </form>
        //             HTML;
        //         }
        //         $html .= <<<HTML
        //             </td>
        //         HTML;
        //     } else if (Auth::isLogged()) {
        //         $lockUrl = $this->container->router->pathFor('lockItemPage', ['id' => $item->id]);
        //         $html .= <<<HTML
        //             <td><a href="{$lockUrl}" class="btn btn-light">Réserver</button></td>
        //         HTML;
        //     } else {
        //         $html .= <<<HTML
        //             <td>Vous devez être connecté pour réserver.</td>
        //         HTML;
        //     }

        //     $html .= '<tr>';
        // }

        // return $html . <<<HTML
        //             </tbody>
        //         </table>
        //     </div>
        // HTML;
    }

    /**
     * Construit le contenu d'un formulaire d'edition de liste
     *
     * @return string
     */
    private function editListPage(): string
    {
        $list = $this->params['list'];
        $editUrl = $this->container->router->pathFor('editList', ['id' => $list->id]);

        return <<<HTML
            <div class="container">
                <h1>Éditer une liste</h1>
                <form method="POST" action="{$editUrl}">
                    <div class="form-group">
                        <label for="title">Titre</label>
                        <input type="text" name="title" id="title" value="{$list->title}">
                    </div>        
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" cols="30" rows="10">{$list->description}</textarea>
                    </div>        
                    <div class="form-group">
                        <label for="expiration">Expiration</label>
                        <input type="date" name="expiration" id="expiration" value="{$list->expiration}">
                    </div>        
                    <div class="form-group">
                        <label for="token">Token</label>
                        <input type="text" name="token" id="token" value="{$list->token}">
                    </div>      
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>  
                </form>
            </div>
        HTML;
    }

    /**
     * Construit le contenu du bilan de reservations d'une liste
     *
     * @return string
     */
    public function getListResults() : string
    {
        $list = $this->params['list'];
        $items = $this->params['items'];
        $html = <<<HTML
            <div class="container big list-results">
                <h1>Bilan des réservations de votre liste : <br> $list->title</h1>
                <div class="results-container">
        HTML;        
        
        foreach($items as $item) {
            if(!is_null($item->reservation) || !is_null($item->foundingPot)){
                $imgUrl = "/img/{$item->image}";
                $reservation = $item->reservation;
                $foundingPot = $item->foundingPot;
                
                if(!is_null($foundingPot)) {
                    $type = 'Cagnotte';
                    $content = '<div class="item-participants">
                    <p> <strong>Participants :</strong> </p>';
                    
                   foreach($foundingPot->participations as $participant) {
                        $content .= <<<HTML
                            <div>
                                <p><strong>-</strong>  {$participant->user->firstname} {$participant->user->lastname} 
                                   | {$participant->amount} €</p>
                            </div>
                        HTML;
                    }
                    $content .= '</div>
                    <p> <strong> Montant restant : </strong>' . $foundingPot->getRest() . '</p>';
                } else {
                    $type = 'Réservation';
                    $content = <<<HTML
                        <p><strong>auteur de la réservation : </strong> {$reservation->user->firstname} {$reservation->user->lastname}</p>
                    HTML;
                    if($reservation->message != '') {
                        $content .= <<<HTML
                            <p><strong>message de l'expéditeur :</strong> {$reservation->message} </p>
                        HTML;
                    } 
                }   
                $html .= <<<HTML
                    <div class="item-result">
                        <img src="{$imgUrl}"/>
                        <div>
                            <h3><strong>Nom : </strong> {$item->name} </h3>
                            <p><strong>Type :</strong> {$type}</p>
                            <p><strong>prix :</strong> {$item->price} €</p>
                            {$content}
                        </div>
                    </div>
                HTML;
            } 
        }
        
        $html .= <<<HTML
                </div>
            </div>
        HTML;

        return $html;
    }

    /**
     * @inheritdoc
     */
    public function render(int $selector): string
    {
        $title = "MyWhishList | ";
        switch ($selector) {
            case 0: {
                    $content = $this->newListPage();
                    $title .= "Créer une liste";
                    break;
                }
            case 1: {
                $content = $this->getAllList();
                    $title .= "Listes de souhaits";
                    break;
                }
            case 2: {
                    $content = $this->getList();
                    $title .= "Liste";
                    break;
                }
            case 3: {
                    $content = $this->editListPage();
                    $title .= "Éditer une liste";
                    break;
                }
            case 4: {
                    $content = $this->getListResults();
                    $title .= "Bilan réservations";
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