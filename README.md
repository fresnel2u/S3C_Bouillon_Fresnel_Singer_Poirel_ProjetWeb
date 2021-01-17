# S3C_Bouillon_Fresnel_Singer_Poirel_ProjetWeb

URL de l'application : 'à écrire ici'

Liste des fonctionnalité réalisées :

Lexique des paramètres utilisés dans les URLs associées à chaque fonctionnalité : 
'tokenList' : le token de la liste concernée --> exemple : à remplacer par nosecure1 pour la liste 1
'idItem' : l'id de l'item concerné
'idList' : l'id de la liste concernée

Fonctionnalités : 

-  Afficher une liste de souhaits : affiche les informations général de la liste (titre, créateur etc...) + les items contenu dans cette liste.
URL associée : /lists/{tokenList}/show
Contributeur : Fresnel Hugo

- Afficher un item d'une liste : affiche le détail d'un item (titre, description, prix ...).
URL associée : /lists/{tokenList}/item/{idItem} 'idItem' l'id de l'item souhaité à afficher.
Contributeur : Fresnel Hugo 

- Réserver un item : possibilité de réserver un item si celui-ci ne l'ai pas déjà.
URL associée : /lists/{idList}/items/{idItem}/lock
Contributeur : Bouillon Thomas

- Ajouter un message avec sa réservation : au moment de réserver, permet d'ajouter (en option) un message associé à l'item à destination du créateur de la liste.
URL associée : /lists/{idList}/items/{idItem}/lock --> fonctionnalité présente sur la même page que l'URL précédente (réservation d'un item).
Contributeur : Singer Jules

- Annuler une réservation : possibilité d'annuler une réservation pour l'utilisateur qui en a fait.
URL associée : /lists/{tokenList}/item/{idItem} --> necessite d'avoir réservé l'item. puis appuyer sur 'annuler la réservation'.
Contributeur : Singer Jules

- Ajouter un message sur une liste : ajoute un message public rattaché à la liste visible par tous les autres utilisateurs de la liste.
URL associée : /lists/{tokenList}/show --> fonctionnalité présente sur la page d'affichage d'une liste (en bas de page)
Contributeur : Poirel Jeremy

- Supprimer un message sur une liste : supprime un message public rattaché à la liste.
URL associée : /lists/{tokenList}/show --> necessite un message public sur la liste. puis appuyer sur 'Supprimer' pour le message souhaité.
Contributeur : Poirel Jeremy

- Créer une liste : Créé une liste (titre, description, expiration, token) lié à un utilisateur.
URL associée : /lists/new
Contributeur : Fresnel Hugo

- Supprimer une liste : Suppression de la liste par le créateur
URL associée : /lists --> puis appuyer sur le bouton 'Supprimer' de la liste souhaitée.
Contributeurs : Fresnel Hugo et Singer Jules

- Modifier les informations générales d'une de ses listes : Le créateur de la liste peut modifier ses listes.
URL associée : /lists/{idList}/edit
Contributeur : Bouillon Thomas

- Ajouter des items : le créateur d'une liste peut ajouter des items sur celle-ci.
URL associée : /lists/{idList}/items/new --> page est accessible depuis ce chemin sur le site : mes listes -> items -> ajouter un item 
Contributeur : Fresnel Hugo

- Modifier un item : le créateur d'une liste peut modifier les items qu'il a déjà implémentés.
URL associée : /lists/{idList}/items/{idItem}/edit --> page accessible depuis le chemin suivant : mes listes -> items -> editer 
Contributeur : Fresnel Hugo 

- Supprimer un item : le créateur d'une liste peut supprimer les items implémentés dans cette liste.
URL associée : /lists/{idList}/items --> une fois sur cette page, appuyer sur "supprimer" au niveau de chaque item
Contributeur : Fresnel Hugo 

- ajouter une image à un item : Au moment de créer un item le créateur peut ajouter une image à l'item.
URL associée : /lists/{idList}/items/new --> un champ spécifique a l'image est placé dans ce formulaire
Contributeur : Poirel Jeremy

