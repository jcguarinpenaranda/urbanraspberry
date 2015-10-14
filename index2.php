<?php


require "vendor/autoload.php";
/*require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();*/

$app = new \Slim\Slim(array(
    'mode' => 'development'
));

// Only invoked if mode is "development"
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
});

$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'debug' => false
    ));
});


/*
En /equipos un cliente que haga
un get obtendrá los datos de los pines
en los cuales tendrá conectado uno
o varios sensores, y el nombre de
la variable que corresponde a cada
uno.

El formato propuesto para recibir es:

{
	"id":"abc",
	"nombre":"Equipo prueba",
	"variable":"temperatura",
	"pines":["a0","a1"],
	"frecuencia":60 //en segundos
}
*/
$app->get('/equipos/',function(){
	$texto = file_get_contents("equipos.json");
	echo $texto;
});

/*
En /equipos, un cliente que haga delete
podrá borrar un equipo dado por un id
*/
$app->delete('/equipos/:id',function($id){

	try{

	$texto = file_get_contents("equipos.json");

	$equipos = json_decode($texto, true);

	$existe = false;
	$pos;

	for($i =0 ; $i<count($equipos); $i++){
		//var_dump($equipos[$i]['id']);
		//echo "Igual: ".$params['id']." ".$equipos[$i]['id'];
			if($equipos[$i]['id'] === $id){
				//echo $equipos[$i]['id'];
				$existe = true;
				$pos = $i;
			}
	}

	if($existe){

		$status = array();
		$status['status']= 200;
		$status['description'] = "Equipo eliminado.";

		//unset($equipos[$pos]);
		array_splice($equipos, $pos, 1);

		//var_dump($equipos);

		file_put_contents("equipos.json", json_encode($equipos));


		echo json_encode($status);

	}else{

		$status['status']= 404;
		$status['description'] = "Equipo no existe.";

		echo json_encode($status);

	}

}catch(Exception $e){
	var_dump($e);
}

});


/*
En /equipos, un cliente que haga POST
podrá añadir un nuevo equipo
*/
$app->post('/equipos/',function(){
	$app = \Slim\Slim::getInstance();
	$req = $app->request;
	$params = $req->params();

	//var_dump($params);

	$texto = file_get_contents("equipos.json");
	//var_dump($texto);

	$equipos = json_decode($texto, true);
	//var_dump($equipos);

	$existe = false;

	for($i =0 ; $i<count($equipos); $i++){
		//var_dump($equipos[$i]['id']);
		//echo "Igual: ".$params['id']." ".$equipos[$i]['id'];
		if(isset($equipos[$i]['id']) && isset($params['id'])){
			if($equipos[$i]['id'] === $params['id']){
				//echo $equipos[$i]['id'];
				$existe = true;
			}
		}
	}

	if($existe == true){

		$status = array();
		$status['status']= 304;
		$status['description'] = "El equipo ya existe";

		echo json_encode($status);

	}else{

		$equipos[]=array("id"=>$params['id'], "nombre"=>$params['nombre'], "variables"=> array(), "frecuencia"=>null);

		//var_dump($equipos);


		//Se escribe en el archivo el contenido del arreglo con la adición.
		file_put_contents("equipos.json", json_encode($equipos));

		$status = array(
			"status"=>201,
			"description" => "Equipo Creado");

		echo json_encode($status);
	}

});


