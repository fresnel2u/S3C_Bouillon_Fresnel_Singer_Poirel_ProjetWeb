<?php

namespace Whishlist\Helpers;

use Exception;

class Validator {
    /**
     * Verifie qu'aucune valeur du tableau n'est vide
     *
     * @param array $arr
     * @param array $exclude_keys = [], clés du tableau à ignorer
     * @return void
     * @throws Exception
     */
    public static function failIfEmptyOrNull(array $arr, array $exclude_keys = [])
    {
        foreach($arr as $key => $val) {
            if(!in_array($key, $exclude_keys) && ($val === '' || $val === null))
                throw new Exception("$key ne peut pas être vide");
        }
    }
}