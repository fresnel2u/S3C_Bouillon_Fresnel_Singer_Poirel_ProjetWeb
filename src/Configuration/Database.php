<?php

namespace Whishlist\Configuration;

use Illuminate\Database\Capsule\Manager as DB;

/**
 * cette classe est la configuration de la base de données
 */
class Database
{
    /**
     * connect : connexion a la base de données selon le contenu du fichier de configuration
     *
     * @return void
     */
    public static function connect()
    {
        $bddConfig = parse_ini_file('conf.ini');

        $db = new DB();
        $db->addConnection([
            'driver'    => 'mysql',
            'host'      => $bddConfig['host'],
            'port'      => $bddConfig['port'],
            'database'  => $bddConfig['database'],
            'username'  => $bddConfig['username'],
            'password'  => $bddConfig['password'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => ''
        ]);
        $db->setAsGlobal();
        $db->bootEloquent();
    }
}
