archivos=/home/callcenter/interfaz/proceso/archivos
queries=/home/callcenter/interfaz/proceso/queries
reportes=/home/callcenter/interfaz/proceso/reportes
from="sistemas@callmora.com.ar"
varLog=/home/callcenter/interfaz/proceso/logs/reportes_log.txt
paso1=$queries/reporte1_CierrePorUsuario.sql
paso2=$queries/reporte2_CierrePorGrupo.sql
paso3=$queries/reporte3_CierrePorSucursal.sql
eMail=/home/callcenter/interfaz/proceso/eMailReporte.sh
archivo1=$archivos/ultimaFechaGuardadaCierre1.txt
archivo2=$archivos/ultimaFechaGuardadaCierre2.txt
archivo3=$archivos/ultimaFechaGuardadaCierre3.txt




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

mandarMailAResponsablesReporteLog ()
{
	echo -e "$mensajeLog" | mail -s "[CallMora]-Log_$1" -r "$from" Oscar.Cohen@fravega.com.ar
	echo -e "$mensajeLog" | mail -s "[CallMora]-Log_$1" -r "$from" Portal.Novedades@fravega.com.ar

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
	
	mandarMailAResponsablesReporteLog $2
}

ejecutarQuery ()
{
query=$(cat $1)

mysql -se "$query"

}

mandarMailAResponsablesReporte ()
{
$eMail 3 Pablo.Peirano@fravega.com.ar "[CallMora]-Reporte_de_Cierre" $reportes/Reporte_Cierre_Por_Usuario$ultimaFecha1.xls $reportes/Reporte_Cierre_Por_Grupo$ultimaFecha2.xls $reportes/Reporte_Cierre_Por_Sucursal$ultimaFecha3.xls

$eMail 3 Pablo.Canteli@fravega.com.ar "[CallMora]-Reporte_de_Cierre" $reportes/Reporte_Cierre_Por_Usuario$ultimaFecha1.xls $reportes/Reporte_Cierre_Por_Grupo$ultimaFecha2.xls $reportes/Reporte_Cierre_Por_Sucursal$ultimaFecha3.xls
$eMail 3 Oscar.Cohen@fravega.com.ar "[CallMora]-Reporte_de_Cierre" $reportes/Reporte_Cierre_Por_Usuario$ultimaFecha1.xls $reportes/Reporte_Cierre_Por_Grupo$ultimaFecha2.xls $reportes/Reporte_Cierre_Por_Sucursal$ultimaFecha3.xls
$eMail 3 Portal.Novedades@fravega.com.ar "[CallMora]-Reporte_de_Cierre" $reportes/Reporte_Cierre_Por_Usuario$ultimaFecha1.xls $reportes/Reporte_Cierre_Por_Grupo$ultimaFecha2.xls $reportes/Reporte_Cierre_Por_Sucursal$ultimaFecha3.xls
#$eMail 3 Matias.Gentini@fravega.com.ar "[CallMora]-Reporte_de_Cierre" $reportes/Reporte_Cierre_Por_Usuario$ultimaFecha1.xls $reportes/Reporte_Cierre_Por_Grupo$ultimaFecha2.xls $reportes/Reporte_Cierre_Por_Sucursal$ultimaFecha3.xls

}

generarReporte $paso1 Reporte_Cierre_Por_Usuario $archivo1 
generarReporte $paso2 Reporte_Cierre_Por_Grupo $archivo2
generarReporte $paso3 Reporte_Cierre_Por_Sucursal $archivo3

ultimaFecha1=$(cat $archivo1)
ultimaFecha2=$(cat $archivo2)
ultimaFecha3=$(cat $archivo3)


mandarMailAResponsablesReporte