varLog=/home/callcenter/interfaz/proceso/logs/asignacion.txt
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

ejecutarQuery ()
{
query=$(cat $1)

mysql -se "$query"

}

hacer_redistribucion()
{
	cd /home/callcenter/htdocs/phpinclude
	/usr/bin/php redistribucion.php

}


query1=$( cat $queries/asignacionPaso3ColumnaQ.sql | sed -e 's/USUARIOPERFILES_TMP/USUARIOPERFILES/g')

query2=$( cat $queries/asignacionPaso4ColumnaR.sql | sed -e 's/USUARIOPERFILES_TMP/USUARIOPERFILES/g')


mysql -se "$query1"
mysql -se "$query2"

hacer_redistribucion



