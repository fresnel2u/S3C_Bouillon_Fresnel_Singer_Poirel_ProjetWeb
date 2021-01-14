<?php

namespace Whishlist\Controllers;

use Slim\Container;
use Whishlist\Helpers\RouteTrait;

abstract class BaseController
{
    use RouteTrait;
    
    /**
     * Container de l'application
     *
     * @var Container
     */
    protected $container;

    /**
     * Constructeur du controleur
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;    
    }
}