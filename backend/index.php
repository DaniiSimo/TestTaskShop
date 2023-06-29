<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
ini_set('display_errors', 'Off');

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\App as App;

require_once 'vendor/autoload.php';

$app = new App();
$app->get('/hello', function (Request $request, Response $response) {
    $response->getBody()->write("Hello");
    return $response;
});
$app->run();