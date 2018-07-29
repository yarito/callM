queries=/home/callcenter/interfaz/proceso/queries
archivos=/home/callcenter/interfaz/proceso/archivos

paso1=$queries/reporte_Paso1Inicio.sql
paso2=$queries/reporte_Paso2Inicio.sql
paso3=$queries/reporte_Paso3Inicio.sql
eMail=/home/callcenter/interfaz/proceso/eMailReporte.sh
reportes=/home/callcenter/interfaz/proceso/reportes
archivo1=$archivos/ultimaFechaGuardadaInicio1.txt
plantilla=/home/callcenter/interfaz/proceso/archivos/mensajePlantillaReporteDeInicio.txt
from="sistemas@callmora.com.ar"


varLog=/home/callcenter/interfaz/proceso/logs/reportes_log.txt



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
	
	echo -e "$mensajeLog" | mail -s "[CallMora]-Log_$2" -r "$from" Oscar.Cohen@fravega.com.ar
	echo -e "$mensajeLog" | mail -s "[CallMora]-Log_$2" -r "$from" Portal.Novedades@fravega.com.ar

}

ejecutarQuery ()
{
query=$(cat $1)

mysql -se "$query"

}

ejecutarQuery $paso1
ejecutarQuery $paso2
generarReporte $paso3 Reporte_de_Inicio $archivo1

ultimaFecha1=$(cat $archivo1)

$eMail 1 Pablo.Peirano@fravega.com.ar "[CallMora]-Reporte_de_Inicio" $reportes/Reporte_de_Inicio$ultimaFecha1.xls
$eMail 1 Pablo.Canteli@fravega.com.ar "[CallMora]-Reporte_de_Inicio" $reportes/Reporte_de_Inicio$ultimaFecha1.xls
$eMail 1 Oscar.Cohen@fravega.com.ar "[CallMora]-Reporte_de_Inicio" $reportes/Reporte_de_Inicio$ultimaFecha1.xls
$eMail 1 Portal.Novedades@fravega.com.ar "[CallMora]-Reporte_de_Inicio" $reportes/Reporte_de_Inicio$ultimaFecha1.xls
#$eMail 1 Matias.Gentini@fravega.com.ar "[CallMora]-Reporte_de_Inicio" $reportes/Reporte_de_Inicio$ultimaFecha1.xls
