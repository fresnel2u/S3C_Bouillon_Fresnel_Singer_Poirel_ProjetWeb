<?php

namespace Whishlist\controleur;

use Slim\Http\Request;
use Slim\Http\Response;
use Whishlist\modele\FoundingPot;
use Whishlist\modele\Liste;

/**
 * Controleur pour les cagnottes
 */
class FoundingPotController
{
    private $container;

    /**
     * Constructeur du controleur
     *
     * @param \Slim\Container $c
     */
    function __construct(\Slim\Container $c)
    {
        $this->container = $c;
    }

    /**
     * Crée une cagnotte
     *
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function create(Request $rq, Response $rs, array $args): Response
    {
        $post = $rq->getParsedBody();
        $post = array_map(function ($field) {
            return filter_var($field, FILTER_SANITIZE_STRING);
        }, $post);

        try {
            $list = Liste::findOrFail($args); // vérifie si la liste existe

            $foundingPot = new FoundingPot();
            $foundingPot->liste_id = $list->id;
            $foundingPot->montant = $post['montant'];
            $foundingPot->save();

            return $rs->withRedirect($this->container->router->pathFor('editList', ['id' => $list->id]));
        } catch (\Throwable $th) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('displayAllList'));
            return $rs;
        }
    }

    /**
     * Met à jour une cagnotte
     *
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function update(Request $rq, Response $rs, array $args): Response
    {
        $post = $rq->getParsedBody();
        $post = array_map(function ($field) {
            return filter_var($field, FILTER_SANITIZE_STRING);
        }, $post);

        try {
            $list = Liste::findOrFail($args); // vérifie si la liste existe

            $foundingPot = FoundingPot::findOrFail($args['founding_pot_id']);
            $foundingPot->liste_id = $list->id;
            $foundingPot->montant = $post['montant'];
            $foundingPot->save();

            return $rs->withRedirect($this->container->router->pathFor('editList', ['id' => $list->id]));
        } catch (\Throwable $th) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('displayAllList'));
            return $rs;
        }
    }

    /**
     * Supprime une cagnotte
     *
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function delete(Request $rq, Response $rs, array $args): Response
    {
        try {
            $list = Liste::findOrFail($args); // vérifie si la liste existe

            $foundingPot = FoundingPot::findOrFail($args['founding_pot_id']);
            $foundingPot->delete();

            return $rs->withRedirect($this->container->router->pathFor('editList', ['id' => $list->id]));
        } catch (\Throwable $th) {
            $rs->withStatus(400);
            $rs->withRedirect($this->container->router->pathFor('displayAllList'));
            return $rs;
        }

        return $rs;
    }
}
