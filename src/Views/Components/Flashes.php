<?php

namespace Whishlist\Views\Components;

use Whishlist\Helpers\Flashes as FlashesHelper;

class Flashes
{
    /**
     * Construit la structure du document
     *
     * @return string l'HTML du footer du document
     */
    public static function getFlashes(): string
    {
        $html = "<div class='flashes'>";
        foreach(FlashesHelper::getFlashes() as $flash)
            $html .= <<<HTML
                <div class="flash flash-{$flash['type']}">
                    <p>{$flash['message']}</p>
                </div>
            HTML;
        $html .= "</div>";
        return $html;
    }
}
