#Urban Raspberry

Este es un repositorio que aloja el código de un webservice
que va a estar ejecutándose en una Raspberry, que la va a hacer actuar como punto
de paso de información proveniente de una variedad de sensores hacia
la plataforma de IoT UrbanEyes, de la Universidad Autónoma de Occidente.

Debe tenerse en cuenta que se trabaja sobre la siguiente arquitectura:

![Muestra de la arquitectura del proyecto](/img/arquitectura.png?raw=true)

Figura 1. Arquitectura del proyecto para enviar datos a UrbanEyes

El presente repositorio corresponde al código que iría en donde en la Figura 1 se
tiene Raspberry 1,2,3,4.


Cali, Colombia, 2015.

##Instalación

Para correr el proyecto se debe descargar el presente código e insertarlo
en un servidor web, como por ejemplo Apache. Paso a paso, en Linux, se tendría lo
siguiente:

1. Instalar un servidor apache para la versión de Linux correspondiente
2. Instalar php (>v5.5)
3. Descargar el presente repositorio como zip
4. Descomprimir el zip
5. Copiar la carpeta en /var/www/ (centos) ó /var/www/html (ubuntu)

##Rutas Webservice

Para utilizar todas las siguientes rutas del presente servicio web, se debe
tener en cuenta la IP de la Raspberry (o computador de pruebas) donde se
está ejecutando el servicio.

###/IP_RASPBERRY/urbanraspberry/variables/
Vía: GET
Params: ninguno

Obtiene las variables que están activas en UrbanEyes

###/IP_RASPBERRY/urbanraspberry/datosensor
Via: POST
Params:

El formato que debe enviar el cliente
será:

```json
{
	"variable_id":3,
	"value":123
}
```

Ó utilizando los parámetros:

```
value=123&variable_id=3
```

Un cliente que haga una petición POST podrá enviar datos
directamente a la plataforma de UrbanEyes.

Nótese que se hace referencia a un id de una variable. Este id de variable puede ser cualquiera de los siguientes:

| id | nombre               |
|----|----------------------|
| 1  | robo                 |
| 2  | vial                 |
| 3  | radiacion_solar      |
| 4  | temperatura          |
| 5  | humedad              |
| 6  | NO2                  |
| 7  | CO2                  |
| 8  | nivel_de_sonido      |
| 9  | presion_atmosferica  |
| 10 | velocidad_del_viento |
| 11 | direccion_del_viento |
| 12 | homicidio            |

###/IP_RASPBERRY/urbanraspberry/equipos

#### GET
Params: ninguno

Obtener toda la lista de equipos conectados a la
Raspberry.

El formato de recepción es el siguiente:

```json
[{"id":"ca",
	"nombre":"Juan",
	"variables":
	[{
		"nombre":"temperatura",
		"pines":["a0","a1"]
	}],
	"frecuencia":60
}]
```

NOTA: La frecuencia se mide en segundos. Es la
frecuencia con la cual se deben enviar los datos
sensados a la Raspberry. Esto, de momento, no se
encuentra implementado.

#### POST
Params:
- id: Un identificador que caracteriza al dispositivo que se quiere agregar
- nombre: El nombre que va a tener el dispositivo

Permite agregar un equipo.

###/IP_RASPBERRY/urbanraspberry/equipos/:id
El id es un dato que hace parte de la ruta y que debe corresponder
a uno de los identificadores existentes de los equipos

#### GET
Params: ninguno

Permite obtener toda la información del equipo identificado por id

#### DELETE
Params: ninguno

Permite borrar el equipo identificado por id


###/IP_RASPBERRY/urbanraspberry/equipos/:id/variables

#### POST
Params:
- nombre: El nombre de la variable
- pinesTexto: Una cadena con los pines del dispositivo que leen dicha variable. Por ejemplo, para un sensor que obtenga datos de dos pines, pinesTexto podría ser: a0,a1

Agrega una variable a un equipo existente, es decir, le dice a un equipo existente que va a sensar una nueva variable.

##Versiones
###v1.0.0
* Primer release.
* Se pueden agregar, leer y borrar equipos a la lista de equipos
* Se pueden agregar y leer variables de la lista de variables de cada equipo
* Se pueden leer las variabes remotas de UrbanEyes
* Se pueden reportar datos enviados por medio de sensores a UrbanEyes y saber si la recepción ha sido correcta o no.

##Créditos

Desarrollado por:

* Nicolás Escobar Cruz
* Juan David Orejuela
* Juan Camilo Guarín P - twitter: @jcguarinp
