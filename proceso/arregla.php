<?php
include('../../htdocs/conndb/conndb.php');
include('utils.php');
$DIRECTORYTO="/home/callcenter/interfaz/procesados/";
$FILE=exec ('ls -1 '.$DIRECTORYTO.'*140429* ',$FILES); 
foreach($FILES as $FILE){
                  echo $FILE;
	importar($FILE,$conn);
}
die();
function importar($FILE,$conn){
	$gestor = fopen($FILE, "rb");
	$contenido = '';
	$C=1;
	while (!feof($gestor)) {
	  $linea =  fread($gestor, 1347);
	//datos del Moroso
	    $DOCUMENTOTIPO=fixtext(substr($linea ,22,1));
	    $DOCUMENTONUMERO=fixtext(substr($linea ,23,8));
	    $TITULAR=fixtext(substr($linea ,149,30));
	     //echo $TITULAR;
	    $IDTITULAR='';
	    $TIPOCARTERA=fixtext(substr($linea ,6,3));
	    $TIPOPROCESO=fixtext(substr($linea ,9,3));
	    $SELECT= "select * from PERFILES pe where pe.TIPOCARTERA='$TIPOCARTERA' and pe.TIPOPROCESO='$TIPOPROCESO' ";
	    $result = mysql_query($SELECT, $conn ); 
	     if ($row = mysql_fetch_array($result)){
		$PERFIL= $row['PERFIL'];
		 if (1==1){
		$IDMOROSO=$IDTITULAR;
		$TIPOCARTERA=fixtext(substr($linea ,6,3));
		$FECHAPROCESO='20'.substr($linea ,0,2).'-'.substr($linea ,2,2).'-'.substr($linea ,4,2);
		$TIPOPROCESO=fixtext(substr($linea ,9,3));
		$SUCURSAL=fixtext(substr($linea ,32,3));
		if($TIPOCARTERA=='LIC'){
		$NUMCREDITO=$DOCUMENTONUMERO;}
		else
		$NUMCREDITO=fixtext(substr($linea ,36,7));
		$DOLARES=fixtext(substr($linea ,43,3));
		$IMPORTE=fixtext(substr($linea ,47,10))/100;
		$FECHA='20'.substr($linea ,64,2).'-'.substr($linea ,61,2).'-'.substr($linea ,58,2);
		$CUOTAS=fixtext(substr($linea ,67,2));
		$ULTIMACUOTA =fixtext(substr($linea ,70,2));
		$DIAS=fixtext(substr($linea ,73,3));
		$SALDO=fixtext(substr($linea ,78,14)/100);
		$FRAVSAENZ=fixtext(substr($linea ,115,7));
		$CUOTASATRASADAS=fixtext(substr($linea ,123,2));
		$AUXNUEVO=fixtext(substr($linea ,126,2));
		if (!$AUXNUEVO)$AUXNUEVO='00';
		$CANTATRASADAS=fixtext(substr($linea ,134,5));
		$CATEGORIA=fixtext(substr($linea ,255,1));
		$TIPOCREDITO=fixtext(substr($linea ,489,2));
		$VENDEDOR=fixtext(substr($linea ,492,4));
		$OBSERVACIONES=fixtext(substr($linea ,828,30));
		$DEBEPAGARINT=fixtext(substr($linea ,869,12));
		$FECVENCIMIENTO=fixtext(substr($linea ,882,6));
		$SALDOBCO=fixtext(substr($linea ,889,10));
		$TIPOTARJETA=fixtext(substr($linea ,900,10));
		$SALDOPESOS=fixtext(substr($linea ,911,10)/100);
		$SALDODOLAR=fixtext(substr($linea ,922,10)/100);
		$CANTIMP=fixtext(substr($linea ,932,4));
					$UPDATE= "update CARTERA_201405 set 
					TIPOCARTERA='$TIPOCARTERA',
					TIPOPROCESO='$TIPOPROCESO',
					SALDOTOMAR='$SALDO',
					PERFIL='$PERFIL'
					where
					NUMCREDITO='$NUMCREDITO' and SUCURSAL='$SUCURSAL' ";
				        mysql_query($UPDATE);
					echo 1;
				}
			}
			
			
			
		}

	fclose($gestor);
}
?>
