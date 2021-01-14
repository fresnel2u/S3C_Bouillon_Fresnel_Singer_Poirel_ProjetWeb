<?php

namespace Whishlist\Helpers;

/**
 * Ajoute des helpers concernant le router
 */
trait RouteTrait
{
    /**
     * Génère l'URL d'une route
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function pathFor(string $route, array $params = []): string
    {
        return $this->container->router->pathFor($route, $params);
    }
}