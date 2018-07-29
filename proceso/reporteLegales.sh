varLog=/home/callcenter/interfaz/proceso/logs/legalesLog.txt
comprobar=/home/callcenter/interfaz/proceso/comprobadorDeErrores.sh
from="sistemas@callmora.com.ar"
queries=/home/callcenter/interfaz/proceso/queries
legalesProcesar=/home/callcenter/interfaz/legales
procesados=/home/callcenter/interfaz/procesados
proceso=/home/callcenter/interfaz/proceso
legalesProcesados=/home/callcenter/interfaz/procesados/legales
errorMensaje=/home/callcenter/interfaz/proceso/archivos/mensajeErrorDirectorioVacio.txt
archivos=/home/callcenter/interfaz/proceso/archivos/
legales1=/home/callcenter/interfaz/legales/legales1
legales2=/home/callcenter/interfaz/legales/legales2
temporalLegales=/home/callcenter/interfaz/proceso/archivos/temporalLegales.txt
temporalLegales2=/home/callcenter/interfaz/proceso/archivos/temporalLegales2.txt
queryGenericaIn=$queries/genericaLegalesIn.sql
queryGenericaNotIn=$queries/genericaLegalesNotIn.sql
reportes=/home/callcenter/interfaz/proceso/reportes
archivoIn=$archivos/ultimaFechaGuardadaLegalesIn.txt
archivoNotIn=$archivos/ultimaFechaGuardadaLegalesNotIn.txt
eMail=/home/callcenter/interfaz/proceso/eMailReporte.sh


appendearArchivos()
{
	echo "$(cat $1)" > $temporalLegales
	echo "$(cat $2)" >>$temporalLegales
}

cargarRegistrosEnTabla()
{
	#Borro todo el contenido de la tabla
	mysql -se "DELETE FROM legales_registros_copy;"
	
	#Cargo la tabla con las cosas del mes
	for i in $(cat $temporalLegales)
	do
		numeroAInsertar=$(echo -e "'$i'")
		#mysql -se "INSERT INTO legales_registros_copy VALUES (NULL,$numeroAInsertar, (SELECT CURDATE() FROM DUAL));"
		mysql -se "INSERT INTO legales_registros_copy VALUES (NULL,$numeroAInsertar);"
	done

}

chequearCantidadDeArchivos()
{
	#ambos existen
	if [[ -f $legales1 && -f $legales2 ]]
	then
		appendearArchivos $legales1 $legales2
		echo -e "===============================\n$(date +%d/%m/%Y----%H.%M)\nSe generará el reporte, se encontraron los archivos legales1 y legales2.\n===============================">>$varLog
		
		#solo existe el 1
		else if [ -f $legales1 ]
		then
			echo -e "===============================\n$(date +%d/%m/%Y----%H.%M)\nSe generará el reporte pero solo se encontró el archivo legales1.\n===============================">>$varLog
			echo "$(cat $legales1 )" > $temporalLegales
			#solo existe el 2
			else if [ -f $legales2 ]
			then
					echo -e "===============================\n$(date +%d/%m/%Y----%H.%M)\nSe generará el reporte pero solo se encontró el archivo legales2.\n===============================">>$varLog
					echo "$(cat $legales2)" > $temporalLegales
					#no hay archivos
					else 
						echo -e "===============================\n$(date +%d/%m/%Y----%H.%M)\nNo se encontraron los archivos legales1 ni legales2, no se generará ningun reporte.\n===============================">>$varLog
						exit 1			
			fi
		fi

		
	fi


}

comprobarErrores () 

{


#El parametro 3 es el nombre del script ejecutado y el parametro 2 es la direccion donde se encuentra el log, parametro 1 es $?

if [ $1 -eq 0 ]
then
	mensajeLog="===============================\n$(date +%d/%m/%Y----%H.%M)\nSe ejecuto correctamente $3\n==============================="
	echo -e $mensajeLog >> $2
	
	
	else
	mensajeLog="===============================\n$(date +%d/%m/%Y----%H.%M)\nHubo un error al ejecutar $3\n==============================="
	echo -e "$mensajeLog" >> $2
	
	

fi

}


generarReporte ()
{
	query=$(cat $1)
	fecha=$(date +%d-%m-%Y-%H.%M)
	echo $fecha > $3

	#Esto te pide como primer parametro la query a ejecutar de la carpeta queries, y como segundo parametro el string para generar el reporte

	mysql -se "$query" > /home/callcenter/interfaz/proceso/reportes/$2$fecha.xls
	error=$?
	comprobarErrores $error $varLog $2
	
#	mandarMailLog $error $2 yari.taft@gmail.com
	echo -e "$mensajeLog" | mail -s "[CallMora]-Log_$2" -r "$from" esteban.kutifak@fravega.com.ar
#	echo -e "$mensajeLog" | mail -s "[CallMora]-Log_$2" -r "$from" yari.taft@gmail.com


}


filtrarMovidaDeArchivos()
{

	for i in $(ls $procesar)
	do

		if [ -f $i  ]
		then	
			mv $i $procesados
		fi
	done

}




generarReportes()
{
generarReporte $queryGenericaIn "Reporte_Legales_In" $archivoIn
generarReporte $queryGenericaNotIn "Reporte_Legales_NotIn" $archivoNotIn
}

mandarMail()
{

ultimaFechaIn=$(cat $archivoIn)
ultimaFechaNotIn=$(cat $archivoNotIn)


tar -czvf $reportes/$ultimaFechaNotIn-Reporte_Legales.tar.gz -C $reportes Reporte_Legales_In$ultimaFechaIn.xls Reporte_Legales_NotIn$ultimaFechaNotIn.xls

$eMail 4 Pablo.Canteli@fravega.com.ar "[CallMora]-Reporte_de_Legales" $reportes/$ultimaFechaNotIn-Reporte_Legales.tar.gz
}


moverYRenombrarArchivosOriginales()
{
	
	fecha=$(date +%d-%m-%Y-%H.%M)
	mv $legales1 $legalesProcesados/legales1_$fecha
	mv $legales2 $legalesProcesados/legales2_$fecha

}

verificarSiEsFechaDeLegales()
{
	dia=$(date +%D | cut -d "/" -f 2)

	if [ $dia -lt 5 ]
	then
		exit 1

	fi

}


verificarSiEsFechaDeLegales

chequearCantidadDeArchivos

cargarRegistrosEnTabla

generarReportes

mandarMail

moverYRenombrarArchivosOriginales




