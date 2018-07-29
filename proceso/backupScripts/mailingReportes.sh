if [ $1 -eq 0 ]
	then
		echo -e "$(date +%d/%m/%Y-----%H:%M)\n===========================\nEl $2 se realizo correctamente\n===========================" > /home/callcenter/interfaz/proceso/message.txt
	else
		echo -e "$(date +%d/%m/%Y-----%H:%M)\n===========================\nERROR: en el $2\n===========================" > /home/callcenter/interfaz/proceso/message.txt

fi

cat /home/callcenter/interfaz/proceso/message.txt | mail -s "[Desarrollo] Reporte $2" "esteban.kutifak@fravega.com.ar"
#como parametro recibe el tipo de reporte que se va a realizar