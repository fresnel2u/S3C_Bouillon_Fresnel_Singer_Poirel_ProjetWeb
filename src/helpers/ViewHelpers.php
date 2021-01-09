<?php

namespace WhishList\helpers;

class ViewHelpers
{
    public static function generateLogOut($url): string
    {
        $html = <<<HTML
            <form method="POST" action="{$url}" class="logout">
                <div>
                    <button class="btn btn-primary">Déconnexion</button>
                </div>
            </form>
        HTML;

        return $html;
    }
}
