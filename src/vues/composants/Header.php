<?php

namespace Whishlist\vues\composants;

class Header
{
       
    /**
     * construit la structure du document
     *
     * @return string l'HTML de l'entete du document
     */
    public static function getHeader($title) : string
    {
        $html = 
        '<!DOCTYPE html>
        <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>' . $title . '</title>
                <link rel="stylesheet" href="/public/css/style.css">
            </head>
            <body>
                ';

        return $html;
    }
}