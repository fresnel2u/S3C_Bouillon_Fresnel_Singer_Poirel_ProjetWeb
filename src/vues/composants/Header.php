<?php

namespace Whishlist\vues\composants;

class Header
{
    /**
     * Construit la structure du document
     *
     * @return string l'HTML de l'entete du document
     */
    public static function getHeader($title): string
    {
        return <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <title>{$title}</title>
                    <link rel="stylesheet" href="/public/css/style.css">
                </head>
                <body>
        HTML;
    }
}
