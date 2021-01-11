<?php

namespace Whishlist\Middlewares;

use Slim\Container;

abstract class BaseMiddleware
{
    /**
     * Container de l'application
     *
     * @var Container|null
     */
    protected $container;

    /**
     * Constructeur du middleware
     *
     * @param Container|null $container
     */
    public function __construct(?Container $container = null)
    {
        $this->container = $container;
    }
}