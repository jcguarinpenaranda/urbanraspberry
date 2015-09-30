<?php


require "vendor/autoload.php";
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
	$texto = file_get_contents("variables.json");
	echo $texto;
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
	"variable_id":3
00}

*/
$app->post('/datosensor/',function() use($app){
	$a = $app->request->params();
	$geoip = file_get_contents("http://telize.com/geoip");
	//var_dump($geoip);
	$jsonip = json_decode($geoip,true);

	//var_dump($a); // Crea el valor para realizar el post.
    //echo $a["value"]; // Busca el valor referenciado por la clave.
    $a ["longitude"] = $jsonip ["longitude"];
    $a ["latitude"]=   $jsonip ["latitude"];
    $a ["date"]= date('d-m-y_H:i:s');
    $a ["description"]= "urbanraspberry";

//    $req = \Httpful\Request::post('http://181.118.150.147/sensor/create/', json_encode($a), "application/json");

    $headers= array("Accept" => "application/x-www-form-urlencoded");

	$response = Unirest\Request::post("http://181.118.150.147/sensor/create/", $headers, $a);

    var_dump($response->body);
});


$app->run();
