<?php

namespace Whishlist\Views;

use Slim\Container;
use Whishlist\Helpers\RouteTrait;
use Whishlist\Views\Components\Menu;
use Whishlist\Views\Components\Header;
use Whishlist\Views\Components\Flashes;

abstract class BaseView
{
    use RouteTrait;

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
     * Met en place le layout autour de la page
     *
     * @param string $html
     * @param string|null $title
     * @param boolean $withMenu
     * @param boolean $withFlashes
     * @return string
     */
    public function layout(string $html, ?string $title = null, bool $withMenu = true, bool $withFlashes = true): string
    {
        $result = (new Header($title))->render();
        if ($withMenu) {
            $result .= (new Menu($this->container))->render();
        }
        if ($withFlashes) {
            $result .= (new Flashes())->render();
        }
        $result .= $html;
        $result .= "</body></html>";
        return $result;
    }
}
