<?php
error_reporting(error_level: E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
ini_set(option: 'display_errors', value: 'Off');

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\App as App;
use Service\Db as Db;
use Model\Product as Product;
use Model\Basket as Basket;

require_once 'vendor/autoload.php';
require_once 'connectionDB.php';
require_once 'services/Db.php';
require_once 'model/Basket.php';
require_once 'model/Product.php';

$app = new App();
$db = new Db(name: DBNAME,host: HOST,user: USER,password: PASSWORD);
$connection = $db->createConnection();
if($connection instanceof PDOException){
    $db->create();
    $connection = $db->createConnection();
}
$product = new Product(connection: $connection);
if(!$product->checkExists()){
    $product->create();
}
$basket = new Basket(connection: $connection);
if(!$basket->checkExists()){
    $basket->create();
}

$app->get('/hello', function (Request $request, Response $response) {
    $response->getBody()->write("Hello");
    return $response;
});
$app->run();
