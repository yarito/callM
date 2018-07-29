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

	echo hola
	armarMensajeProcesoAsignacion $?
	mandarMailDestinatarios "Asignacion%$mensaje%1%esteban.kutifak@fravega.com.ar"
	$comprobar $? $varLog asignacion.sh

}

esFechaDeCierre ()

{

mes=$(date +%D | cut -d "/" -f 1)
dia=$(date +%D | cut -d "/" -f 2)
anio=$(date +%D | cut -d "/" -f 3)


#FECHAS DE CIERRE EXACTAS SIN SUMAR NADA

if [[ $dia -eq 29 && $mes -eq 1 && $anio -eq 18  ]]
then
	
	procesoAsignacion 

fi

if [[ $dia -eq 26 && $mes -eq 2 && $anio -eq 18  ]]
then
	
	procesoAsignacion 


fi

if [[ $dia -eq 27 && $mes -eq 3 && $anio -eq 18  ]]
then
	
	procesoAsignacion
fi

if [[ $dia -eq 26 && $mes -eq 4 && $anio -eq 18  ]]
then
	
	procesoAsignacion
fi

if [[ $dia -eq 29 && $mes -eq 5 && $anio -eq 18  ]]
then
	
	procesoAsignacion
fi

if [[ $dia -eq 28 && $mes -eq 6 && $anio -eq 18  ]]
then
	
	procesoAsignacion
fi

if [[ $dia -eq 28 && $mes -eq 7 && $anio -eq 18  ]]
then
	
	procesoAsignacion

fi

if [[ $dia -eq 29 && $mes -eq 8 && $anio -eq 18  ]]
then
	
	procesoAsignacion

fi

if [[ $dia -eq 27 && $mes -eq 9 && $anio -eq 18  ]]
then
	
	procesoAsignacion

fi

if [[ $dia -eq 29 && $mes -eq 10 && $anio -eq 18  ]]
then
	
	procesoAsignacion
fi

if [[ $dia -eq 28 && $mes -eq 11 && $anio -eq 18  ]]
then
	
	procesoAsignacion
fi

if [[ $dia -eq 27 && $mes -eq 12 && $anio -eq 18  ]]
then
	
	procesoAsignacion

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


esFechaDeCierre