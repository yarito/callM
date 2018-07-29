varLog=/home/callcenter/interfaz/proceso/logs/procesoDiarioMes.txt
comprobar=/home/callcenter/interfaz/proceso/comprobadorDeErrores.sh
mensaje=/home/callcenter/interfaz/proceso/archivos/messageProcesoDiario.txt
realizarProcesoDiario=/home/callcenter/interfaz/proceso/proceso.sh
realizarReporteDeCierre=/home/callcenter/interfaz/proceso/reporteDeCierre.sh
realizarReporteDiarioEvolutivo=/home/callcenter/interfaz/proceso/reporteDiarioEvolutivo.sh
cerrarMes=/home/callcenter/interfaz/proceso/cierreDeMes.sh
comprobar=/home/callcenter/interfaz/proceso/comprobadorDeErrores.sh
realizarReporteDeInicio=/home/callcenter/interfaz/proceso/reporteDeInicio.sh
from="sistemas@callmora.com.ar"
queries=/home/callcenter/interfaz/proceso/queries
procesar=/home/callcenter/interfaz/procesar
errorMensaje=/home/callcenter/interfaz/proceso/archivos/mensajeErrorDirectorioVacio.txt
ejecutarQuery=/home/callcenter/interfaz/proceso/run_n_log_query.sh

#ejecutarQuery ()
#{
#query=$(cat $1)
#
#mysql -se "$query"
#
#}

registrarContadorDistribucionEnLog ()
{

	resultadoContador=$(ejecutarQuery $queries/contadorDeDistribucion.sql)
	echo -e "===========================\n$(date +%d/%m/%Y----%H:%M)\nSe distribuyeron $resultadoContador registros.\n===========================" >>$varLog

}

backupPreDistribucion()
{
	$ejecutarQuery $queries/backupDistribucion.sql $varLog
	$ejecutarQuery $queries/backupUsuariosPerfiles.sql $varLog
	$ejecutarQuery $queries/backupUsuariosPerfilesTMP.sql $varLog
}

backupPostDistribucion()
{
backupPreDistribucion
$ejecutarQuery $queries/backupCartera.sql $varLog

}



armarMensajeProcesoDistribucion ()
{

if [ $1 -eq 0 ]
	then
		echo -e "===========================\nEl cierre de mes se realizo correctamente\n===========================" >> /home/callcenter/interfaz/proceso/archivos/messageProcesoDiario.txt
	else
		echo -e "===========================\nERROR: en el cierre de mes\n===========================" >> /home/callcenter/interfaz/proceso/archivos/messageProcesoDiario.txt

fi

}

actividadesCierre ()
{
	backupPreDistribucion
	$cerrarMes
	registrarContadorDistribucionEnLog
	backupPostDistribucion
	error=$?
	armarMensajeProcesoDistribucion $error
	$comprobar $error $varLog cierreDeMes.sh
	$realizarReporteDeCierre
}

esFechaDeCierre ()

