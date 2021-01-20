# S3C_Bouillon_Fresnel_Singer_Poirel_ProjetWeb

URL de l'application : `à écrire ici`

Membres :
- Hugo Fresnel
- Jules Singer
- Thomas Bouillon
- Jérémy Poirel

## Guide d'installation du projet ##

1) Commencez par cloner le projet avec git: `git clone https://github.com/fresnel2u/S3C_Bouillon_Fresnel_Singer_Poirel_ProjetWeb.git && cd S3C_Bouillon_Fresnel_Singer_Poirel_ProjetWeb/`.
2) Créez la base de données
- Avec mysql | mariadb déjà installé
- Connectez vous avec un outil comme PhpMyAdmin
- Créez une base de donnée avec le nom de votre choix
- Executez toutes les requêtes dans mywishlist.sql
3) Dans src/Configuration/ copiez 'conf.example.ini' en 'conf.ini', ouvrez le et remplacez les informations par celles qui vous correspondent
4) Retournez à la racine du projet puis initialisez le projet avec
- `composer install`
- `npm install` ou `yarn install` (Pour utiliser Sass)
- `npm run dev` ou `yarn run dev` (Pour utiliser Sass)
5) Démarrez le serveur local de php avec `make serve`
6) Ouvrez le navigateur et rendez-vous à `http://localhost:8080/`

## Lexique des paramètres utilisés dans les URLs : 

- `tokenList` : le token de la liste concernée
- `idItem` : l'id de l'item concerné
- `idList` : l'id de la liste concernée

## Fonctionnalités : 

