<?php

namespace Whishlist\Helpers;

/**
 * Classe pour ajouter des flashs
 */
class Flashes
{
    /**
     * Ajoute une flash en session
     *
     * @param string $message
     * @param string $type
     * @return void
     */
    public static function addFlash(string $message, string $type): void
    {
        $_SESSION['flashes'][] = [$type => $message];
    }

    /**
     * Getter de touts les flash en session
     *
     * @return array flashes
     */
    public static function getFlashes(): array
    {
        $arr = $_SESSION['flashes'] ?? [];
        unset($_SESSION['flashes']);
        return $arr;
    }
}
