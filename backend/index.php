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
$resultGetAllRecords = $product->getAllRecords();
if($resultGetAllRecords instanceof PDOException || count($resultGetAllRecords) == 0){
    require_once("addProduct.php");
}
/**
 * Добавление заголовков CORS
 */
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', ' http://localhost:8081')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});
/**
 * Путь для просмотра товаров
 */
$app->get(pattern: '/products',callable: function (Request $request, Response $response) {
    $db = new Db(name: DBNAME,host: HOST,user: USER,password: PASSWORD);
    $product = new Product(connection: $db->createConnection());
    $resultGetAllRecords = $product->getAllRecords();
    if($resultGetAllRecords instanceof PDOException){
        $response->withStatus(code: 500)
            ->withHeader(name: 'Content-Type', value: 'application/json')
            ->write(data: json_encode([
                "message" => $resultGetAllRecords->getMessage()
            ]));
        return $response;
    }
    if (count($resultGetAllRecords) == 0){
        $response->withStatus(code: 500)
            ->withHeader(name: 'Content-Type', value: 'application/json')
            ->write(data: json_encode([
                "message" => "There are no entries in the bucket"
            ]));
        return $response;
    }
    $productsJson = json_encode($resultGetAllRecords);
    $response->withStatus(code: 200)
        ->withHeader(name: 'Content-Type', value: 'application/json')
        ->write(data: $productsJson);
    return $response;
});
/**
 * Путь для просмотра товаров в корзине
 */
$app->get(pattern: '/basket',callable: function (Request $request, Response $response) {
    $db = new Db(name: DBNAME,host: HOST,user: USER,password: PASSWORD);
    $basket = new Basket(connection: $db->createConnection());
    $resultGetAllRecords = $basket->getAllRecords();
    if($resultGetAllRecords instanceof PDOException){
        $response->withStatus(code: 500)
            ->withHeader(name: 'Content-Type', value: 'application/json')
            ->write(data: json_encode([
                "message" => $resultGetAllRecords->getMessage()
            ]));
        return $response;
    }
    if (count($resultGetAllRecords) == 0){
        $response->withStatus(code: 500)
            ->withHeader(name: 'Content-Type', value: 'application/json')
            ->write(data: json_encode([
                "message" => "There are no entries in the bucket"
            ]));
        return $response;
    }
    $basketJson = json_encode($resultGetAllRecords);
    $response->withStatus(code: 200)
        ->withHeader(name: 'Content-Type', value: 'application/json')
        ->write(data: $basketJson);
    return $response;
});
/**
 * Путь для добавления товара в корзину
 */
$app->post(pattern: '/basket/add',callable: function (Request $request, Response $response) {
    $paramsRequest = $request->getParsedBody();
    if($paramsRequest == null || $paramsRequest["data"] == null || count(value: $paramsRequest["data"]) == 0){
        $response->withStatus(code: 400)
            ->withHeader(name: 'Content-Type', value: 'application/json')
            ->write(data: json_encode([
                "message" => "No input parameters were specified"
            ]));
        return $response;
    }
    $db = new Db(name: DBNAME,host: HOST,user: USER,password: PASSWORD);
    $basket = new Basket(connection: $db->createConnection());
    $resultAdd = $basket->addRecord(params: $paramsRequest["data"]);
    if($resultAdd instanceof PDOException && $resultAdd->errorInfo[1] == 1048){
        $response->withStatus(code: 400)
            ->withHeader(name: 'Content-Type', value: 'application/json')
            ->write(data: json_encode([
                "message" => "To add to the cart, you must specify the product_id parameter"
            ]));
    }
    if($resultAdd instanceof PDOException){
        $response->withStatus(code: 500)
            ->withHeader(name: 'Content-Type', value: 'application/json')
            ->write(data: json_encode([
                "message" => $resultAdd->getMessage()
            ]));
        return $response;
    }
    $response->withStatus(code: 200)
        ->withHeader(name: 'Content-Type', value: 'application/json')
        ->write(data: json_encode($resultAdd));
    return $response;
});
/**
 * Путь для удаления товара из корзины по id
 */
$app->delete(pattern: '/basket/delete/{id}',callable: function (Request $request, Response $response) {
    $id = $request->getAttribute(name: "id",default: null);
    if($id == null || !is_numeric($id)){
        $response->withStatus(code: 400)
            ->withHeader(name: 'Content-Type', value: 'application/json')
            ->write(data: json_encode([
                "message" => "Id is specified incorrectly"
            ]));
        return $response;
    }
    $db = new Db(name: DBNAME,host: HOST,user: USER,password: PASSWORD);
    $basket = new Basket(connection: $db->createConnection());
    $resultDelete = $basket->deleteRecord(id:$id);
    if($resultDelete instanceof PDOException){
        $response->withStatus(code: 400)
            ->withHeader(name: 'Content-Type', value: 'application/json')
            ->write(data: json_encode([
                "message" => $resultDelete->getMessage()
            ]));
        return $response;
    }
    $response->withStatus(code: 200)
        ->withHeader(name: 'Content-Type', value: 'application/json')
        ->write(data: json_encode($resultDelete));
    return $response;
});

$app->run();
