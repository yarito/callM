<?
date_default_timezone_set ('America/Argentina/Buenos_Aires');

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/******** CUSTOM DATA ********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// local path
$PATH = "/home/callcenter/interfaz/sms/out/";
$SENTPATH = "/home/callcenter/interfaz/sms/out/sent/";
// log
$LOG = "/home/callcenter/interfaz/sms/log/out.log";
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/******** CUSTOM DATA ********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


// include
include("/home/callcenter/htdocs/conndb/conndb.php");
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/callcenter/htdocs/phplib/phpseclib/');
include('Net/SFTP.php');
// date
$DATE = date("Ymd",time());

// files
$FILE['SMS30'] = "sms30_".$DATE.".txt";
$FILE['SMS60'] = "sms60_".$DATE.".txt";
$FILE['SMS90'] = "sms90_".$DATE.".txt";


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/************ LOG ************/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
function savelog($msg) {
  global $LOG;
  if ($handle = fopen($LOG, "a"))  {
    @fwrite($handle, date("Y-m-d H:i:s")." - $msg\r\n" );
    @fclose($handle);
  }
}
function errorlog($type, $info, $file, $row) {
  global $LOG;
  if ($handle = fopen($LOG, "a"))  {
    @fwrite($handle, date("Y-m-d H:i:s")." - ERR --> $type: $info FILE: $file -  Row $row\r\n" );
    @fclose($handle);
  }
}
set_error_handler(errorlog, E_USER_ERROR);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/************ LOG ************/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
 

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/********** SMS 30 ***********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// open file
$fp = fopen($PATH.$FILE[SMS30], "w");
// get query
$SQL = "SELECT 
       ca.IDCARTERA IDCARTERA,
       ca.IDMOROSO IDMOROSO,
	LPAD(IFNULL(ca.SUCURSAL,''),3,'0') SUCURSAL,
	LPAD(IFNULL(ca.NUMCREDITO,''),7,'0') NUMCREDITO,
       (SELECT CONCAT(CODIGOAREA,NUMERO)
	   FROM TELEFONOS t
	  WHERE ca.IDMOROSO=t.IDCONTACTO
	    AND ESTADO=1
	  ORDER BY FECHAALTA DESC
	  LIMIT  1) TELEFONO,
         DATE_FORMAT(DATE_ADD(ca.FECHA, INTERVAL CASE WHEN ca.TIPOCREDITO IN ('BI','BY') THEN ca.ULTIMACUOTA*2 ELSE ca.ULTIMACUOTA END MONTH),'%d/%m/%y') FECVEN
  FROM CARTERA ca
 WHERE ca.TIPOPROCESO='1AT' 
   AND ca.FECHAPROCESO = (SELECT MAX(FECHAPROCESO) FROM CARTERA) 
   AND ca.ESTADO NOT IN (3,5,10,14)
   AND ca.ULTIMACUOTA=0
   AND ca.SUCURSAL in ('001','002','004','005','010','021','026','033','037','045','054','056','059','061','064','065','070','071','083','084','106','107','116','122','126','130','133','134','136','145','148','153','160','162','163')";
$result = mysql_query($SQL, $conn);
while($rs = mysql_fetch_array($result)) {
  // clear line buffer
  $buffer = "";
  $buffer.= $rs['IDCARTERA'].";";
  $buffer.= $rs['IDMOROSO'].";";
  $buffer.= $rs['SUCURSAL'].";";
  $buffer.= $rs['NUMCREDITO'].";";
  $buffer.= $rs['TELEFONO'].";";
  $buffer.= "Le recordamos que está venciendo su crédito número ".$rs['SUCURSAL'].$rs['NUMCREDITO']." de la compra realizada en FRAVEGA, si ya abono por favor desestimar el mensaje. Muchas Gracias";
  $buffer.= "\n";
  // write line buffer to file
  fwrite($fp, $buffer);
}
// close file
fclose($fp);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/********** SMS 30 ***********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/********** SMS 60 ***********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// open file
$fp = fopen($PATH.$FILE[SMS60], "w");
// get query
$SQL = "SELECT 
       ca.IDCARTERA IDCARTERA,
       ca.IDMOROSO IDMOROSO,
	LPAD(IFNULL(ca.SUCURSAL,''),3,'0') SUCURSAL,
	LPAD(IFNULL(ca.NUMCREDITO,''),7,'0') NUMCREDITO,
       (SELECT CONCAT(CODIGOAREA,NUMERO)
	   FROM TELEFONOS t
	  WHERE ca.IDMOROSO=t.IDCONTACTO
	    AND ESTADO=1
	  ORDER BY FECHAALTA DESC
	  LIMIT  1) TELEFONO,
         DATE_FORMAT(DATE_ADD(ca.FECHA, INTERVAL CASE WHEN ca.TIPOCREDITO IN ('BI','BY') THEN ca.ULTIMACUOTA*2 ELSE ca.ULTIMACUOTA END MONTH),'%d/%m/%y') FECVEN
  FROM CARTERA ca
 WHERE ca.TIPOPROCESO='002' 
   AND ca.FECHAPROCESO = (SELECT MAX(FECHAPROCESO) FROM CARTERA) 
   AND ca.ESTADO NOT IN (3,5,10,14)
   AND ca.ULTIMACUOTA=0
   AND ca.SUCURSAL in ('001','009','025','026','033','051','054','071','104','108','113','118','126','134','148','153')";
$result = mysql_query($SQL, $conn);
while($rs = mysql_fetch_array($result)) {
  // clear line buffer
  $buffer = "";
  $buffer.= $rs['IDCARTERA'].";";
  $buffer.= $rs['IDMOROSO'].";";
  $buffer.= $rs['SUCURSAL'].";";
  $buffer.= $rs['NUMCREDITO'].";";
  $buffer.= $rs['TELEFONO'].";";
  $buffer.= "Lo citamos a concurrir a fravega con la finalidad de resolver su situacion crediticia ".$rs['SUCURSAL'].$rs['NUMCREDITO']." con 2 meses de atraso.Si ya abono descarte este mensaje.Gracias";
  $buffer.= "\n";
  // write line buffer to file
  fwrite($fp, $buffer);
}
// close file
fclose($fp);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/********** SMS 60 ***********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/********** SMS 90 ***********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// open file
$fp = fopen($PATH.$FILE[SMS90], "w");
// get query
$SQL = "SELECT 
       ca.IDCARTERA IDCARTERA,
       ca.IDMOROSO IDMOROSO,
	LPAD(IFNULL(ca.SUCURSAL,''),3,'0') SUCURSAL,
	LPAD(IFNULL(ca.NUMCREDITO,''),7,'0') NUMCREDITO,
       (SELECT CONCAT(CODIGOAREA,NUMERO)
	   FROM TELEFONOS t
	  WHERE ca.IDMOROSO=t.IDCONTACTO
	    AND ESTADO=1
	  ORDER BY FECHAALTA DESC
	  LIMIT  1) TELEFONO,
         DATE_FORMAT(DATE_ADD(ca.FECHA, INTERVAL CASE WHEN ca.TIPOCREDITO IN ('BI','BY') THEN ca.ULTIMACUOTA*2 ELSE ca.ULTIMACUOTA END MONTH),'%d/%m/%y') FECVEN
  FROM CARTERA ca
 WHERE ca.TIPOPROCESO='003' 
   AND ca.FECHAPROCESO = (SELECT MAX(FECHAPROCESO) FROM CARTERA) 
   AND ca.ESTADO NOT IN (3,5,10,14)
   AND ca.ULTIMACUOTA=0
   AND ca.SUCURSAL in ('001','009','025','026','033','051','054','071','104','108','113','118','126','134','148','153')";
$result = mysql_query($SQL, $conn);
while($rs = mysql_fetch_array($result)) {
  // clear line buffer
  $buffer = "";
  $buffer.= $rs['IDCARTERA'].";";
  $buffer.= $rs['IDMOROSO'].";";
  $buffer.= $rs['SUCURSAL'].";";
  $buffer.= $rs['NUMCREDITO'].";";
  $buffer.= $rs['TELEFONO'].";";
  $buffer.= "Por favor presentese en FRAVEGA a regularizar su credito ".$rs['SUCURSAL'].$rs['NUMCREDITO']." con 3 meses de atraso. Si ya abono descarte este mensaje. Gracias.";
  $buffer.= "\n";
  // write line buffer to file
  fwrite($fp, $buffer);
}
// close file
fclose($fp);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/********** SMS 90 ***********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/

?>
