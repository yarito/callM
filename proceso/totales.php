<?php
include('../../htdocs/conndb/conndb.php');
include('utils.php');
$DIRECTORY="/home/callcenter/interfaz/procesar/";
$DIRECTORYTO="/home/callcenter/interfaz/procesados/";
$TOTALES="suc148geuimp";
$FILE=$DIRECTORY.$TOTALES;
if(file_exists  ($FILE ))importar($FILE,$conn);
$FILETO=$DIRECTORYTO.date("Ymd").$FILES[$a];
//rename($FILE,$FILETO);
function importar($FILE,$conn){
$CONTENIDO=file_get_contents($FILE); 
$CONTENIDO=str_replace("  "," ",$CONTENIDO);
$CONTENIDO=str_replace("  "," ",$CONTENIDO);
$CONTENIDO=str_replace("  "," ",$CONTENIDO);
$CONTENIDO=str_replace("  "," ",$CONTENIDO);
$CONTENIDO=str_replace("  "," ",$CONTENIDO);
$CONTENIDO=str_replace("  "," ",$CONTENIDO);
$row=explode(chr(10),$CONTENIDO);
	$SQL="Truncate TABLE TOTALCARTERA";
	mysql_query($SQL, $conn);
	foreach ($row as $rs){
		$rs1=explode(" ",$rs);
		$SQL="select IDSUCURSAL FROM SUCURSALES where SUCURSAL ='".substr($rs1[0],0,3)."'";
echo $SQL;
		$result = mysql_query($SQL, $conn );
		if ($row1= mysql_fetch_array($result)){
			$INSERT= "insert into  TOTALCARTERA(IDSUCURSAL,TOTALCARTERA) values ((select IDSUCURSAL FROM SUCURSALES where SUCURSAL ='".substr($rs1[0],0,3)."'),".$rs1[1].")";
			mysql_query($INSERT, $conn );
		}else
			echo $rs1[0];
	}

}

?>
