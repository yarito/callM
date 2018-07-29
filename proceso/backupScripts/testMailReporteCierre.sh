generarMensaje ()
{

fecha=$(date +%d-%m-%Y)
hora=$(date +%H.%M)
mensaje=/home/callcenter/interfaz/proceso/archivos/mensajePlantillaReporteCierre.txt
echo -e "Estimados, adjuntamos los reportes correspondientes a la cierre de las cobranzas del mes en curso, generados el dia $fecha a las $hora HS.\n\nSaludos\n\nSistemas-Call Mora" >$mensaje

}

generarMensajeTxt ()
{
plantilla=/home/callcenter/interfaz/proceso/archivos/mensajePlantillaReporteCierre.txt
generarMensaje > $plantilla
mensaje=$(cat $plantilla)
}


mandarReportesEmail ()
{
archivos=/home/callcenter/interfaz/proceso/archivos
plantilla=$archivos/mensajePlantillaReporteCierre.txt

from="sistemas@callmora.com.ar"
# more receiver like a@example.com b@example.com ...
to="$1"
subject="$2"
#you can also read content from the file just use $(cat yourfile)

j=1
generarMensajeTxt

for i in $(seq 3 $#);
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

mandarReportesEmail $@ 
