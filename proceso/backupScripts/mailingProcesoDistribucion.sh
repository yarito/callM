if [ $1 -eq 0 ]
	then
		echo -e "===========================\nEl cierre de mes se realizo correctamente\n===========================" >> /home/callcenter/interfaz/proceso/archivos/message.txt
	else
		echo -e "===========================\nERROR: en el cierre de mes\n===========================" >> /home/callcenter/interfaz/proceso/archivos/message.txt

fi