fecha=$(date +%d-%m-%Y)
hora=$(date +%H:%M)
mensaje=/home/callcenter/interfaz/proceso/mensajePlantillaReporteEvolutivo.txt
echo -e "Estimados, adjuntamos los reportes correspondientes a la evolucion de las cobranzas del mes en curso, generados el dia $fecha a las $hora HS.\n\nSaludos\n\nSistemas-Call Mora" >$mensaje