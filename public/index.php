<?php
require_once('../vendor/autoload.php');

use Whishlist\Configuration\Database;
use Whishlist\Controllers\AuthController;
use Whishlist\Controllers\HomeController;
use Whishlist\Controllers\ItemController;
use Whishlist\Controllers\ListController;
use Whishlist\Controllers\AccountController;
use Whishlist\Controllers\FoundingPotController;

session_start();

$config = require_once('../settings.php');

Database::connect();

$container = new \Slim\Container($config);
$app = new \Slim\App($container);

$app->get('/', HomeController::class . ':home')->setName('home');

// Lists
$app->get('/lists', ListController::class . ':displayAllList')->setName('displayAllList');
$app->get('/lists/new', ListController::class . ':newListPage')->setName('newListPage');
$app->post('/lists/new', ListController::class . ':newList')->setName('newList');
$app->get('/lists/{id}/show', ListController::class . ':displayList')->setName('displayList');
$app->get('/lists/{id}/edit', ListController::class . ':editListPage')->setName('editListPage');
$app->post('/lists/{id}/edit', ListController::class . ':editList')->setName('editList');
$app->post('/lists/{id}/delete', ListController::class . ':deleteList')->setName('deleteList');

// Items
$app->get('/items', ItemController::class . ':displayAllItems')->setName('displayAllItems');
$app->get('/items/new', ItemController::class . ':newItemPage')->setName('newItemPage');
$app->post('/items/new', ItemController::class . ':newItem')->setName('newItem');
$app->get('/items/{id}/edit', ItemController::class . ':editItemPage')->setName('editItemPage');
$app->post('/items/{id}/edit', ItemController::class . ':editItem')->setName('editItem');
$app->post('/items/{id}/lock', ItemController::class . ':lockItem')->setName('lockItem');
$app->post('/items/{id}/delete', ItemController::class . ':deleteItem')->setName('deleteItem');

// Founding pot
$app->get('/items/{item_id}/founding_pot/create', FoundingPotController::class . ':createPage')->setName('createFoundingPotPage');
$app->post('/items/{item_id}/founding_pot/create', FoundingPotController::class . ':create')->setName('createFoundingPot');
$app->get('/items/{item_id}/founding_pot/participate', FoundingPotController::class . ':participatePage')->setName('participateFoundingPotPage');
$app->post('/items/{item_id}/founding_pot/participate', FoundingPotController::class . ':participate')->setName('participateFoundingPot');

// Auth
$app->get('/login', AuthController::class . ':getLogin')->setName('loginPage');
$app->post('/login', AuthController::class.':login')->setName('login');
$app->get('/register', AuthController::class.':getRegister')->setName('registerPage');
$app->post('/register', AuthController::class.':register')->setName('register');
$app->post('/logout', AuthController::class.':logout')->setName('logout');

// Account
$app->get('/account', AccountController::class.':displayAccount')->setName('displayAccount');
$app->get('/account/edit', AccountController::class.':displayEditAccount')->setName('editAccountPage');
$app->post('/account/edit', AccountController::class.':editAccount')->setName('editAccount');
$app->post('/account/delete', AccountController::class . ':deleteAccount')->setName('deleteAccount');

$app->run();





 

