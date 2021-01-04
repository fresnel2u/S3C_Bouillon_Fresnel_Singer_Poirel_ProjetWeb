<?php
require_once('../vendor/autoload.php');

use Whishlist\conf\Database;
use Whishlist\controleur\ParticipationController;
use Whishlist\controleur\CreationController;
use Whishlist\controleur\ConnectionController;

$config = require_once('../settings.php');

Database::connect();

$container = new \Slim\Container($config);
$app = new \Slim\App($container);

$app->get('/', ParticipationController::class . ':home')->setName('home');

// Lists
$app->get('/allList', ParticipationController::class . ':displayAllList')->setName('displayAllList');
$app->get('/list/{id}', ParticipationController::class . ':displayList')->setName('displayList');
$app->get('/newList', CreationController::class . ':newListPage')->setName('newListPage');
$app->post('/newList', CreationController::class . ':newList')->setName('newList');

$app->get('/editList/{id}', CreationController::class . ':editListPage')->setName('editListPage');
$app->post('/editList/{id}', CreationController::class . ':saveList')->setName('saveList');

// Items
$app->get('/items', ParticipationController::class . ':displayAllItems')->setName('displayAllItems');
$app->get('/items/new', CreationController::class . ':newItemPage')->setName('newItemPage');
$app->post('/items/new', CreationController::class . ':newItem')->setName('newItem');
$app->get('/items/{id}', ParticipationController::class . ':displayItem')->setName('displayItem');
$app->get('/items/{id}/edit', CreationController::class . ':editItemPage')->setName('editItemPage');
$app->post('/items/{id}/edit', CreationController::class . ':editItem')->setName('editItem');
$app->post('/items/{id}/delete', CreationController::class . ':deleteItem')->setName('deleteItem');

// Auth

$app->get('/login', ConnectionController::class . ':getLogin')->setName('loginPage');
$app->get('/register', ConnectionController::class.':getRegister')->setName('registerPage');

$app->post('/login', ConnectionController::class.':login')->setName('login');
$app->post('/register', ConnectionController::class.':register')->setName('register');

$app->run();

 






 

