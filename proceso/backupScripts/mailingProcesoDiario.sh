if [ $1 -eq 0 ]
	then
		echo -e "$(date +%d/%m/%Y-----%H:%M)\n===========================\nEl proceso diario se realizo correctamente\n===========================" > /home/callcenter/interfaz/proceso/archivos/message.txt
	else
		echo -e "$(date +%d/%m/%Y-----%H:%M)\n===========================\nERROR: en el proceso diario\n===========================" > /home/callcenter/interfaz/proceso/archivos/message.txt

fi