{

mes=$(date +%D | cut -d "/" -f 1)
dia=$(date +%D | cut -d "/" -f 2)
anio=$(date +%D | cut -d "/" -f 3)
flagDistribucion=$(cat /home/callcenter/interfaz/proceso/archivos/flagDistribucion.txt)
flagDistribucionArchivo=/home/callcenter/interfaz/proceso/archivos/flagDistribucion.txt

#------------------------------------------------
#             FECHA DE CIERRE + 1
#------------------------------------------------

#TENER EN CUENTA QUE SE LE DEBE SUMAR UN DIA AL CIERRE, PORQUE SE PROCESA DESPUES DE MEDIANOCHE DEL DIA DE CIERRE DE COBRANZAS


if [[ $dia -eq 30 && $mes -eq 1 && $anio -eq 18 && $flagDistribucion -eq 0 ]]
then
	echo 1 > $flagDistribucionArchivo
	actividadesCierre 

fi

if [[ $dia -eq 27 && $mes -eq 2 && $anio -eq 18 && $flagDistribucion -eq 1 ]]
then
	echo 0 > $flagDistribucionArchivo
	actividadesCierre 


fi

if [[ $dia -eq 29 && $mes -eq 3 && $anio -eq 18 && $flagDistribucion -eq 0 ]]
then
	echo 1 > $flagDistribucionArchivo
	actividadesCierre
fi

if [[ $dia -eq 28 && $mes -eq 4 && $anio -eq 18 && $flagDistribucion -eq 1 ]]
then
	echo 0 > $flagDistribucionArchivo
	actividadesCierre
fi

if [[ $dia -eq 30 && $mes -eq 5 && $anio -eq 18 && $flagDistribucion -eq 0 ]]
then
	echo 1 > $flagDistribucionArchivo
	actividadesCierre
fi

if [[ $dia -eq 29 && $mes -eq 6 && $anio -eq 18 && $flagDistribucion -eq 1 ]]
then
	echo 0 > $flagDistribucionArchivo
	actividadesCierre
fi

if [[ $dia -eq 31 && $mes -eq 7 && $anio -eq 18 && $flagDistribucion -eq 0 ]]
then
	echo 1 > $flagDistribucionArchivo
	actividadesCierre

fi

if [[ $dia -eq 30 && $mes -eq 8 && $anio -eq 18 && $flagDistribucion -eq 1 ]]
then
	echo 0 > $flagDistribucionArchivo
	actividadesCierre

fi

if [[ $dia -eq 28 && $mes -eq 9 && $anio -eq 18 && $flagDistribucion -eq 0 ]]
then
	echo 1 > $flagDistribucionArchivo
	actividadesCierre

fi

if [[ $dia -eq 30 && $mes -eq 10 && $anio -eq 18 && $flagDistribucion -eq 1 ]]
then
	echo 0 > $flagDistribucionArchivo
	actividadesCierre
fi

if [[ $dia -eq 29 && $mes -eq 11 && $anio -eq 18 && $flagDistribucion -eq 0 ]]
then
	echo 1 > $flagDistribucionArchivo
	actividadesCierre
fi

if [[ $dia -eq 28 && $mes -eq 12 && $anio -eq 18 && $flagDistribucion -eq 1 ]]
then
	echo 0 > $flagDistribucionArchivo
	actividadesCierre

fi

}

#===========================
actividadesDeInicio ()
{

	$realizarReporteDeInicio
}

esFechaDeInicio ()

{

mes=$(date +%D | cut -d "/" -f 1)
dia=$(date +%D | cut -d "/" -f 2)
anio=$(date +%D | cut -d "/" -f 3)

#------------------------------------------------
#             FECHA DE CIERRE + 2
#------------------------------------------------

#TENER EN CUENTA QUE SE LE DEBE SUMAR DOS DIAS AL DIA DE CIERRE, PORQUE SE PROCESA DESPUES DE MEDIANOCHE DEL DIA SIGUIENTE AL CIERRE DE COBRANZAS

if [[ $dia -eq 31 && $mes -eq 1 && $anio -eq 18 ]]
then
	actividadesDeInicio 

fi

if [[ $dia -eq 28 && $mes -eq 2 && $anio -eq 18 ]]
then
	actividadesDeInicio 


fi

if [[ $dia -eq 29 && $mes -eq 3 && $anio -eq 18 ]]
then
	actividadesDeInicio 
fi

if [[ $dia -eq 29 && $mes -eq 4 && $anio -eq 18 ]]
then
	actividadesDeInicio 
fi

if [[ $dia -eq 31 && $mes -eq 5 && $anio -eq 18 ]]
then
	actividadesDeInicio 
fi

if [[ $dia -eq 30 && $mes -eq 6 && $anio -eq 18 ]]
then
	actividadesDeInicio 
fi

if [[ $dia -eq 1 && $mes -eq 8 && $anio -eq 18 ]]
then
	actividadesDeInicio 

fi

if [[ $dia -eq 31 && $mes -eq 8 && $anio -eq 18 ]]
then
	actividadesDeInicio 

fi

if [[ $dia -eq 29 && $mes -eq 9 && $anio -eq 18 ]]
then
	actividadesDeInicio 

fi

if [[ $dia -eq 31 && $mes -eq 10 && $anio -eq 18 ]]
then
	actividadesDeInicio 
fi

if [[ $dia -eq 30 && $mes -eq 11 && $anio -eq 18 ]]
then
	actividadesDeInicio 
fi

if [[ $dia -eq 29 && $mes -eq 12 && $anio -eq 18 ]]
then
	actividadesDeInicio 

fi

}


