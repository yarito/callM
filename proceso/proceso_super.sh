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
queryFechaBaseCount=$(cat $queries/fechaBaseCount.sql)
queryCierreBase=$(cat $queries/fechaCierreBase.sql)
fechaHoy=$(date +%Y-%m-%d)
resultadoFlagNecesarioParaEjecutarCierre=$(mysql -se "$queryCierreBase $fechaHoy',interval -1 day) limit 1" | cut -d "	" -f1)
cantResultadoFechaCierre=$(mysql -se "$queryFechaBaseCount $fechaHoy',interval -1 day) limit 1")
cantResultadoFechaInicio=$(mysql -se "$queryFechaBaseCount $fechaHoy',interval -2 day) limit 1")
cantResultadoFechaAsignacion=$(mysql -se "$queryFechaBaseCount $fechaHoy',interval 0 day) limit 1")
queryFlagDistribucionSQL=$queries/obtenerFlagDistribucion.sql
queryActualizarFlagDistribucion=$queries/actualizarFlagDistribucion.sql
fecha=$(date +%d-%m-%Y-%H.%M)
varLogCierreAsigInicio=/home/callcenter/interfaz/proceso/logs/cierreInicioAsignacionFlag.txt

obtenerFlagDistribucion()
{
	flagDistribucionAux=$(cat $queryFlagDistribucionSQL)
	flagVigenteEnDistribucion=$(mysql -se "$flagDistribucionAux")
}

actualizarFlagDistribucion()
{
	queryBaseFlag=$(cat $queryActualizarFlagDistribucion)
	if [ $1 -eq 1 ]
		then
			mysql -se "$queryBaseFlag 0"
			echo -e "=============================\n$fecha\nSe modifico el flag de 1 a 0\n=============================" >>$varLogCierreAsigInicio
		else
			mysql -se "$queryBaseFlag 1"
			echo -e  "=============================\n$fecha\nSe modifico el flag de 0 a 1\n=============================" >>$varLogCierreAsigInicio

	fi
}

loguearCierreOAsignacionOInicio()
{
	fecha=$(date +%d-%m-%Y-%H.%M)
	case $1 in
		1)
			echo -e "=============================\n$fecha\nNo es fecha de inicio, no se realizaron actividades de inicio.\n=============================" >>$varLogCierreAsigInicio
		;;
		2)
			echo -e "=============================\n$fecha\nSe realizaron las actividades de inicio.\n=============================" >>$varLogCierreAsigInicio
		;;
		3)
			echo -e "=============================\n$fecha\nNo es fecha de cierre, no se realizaron actividades de cierre.\n=============================" >>$varLogCierreAsigInicio
		;;
		4)
			echo -e "=============================\n$fecha\nSe han ejecutado las actividades de cierre y se ha modificado el valor del flag.\n=============================" >>$varLogCierreAsigInicio
		;;
		5)
			echo -e "=============================\n$fecha\nNo se ejecutaron actividades de cierre porque si bien la fecha es de cierre, el flag Necesario y el actual no coinciden.\n=============================" >>$varLogCierreAsigInicio
		;;
		6)
			echo -e "=============================\n$fecha\nNo es fecha de asignacion, no se ejecutaron actividades de asignación.\n=============================" >>$varLogCierreAsigInicio
		;;
		7)
			echo -e "=============================\n$fecha\nSe han ejecutado actividades de asignacion.\n=============================" >>$varLogCierreAsigInicio
		;;
		*)
			echo -e "=============================\n$fecha\nERROR: No recibí ningun numero como parametro.\n=============================" >>$varLogCierreAsigInicio
		;;
	esac
}

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
	#Pregunto si es fecha de ejecución de cierre ( un día posterior a la fecha de cierre del banco)
	if [ $cantResultadoFechaCierre -eq 0 ] 
		then
			loguearCierreOAsignacionOInicio 3 
		else
			obtenerFlagDistribucion
			if [ $resultadoFlagNecesarioParaEjecutarCierre -eq $flagVigenteEnDistribucion ]
				then 
					actividadesCierre
					loguearCierreOAsignacionOInicio 4
					actualizarFlagDistribucion $flagVigenteEnDistribucion
				else
					loguearCierreOAsignacionOInicio 5
			fi
	fi
}

actividadesDeInicio ()
{

	$realizarReporteDeInicio
}

esFechaDeInicio ()
{
	#Pregunto si es fecha de inicio ( dos días posteriores a la fecha de cierre del banco)
	if [ $cantResultadoFechaInicio -eq 0 ] 
		then
			loguearCierreOAsignacionOInicio 1
		else
			actividadesDeInicio 
			loguearCierreOAsignacionOInicio 2
	fi
}

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