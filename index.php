<?php


require "vendor/autoload.php";

$app = new \Slim\App;
$app->get('/foo', function ($req, $res, $args) {
    return $res->withHeader(
        'Content-Type',
        'application/json'
    );
});
$app->run();
