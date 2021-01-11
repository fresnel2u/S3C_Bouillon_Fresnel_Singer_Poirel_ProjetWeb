<?php

namespace Whishlist\Views;

use Slim\Container;
use Whishlist\Views\Components\Flashes;
use Whishlist\Views\Components\Menu;
use Whishlist\Views\Components\Header;

abstract class BaseView
{
    /**
     * Container de l'application
     *
     * @var Container
     */
    protected $container;
    /**
     * Paramètres de la vue
     *
     * @var array
     */
    protected $params;

    /**
     * Constructeur de la vue
     *
     * @param Container $container
     * @param array $params
     */
    public function __construct(Container $container, array $params = [])
    {
        $this->container = $container;
        $this->params = $params;
    }

    /**
     * Rend la page HTML
     *
     * @param integer $selector
     * @return string page HTML en chaine de caractères
     */
    public abstract function render(int $selector): string;

    /**
     * Met en place le layout autour de la page HTML
     *
     * @param string $html
     * @param string|null $title
     * @return string html avec son layout
     */
    public function layout(string $html, ?string $title = null, ?int $set_menu = 1, ?int $set_flashes = 1): string
    {
        $result = Header::getHeader($title);
        if($set_menu != 0)
            $result .= Menu::getMenu();
        if($set_flashes)
            $result .= Flashes::getFlashes();
        $result .= $html;
        $result .= "</body></html>";
        return $result;
    }
}