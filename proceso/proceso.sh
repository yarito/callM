cd /home/callcenter/interfaz/proceso
/usr/bin/php totales.php
cd /home/callcenter/interfaz/procesar
mv a* ../auxiliar/
mv * ../procesados
mv ../auxiliar/* ./
gunzip *.gz
cd ../proceso
/usr/bin/php interfaz.php
# Call Externo Gering discontinuado
# /usr/bin/php ccext_gering_out.php
/usr/bin/php ccext_contactogarantido_in.php
/usr/bin/php ccext_contactogarantido_out.php
/usr/bin/php ccext_contactogarantido_out_send.php

cd  /home/callcenter/htdocs/phpinclude

#CARGAR TABLA DE PARAMETROS DE FECHA DE CIERRE EFICIENCIA

#EN DISTRIBUCION.PHP TENGO QUE CONSULTAR ESTA TABLA Y METER UN IF PARA QUE CORRA O NO EL PROCESO

# D E S C O M E N T A R  A LA NOCHE ANTERIOR DE LA DISTRIBUCION

# COMIENZA A EJECUTARSE A LAS 4am APROX

# /usr/bin/php distribucion.php

# C O M E N T A R   AL DIA SIGUIENTE   (SHELL DE LINUX)


#echo -e "=====\n$(date +%d/%m/%Y-----%H:%M) Proceso Original Diario Ejecutado\n=====" >> /home/callcenter/interfaz/proceso/logs/provisorio.txt
