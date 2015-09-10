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
	"value":123,
	"variable":"temperatura"
}
```

###/IP_RASPBERRY/urbanraspberry/variables
En /variables un cliente que haga
un GET obtendrá los datos de los pines
en los cuales tendrá conectado uno
o varios sensores, y el nombre de
la variable que corresponde a cada
uno.

El formato propuesto para recibir es:

```json
{
	"variable":"temperatura",
	"pines":["a0","a1"],
	"frecuencia":60 //en segundos
}
```
##Licencia

The MIT License (MIT)

Copyright (c) 2014 Alf Eaton

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

##Créditos
Nicolás Escobar Cruz
Juan David Orejuela
Juan Camilo Guarín P - @jcguarinp