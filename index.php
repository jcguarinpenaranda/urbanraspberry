<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

/*
En /variables un cliente que haga
un get obtendrá los datos de los pines
en los cuales tendrá conectado uno
o varios sensores, y el nombre de
la variable que corresponde a cada
uno.

El formato propuesto para recibir es:

{
	"variable":"temperatura",
	"pines":["a0","a1"],
	"frecuencia":60 //en segundos
}

*/
$app->get('/variables/',function(){


});


/*
En /datosensor, un cliente que haga
una petición post podrá enviar datos
directamente a la plataforma de
UrbanEyes

El formato que debe enviar el cliente
será:

{
	"value":123,
	"variable":"temperatura"
}

*/
$app->post('/datosensor/',function() use($app){
	//$a = $app->request->params();

});


$app->run();
