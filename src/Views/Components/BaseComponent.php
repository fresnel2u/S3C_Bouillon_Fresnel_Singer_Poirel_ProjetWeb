<?php

namespace Whishlist\Views\Components;

use Slim\Container;
use Whishlist\Helpers\RouteTrait;

abstract class BaseComponent
{
    use RouteTrait;

    /**
     * Container de l'application
     *
     * @var Container|null
     */
    protected $container;

    public function __construct(?Container $container = null)
    {
        $this->container = $container;
    }

    /**
     * Rend le composant au format HTML
     *
     * @return string
     */
    public abstract function render(): string;
}