#!/bin/bash
 
# Aquíonemos los datos de conexióSUARIO=usuario
USUARIO=fvgdatos
CLAVE=M0ntevide0
HOST=201.216.221.81
PUERTO=18024
lftp -e "set ftp:ssl-allow false" -p${PUERTO} -u ${USUARIO},${CLAVE} sftp://${HOST} << CMD
mput /home/callcenter/interfaz/data/* 
bye
CMD
