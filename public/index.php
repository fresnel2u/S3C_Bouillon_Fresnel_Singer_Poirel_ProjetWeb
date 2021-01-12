<?php
require_once('../vendor/autoload.php');

use Slim\App;
use Whishlist\Configuration\Database;
use Whishlist\Controllers\AuthController;
use Whishlist\Controllers\HomeController;
use Whishlist\Controllers\ItemController;
use Whishlist\Controllers\ListController;
use Whishlist\Middlewares\AuthMiddleware;
use Whishlist\Middlewares\GuestMiddleware;
use Whishlist\Controllers\AccountController;
use Whishlist\Controllers\FoundingPotController;

session_start();

$config = require_once('../settings.php');
const ROUTE = __DIR__;

Database::connect();

$container = new \Slim\Container($config);
$app = new \Slim\App($container);

// Middlewares
$authMiddleware = new AuthMiddleware($container);
$guestMiddleware = new GuestMiddleware($container);

// Home
$app->get('/', HomeController::class . ':home')->setName('home');

// Lists
$app->group('', function (App $app) {
    
    $app->get('/lists', ListController::class . ':displayAllList')->setName('displayAllList');
    $app->get('/lists/new', ListController::class . ':newListPage')->setName('newListPage');
    $app->post('/lists/new', ListController::class . ':newList')->setName('newList');
    $app->get('/lists/{id}/show', ListController::class . ':displayList')->setName('displayList');
    $app->get('/lists/{id}/edit', ListController::class . ':editListPage')->setName('editListPage');
    $app->post('/lists/{id}/edit', ListController::class . ':editList')->setName('editList');
    $app->post('/lists/{id}/delete', ListController::class . ':deleteList')->setName('deleteList');
    $app->get('/lists/{id}/results', ListController::class . ':displayListResults')->setName('displayListResults');
})->add($authMiddleware);

// Items
$app->group('', function (App $app) {
    $app->get('/items', ItemController::class . ':displayAllItems')->setName('displayAllItems');
    $app->get('/items/new', ItemController::class . ':newItemPage')->setName('newItemPage');
    $app->post('/items/new', ItemController::class . ':newItem')->setName('newItem');
    $app->get('/items/{id}/edit', ItemController::class . ':editItemPage')->setName('editItemPage');
    $app->post('/items/{id}/edit', ItemController::class . ':editItem')->setName('editItem');
    $app->get('/items/{id}/lock', ItemController::class . ':lockItemPage')->setName('lockItemPage');
    $app->post('/items/{id}/lock', ItemController::class . ':lockItem')->setName('lockItem');
    $app->post('/items/{id}/lock/cancel', ItemController::class . ':cancelLockItem')->setName('cancelLockItem');
    $app->post('/items/{id}/delete', ItemController::class . ':deleteItem')->setName('deleteItem');
})->add($authMiddleware);

// Founding pot
$app->group('', function (App $app) {
    $app->get('/items/{item_id}/founding_pot/create', FoundingPotController::class . ':createPage')->setName('createFoundingPotPage');
    $app->post('/items/{item_id}/founding_pot/create', FoundingPotController::class . ':create')->setName('createFoundingPot');
    $app->get('/items/{item_id}/founding_pot/participate', FoundingPotController::class . ':participatePage')->setName('participateFoundingPotPage');
    $app->post('/items/{item_id}/founding_pot/participate', FoundingPotController::class . ':participate')->setName('participateFoundingPot');
})->add($authMiddleware);

// Auth
$app->group('', function (App $app) {
    $app->get('/login', AuthController::class . ':getLogin')->setName('loginPage');
    $app->post('/login', AuthController::class.':login')->setName('login');
    $app->get('/register', AuthController::class.':getRegister')->setName('registerPage');
    $app->post('/register', AuthController::class.':register')->setName('register');
})->add($guestMiddleware);
$app->post('/logout', AuthController::class.':logout')->setName('logout');

// Account
$app->group('', function (App $app) {
    $app->get('/account', AccountController::class.':displayAccount')->setName('displayAccount');
    $app->get('/account/edit', AccountController::class.':displayEditAccount')->setName('editAccountPage');
    $app->post('/account/edit', AccountController::class.':editAccount')->setName('editAccount');
    $app->post('/account/delete', AccountController::class . ':deleteAccount')->setName('deleteAccount');
})->add($authMiddleware);

$app->run();





 

