<?php

namespace Whishlist\Views\Components;

use Slim\Container;

class Header extends BaseComponent
{
    /**
     * Titre de la page
     *
     * @var string|null
     */
    private $title;

    public function __construct(?string $title, ?Container $container = null)
    {
        parent::__construct($container);
        $this->title = $title;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <title>{$this->title}</title>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" href="/css/style.css">
                    <link rel="preconnect" href="https://fonts.gstatic.com">
                    <link href="https://fonts.googleapis.com/css2?family=Abhaya+Libre&family=Nunito&display=swap" rel="stylesheet">
                </head>
                <body>
        HTML;
    }
}
