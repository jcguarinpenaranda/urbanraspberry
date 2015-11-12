#Urban Raspberry

Este es un repositorio que aloja el código de un webservice
que va a estar ejecutándose en una Raspberry.

Este código hace parte de un proyecto mayor, que apunta a
conectar una Raspberry con un Arduino para recopilar datos sensados
en este último y posteriormente enviarlos al API de UrbanEyes.

Cali, Colombia, 2015.

##Rutas Webservice

###/IP_RASPBERRY/urbanraspberry/datosensor
Un cliente que haga una petición POST podrá enviar datos
directamente a la plataforma de UrbanEyes

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


##Versiones
###v1.0.0
* Primer release.
* Se pueden agregar, leer y borrar equipos a la lista de equipos
* Se pueden agregar y leer variables de la lista de variables de cada equipo
* Se pueden leer las variabes remotas de UrbanEyes
* Se pueden reportar datos enviados por medio de sensores a UrbanEyes y saber si la recepción ha sido correcta o no.


##Licencia

The MIT License (MIT)

Copyright (c) 2014 Alf Eaton

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

##Créditos

Desarrollado por:

* Nicolás Escobar Cruz
* Juan David Orejuela
* Juan Camilo Guarín P - twitter: @jcguarinp
