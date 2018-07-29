comprobar=/home/callcenter/interfaz/proceso/comprobadorDeErrores.sh
varLog=/home/callcenter/interfaz/proceso/logs/php_log.txt

cd /home/callcenter/interfaz/proceso


/usr/bin/php totales.php
comprobar $? $varLog totales.php



cd /home/callcenter/interfaz/procesar
mv a* ../auxiliar/
mv * ../procesados
mv ../auxiliar/* ./
gunzip *.gz
cd ../proceso

/usr/bin/php interfaz.php
comprobar $? $varLog interfaz.php


# /usr/bin/php ccext_gering_out.php
# comprobar $? $varLog ccext_gering_out.php

/usr/bin/php ccext_contactogarantido_in.php
comprobar $? $varLog ccext_contactogarantido_in.php

/usr/bin/php ccext_contactogarantido_out.php
comprobar $? $varLog ccext_contactogarantido_out.php


# /usr/bin/php ccext_contactogarantido_out_send.php
# comprobar $? $varLog ccext_contactogarantido_out_send.php

# cd /home/callcenter/

# /usr/bin/php distribucion.php
# comprobar $? $varLog distribucion.php

#SI SE VA A DESCOMENTAR ccext_gering_out.php o ccext_contactogarantido_out_send.php  tambien hacerlo con los echo mas abajo para que quede su corrida en el LOG
