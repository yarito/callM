archivos=/home/callcenter/interfaz/proceso/archivos

mandarReportesEmail ()
{


from="sistemas@callmora.com.ar"
# more receiver like a@example.com b@example.com ...
to="$2"
subject="$3"
#you can also read content from the file just use $(cat yourfile)

j=1

for i in $(seq 4 $#);
do

 files[$j]=$(echo $@ | cut -d " " -f $i) 
 let j=j+1
done


for att in "${files[@]}"; do
   [ ! -f "$att" ] && echo "Warning: attachment $att not found, skipping" >&2 && continue	
  attargs+=( "-a"  "$att" )
done

cat $plantilla |  mail -s "$subject" -r "$from" "${attargs[@]}" "$to"


}


mailInicio()
{
plantilla=$archivos/mensajePlantillaReporteDeInicio.txt
fecha=$(date +%d-%m-%Y)
hora=$(date +%H.%M)
echo -e "Estimados, adjuntamos los reportes correspondientes al Inicio de cobranzas del mes en curso, generados el dia $fecha a las $hora HS.\n\nSaludos\n\nSistemas-Call Mora" >$plantilla
}

mailDiarioEvolutivo()
{

plantilla=$archivos/mensajePlantillaReporteEvolutivo.txt
fecha=$(date +%d-%m-%Y)
hora=$(date +%H.%M)

echo -e "Estimados, adjuntamos los reportes correspondientes a la evolucion de las cobranzas del mes en curso, generados el dia $fecha a las $hora HS.\n\nSaludos\n\nSistemas-Call Mora" >$plantilla

}

mailDeCierre()
{
plantilla=$archivos/mensajePlantillaReporteCierre.txt
fecha=$(date +%d-%m-%Y)
hora=$(date +%H.%M)
echo -e "Estimados, adjuntamos los reportes correspondientes al cierre de las cobranzas del mes en curso, generados el dia $fecha a las $hora HS.\n\nSaludos\n\nSistemas-Call Mora" >$plantilla

}

mailDeLegales()
{
plantilla=$archivos/mensajePlantillaReporteLegales.txt
fecha=$(date +%d-%m-%Y)
hora=$(date +%H.%M)
echo -e "Estimados, adjuntamos los reportes correspondientes a Legales, generados el dia $fecha a las $hora HS.\n\nSaludos\n\nSistemas-Call Mora" >$plantilla

}

if [ $1 -eq 1 ]
then

mailInicio

fi

if [ $1 -eq 2 ]
then

mailDiarioEvolutivo
fi

if [ $1 -eq 3 ]
then

mailDeCierre
fi

if [ $1 -eq 4 ]
then

mailDeLegales
fi

mandarReportesEmail $@ 