comprobar=/home/callcenter/interfaz/proceso/comprobadorDeErrores.sh
varLog=/home/callcenter/interfaz/proceso/logs/php_log.txt

cd  /home/callcenter/htdocs/phpinclude


/usr/bin/php distribucion.php
$comprobar $? $varLog distribucion.php
