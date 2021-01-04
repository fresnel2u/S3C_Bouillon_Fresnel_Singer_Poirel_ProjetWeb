<?php

use Whishlist\conf\Database;
use Whishlist\controleur\ParticipationController;
use Whishlist\controleur\CreationController;

use Whishlist\modele\Item;
use Whishlist\modele\Liste;

use Slim\Http\Request;
use Slim\Http\Response;
require_once 'vendor/autoload.php';
$config = require_once 'settings.php';

Database::connect();

$container = new \Slim\Container($config);
$app = new \Slim\App($container);

$app->get('/', ParticipationController::class . ':home')->setName('home');

$app->get('/allList', ParticipationController::class . ':displayAllList')->setName('displayAllList');

$app->get('/list/{id}', ParticipationController::class . ':displayList')->setName('displayList');

$app->get('/item/{id}', ParticipationController::class . ':displayItem')->setName('displayItem');

$app->get('/newList', CreationController::class . ':formList')->setName('formList');

$app->post('/newList', CreationController::class . ':newList')->setName('newList');

$app->run();

 






 

