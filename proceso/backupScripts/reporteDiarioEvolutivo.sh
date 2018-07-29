generarReporte=/home/callcenter/interfaz/proceso/generarReporte.sh
paso1=/home/callcenter/interfaz/proceso/queries/reporteDiarioEvolutivoPaso1.sql
paso2=/home/callcenter/interfaz/proceso/queries/reporteDiarioEvolutivoPaso2.sql
paso3=reporteDiarioEvolutivoPaso3PorGrupo.sql
paso4=reporteDiarioEvolutivoPaso4PorUsuario.sql
ejecutarQuery=/home/callcenter/interfaz/proceso/ejecutarQuery.sh
eMail=/home/callcenter/interfaz/proceso/eMail.sh
/home/callcenter/interfaz/proceso/plantillaMensaje.sh > mensajePlantillaReporteEvolutivo.txt
mensaje=$(cat /home/callcenter/interfaz/proceso/mensajePlantillaReporteEvolutivo.txt)
reportes=/home/callcenter/interfaz/proceso/reportes
archivo1=/home/callcenter/interfaz/proceso/ultimaFechaGuardada1.txt
archivo2=/home/callcenter/interfaz/proceso/ultimaFechaGuardada2.txt



$ejecutarQuery $paso1
$ejecutarQuery $paso2
$generarReporte $paso3 reporteDiarioEvolutivoPorGrupo $archivo1
$generarReporte $paso4 reporteDiarioEvolutivoPorUsuario $archivo2

ultimaFecha1=$(cat $archivo1)
ultimaFecha2=$(cat $archivo2)


$eMail esteban.kutifak@fravega.com.ar "[Desarrollo] Reporte" "$mensaje" $reportes/reporteDiarioEvolutivoPorGrupo$ultimaFecha1.xls $reportes/reporteDiarioEvolutivoPorUsuario$ultimaFecha2.xls