#===========================


armarMensajeProcesoDiario ()

{

if [ $1 -eq 0 ]
	then
		echo -e "$(date +%d/%m/%Y-----%H:%M)\n===========================\nEl proceso diario se realizo correctamente\n===========================" > /home/callcenter/interfaz/proceso/archivos/messageProcesoDiario.txt
	else
		echo -e "$(date +%d/%m/%Y-----%H:%M)\n===========================\nERROR: en el proceso diario\n===========================" > /home/callcenter/interfaz/proceso/archivos/messageProcesoDiario.txt

fi

}

mandarMailDestinatarios ()
{
#parametro 1=subject, 2=mensaje,3 cantidad De mails destinatarios, 4 en adelante lista de mails
#en realidad es un solo parametro que es un string y de el se extraen los parametros porque sino hay problemas
#ya que el mensaje tiene espacios y los espacios hace creer a linux que son muchos parametros por mas
#que el mismo se encuentre entre comillas

subject="$(echo $1 | cut -d "%" -f 1)"
destinatario="$(echo $1 | cut -d "%" -f 4)"
cantDestinatarios=$(echo $1 | cut -d "%" -f 3)
let cantDestinatarios=cantDestinatarios+3
mensajeMail=$(cat $(echo $1 | cut -d "%" -f 2))

for i in $(seq 4 $cantDestinatarios)
do
	echo $1 | cut -d "%" -f $i
	echo -e "$mensajeMail" | mail -s "$subject" -r "$from" "$(echo $1 | cut -d "%" -f $i)"
	
done

}



if [ "$(ls -A $procesar)" ]
then

	#Se registra en el log lo que se realizó en el dia

	$realizarProcesoDiario
	error=$?
	armarMensajeProcesoDiario $error
	$comprobar $error $varLog procesoDiario.sh
	$realizarReporteDiarioEvolutivo

	#Se chequea si se trata de una fecha de cierre de mes, en base a eso se decide si se ejecuta el script de cierre del mes o no, se checkea por fecha y por flag
	#como medida de seguridad extra en caso que se quiera por x motivo ejecutar dos veces en un mismo mes el script.

	esFechaDeCierre
	esFechaDeInicio 
	
	#poner %numerodemails%mail1%mail2etc

	mandarMailDestinatarios "[CallMora]-Proceso%$mensaje%4%esteban.kutifak@fravega.com.ar%Portal.Novedades@fravega.com.ar%Oscar.Cohen@fravega.com.ar%Pablo.Canteli@fravega.com.ar"
	

	#Se envia por mail lo que se realizó a la gente encargada
else
	echo -e "====================\n$(date +%d/%m/%Y-----%H.%M)\n\nError al Ejecutar Proceso Diario\n\nDetalle: No se encontraron archivos en el directorio Procesar.\n\nDistribucion no se ejecutara aunque corresponda por fecha, ya que no se realizo el proceso diario.\n====================">$errorMensaje
	
	mandarMailDestinatarios "[CallMora]-Proceso%$errorMensaje%4%esteban.kutifak@fravega.com.ar%Portal.Novedades@fravega.com.ar%Oscar.Cohen@fravega.com.ar%Pablo.Canteli@fravega.com.ar"
	$comprobar 1 $varLog Proceso_Diario_No_Hay_Archivos_En_Procesar

fi