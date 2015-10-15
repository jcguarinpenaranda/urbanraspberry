<?php


require "vendor/autoload.php";

$app = new \Slim\App;


/*--------------------------------------VARIABLES-------------------------*/

$app->get('/variables/',function($req,$res,$args){

	$cont = file_get_contents('http://181.118.150.147/sensor/list');

	$variables = json_decode($cont,true);
	$variablesActivas = array();

	for($i=0; $i<count($variables);$i++){
		if($variables[$i]['enable']==true){
			$variablesActivas[]=$variables[$i];
		}
	}

	echo json_encode($variablesActivas);
});

/*------------------------------------- EQUIPOS --------------------------*/

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
$app->get('/equipos/',function($req,$res,$args){
	$texto = file_get_contents("equipos.json");
	$json = json_decode($texto,true);

	for($i=0;$i<count($json);$i++){
		for($j=0;$j<count($json[$i]['variables']);$j++){
			$json[$j]['variables'][$j]['pinesTexto'] = implode(",",$json[$i]['variables'][$j]['pines']);
		}
	}

	echo json_encode($json);

	return $res->withHeader(
		"Content-Type",
		"application/json");
});

/*
En /equipos, un cliente que haga delete
podrá borrar un equipo dado por un id
*/
$app->delete('/equipos/{id}',function($req, $res, $args){

	$id = $args["id"];

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

		//unset($equipos[$pos]);
		array_splice($equipos, $pos, 1);

		//var_dump($equipos);

		file_put_contents("equipos.json", json_encode($equipos));

		$status = array();
		$status['status']= 200;
		$status['description'] = "Equipo eliminado.";

		echo json_encode($status);

	}else{

		$status['status']= 404;
		$status['description'] = "Equipo no existe.";

		echo json_encode($status);

	}

});


/*
En /equipos, un cliente que haga POST
podrá añadir un nuevo equipo
*/
$app->post('/equipos/',function($req, $res, $args){

	$body = $req->getParsedBody();

	$params = $body;

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

		//Se escribe en el archivo el contenido del arreglo con la adición.
		file_put_contents("equipos.json", json_encode($equipos));

		$status = array(
			"status"=>201,
			"description" => "Equipo Creado");

		echo json_encode($status);
	}

});



/*-----------------------------DATOS SENSORES ----------------------------*/

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
$app->post('/datosensor/',function($req,$res,$args){

	$a = $req->getParsedBody();

	$geoip = file_get_contents("http://telize.com/geoip");
	$jsonip = json_decode($geoip,true);

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




$app->run();
