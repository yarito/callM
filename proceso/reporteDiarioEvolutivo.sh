from="sistemas@callmora.com.ar"
varLog=/home/callcenter/interfaz/proceso/logs/reportes_log.txt
eMail=/home/callcenter/interfaz/proceso/eMailReporte.sh
queries=/home/callcenter/interfaz/proceso/queries
archivos=/home/callcenter/interfaz/proceso/archivos
paso1=$queries/reporteDiarioEvolutivoPaso1.sql
paso2=$queries/reporteDiarioEvolutivoPaso2.sql
paso3=$queries/reporteDiarioEvolutivoPaso3PorGrupo.sql
paso4=$queries/reporteDiarioEvolutivoPaso4PorUsuario.sql
reportes=/home/callcenter/interfaz/proceso/reportes
archivo1=$archivos/ultimaFechaGuardada1.txt
archivo2=$archivos/ultimaFechaGuardada2.txt
ultimaFecha1=$(cat $archivo1)
ultimaFecha2=$(cat $archivo2)





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

mandarMailAResponsablesReporteEvolutivo ()
{
$eMail 2 Pablo.Canteli@fravega.com.ar "[CallMora]-Reporte_Diario_Evolutivo" $reportes/reporte_Diario_Evolutivo_Por_Grupo$ultimaFecha1.xls $reportes/reporte_Diario_Evolutivo_Por_Usuario$ultimaFecha2.xls
$eMail 2 Portal.Novedades@fravega.com.ar "[CallMora]-Reporte_Diario_Evolutivo" $reportes/reporte_Diario_Evolutivo_Por_Grupo$ultimaFecha1.xls $reportes/reporte_Diario_Evolutivo_Por_Usuario$ultimaFecha2.xls
#$eMail 2 Matias.Gentini@fravega.com.ar "[CallMora]-Reporte_Diario_Evolutivo" $reportes/reporte_Diario_Evolutivo_Por_Grupo$ultimaFecha1.xls $reportes/reporte_Diario_Evolutivo_Por_Usuario$ultimaFecha2.xls

}

ejecutarQuery $paso1
ejecutarQuery $paso2
generarReporte $paso3 reporte_Diario_Evolutivo_Por_Grupo $archivo1
generarReporte $paso4 reporte_Diario_Evolutivo_Por_Usuario $archivo2

mandarMailAResponsablesReporteEvolutivo