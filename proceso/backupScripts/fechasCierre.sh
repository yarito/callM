varLog=/home/callcenter/interfaz/proceso/logs/procesoDiarioMes.txt
comprobar=/home/callcenter/interfaz/proceso/comprobadorDeErrores.sh
mes=$(date +%D | cut -d "/" -f 1)
dia=$(date +%D | cut -d "/" -f 2)
anio=$(date +%D | cut -d "/" -f 3)
cerrarMes=/home/callcenter/interfaz/proceso/cierreDeMes.sh
armarMensaje=/home/callcenter/interfaz/proceso/mailingProcesoDistribucion.sh 

#TENER EN CUENTA QUE SE LE DEBE SUMAR UN DIA AL DIA DE CIERRE, PORQUE SE PROCESA LA MADRUGADA DEL DIA POSTERIOR

if [[ $dia -eq 28 && $mes -eq 12 && $anio -eq 17 ]]
then

	$cerrarMes
	error=$?
	$armarMensaje $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi

if [[ $dia -eq 30 && $mes -eq 1 && $anio -eq 18 ]]
then
	$cerrarMes
	error=$?
	$armarMensaje $error
	$comprobar $error $varLog cierreDeMes.sh
	


fi

if [[ $dia -eq 27 && $mes -eq 2 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	$armarMensaje $error
	$comprobar $error $varLog cierreDeMes.sh
	


fi

if [[ $dia -eq 27 && $mes -eq 4 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	$armarMensaje $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi

if [[ $dia -eq 30 && $mes -eq 5 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	$armarMensaje $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi

if [[ $dia -eq 29 && $mes -eq 6 && $anio -eq 18 ]]
then
	$cerrarMes
	error=$?
	$armarMensaje $error
	$comprobar $error $varLog cierreDeMes.sh
	
fi

if [[ $dia -eq 29 && $mes -eq 7 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	$armarMensaje $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi

if [[ $dia -eq 30 && $mes -eq 8 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	$armarMensaje $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi

if [[ $dia -eq 28 && $mes -eq 9 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	$armarMensaje $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi

if [[ $dia -eq 30 && $mes -eq 10 && $anio -eq 18 ]]
then
	$cerrarMes
	error=$?
	$armarMensaje $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi

if [[ $dia -eq 29 && $mes -eq 11 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	$armarMensaje $error
	$comprobar $error $varLog cierreDeMes.sh
	
fi

if [[ $dia -eq 28 && $mes -eq 12 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	$armarMensaje $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi