<?php

namespace Whishlist\Controllers;

use Slim\Container;

abstract class BaseController
{
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