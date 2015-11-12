<?php


require "vendor/autoload.php";

$app = new \Slim\App;


/*--------------------------------------VARIABLES-------------------------*/

//Un cliente que haga una petición a variables
//podrá obtener toda la lista de variables que se sensan
//en UrbanEyes.

$app->get('/variables/',function($req,$res,$args){

	$cont = file_get_contents('http://181.118.150.147/sensor/list');

	$variables = json_decode($cont,true);
	$variablesActivas = array();

	for($i=0; $i<count($variables);$i++){
		//Solo se muestran las variables que están
		//prendidas, de momento
		if($variables[$i]['enable']==true){
			$variablesActivas[]=$variables[$i];
		}
	}

	echo json_encode($variablesActivas);
});

/*------------------------------------- EQUIPOS --------------------------*/

//En /equipos/ un cliente que haga
//un get obtendrá los datos de los pines
//en los cuales tendrá conectado uno
//o varios sensores, y el nombre de
//la variable que corresponde a cada
//uno.
//
//El formato propuesto para recibir es:
//
//{
//	"id":"abc",
//	"nombre":"Equipo prueba",
//	"variable":"temperatura",
//	"pines":["a0","a1"],
//	"frecuencia":60 //en segundos
//}
$app->group('/equipos', function () {

	$this->get('/',function($req,$res,$args){
		$texto = file_get_contents("devices.json");
		$json = json_decode($texto,true);

		echo json_encode($json);

		return $res->withHeader(
			"Content-Type",
			"application/json");
	});



	//En /equipos/, un cliente que haga POST
	//podrá añadir un nuevo equipo
	$this->post('/',function($req, $res, $args){

		//Se coge el cuerpo de la petición
		//como un arreglo de PHP
		$body = $req->getParsedBody();

		$params = $body;

		//se carga el archivo de equipos
		$texto = file_get_contents("devices.json");

		//se interpreta dicho archivo como un arreglo
		//de PHP
		$equipos = json_decode($texto, true);

		//Se mira si el id del equipo enviado en los
		//existe para saber si se actualiza el registro
		//o se crea uno nuevo
		$existe = false;
		for($i =0 ; $i<count($equipos); $i++){

			if(isset($equipos[$i]['id']) && isset($params['id'])){
				if($equipos[$i]['id'] === $params['id']){
					$existe = true;
				}
			}

		}

		if($existe == true){

			//Se dice que el equipo ya existe
			$status = array();
			$status['status']= 304;
			$status['description'] = "El equipo ya existe";

			echo json_encode($status);

		}else{
			//Se crea el equipo

			$equipos[]=array("id"=>$params['id'], "nombre"=>$params['nombre'], "variables"=> array(), "frecuencia"=>null);

			//Se escribe en el archivo el contenido del arreglo con la adición.
			file_put_contents("devices.json", json_encode($equipos));

			$status = array(
				"status"=>201,
				"description" => "Equipo Creado");

			echo json_encode($status);
		}

	});

	//Ruta: /equipos/{id}
	$this->group('/{id}',function(){

		function getEquipos(){
			$texto = file_get_contents("devices.json");
			$equipos = json_decode($texto,true);
			return $equipos;
		}

		$this->map(['GET','DELETE'],'/',function($req,$res,$args){
			$texto = file_get_contents("devices.json");
			$equipos = json_decode($texto,true);
			$body = $req->getParsedBody();
			$method = $req->getMethod();

			$pos = $req->getHeaderLine("posicion");
			if($method == "GET"){
				//se devuelve el equipo con el id enviado
				echo json_encode($equipos[$pos]);

			}elseif($method == "DELETE"){
				//Si es una petición DELETE se borra el equipo correspondiente
				array_splice($equipos, $pos, 1);

				file_put_contents("devices.json", json_encode($equipos));

				$status = array();
				$status['status']= 200;
				$status['description'] = "Equipo eliminado.";

				echo json_encode($status);
			}


		});

		$this->group('/variables',function(){

			$this->get('/',function($req,$res,$args){
				$pos = $req->getHeaderLine("posicion");

			});

			//Se añaden nuevas variables
			$this->post('/',function($req,$res,$args){
				$equipos = getEquipos();
				$body = $req->getParsedBody();
				$pos = $req->getHeaderLine("posicion");

				if(!isset($body['nombre']) || !isset($body['pinesTexto'])){
					$res= $res->withStatus(400,"Se necesitan las variables nombre y pinesTexto");

				}else{

					$pines;
					try{
						$pines = explode(',',$body['pinesTexto']);
					}catch(Exception $e){
						$pines = $body['pinesTexto'];
					}

					$var = array(
						"id"=>md5(date()),
						"nombre"=>$body['nombre'],
						"pines"=>$pines,
						"pinesTexto"=>$body['pinesTexto']
					);

					$equipos[$pos]['variables'][]=$var;

					file_put_contents("devices.json", json_encode($equipos));

					$status = array();
					$status['status']= 201;
					$status['description'] = "Variable creada.";

					$res = $res->withStatus(201);

					echo json_encode($status);
				}

				return $res;
			});

			//Se modifican o borran las variables con id varid
			//Por implementar...
			$this->map(['PUT','DELETE'],'/{varid}',function($req,$res,$args){
				$equipos = getEquipos();

			});

		});

	})->add(function($req,$res,$next){

		//Se lee el path en el que estamos
		//p.ej /equipos/{id}

		$path = $_SERVER['REDIRECT_URL'];
		$paths = explode('/',$path);

		//Se lee el 4 parametro (id)
		$id = $paths[3];

		$existe = false;
		$pos;

		//Se leen los diferentes equipos
		//qu ya están registrados para mirar
		//si dicho equipo ya existe
		$texto = file_get_contents("devices.json");
		$equipos = json_decode($texto, true);

		//se mira si algún equipo coincide con el
		//id obtenido en la ruta
		for($i =0 ; $i<count($equipos); $i++){
				if($equipos[$i]['id'] === $id){
					$existe = true;
					$pos = $i;
				}
		}


		if($existe){
			//si existe entonces se añade un header
			//para que sea leido en las funciones definidas
			//arriba
			$req = $req->withAddedHeader("posicion",$pos);
			$res=$next($req,$res);

		}else{
			//Si no está definido el id entonces
			//no tiene caso que ejecutemos las
			//funciones que están adentro
			$status['status']= 404;
			$status['description'] = "Equipo no existe.";

			echo json_encode($status);
		}

		return $res;
	});

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

	//Se envía la información a UrbanEyes!
	//De momento, no se pudo encontrar una
	//manera en que esto funcionara por POST
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
