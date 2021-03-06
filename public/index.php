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
use Whishlist\Middlewares\EditMiddleWare;
use Whishlist\Middlewares\OwnerMiddleware;
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
$itemOwnerMiddleware = new OwnerMiddleware($container, OwnerMiddleware::ITEM, 'item_id');
$listOwnerMiddleware = new OwnerMiddleware($container, OwnerMiddleware::WISHLIST, 'list_id');
$foundingPotItemOwnerMiddleware = new OwnerMiddleware($container, OwnerMiddleware::ITEM, 'item_id');
$messageOwnerMiddleware = new OwnerMiddleware($container, OwnerMiddleware::MESSAGE);
$canEditMiddleWare = new EditMiddleWare($container);

// Home
$app->get('/', HomeController::class . ':home')->setName('home');

// Lists
$app->get('/lists/{token}/show', ListController::class . ':displayList')->setName('displayList');
$app->get('/lists/public', ListController::class . ':publicLists')->setName('publicLists');

$app->group('', function (App $app) {
    $app->get('/lists', ListController::class . ':displayAllLists')->setName('displayAllLists');
    $app->get('/lists/new', ListController::class . ':newListPage')->setName('newListPage');
    $app->post('/lists/new', ListController::class . ':newList')->setName('newList');
    $app->post('/lists/{token}/show', ListController::class . ':addListMessage')->setName('newListMessage');
    $app->get('/list/{token}/edit', ListController::class . ':editListMessagePage')->setName('editListMessagePage');
    $app->get('/lists/join', ListController::class . ':joinListPage')->setName('joinListPage');
    $app->post('/lists/join', ListController::class . ':joinList')->setName('joinList');
})->add($authMiddleware);

$app->group('', function(App $app) {
    $app->post('/lists/{token}/show/{id}', ListController::class . ':deleteListMessage')->setName('deleteListMessage');
    $app->post('/lists/{token}/edit/{id}', ListController::class . ':editListMessage')->setName('editListMessage');
})->add($authMiddleware)->add($messageOwnerMiddleware);

$app->group('', function (App $app) {
    $app->get('/lists/{token}/edit', ListController::class . ':editListPage')->setName('editListPage');
    $app->post('/lists/{token}/edit', ListController::class . ':editList')->setName('editList');
})->add($canEditMiddleWare);

// Items
$app->group('', function (App $app) {
    $app->get('/lists/{list_id}/items/{item_id}/lock', ItemController::class . ':lockItemPage')->setName('lockItemPage');
    $app->post('/lists/{list_id}/items/{item_id}/lock', ItemController::class . ':lockItem')->setName('lockItem');
    $app->post('/lists/{list_id}/items/{item_id}/lock/cancel', ItemController::class . ':cancelLockItem')->setName('cancelLockItem');
})->add($authMiddleware);

$app->group('', function (App $app) {
    $app->get('/lists/{list_id}/items', ItemController::class . ':displayAllItems')->setName('displayAllItems');
    $app->get('/lists/{list_id}/items/new', ItemController::class . ':newItemPage')->setName('newItemPage');
    $app->post('/lists/{list_id}/items/new', ItemController::class . ':newItem')->setName('newItem');
    $app->post('/lists/{list_id}/delete', ListController::class . ':deleteList')->setName('deleteList');
    $app->get('/lists/{list_id}/results', ListController::class . ':displayListResults')->setName('displayListResults');
})->add($authMiddleware)->add($listOwnerMiddleware);

$app->get('/lists/{token}/item/{item_id}', ItemController::class . ':displayItem')->setName('displayItem');

$app->group('', function (App $app) {
    $app->get('/lists/{list_id}/items/{item_id}/edit', ItemController::class . ':editItemPage')->setName('editItemPage');
    $app->post('/lists/{list_id}/items/{item_id}/edit', ItemController::class . ':editItem')->setName('editItem');
    $app->post('/lists/{list_id}/items/{item_id}/delete', ItemController::class . ':deleteItem')->setName('deleteItem');
})->add($authMiddleware)->add($itemOwnerMiddleware);

// Founding pot
$app->group('', function (App $app) {
    $app->get('/lists/{list_id}/items/{item_id}/founding_pot/create', FoundingPotController::class . ':createPage')->setName('createFoundingPotPage');
    $app->post('/lists/{list_id}/items/{item_id}/founding_pot/create', FoundingPotController::class . ':create')->setName('createFoundingPot');
    $app->get('/lists/{token}/items/{item_id}/founding_pot/participate', FoundingPotController::class . ':participatePage')->setName('participateFoundingPotPage');
    $app->post('/lists/{token}/items/{item_id}/founding_pot/participate', FoundingPotController::class . ':participate')->setName('participateFoundingPot');
})->add($authMiddleware)->add($foundingPotItemOwnerMiddleware);

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
$app->get('/accounts/public', AccountController::class . ':allPublicAccounts')->setName('publicAccounts');

$app->run();





 

