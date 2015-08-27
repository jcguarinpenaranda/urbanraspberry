<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();


$app->get('/',function(){
	echo "Hola";
});

$app->post('/',function() use($app){
	$a = $app->request->params();

	echo "Datos enviados:";
	var_dump($a);
});


$app->run();
