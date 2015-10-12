<?php


require "vendor/autoload.php";
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

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

    //$req = \Httpful\Request::post('http://181.118.150.147/sensor/create/', json_encode($a), "application/json");


    $headers= array("Accept" => "application/x-www-form-urlencoded");

	$response = Unirest\Request::post("http://181.118.150.147/sensor/create/", $headers, $a);

    var_dump($response->body);
});


$app->run();