/*
En /variables, un cliente que haga un
get, obtendrá la lista de variables
disponibles para hacer reportes en el
sistema de UrbanEyes.
*/
$app->get('/variables/',function(){
	$app = \Slim\Slim::getInstance();



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
}

ó

?value=123&variable_id=3

*/
$app->get('/datosensor/',function(){
	$app = \Slim\Slim::getInstance();
	$geoip = file_get_contents("http://telize.com/geoip");
	$jsonip = json_decode($geoip,true);
	$a = $app->request->params();

	if(!isset($a["value"]) || !isset($a["variable_id"])){
		echo json_encode(array("status"=>400,"description"=>"Mala petición. Debes enviar los parámetros value y variable_id."));
		return;
	}

	//Se añaden valores adicionales a la petición del cliente
	$ar = array();
	$ar ["value"] = $a["value"];
	$ar ["variable_id"] = $a["variable_id"];
	$ar ["longitude"] = $jsonip ["longitude"];
	$ar ["latitude"]=   $jsonip ["latitude"];
	$ar ["date"]= date('d-m-y_H:i:s');
	$ar ["description"]= "urbanraspberry";

	$response = file_get_contents("http://181.118.150.147/sensor/create?latitude=".$jsonip["latitude"]."&longitude=".$jsonip["longitude"]."&date=".date('d-m-y_H:i:s')."&description=urbanraspberry&value=".$ar["value"]."&variable_id=".$ar["variable_id"]);

	if(is_string($response)){
		$resp = json_decode($response, true);

		if($resp['create']=="True"){
			echo json_encode(array("status"=>201, "description"=>"Dato creado."));
		}else{
			echo json_encode(array("status"=>202, "description"=>"La petición se ha realizado, pero el servidor remoto no ha creado el dato."));
		}
	}else{
		echo json_encode(array("status"=>500,"description"=>"Ha ocurrido un error en la conexión a internet."));
	}
});


$app->post('/datosensor/',function(){
		$app = \Slim\Slim::getInstance();

		$params = $app->request->params();
		$body = $app->request->getBody();

		$arr = array();
		if(count($params)>0){
			$arr = $params;
		}else{
			if()
		}

});

/*$app->map('/datosensor/',function(){
	$app = \Slim\Slim::getInstance();
	$body = $app->request->getBody();

	$a = $app->request->params();
	$jsonbody = array();

	if(!count($body)> 0){
		echo "Parms";
	}

	if((count($a)> 0 && strlen($body)<0) && ( !isset($a["value"]) || !isset($a["variable_id"]))){
		echo json_encode(array("status"=>400,"description"=>"Mala petición. Debes enviar los parámetros value y variable_id."));
		return;
	}elseif (strlen ($body)>0){
		$jsonbody = json_decode($body,true);

		if(!isset($jsonbody['value']) || !isset($jsonbody['variable_id'])){
			echo json_encode(array("status"=>400,"description"=>'Mala petición. Debes enviar los parámetros {"value":float,"variable_id":int}'));
			return;
		}

	}

	//Se obtiene la geolocalización de la petición
/*	$geoip = file_get_contents("http://telize.com/geoip");
	$jsonip = json_decode($geoip,true);

	//Se añaden valores adicionales a la petición del cliente
	$ar = array();

	if(count($body)>0){ //viene por json
		$ar ["value"] = $jsonbody["value"];
		$ar ["variable_id"] = $jsonbody["variable_id"];
	}else//viene por parámetros
		$ar ["value"] = $a["value"];
		$ar ["variable_id"] = $a["variable_id"];
	}

	$ar ["longitude"] = $jsonip ["longitude"];
	$ar ["latitude"]=   $jsonip ["latitude"];
	$ar ["date"]= date('d-m-y_H:i:s');
	$ar ["description"]= "urbanraspberry";

	$response = file_get_contents("http://181.118.150.147/sensor/create?latitude=".$jsonip["latitude"]."&longitude=".$jsonip["longitude"]."&date=".date('d-m-y_H:i:s')."&description=urbanraspberry&value=".$ar["value"]."&variable_id=".$ar["variable_id"]);

	if(is_string($response)){
		$resp = json_decode($response, true);

		if($resp['create']=="True"){
			echo json_encode(array("status"=>201, "description"=>"Dato creado."));
		}else{
			echo json_encode(array("status"=>202, "description"=>"La petición se ha realizado, pero el servidor remoto no ha creado el dato."));
		}
	}else{
		echo json_encode(array("status"=>500,"description"=>"Ha ocurrido un error en la conexión a internet."));
	}
}) -> via ('GET','POST');*/


$app->get('/pruebapost2/',function(){
	$headers= array("Accept" => "application/x-www-form-urlencoded");
	$response = file_get_contents("http://127.0.0.1/urbanraspberry/pruebapost/?value=123&variable_id=4");
	var_dump($response);
});


$app->run();
