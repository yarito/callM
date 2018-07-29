queries=/home/callcenter/interfaz/proceso/queries
queriesLog=$2

ejecutarQuery ()
{
	#tomo query a query la ejecuto y registro su output(si fue correcto queda un espacio en blanco 
	#o el resultado de salida si es un select y si hay error el error)
	

	maximo=$(wc -l $1 | cut -d " " -f 1)
	let maximo=maximo+1
	for i in $(seq 1 $maximo)
	do
		query=$(cat $1 | head -$i | tail -1)
		#echo $query
		error=$(mysql -se "$query" 2>&1)
		fecha=$(date +%d-%m-%Y-%H.%M)	
		if [ "$(echo $error | cut -d " " -f1)"  == "ERROR" ]
		then
			echo -e "===============================================================================\n$fecha\n$query\n$error">>$queriesLog
		else		
			echo -e "===============================================================================\n$fecha\n$query">>$queriesLog	
		fi		
	done
}

#este script debe ser llamado desde otro script, hay que mandarle como primer parametro la query ( en un archivo guardado) y el log asociado ( archivo)

ejecutarQuery $1 $2

