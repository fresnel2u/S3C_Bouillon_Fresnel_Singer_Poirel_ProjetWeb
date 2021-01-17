<?php

namespace Whishlist\Views\Components;

use Whishlist\Helpers\Flashes as FlashesHelper;

class Flashes extends BaseComponent
{
    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $html = "<div class='flashes'>";
        foreach(FlashesHelper::getFlashes() as $flash) {
            $html .= <<<HTML
                <div class="flash flash-{$flash['type']}">
                    <p>{$flash['message']}</p>
                </div>
            HTML;
        }
        return $html . "</div>";
    }
}
