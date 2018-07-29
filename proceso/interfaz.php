<?php
include('../../htdocs/conndb/conndb.php');
include('utils.php');
$DIRECTORY="/home/callcenter/interfaz/procesar/";
$DIRECTORYTO="/home/callcenter/interfaz/procesados/";
$FILES=(getFiles($DIRECTORY)); 
for($a=0;$a<count($FILES);$a++){
	$FILE=$DIRECTORY.$FILES[$a];
	$FILETO=$DIRECTORYTO.date("Ymd").$FILES[$a];
	importar($FILE,$conn);
	rename($FILE,$FILETO);
}

function importar($FILE,$conn){
	$gestor = fopen($FILE, "rb");
	$contenido = '';
	$C=1;
	while (!feof($gestor)) {
	  $linea =  fread($gestor, 1348);
	//datos del Moroso
	    $DOCUMENTOTIPO=fixtext(substr($linea ,22,1));
	    $DOCUMENTONUMERO=fixtext(substr($linea ,23,8));
	    $TITULAR=fixtext(substr($linea ,149,30));
	     echo $TITULAR;
	    $IDTITULAR='';
	    $TIPOCARTERA=fixtext(substr($linea ,6,3));
	    $TIPOPROCESO=fixtext(substr($linea ,9,3));
	    $SELECT= "select * from PERFILES pe where pe.TIPOCARTERA='$TIPOCARTERA' and pe.TIPOPROCESO='$TIPOPROCESO' ";
	    $result = mysql_query($SELECT, $conn ); 
	     if ($row = mysql_fetch_array($result)){
		    if ($TITULAR){
			    $SELECT= "select * from CONTACTOS where DOCUMENTONUMERO=$DOCUMENTONUMERO and DOCUMENTOTIPO='$DOCUMENTOTIPO'";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
				$IDTITULAR=$row['IDCONTACTO'];
				$UPDATE= "update CONTACTOS set NOMBRE='$TITULAR' where IDCONTACTO=$IDTITULAR";
			   	mysql_query($UPDATE);
			    }else{
				$INSERT= "insert into  CONTACTOS (DOCUMENTONUMERO,DOCUMENTOTIPO,NOMBRE,FECHAALTA) values ($DOCUMENTONUMERO,'$DOCUMENTOTIPO','$TITULAR',now())";
			   	mysql_query($INSERT, $conn );
				$IDTITULAR=mysql_insert_id();
			    }
		   }
		     if ($IDTITULAR){ 
			$CALLE=fixtext(substr($linea ,180,30));
			$NUMERO=fixtext(substr($linea ,210,5));
		  	$PISO=fixtext(substr($linea ,216,2));
		  	$DEPTO=fixtext(substr($linea ,219,2));
		  	$LOCALIDAD=fixtext(substr($linea ,222,20));
		  	$CODIGOPOSTAL=fixtext(substr($linea ,243,4));
		  	$PLANO=fixtext(substr($linea ,248,3));
		  	$COORDENADAS=fixtext(substr($linea ,252,2));
			    $SELECT= "select * from DIRECCIONES where CALLE='$CALLE' and NUMERO='$NUMERO' and LOCALIDAD='$LOCALIDAD' and IDCONTACTO=$IDTITULAR";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
				$IDDIRECCION=$row['IDDIRECCION'];
			    }else{
				$INSERT= "insert into  DIRECCIONES (IDCONTACTO,CALLE,NUMERO,PISO,DEPTO,LOCALIDAD,CODIGOPOSTAL,PLANO, COORDENADAS,FECHAALTA) 
				values ($IDTITULAR,'$CALLE','$NUMERO','$PISO','$DEPTO','$LOCALIDAD','$CODIGOPOSTAL','$PLANO','$COORDENADAS',now())";
			   	mysql_query($INSERT);
				$IDDIRECCION=mysql_insert_id();
			    }
			}
		  if ($IDTITULAR){
			  $CODIGOAREA=fixtext(substr($linea ,93,5));
			  $NUMERO=fixtext(substr($linea ,99,15));
			  if ($NUMERO){
			 	  $SELECT= "select * from TELEFONOS where CODIGOAREA='$CODIGOAREA' and NUMERO='$NUMERO' and IDCONTACTO=$IDTITULAR";
				    $result = mysql_query($SELECT, $conn );
					if (mysql_error()) echo $SELECT;
					echo mysql_error();
				    if ($row = mysql_fetch_array($result)){
					$IDTELEFONO=$row['IDTELEFONO'];
				    }else{
					$INSERT= "insert into  TELEFONOS (IDCONTACTO,CODIGOAREA,NUMERO,FECHAALTA) values ('$IDTITULAR','$CODIGOAREA','$NUMERO',now())";
				   	mysql_query($INSERT, $conn );
					$IDTELEFONO=mysql_insert_id();
				    }
			}
		  }
	
		 if ($IDTITULAR){
		  $EMPRESA=fixtext(substr($linea ,266,20));
		  $DIRCALLE=fixtext(substr($linea ,287,19));
		  $DIRNUMERO=fixtext(substr($linea ,313,5));
		  $DIRLOCALIDAD=fixtext(substr($linea ,319,20));
		  $SECCION=fixtext(substr($linea ,340,10));
		  $TELCODIGOAREA=fixtext(substr($linea ,351,5));
		  $TELNUMERO=fixtext(substr($linea ,357,15));
		  $TELINTERNO=fixtext(substr($linea ,373,12));
		 $SELECT= "select * from EMPLEOS where EMPRESA='$EMPRESA' and DIRCALLE='$DIRCALLE' and TELNUMERO='$TELNUMERO' and IDCONTACTO=$IDTITULAR";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
				$IDEMPLEO=$row['IDEMPLEO'];
				$UPDATE= "update EMPLEOS set DIRLOCALIDAD='$DIRLOCALIDAD' where EMPRESA='$EMPRESA' and DIRCALLE='$DIRCALLE' and TELNUMERO='$TELNUMERO' and IDCONTACTO=$IDTITULAR";
				mysql_query($UPDATE, $conn );
			    }else{
				$INSERT= "insert into  EMPLEOS (IDCONTACTO,EMPRESA,DIRCALLE,DIRNUMERO,DIRLOCALIDAD,SECCION,TELCODIGOAREA,TELNUMERO,TELINTERNO,FECHAALTA)
				 values ('$IDTITULAR','$EMPRESA','$DIRCALLE','$DIRNUMERO','$DIRLOCALIDAD','$SECCION','$TELCODIGOAREA','$TELNUMERO','$TELINTERNO',now())";
			   	mysql_query($INSERT, $conn );
				$IDEMPLEO=mysql_insert_id();
				if (mysql_error()) echo $INSERT;
				echo mysql_error();
			    }
	
		}
		 if ($IDTITULAR){
		  $REFERENCIA=fixtext(substr($linea ,395,30));
		  $TELEFONO=fixtext(substr($linea ,426,15));
			$SELECT= "select * from REFERENCIAS where REFERENCIA='$REFERENCIA' and TELEFONO='$TELEFONO' and IDCONTACTO=$IDTITULAR";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
				$REFERENCIA1=$row['IDREFERENCIA'];
			    }else{
				$INSERT= "insert into  REFERENCIAS (IDCONTACTO,REFERENCIA,TELEFONO,FECHAALTA)
				 values ('$IDTITULAR','$REFERENCIA','$TELEFONO',now())";
			   	mysql_query($INSERT, $conn );
				$REFERENCIA1=mysql_insert_id();
				if (mysql_error()) echo $INSERT;
				echo mysql_error();
			    }
	
		  $REFERENCIA=fixtext(substr($linea ,442,30));
		  $TELEFONO=fixtext(substr($linea ,473,15));
		$SELECT= "select * from REFERENCIAS where REFERENCIA='$REFERENCIA' and TELEFONO='$TELEFONO' and IDCONTACTO=$IDTITULAR";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
				$REFERENCIA2=$row['IDREFERENCIA'];
			    }else{
				$INSERT= "insert into  REFERENCIAS (IDCONTACTO,REFERENCIA,TELEFONO,FECHAALTA)
				 values ('$IDTITULAR','$REFERENCIA','$TELEFONO',now())";
			   	mysql_query($INSERT, $conn );
				$REFERENCIA2=mysql_insert_id();
				if (mysql_error()) echo $INSERT;
				echo mysql_error();
			    }
	
		}
	
		  $DOCUMENTOTIPO='T';
		  $DOCUMENTONUMEROG=fixtext(substr($linea ,36,7));
		  $GARANTE=fixtext(substr($linea ,506,30));
		    $IDGARANTE='';
		    if ($DOCUMENTONUMEROG&&$GARANTE){
			    $SELECT= "select * from CONTACTOS where DOCUMENTONUMERO=$DOCUMENTONUMEROG and DOCUMENTOTIPO='$DOCUMENTOTIPO'";
			    if (mysql_error()) echo $INSERT;
			  echo mysql_error();
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
				$IDGARANTE=$row['IDCONTACTO'];
				$UPDATE= "update CONTACTOS set NOMBRE='$GARANTE' where IDCONTACTO=$IDGARANTE";
			   	mysql_query($UPDATE);
			    }else{
				$INSERT= "insert into  CONTACTOS (DOCUMENTONUMERO,DOCUMENTOTIPO,NOMBRE,FECHAALTA) values ($DOCUMENTONUMEROG,'$DOCUMENTOTIPO','$GARANTE',now())";
			   	mysql_query($INSERT, $conn );
				$IDGARANTE=mysql_insert_id();
				if (mysql_error()) echo $INSERT;
				echo mysql_error();
			    }
		   }
		     if ($IDGARANTE){ 
		 	 $CALLE=fixtext(substr($linea ,537,35));
		 	 $NUMERO=fixtext(substr($linea ,573,2));
			 $PISO=fixtext(substr($linea ,576,2));
			 $DEPTO=fixtext(substr($linea ,579,20));
			 $LOCALIDAD=fixtext(substr($linea ,600,4));
			    $SELECT= "select * from DIRECCIONES where CALLE='$CALLE' and NUMERO='$NUMERO' and PISO='$PISO' and DEPTO='$DEPTO' and LOCALIDAD='$LOCALIDAD' and IDCONTACTO=$IDGARANTE";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
				$IDDIRECCIONG=$row['IDDIRECCION'];
			    }else{
				$INSERT= "insert into  DIRECCIONES (IDCONTACTO,CALLE,NUMERO,PISO,DEPTO,LOCALIDAD,CODIGOPOSTAL,PLANO, COORDENADAS,FECHAALTA) 
				values ($IDGARANTE,'$CALLE','$NUMERO','$PISO','$DEPTO','$LOCALIDAD','$CODIGOPOSTAL','$PLANO','$COORDENADAS',now())";
			   	mysql_query($INSERT);
				$IDDIRECCIONG=mysql_insert_id();
			    }
			}
	
		 if ($IDGARANTE){
		  $EMPRESA=fixtext(substr($linea ,614,20));
		  $DIRCALLE=fixtext(substr($linea ,635,25));
		  $NROEMPGARANTE=fixtext(substr($linea ,661,5));
		  $DIRNUMERO=fixtext(substr($linea ,667,20));
		  $SECCION=fixtext(substr($linea ,689,10));
		  $TELCODIGOAREA=fixtext(substr($linea ,609,5));
		  $TELNUMERO=fixtext(substr($linea ,705,15));
		  $TELINTERNO=fixtext(substr($linea ,721,12));
		 
		 $SELECT= "select * from EMPLEOS where EMPRESA='$EMPRESA' and DIRCALLE='$DIRCALLE' and TELNUMERO='$TELNUMERO' and IDCONTACTO=$IDGARANTE";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
				$IDEMPLEOG=$row['IDEMPLEO'];
			    }else{
				$INSERT= "insert into  EMPLEOS (IDCONTACTO,EMPRESA,DIRCALLE,DIRNUMERO,SECCION,TELCODIGOAREA,TELNUMERO,TELINTERNO,FECHAALTA)
				 values ('$IDGARANTE','$EMPRESA','$DIRCALLE','$DIRNUMERO','$SECCION','$TELCODIGOAREA','$TELNUMERO','$TELINTERNO',now())";
			   	mysql_query($INSERT, $conn );
				$IDEMPLEOG=mysql_insert_id();
				if (mysql_error()) echo $INSERT;
				echo mysql_error();
			    }
	
		}
	
	
		 if ($IDTITULAR){
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
    $DEBAUTO=fixtext(substr($linea ,1346,1));
		$SELECT= "select * from HSTCARTERA where NUMCREDITO='$NUMCREDITO' and SUCURSAL='$SUCURSAL' and TIPOCARTERA='$TIPOCARTERA'";
		$result = mysql_query($SELECT, $conn );
		if ($row = mysql_fetch_array($result)){
		    	$INSERT= "insert into CARTERA select * from HSTCARTERA where NUMCREDITO='$NUMCREDITO' and SUCURSAL='$SUCURSAL' and TIPOCARTERA='$TIPOCARTERA'";
			 mysql_query($INSERT, $conn );
			$DELETE= "delete from HSTCARTERA where NUMCREDITO='$NUMCREDITO' and SUCURSAL='$SUCURSAL'";
			 mysql_query($DELETE, $conn );
		}	

		$SELECT= "select * from CARTERA where NUMCREDITO='$NUMCREDITO' and SUCURSAL='$SUCURSAL'";
			    $result = mysql_query($SELECT, $conn );

			    if ($row = mysql_fetch_array($result)){
						$IDCARTERA=$row['IDCARTERA'];
					$UPDATE= "update CARTERA set 
					FECHAPROCESO= '$FECHAPROCESO',
					AUXNUEVO='$AUXNUEVO',
					IDMOROSO='$IDMOROSO',
					TIPOCARTERA='$TIPOCARTERA',
					TIPOPROCESO='$TIPOPROCESO',
					SUCURSAL='$SUCURSAL',
					IMPORTE='$IMPORTE',
					FECHA='$FECHA',
					CUOTAS='$CUOTAS',
					ULTIMACUOTA='$ULTIMACUOTA',
					DIAS='$DIAS',
					SALDO='$SALDO',
					FRAVSAENZ='$FRAVSAENZ',
					CUOTASATRASADAS='$CUOTASATRASADAS',
					CANTATRASADAS='$CANTATRASADAS',
					CATEGORIA='$CATEGORIA',
					TIPOCREDITO='$TIPOCREDITO',
					VENDEDOR='$VENDEDOR',
					OBSERVACIONES='$OBSERVACIONES',
					DEBEPAGARINT='$DEBEPAGARINT',
					FECVENCIMIENTO='$FECVENCIMIENTO',
					SALDOBCO='$SALDOBCO',
					TIPOTARJETA='$TIPOTARJETA',
					SALDOPESOS='$SALDOPESOS',
					SALDODOLAR='$SALDODOLAR',
					CANTIMP='$CANTIMP',
          DEBAUTO='$DEBAUTO'
					where
					NUMCREDITO='$NUMCREDITO' and SUCURSAL='$SUCURSAL' ";
					 mysql_query($UPDATE);echo mysql_error();
			    }else{
				$INSERT= "insert into  CARTERA (
				IDMOROSO,
				IDGARANTE,
				TIPOCARTERA,
				FECHAPROCESO,
				TIPOPROCESO,
				SUCURSAL,
				NUMCREDITO,
				DOLARES,
				IMPORTE,
				FECHA,
				CUOTAS,
				ULTIMACUOTA,
				DIAS,
				SALDO,
				FRAVSAENZ,
				CUOTASATRASADAS,
				AUXNUEVO,
				CANTATRASADAS,
				CATEGORIA,
				TIPOCREDITO,
				VENDEDOR,
				OBSERVACIONES,
				DEBEPAGARINT,
				FECVENCIMIENTO,
				SALDOBCO,
				TIPOTARJETA,
				SALDOPESOS,
				SALDODOLAR,
				CANTIMP,FECHAALTA,DEBAUTO)
				values (
					'$IDMOROSO',
					'$IDGARANTE',
					'$TIPOCARTERA',
					'$FECHAPROCESO',
					'$TIPOPROCESO',
					'$SUCURSAL',
					'$NUMCREDITO',
					'$DOLARES',
					'$IMPORTE',
					'$FECHA',
					'$CUOTAS',
					'$ULTIMACUOTA',
					'$DIAS',
					'$SALDO',
					'$FRAVSAENZ',
					'$CUOTASATRASADAS',
					'$AUXNUEVO',
					'$CANTATRASADAS',
					'$CATEGORIA',
					'$TIPOCREDITO',
					'$VENDEDOR',
					'$OBSERVACIONES',
					'$DEBEPAGARINT',
					'$FECVENCIMIENTO',
					'$SALDOBCO',
					'$TIPOTARJETA',
					'$SALDOPESOS',
					'$SALDODOLAR',
					'$CANTIMP',NOW(),'$DEBAUTO')";
			   	mysql_query($INSERT, $conn );
				$IDCARTERA=mysql_insert_id();
				echo mysql_error();	
			    }
		   if ($IDCARTERA){
		  $CODIGOPRODUCTO=fixtext(substr($linea ,743,6));
		  $PRODUCTO=fixtext(substr($linea ,750,30));
			$SELECT= "select * from PRODUCTOS where CODIGOPRODUCTO='$CODIGOPRODUCTO' and IDCARTERA=$IDCARTERA";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
				
			$IDPRODUCTO=$row['IDPRODUCTO'];
			    }elseif($CODIGOPRODUCTO){
				$INSERT= "insert into  PRODUCTOS (IDCARTERA,CODIGOPRODUCTO,PRODUCTO,FECHAALTA)
				 values ('$IDCARTERA','$CODIGOPRODUCTO','$PRODUCTO',now())";
			   	mysql_query($INSERT, $conn );
				$IDPRODUCTO=mysql_insert_id();
				if (mysql_error()) echo $INSERT;
				echo mysql_error();
			    }
		  $CODIGOPRODUCTO=fixtext(substr($linea ,781,6));
		  $PRODUCTO=fixtext(substr($linea ,788,30));
		  $SELECT= "select * from PRODUCTOS where CODIGOPRODUCTO='$CODIGOPRODUCTO' and IDCARTERA=$IDCARTERA";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
			$IDPRODUCTO=$row['IDPRODUCTO'];
			    }elseif($CODIGOPRODUCTO){
				$INSERT= "insert into  PRODUCTOS (IDCARTERA,CODIGOPRODUCTO,PRODUCTO,FECHAALTA)
				 values ('$IDCARTERA','$CODIGOPRODUCTO','$PRODUCTO',now())";
			   	mysql_query($INSERT, $conn );
				$IDPRODUCTO=mysql_insert_id();
				if (mysql_error()) echo $INSERT;
				echo mysql_error();
			    }
	$CODIGOPRODUCTO=fixtext(substr($linea ,936,6));
		  $PRODUCTO=fixtext(substr($linea ,943,30));
		  $SELECT= "select * from PRODUCTOS where CODIGOPRODUCTO='$CODIGOPRODUCTO' and IDCARTERA=$IDCARTERA";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
			$IDPRODUCTO=$row['IDPRODUCTO'];
			    }elseif($CODIGOPRODUCTO){
				$INSERT= "insert into  PRODUCTOS (IDCARTERA,CODIGOPRODUCTO,PRODUCTO,FECHAALTA)
				 values ('$IDCARTERA','$CODIGOPRODUCTO','$PRODUCTO',now())";
			   	mysql_query($INSERT, $conn );
				$IDPRODUCTO=mysql_insert_id();
				if (mysql_error()) echo $INSERT;
				echo mysql_error();
			    }
	$CODIGOPRODUCTO=fixtext(substr($linea ,977,6));
		  $PRODUCTO=fixtext(substr($linea ,984,30));
		  $SELECT= "select * from PRODUCTOS where CODIGOPRODUCTO='$CODIGOPRODUCTO' and IDCARTERA=$IDCARTERA";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
			$IDPRODUCTO=$row['IDPRODUCTO'];
			    }elseif($CODIGOPRODUCTO){
				$INSERT= "insert into  PRODUCTOS (IDCARTERA,CODIGOPRODUCTO,PRODUCTO,FECHAALTA)
				 values ('$IDCARTERA','$CODIGOPRODUCTO','$PRODUCTO',now())";
			   	mysql_query($INSERT, $conn );
				$IDPRODUCTO=mysql_insert_id();
				if (mysql_error()) echo $INSERT;
				echo mysql_error();
			    }
		$CODIGOPRODUCTO=fixtext(substr($linea ,1018,6));
		  $PRODUCTO=fixtext(substr($linea ,1025,30));
		  $SELECT= "select * from PRODUCTOS where CODIGOPRODUCTO='$CODIGOPRODUCTO' and IDCARTERA=$IDCARTERA";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
			$IDPRODUCTO=$row['IDPRODUCTO'];
			    }elseif($CODIGOPRODUCTO){
				$INSERT= "insert into  PRODUCTOS (IDCARTERA,CODIGOPRODUCTO,PRODUCTO,FECHAALTA)
				 values ('$IDCARTERA','$CODIGOPRODUCTO','$PRODUCTO',now())";
			   	mysql_query($INSERT, $conn );
				$IDPRODUCTO=mysql_insert_id();
				if (mysql_error()) echo $INSERT;
				echo mysql_error();
			    }
			$CODIGOPRODUCTO=fixtext(substr($linea ,1059,6));
		  $PRODUCTO=fixtext(substr($linea ,1066,30));
		  $SELECT= "select * from PRODUCTOS where CODIGOPRODUCTO='$CODIGOPRODUCTO' and IDCARTERA=$IDCARTERA";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
			$IDPRODUCTO=$row['IDPRODUCTO'];
			    }elseif($CODIGOPRODUCTO){
				$INSERT= "insert into  PRODUCTOS (IDCARTERA,CODIGOPRODUCTO,PRODUCTO,FECHAALTA)
				 values ('$IDCARTERA','$CODIGOPRODUCTO','$PRODUCTO',now())";
			   	mysql_query($INSERT, $conn );
				$IDPRODUCTO=mysql_insert_id();
				if (mysql_error()) echo $INSERT;
				echo mysql_error();
			    }
						$CODIGOPRODUCTO=fixtext(substr($linea ,1100,6));
		  $PRODUCTO=fixtext(substr($linea ,1107,30));
		  $SELECT= "select * from PRODUCTOS where CODIGOPRODUCTO='$CODIGOPRODUCTO' and IDCARTERA=$IDCARTERA";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
			$IDPRODUCTO=$row['IDPRODUCTO'];
			    }elseif($CODIGOPRODUCTO){
				$INSERT= "insert into  PRODUCTOS (IDCARTERA,CODIGOPRODUCTO,PRODUCTO,FECHAALTA)
				 values ('$IDCARTERA','$CODIGOPRODUCTO','$PRODUCTO',now())";
			   	mysql_query($INSERT, $conn );
				$IDPRODUCTO=mysql_insert_id();
				if (mysql_error()) echo $INSERT;
				echo mysql_error();
			    }
									$CODIGOPRODUCTO=fixtext(substr($linea ,1141,6));
		  $PRODUCTO=fixtext(substr($linea ,1148,30));
		  $SELECT= "select * from PRODUCTOS where CODIGOPRODUCTO='$CODIGOPRODUCTO' and IDCARTERA=$IDCARTERA";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
			$IDPRODUCTO=$row['IDPRODUCTO'];
			    }elseif($CODIGOPRODUCTO){
				$INSERT= "insert into  PRODUCTOS (IDCARTERA,CODIGOPRODUCTO,PRODUCTO,FECHAALTA)
				 values ('$IDCARTERA','$CODIGOPRODUCTO','$PRODUCTO',now())";
			   	mysql_query($INSERT, $conn );
				$IDPRODUCTO=mysql_insert_id();
				if (mysql_error()) echo $INSERT;
				echo mysql_error();
			    }
										$CODIGOPRODUCTO=fixtext(substr($linea ,1182,6));
		  $PRODUCTO=fixtext(substr($linea ,1189,30));
		  $SELECT= "select * from PRODUCTOS where CODIGOPRODUCTO='$CODIGOPRODUCTO' and IDCARTERA=$IDCARTERA";
			    $result = mysql_query($SELECT, $conn );
			    if ($row = mysql_fetch_array($result)){
			$IDPRODUCTO=$row['IDPRODUCTO'];
			    }elseif($CODIGOPRODUCTO){
				$INSERT= "insert into  PRODUCTOS (IDCARTERA,CODIGOPRODUCTO,PRODUCTO,FECHAALTA)
				 values ('$IDCARTERA','$CODIGOPRODUCTO','$PRODUCTO',now())";
			   	mysql_query($INSERT, $conn );
				$IDPRODUCTO=mysql_insert_id();
				if (mysql_error()) echo $INSERT;
				echo mysql_error();
			    }
			
			}
			
			
			
		}

		}
		echo $IDCARTERA."
";
	}
	fclose($gestor);
}
?>
