armarMensajeProcesoDistribucion ()
{

if [ $1 -eq 0 ]
	then
		echo -e "===========================\nEl cierre de mes se realizo correctamente\n===========================" >> /home/callcenter/interfaz/proceso/archivos/message.txt
	else
		echo -e "===========================\nERROR: en el cierre de mes\n===========================" >> /home/callcenter/interfaz/proceso/archivos/message.txt

fi

}


esFechaDeCierre ()

{

varLog=/home/callcenter/interfaz/proceso/logs/procesoDiarioMes.txt
comprobar=/home/callcenter/interfaz/proceso/comprobadorDeErrores.sh
mes=$(date +%D | cut -d "/" -f 1)
dia=$(date +%D | cut -d "/" -f 2)
anio=$(date +%D | cut -d "/" -f 3)
cerrarMes=/home/callcenter/interfaz/proceso/b.sh

#TENER EN CUENTA QUE SE LE DEBE SUMAR UN DIA AL DIA DE CIERRE, PORQUE SE PROCESA LA MADRUGADA DEL DIA POSTERIOR

if [[ $dia -eq 22 && $mes -eq 12 && $anio -eq 17 ]]
then

	$cerrarMes
	error=$?
	armarMensajeProcesoDistribucion $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi

if [[ $dia -eq 30 && $mes -eq 1 && $anio -eq 18 ]]
then
	$cerrarMes
	error=$?
	armarMensajeProcesoDistribucion $error
	$comprobar $error $varLog cierreDeMes.sh
	


fi

if [[ $dia -eq 27 && $mes -eq 2 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	armarMensajeProcesoDistribucion $error
	$comprobar $error $varLog cierreDeMes.sh
	


fi

if [[ $dia -eq 27 && $mes -eq 4 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	armarMensajeProcesoDistribucion $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi

if [[ $dia -eq 30 && $mes -eq 5 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	armarMensajeProcesoDistribucion $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi

if [[ $dia -eq 29 && $mes -eq 6 && $anio -eq 18 ]]
then
	$cerrarMes
	error=$?
	armarMensajeProcesoDistribucion $error
	$comprobar $error $varLog cierreDeMes.sh
	
fi

if [[ $dia -eq 29 && $mes -eq 7 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	armarMensajeProcesoDistribucion $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi

if [[ $dia -eq 30 && $mes -eq 8 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	armarMensajeProcesoDistribucion $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi

if [[ $dia -eq 28 && $mes -eq 9 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	armarMensajeProcesoDistribucion $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi

if [[ $dia -eq 30 && $mes -eq 10 && $anio -eq 18 ]]
then
	$cerrarMes
	error=$?
	armarMensajeProcesoDistribucion $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi

if [[ $dia -eq 29 && $mes -eq 11 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	armarMensajeProcesoDistribucion $error
	$comprobar $error $varLog cierreDeMes.sh
	
fi

if [[ $dia -eq 28 && $mes -eq 12 && $anio -eq 18 ]]
then

	$cerrarMes
	error=$?
	armarMensajeProcesoDistribucion $error
	$comprobar $error $varLog cierreDeMes.sh
	

fi

}


armarMensajeProcesoDiario ()

{

if [ $1 -eq 0 ]
	then
		echo -e "$(date +%d/%m/%Y-----%H:%M)\n===========================\nEl proceso diario se realizo correctamente\n===========================" > /home/callcenter/interfaz/proceso/archivos/message.txt
	else
		echo -e "$(date +%d/%m/%Y-----%H:%M)\n===========================\nERROR: en el proceso diario\n===========================" > /home/callcenter/interfaz/proceso/archivos/message.txt

fi

}


varLog=/home/callcenter/interfaz/proceso/logs/procesoDiarioMes.txt
comprobar=/home/callcenter/interfaz/proceso/comprobadorDeErrores.sh
mensaje=/home/callcenter/interfaz/proceso/archivos/message.txt
realizarProcesoDiario=/home/callcenter/interfaz/proceso/a.sh


#Se registra en el log lo que se realizó en el dia

$realizarProcesoDiario
error=$?
armarMensajeProcesoDiario $error
$comprobar $error $varLog procesoDiario.sh


#Se chequea si se trata de de una fecha de cierre de mes, en base a eso se decide si se ejecuta el script de cierre del mes o no

esFechaDeCierre


#Se envia por mail lo que se realizó a la gente encargada

cat $mensaje | mail -s "[Desarrollo] Proceso" "yari.taft@gmail.com" 
cat $mensaje | mail -s "[Desarrollo] Proceso" "esteban.kutifak@fravega.com.ar"