| Tâche | Description | URL Associée | Contributeurs |
| --- | --- | --- | --- |
| Afficher une liste de souhaits | affiche les informations général de la liste (titre, créateur etc...) + les items contenu dans cette liste. | `/lists/{tokenList}/show` | Hugo Fresnel |
| Afficher un item d'une liste | affiche le détail d'un item (titre, description, prix ...). | `/lists/{tokenList}/item/{idItem}` | Hugo Fresnel |
| Réserver un item | possibilité de réserver un item si celui-ci ne l'ai pas déjà. | `/lists/{idList}/items/{idItem}/lock` | Thomas Bouillon |
| Ajouter un message avec sa réservation | au moment de réserver, permet d'ajouter (en option) un message associé à l'item à destination du créateur de la liste. | `/lists/{idList}/items/{idItem}/lock` --> fonctionnalité présente sur la même page que l'URL précédente (réservation d'un item). | Jules Singer |
| Annuler une réservation | possibilité d'annuler une réservation pour l'utilisateur qui en a fait. | `/lists/{tokenList}/item/{idItem}` --> necessite d'avoir réservé l'item. puis appuyer sur 'annuler la réservation'. | Jules Singer |
| Ajouter un message sur une liste | ajoute un message public rattaché à la liste visible par tous les autres utilisateurs de la liste. | `/lists/{tokenList}/show` --> fonctionnalité présente sur la page d'affichage d'une liste (en bas de page) | Jérémy Poirel |
| Supprimer un message sur une liste | supprime un message public rattaché à la liste. | `/lists/{tokenList}/show` --> nécessite un message public sur la liste. puis appuyer sur 'Supprimer' pour le message souhaité. **ATTENTION :** Pour pouvoir tester, il faut que l'utilisateur ait posté un message sur cette liste, car on ne peut pas supprimer le message d'un autre. | Jérémy Poirel |
| Modifier un message sur une liste | modifie un message public rattaché à la liste. | `/list/{tokenList}/edit` --> nécessite un message public sur la liste. puis appuyer sur 'Modifier' pour le message souhaité. **ATTENTION :** Pour pouvoir tester, il faut que l'utilisateur ait posté un message sur cette liste, car on ne peut pas modifier le message d'un autre. | Jérémy Poirel |
| Créer une liste | Créé une liste (titre, description, expiration, token) lié à un utilisateur. | `/lists/new` | Hugo Fresnel |
| Supprimer une liste | Suppression de la liste par le créateur | `/lists` --> puis appuyer sur le bouton 'Supprimer' de la liste souhaitée. | Hugo Fresnel + Jules Singer
| Modifier les informations générales d'une de ses listes | Le créateur de la liste peut modifier ses listes. | `/lists/{idList}/edit` | Thomas Bouillon + Hugo Fresnel |
| Ajouter des items | le créateur d'une liste peut ajouter des items sur celle-ci. | `/lists/{idList}/items/new` --> page est accessible depuis ce chemin sur le site : mes listes -> items -> ajouter un item  | Hugo Fresnel |
| Modifier un item | le créateur d'une liste peut modifier les items qu'il a déjà implémentés. | `/lists/{idList}/items/{idItem}/edit` --> page accessible depuis le chemin suivant : mes listes -> items -> editer  | Hugo Fresnel  |
| Supprimer un item | le créateur d'une liste peut supprimer les items implémentés dans cette liste. | `/lists/{idList}/items` --> une fois sur cette page, appuyer sur "supprimer" au niveau de chaque item | Hugo Fresnel  |
| Ajouter une image à un item | Au moment de créer un item le créateur peut ajouter une image à l'item ou non. (+ sécurité / vérification de l'extension du fichier). | `/lists/{idList}/items/new` --> un champ spécifique a l'image est placé dans ce formulaire | Jérémy Poirel |
| Modifier une image d'un item | le créateur peut modifier les images de ses items. L'ancienne image est alors supprimée dans le dossier et remplacée par la nouvelle (+ sécurité / vérification de l'extension du fichier). | `/lists/{idList}/items/{idItem}/edit` --> accessible via la fonctionnalité d'édtion d'un item, un champ est prévu pour sélectionner une autre image. | Jérémy Poirel |
| Supprimer une image d'un item | le créateur supprime l'image d'un item lorsqu'il supprime l'item (suppression de l'image dans le dossier des images) | `/lists/{idList}/items` --> puis appuyer sur le bouton supprimer de l'item souhaité | Jérémy Poirel |
| Partager une liste | le créateur d'une liste peut la partager via une URL à donner contenant le token de la liste concernée. | `/lists` --> une fois sur cette page vous voyez, pour chacune de vos listes, l'URL publique à partager. | Hugo Fresnel |
| Consulter les réservations d'une de ses listes avant échéance | le créateur d'une liste peut voir les réservations des items déjà réalisées. (sans montrer qui a réservé) | `/lists/{tokenList}/show` --> chaque item est accompagné d'une mention "Non réservé" ou "Réservé". | Hugo Fresnel + Jules Singer + Thomas Bouillon |
| Consulter les réservations et messages d'une de ses listes après échéance | le créateur d'une liste peut accéder au bilan de celle-ci une fois la date d'expiration dépassée. Cette page permet de récapituler les items réservés et qui les a réservés avec différentes informations détaillées. | `/lists/{idList}/results` --> accessible depuis : mes listes -> bilan(ce bouton s'affiche seulement si la liste est expirée). | Jules Singer |
| Créer un compte | l'utilisateur peut créer un compte avec des informations tel que nom prénom email et mot de passe. | `/register` | Jules Singer |
| S'authentifier | l'utilisateur peut se connecter s'il est déjà inscrit (avec son email et son mot de passe). | `/login` | Jules Singer |
| Se déconnecter | l'utilisateur peut se déconnecter via la page de son compte.| `/account` -> puis appuyez sur 'Déconnexion' | Jules Singer |
| Modifier son compte | l'utilisateur peut modifier les informations de son compte (sauf le login donc l'email pour respecter la consigne du sujet). | `/account/edit` | Jules Singer |
| Rendre une liste publique | le créateur d'une liste peut la rendre publique. | `/lists/{idList}/edit` | Hugo Fresnel |
| Afficher la liste des créateurs | Tout le monde peut voir les créateurs qui ont au moins une liste de publique. | `/accounts/public` | Thomas Bouillon |
| Afficher les listes de souhaits publiques | les visiteurs peuvent voir la liste des listes de souhaits publiques | `/lists/public` | Hugo Fresnel |
| Créer une cagnotte sur un item | le créateur d'une liste peut ouvrir une cagnotte sur ses items (avec un montant spécifié ne pouvant pas dépassé le prix de l'item). | `/lists/{tokenList}/items/{idItem}/founding_pot/create` | Hugo Fresnel |
| Participer à une cagnotte | les utilisateurs d'une liste peuvent participer à une cagnotte d'un item en indiquant un montant de participation. | `/lists/{tokenList}/items/{idItem}/founding_pot/participate` | Hugo Fresnel |
| Supprimer son compte | chaque utilisateur inscrit peut supprimer son compte. | `/account` --> puis appuyer sur le bouton 'supprimer mon compte' | Jules Singer |
| Joindre des listes à son compte | Un utilisateur peut joindre une liste à son compte avec un token de modification. | `/lists/join` | Thomas Bouillon |

## Autres tâches réalisées :

| Tâche | Description | Contributeur |
| --- | --- | --- |
| Middlewares d'authentification | facilite la vérification qu'un utilisateur est connecté. | Hugo Fresnel |
| Middlewares de propriété | facilite la vérification qu'un utilisateur est propriétaire ou peut modifier une liste ou un item. | Thomas Bouillon |
| Middleware de propriété d'un message (dans OwnerMiddleware) | vérification qu'un utilisateur est bien l'auteur d'un message public sur une liste afin que celui-ci puisse modifier ou supprimer ce message | Jérémy Poirel |
| Messages flash | message d'erreur ou message de validation utilisés pour les traitements. | Thomas Bouillon |
| Helper de validation | Helper pour valider facilement les données d'un formulaire. | Thomas Bouillon |
| Page d'accueil | point d'entrée du site, contient les explications de l'application | Jules Singer |
| Barre de navigation | intégration de la barre de navigation | Jules Singer |
| Autre | fix de bugs, refactoring, modifications mineures ... | Tout le monde |