- Modifier une image d'un item : le créateur peut modifier les images de ses items.
URL associée : /lists/{idList}/items/{idItem}/edit --> accessible via la fonctionnalité d'édtion d'un item, un champ est prévu pour sélectionner une autre image.
Contributeur : Poirel Jeremy

- Supprimer une image d'un item : le créateur peut supprimer l'image de ses items.
URL associée : /lists/{idList}/items --> puis appuyer sur le bouton supprimer de l'item souhaité
Contributeur : Poirel Jeremy

- Partager une liste : le créateur d'une liste peut la partager via une URL à donner contenant le token de la liste concernée.
URL associée : /lists --> une fois sur cette page vous voyez, pour chacune de vos listes, l'URL publique à partager.
Contributeur : Fresnel Hugo

- Consulter les réservations d'une de ses listes avant échéance : le créateur d'une liste peut voir les réservations des items déjà réalisées.
  (sans montrer qui a réservé)
URL associée : /lists/{tokenList}/show --> chaque item est accompagné d'une mention "Non réservé" ou "Réservé".
Contributeur : Fresnel Hugo et Singer Jules

- Consulter les réservations et messages d'une de ses listes après échéance : le créateur d'une liste peut accéder au bilan de celle-ci une fois la date d'expiration
  dépassée. Cette page permet de récapituler les items réservés et qui les a réservés avec différentes informations détaillées.
URL associée : /lists/{idList}/results --> accessible depuis : mes listes -> bilan(ce bouton s'affiche seulement si la liste est expirée).
Contributeur : Singer Jules

- Créer un compte : l'utilisateur peut créer un compte avec des informations tel que nom prénom email et mot de passe.
URL associée : /register
Contributeur : Singer Jules

- S'authentifier : l'utilisateur peut se connecter s'il est déjà inscrit (avec son email et son mot de passe).
URL associée : /login
Contributeur : Singer Jules

- Se déconnecter : l'utilisateur peut se déconnecter via la page de son compte.
URL associé : /account -> puis appuyez sur 'Déconnexion'
Contributeur : Singer Jules

- Modifier son compte : l'utilisateur peut modifier les informations de son compte (sauf le login donc l'email pour respecter la consigne du sujet).
URL associée : /account/edit
Contributeur : Singer Jules

- Rendre une liste publique : le créateur d'une liste peut la rendre publique.
URL associée : /lists/{idList}/edit
Contributeur : Fresnel Hugo

- Afficher les listes de souhaits publiques : les visiteurs peuvent voir la liste des listes de souhaits publiques
URL associée : /lists/public
Contributeur : Fresnel Hugo

- Créer une cagnotte sur un item : le créateur d'une liste peut ouvrir une cagnotte sur ses items (avec un montant spécifié ne pouvant pas dépassé le prix de l'item).
ULR associée : /lists/{idList}/items/{idItem}/founding_pot/create
Contributeur : Fresnel Hugo

- Participer à une cagnotte : les utilisateurs d'une liste peuvent participer à une cagnotte d'un item en indiquant un montant de participation.
URL associée : /lists/{idList}/items/{idItem}/founding_pot/participate
Contributeur : Fresnel Hugo

- Supprimer son compte : chaque utilisateur inscrit peut supprimer son compte.
URL associée : /account --> puis appuyer sur le bouton 'supprimer mon compte'
Contributeur : Singer Jules

Autres tâches réalisées :

- Middlewares d'authentification et de propriété : facilite la vérification qu'un utilisateur est connecté et qu'un utilisateur est propriétaire d'une liste.
Contributeur : Fresnel Hugo

- Messages flash : message d'erreur ou message de validation utilisés pour les traitements.
Contributeur : Bouillon Thomas

- Page d'accueil : point d'entrée du site, contient les explications de l'application
URL associée : /
Contributeur : Singer Jules

- Barre de navigation 
Contributeur : Singer Jules
  
- fix de bugs, refactoring, modifications mineures ...
Contributeurs : Tout le monde
