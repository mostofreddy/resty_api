<?php
require '../vendor/autoload.php';

$app = new Resty\Api\Api();

$app->get('/', function ($request, $response, $args) {
    $response->write("Welcome to Slim!");
    return $response;
});

$app->get('/hello[/{name}]', function ($request, $response, $args) {
    $response->write("Hello, " . $args['name']);
    return $response;
})->setArgument('name', 'World!');

$app->get('/error/1', function ($request, $response, $args) {
    throw new \Exception("error 1");
    return $response;
});

$app->run();
