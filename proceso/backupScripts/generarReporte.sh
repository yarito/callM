host="10.88.1.32"
user="callcenter"
pass="callcenter"
query=$(cat /home/callcenter/interfaz/proceso/queries/$1)
fecha=$(date +%d-%m-%Y-%H.%M)
comprobarErrores=/home/callcenter/interfaz/proceso/comprobadorDeErrores.sh
varLog=/home/callcenter/interfaz/proceso/logs/reportes_log.txt
mandarMailLog=/home/callcenter/interfaz/proceso/mailingReportes.sh

echo $fecha > $3

#Esto te pide como primer parametro la query a ejecutar de la carpeta queries, y como segundo parametro el string para generar el reporte


mysql -D $host -u $user -p $pass -se "$query" > /home/callcenter/interfaz/proceso/reportes/$2$fecha.xls
error=$?
$comprobarErrores $error $varLog $2
$mandarMailLog $error $2
