varLog=/home/callcenter/interfaz/proceso/logs/asignacion.txt
comprobar=/home/callcenter/interfaz/proceso/comprobadorDeErrores.sh
mensaje=/home/callcenter/interfaz/proceso/archivos/messageAsignacion.txt
comprobar=/home/callcenter/interfaz/proceso/comprobadorDeErrores.sh
from="sistemas@callmora.com.ar"
queries=/home/callcenter/interfaz/proceso/queries
procesar=/home/callcenter/interfaz/procesar
query01=$queries/correrLimpiarUsuarios.sql
#query02=$queries/correrLimpiarUsuarios.sql
query1=$queries/asignacionPaso1ColumnaO.sql
query2=$queries/asignacionPaso2ColumnaP.sql
query3=$queries/asignacionPaso3ColumnaQ.sql
query4=$queries/asignacionPaso4ColumnaR.sql
query5=$queries/asignacionPaso5.sql
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

ejecutarQuery ()
{
query=$(cat $1)

mysql -se "$query"

}


armarMensajeProcesoAsignacion ()
{

if [ $1 -eq 0 ]
	then
		echo -e "$(date +%d/%m/%Y-----%H:%M)\n===========================\nLa asignacion del mes se realizo correctamente\n===========================" > $mensaje
	else
		echo -e "$(date +%d/%m/%Y-----%H:%M)\n===========================\nERROR: La asignacion del mes\n===========================" > $mensaje

fi

}

procesoAsignacion ()
{
	$ejecutarQuery $query01 $varLog
	$ejecutarQuery $query1 $varLog
	$ejecutarQuery $query2 $varLog
	$ejecutarQuery $query3	$varLog
	$ejecutarQuery $query4 $varLog
	$ejecutarQuery $query5 $varLog

	echo SE EJECUTARON TODAS LAS QUERIES CORRESPONDIENTES A LA ASIGNACION
	armarMensajeProcesoAsignacion $?
	mandarMailDestinatarios "Asignacion%$mensaje%1%esteban.kutifak@fravega.com.ar"
	$comprobar $? $varLog asignacion.sh

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

esFechaDeAsignacion ()
{
	#Pregunto si es fecha de asignacion ( el mismo dia que la fecha de cierre del banco)
	if [ $cantResultadoFechaAsignacion -eq 0 ] 
		then
			loguearCierreOAsignacionOInicio 6
		else
			procesoAsignacion 
			loguearCierreOAsignacionOInicio 7
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

#echo -e "echo -e $mensaje | mail -s "$subject" -r "$from" "$(echo $1 | cut -d "%" -f 3)""


for i in $(seq 4 $cantDestinatarios)
do
	echo -e "$(cat $mensaje)" | mail -s "$subject" -r "$from" "$(echo $1 | cut -d "%" -f $i)"
	
done

}


esFechaDeAsignacion 