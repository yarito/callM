contador=$(cat /home/callcenter/interfaz/proceso/archivos/backup_contador.txt)
contador_file=/home/callcenter/interfaz/proceso/archivos/backup_contador.txt
varLog=/home/callcenter/interfaz/proceso/logs/backup_log.txt
comprobar=/home/callcenter/interfaz/proceso/comprobadorDeErrores.sh
fecha=$(date +%d/%m/%Y-----%H:%M)

#Comprimir tambien es para que se entienda mejor el log, pero no es ningun comando
tar -czvf htdocs.tar.gz /home/callcenter/htdocs
$comprobar $? $varLog comprimir_htdocs.tar.gz

tar -czvf procesar.tar.gz /home/callcenter/interfaz/procesar
$comprobar $? $varLog comprimir_procesar.tar.gz

tar -czvf proceso.tar.gz /home/callcenter/interfaz/proceso
$comprobar $? $varLog comprimir_proceso.tar.gz

tar -czvf parametros.tar.gz /home/callcenter/interfaz/parametros
$comprobar $? $varLog comprimir_parametros.tar.gz

tar -czvf prorrogas.tar.gz /home/callcenter/interfaz/prorrogas
$comprobar $? $varLog comprimir_prorrogas.tar.gz

if [ $contador -gt 6 ]
then
	contador=1
	echo $contador>$contador_file
	
else
	let contador=$contador+1
	echo $contador>$contador_file
fi

#numeroDeCarpeta=cat $contador_file
carpeta=/home/callcenter/backup2/$(cat $contador_file)

#Pongo mover para que se entienda mejor el log file, pero mover no es ningun script ni comando
mv htdocs.tar.gz $carpeta
$comprobar $? $varLog Mover_htdocs.tar.gz

mv procesar.tar.gz $carpeta
$comprobar $? $varLog Mover_procesar.tar.gz


mv proceso.tar.gz $carpeta
$comprobar $? $varLog Mover_proceso.tar.gz

mv parametros.tar.gz $carpeta
$comprobar $? $varLog Mover_parametros.tar.gz

mv prorrogas.tar.gz $carpeta
$comprobar $? $varLog Mover_prorrogas.tar.gz
