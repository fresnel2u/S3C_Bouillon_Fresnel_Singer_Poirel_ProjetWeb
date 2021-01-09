<?php

namespace Whishlist\Views;

use Slim\Container;

abstract class BaseView
{
    /**
     * Container de l'application
     *
     * @var Container
     */
    protected $container;
    /**
     * Paramètres de la vue
     *
     * @var array
     */
    protected $params;

    /**
     * Constructeur de la vue
     *
     * @param Container $container
     * @param array $params
     */
    public function __construct(Container $container, array $params = [])
    {
        $this->container = $container;
        $this->params = $params;
    }

    /**
     * Rend la page HTML
     *
     * @param integer $selector
     * @return string page HTML en chaine de caractères
     */
    public abstract function render(int $selector): string;
}