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
                    <link rel="stylesheet" href="/css/style.css">
                    <link rel="preconnect" href="https://fonts.gstatic.com">
                    <link href="https://fonts.googleapis.com/css2?family=Abhaya+Libre&family=Nunito&display=swap" rel="stylesheet">
                </head>
                <body>
        HTML;
    }
}
