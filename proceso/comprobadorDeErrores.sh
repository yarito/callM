#El parametro 3 es el nombre del script ejecutado y el parametro 2 es la direccion donde se encuentra el log, parametro 1 es $?

if [ $1 -eq 0 ]
then

	echo -e "===============================\n#$(date +%d/%m/%Y----%H:%M)\nSe ejecuto correctamente $3\n===============================">> $2
	else
	echo -e "===============================\n#$(date +%d/%m/%Y----%H:%M)\nHubo un error al ejecutar $3\n===============================" >> $2

fi