<?

date_default_timezone_set ('America/Argentina/Buenos_Aires');

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/******** CUSTOM DATA ********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// EXT: Código de Call Center Externo
$EXT = "1";

// local path
$PATH = "/home/callcenter/interfaz/cc_externo/gering/out/";
$SENTPATH = "/home/callcenter/interfaz/cc_externo/gering/out/sent/";
// log
$LOG = "/home/callcenter/interfaz/cc_externo/gering/log/out.log";
// sftp
$SFTP = 1;
$SFTPSERVER = "sftp.gering.com.ar";
$SFTPPORT = "20022";
$SFTPUSER = "fvg";
$SFTPPASS = "Gering007";
$SFTPPATH = "/inbox/";
// ftp
$FTPSERVER = "";
$FTPUSER = "";
$FTPPASS = "";
$FTPPATH = "/inbox/";
//zip extension
$ZIPEXT = ".tar.gz";
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
$FILE['CARTERA'] = "CARTERA_".$DATE.".txt";
$FILE['MOROSOS'] = "MOROSOS_".$DATE.".txt";
$FILE['TELEFONOS'] = "TELEFONOS_".$DATE.".txt";
$FILE['REFERENCIAS'] = "REFERENCIAS_".$DATE.".txt";
$FILE['DIRECCIONES'] = "DIRECCIONES_".$DATE.".txt";
$FILE['EMPLEOS'] = "EMPLEOS_".$DATE.".txt";
$FILE['CREDITOS'] = "CREDITOS_".$DATE.".txt";
$FILE['PRODUCTOS'] = "PRODUCTOS_".$DATE.".txt";
$FILE['SEGUIMIENTO'] = "SEGUIMIENTO_".$DATE.".txt";

// idusuario
$IDUSUARIO = "";
$SQL = "SELECT IDUSUARIO FROM USUARIOS WHERE CODIGO LIKE 'GSA%'";
$result = mysql_query($SQL, $conn);
while($rs = mysql_fetch_array($result)) {
  if ($IDUSUARIO) $IDUSUARIO.= ",";
  $IDUSUARIO.= $rs['IDUSUARIO'];
}

/*
$IDUSUARIO = "102150,
102151,
102152,
102153,
102154,
102155,
102156,
102157,
102158,
102159,
102160,
102161,
102162,
102163,
102164,
102165";
*/


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


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/********** TABLA TEMPORAL **********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
$SQL = "CREATE TABLE EXPORT_CALLEXT_".$DATE." as 
          SELECT DISTINCT ca.IDCARTERA IDCARTERA, ca.IDMOROSO IDMOROSO
            FROM CARTERA ca
            JOIN PERFILES pe ON ca.TIPOCARTERA=pe.TIPOCARTERA AND ca.TIPOPROCESO=pe.TIPOPROCESO
            JOIN SUCURSALES su ON ca.SUCURSAL=su.SUCURSAL
            JOIN DISTRIBUCION di ON ca.IDCARTERA=di.IDCARTERA 
             AND di.IDLOTE=1
             AND di.IDUSUARIO in ($IDUSUARIO)
            JOIN CARTERAESTADOS ce ON ce.ESTADO = (CASE WHEN ca.FECHAPROCESO < ((SELECT MAX(FECHAPROCESO)FECHAPROCESO FROM CARTERA)) OR di.IDPERFIL<>pe.IDPERFIL THEN 5 ELSE ca.ESTADO END)
             AND (CASE WHEN ca.FECHAPROCESO < ((SELECT MAX(FECHAPROCESO)FECHAPROCESO FROM CARTERA)) OR di.IDPERFIL<>pe.IDPERFIL THEN 5 ELSE ca.ESTADO END) <> 5";
mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." TABLE: EXPORT_CALLEXT_".$DATE.(($result) ? "" : "\n".mysql_error($conn)));
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/********** TABLA TEMPORAL **********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/********** CARTERA **********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// open file
$fp = fopen($PATH.$FILE['CARTERA'], "w");
savelog(((!$fp) ? 'ERR FILE: ':'OK FILE: ').$PATH.$FILE['CARTERA']);
// get query
$SQL = "SELECT LPAD(IFNULL(ca.IDCARTERA,''),11,' ') IDCARTERA,
               LPAD(IFNULL(ca.IDMOROSO,''),11,' ') IDMOROSO,
               RPAD(SUBSTRING(IFNULL(ca.SUCURSAL,''),1,10),10,' ') SUCURSAL,
               LPAD(IFNULL(ca.NUMCREDITO,''),7,' ') NUMCREDITO,
               RPAD(SUBSTRING(IFNULL(ca.CATEGORIA,''),1,11),11,' ') CATEGORIA,
               LPAD(TRUNCATE(IFNULL((ca.SALDO+ca.SALDOBCO+ca.SALDOPESOS),'0.00'),2),13,'0') SALDOTOMAR,
               RPAD(SUBSTRING(IFNULL(pe.PERFIL,''),1,50),50,' ') PERFIL,
               RPAD(SUBSTRING(IFNULL(ca.TIPOPROCESO,''),1,10),10,' ') TIPOPROCESO,
               RPAD(SUBSTRING(IFNULL(ca.TIPOCREDITO,''),1,10),10,' ') TIPOCREDITO,
               LPAD(IFNULL(ca.FECHAAGENDA,''),19,' ') FECHAAGENDA,
               LPAD(IFNULL(ca.FECHAPROCESO,''),19,' ') FECHAPROCESO,
               LPAD(IFNULL(ce.ESTADO,''),11,' ') CARTERAESTADO,
               RPAD(SUBSTRING(IFNULL(ce.DETALLE,''),1,50),50,' ') DETALLEESTADO,
               LPAD(IFNULL(ce.ORDEN,''),11,' ') ORDEN
          FROM CARTERA ca
          JOIN PERFILES pe ON ca.TIPOCARTERA=pe.TIPOCARTERA AND ca.TIPOPROCESO=pe.TIPOPROCESO
          JOIN SUCURSALES su ON ca.SUCURSAL=su.SUCURSAL
          JOIN DISTRIBUCION di ON ca.IDCARTERA=di.IDCARTERA 
           AND di.IDLOTE=1
           AND di.SUCURSAL IN (SELECT a.IDSUCURSAL FROM USUARIOPERFILES a WHERE a.IDUSUARIO in ($IDUSUARIO))
          JOIN CARTERAESTADOS ce ON ce.ESTADO = (CASE WHEN ca.FECHAPROCESO < ((SELECT MAX(FECHAPROCESO)FECHAPROCESO FROM CARTERA)) OR di.IDPERFIL<>pe.IDPERFIL THEN 5 ELSE ca.ESTADO END)
           AND (CASE WHEN ca.FECHAPROCESO < ((SELECT MAX(FECHAPROCESO)FECHAPROCESO FROM CARTERA)) OR di.IDPERFIL<>pe.IDPERFIL THEN 5 ELSE ca.ESTADO END) <> 5";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." QUERY: CARTERA".(($result) ? "" : "\n".mysql_error($conn)));
while($rs = mysql_fetch_array($result)) {
  // clear line buffer
  $buffer = "";
  $buffer.= $rs['IDCARTERA'];
  $buffer.= $rs['IDMOROSO'];
  $buffer.= $rs['SUCURSAL'];
  $buffer.= $rs['NUMCREDITO'];
  $buffer.= $rs['CATEGORIA'];
  $buffer.= $rs['SALDOTOMAR'];
  $buffer.= $rs['PERFIL'];
  $buffer.= $rs['TIPOPROCESO'];
  $buffer.= $rs['TIPOCREDITO'];
  $buffer.= $rs['FECHAAGENDA'];
  $buffer.= $rs['FECHAPROCESO'];
  $buffer.= $rs['CARTERAESTADO'];
  $buffer.= $rs['DETALLEESTADO'];
  $buffer.= $rs['ORDEN'];
  $buffer.= "\n";
  // write line buffer to file
  fwrite($fp, $buffer);
}
// close file
fclose($fp);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/********** CARTERA **********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/********** MOROSOS **********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// open file
$fp = fopen($PATH.$FILE['MOROSOS'], "w");
savelog(((!$fp) ? 'ERR FILE: ':'OK FILE: ').$PATH.$FILE['MOROSOS']);
// get query
$SQL = "SELECT DISTINCT
               LPAD(IFNULL(co.IDCONTACTO,''),11,' ') IDCONTACTO,
               RPAD(SUBSTRING(IFNULL(co.NOMBRE,''),1,255),255,' ') NOMBRE,
               LPAD(IFNULL(co.DOCUMENTONUMERO,''),11,' ') DOCUMENTONUMERO,
               LPAD(IFNULL(co.FECHAALTA,''),19,' ') FECHAALTA,
               LPAD(IFNULL(co.FECHABAJA,''),19,' ') FECHABAJA,
               IFNULL(co.ESTADO,'0') ESTADO
          FROM CONTACTOS co
          JOIN EXPORT_CALLEXT_".$DATE." tmp ON tmp.IDMOROSO = co.IDCONTACTO";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." QUERY: MOROSOS".(($result) ? "" : "\n".mysql_error($conn)));
while($rs = mysql_fetch_array($result)) {
  // clear line buffer
  $buffer = "";
  $buffer.= $rs['IDCONTACTO'];
  $buffer.= $rs['NOMBRE'];
  $buffer.= $rs['DOCUMENTONUMERO'];
  $buffer.= $rs['FECHAALTA'];
  $buffer.= $rs['FECHABAJA'];
  $buffer.= $rs['ESTADO'];
  $buffer.= "\n";
  // write line buffer to file
  fwrite($fp, $buffer);
}
// close file
fclose($fp);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/********** MOROSOS **********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/********* TELEFONOS *********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// open file
$fp = fopen($PATH.$FILE['TELEFONOS'], "w");
savelog(((!$fp) ? 'ERR FILE: ':'OK FILE: ').$PATH.$FILE['TELEFONOS']);
// get query
$SQL = "SELECT DISTINCT
               LPAD(IFNULL(t.IDCONTACTO,''),11,' ') IDCONTACTO,
               LPAD(IFNULL(t.IDTELEFONO,''),11,' ') IDTELEFONO,
               LPAD(IFNULL(t.CODIGOAREA,''),11,' ') CODIGOAREA,
               LPAD(IFNULL(t.NUMERO,''),11,' ') NUMERO,
               LPAD(IFNULL(t.INTERNO,''),11,' ') INTERNO,
               RPAD(SUBSTRING(IFNULL(t.HORARIO,''),1,255),255,' ') HORARIO,
               RPAD(SUBSTRING(IFNULL(t.COMENTARIOS,''),1,255),255,' ') COMENTARIOS,
               LPAD(IFNULL(t.FECHAALTA,''),19,' ') FECHAALTA,
               LPAD(IFNULL(t.FECHABAJA,''),19,' ') FECHABAJA,
               IFNULL(t.ESTADO,'0') ESTADO
          FROM TELEFONOS t
          JOIN EXPORT_CALLEXT_".$DATE." tmp ON tmp.IDMOROSO = t.IDCONTACTO
         WHERE t.EXT != '".$EXT."'";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." QUERY: TELEFONOS".(($result) ? "" : "\n".mysql_error($conn)));
while($rs = mysql_fetch_array($result)) {
  // clear line buffer
  $buffer = "";
  $buffer.= $rs['IDCONTACTO'];
  $buffer.= $rs['IDTELEFONO'];
  $buffer.= $rs['CODIGOAREA'];
  $buffer.= $rs['NUMERO'];
  $buffer.= $rs['INTERNO'];
  $buffer.= $rs['HORARIO'];
  $buffer.= $rs['COMENTARIOS'];
  $buffer.= $rs['FECHAALTA'];
  $buffer.= $rs['FECHABAJA'];
  $buffer.= $rs['ESTADO'];
  $buffer.= "\n";
  // write line buffer to file
  fwrite($fp, $buffer);
}
// close file
fclose($fp);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/********* TELEFONOS *********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/******** REFERENCIAS ********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// open file
$fp = fopen($PATH.$FILE['REFERENCIAS'], "w");
savelog(((!$fp) ? 'ERR FILE: ':'OK FILE: ').$PATH.$FILE['REFERENCIAS']);
// get query
$SQL = "SELECT DISTINCT
               LPAD(IFNULL(r.IDCONTACTO,''),11,' ') IDCONTACTO,
               LPAD(IFNULL(r.IDREFERENCIA,''),11,' ') IDREFERENCIA,
               RPAD(SUBSTRING(IFNULL(r.REFERENCIA,''),1,255),255,' ') REFERENCIA,
               RPAD(SUBSTRING(IFNULL(r.TELEFONO,''),1,255),255,' ') TELEFONO,
               LPAD(IFNULL(r.FECHAALTA,''),19,' ') FECHAALTA,
               LPAD(IFNULL(r.FECHABAJA,''),19,' ') FECHABAJA,
               IFNULL(r.ESTADO,'0') ESTADO
          FROM REFERENCIAS r
          JOIN EXPORT_CALLEXT_".$DATE." tmp ON tmp.IDMOROSO = r.IDCONTACTO
         WHERE r.EXT != '".$EXT."'";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." QUERY: REFERENCIAS".(($result) ? "" : "\n".mysql_error($conn)));
while($rs = mysql_fetch_array($result)) {
  // clear line buffer
  $buffer = "";
  $buffer.= $rs['IDCONTACTO'];
  $buffer.= $rs['IDREFERENCIA'];
  $buffer.= $rs['REFERENCIA'];
  $buffer.= $rs['TELEFONO'];
  $buffer.= $rs['FECHAALTA'];
  $buffer.= $rs['FECHABAJA'];
  $buffer.= $rs['ESTADO'];
  $buffer.= "\n";
  // write line buffer to file
  fwrite($fp, $buffer);
}
// close file
fclose($fp);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/******** REFERENCIAS ********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/******** DIRECCIONES ********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// open file
$fp = fopen($PATH.$FILE['DIRECCIONES'], "w");
savelog(((!$fp) ? 'ERR FILE: ':'OK FILE: ').$PATH.$FILE['DIRECCIONES']);
// get query
$SQL = "SELECT DISTINCT
               LPAD(IFNULL(d.IDCONTACTO,''),11,' ') IDCONTACTO,
               LPAD(IFNULL(d.IDDIRECCION,''),11,' ') IDDIRECCION,
               RPAD(SUBSTRING(IFNULL(d.CALLE,''),1,255),255,' ') CALLE,
               RPAD(SUBSTRING(IFNULL(d.NUMERO,''),1,255),255,' ') NUMERO,
               LPAD(IFNULL(d.PISO,''),11,' ') PISO,
               RPAD(SUBSTRING(IFNULL(d.DEPTO,''),1,255),20,' ') DEPTO,
               RPAD(SUBSTRING(IFNULL(d.LOCALIDAD,''),1,255),20,' ') LOCALIDAD,
               RPAD(SUBSTRING(IFNULL(d.CODIGOPOSTAL,''),1,255),20,' ') CODIGOPOSTAL,
               LPAD(IFNULL(d.FECHAALTA,''),19,' ') FECHAALTA,
               LPAD(IFNULL(d.FECHABAJA,''),19,' ') FECHABAJA,
               IFNULL(d.ESTADO,'0') ESTADO
        FROM DIRECCIONES d
        JOIN EXPORT_CALLEXT_".$DATE." tmp ON tmp.IDMOROSO = d.IDCONTACTO
       WHERE d.EXT != '".$EXT."'";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." QUERY: DIRECCIONES".(($result) ? "" : "\n".mysql_error($conn)));
while($rs = mysql_fetch_array($result)) {
  // clear line buffer
  $buffer = "";
  $buffer.= $rs['IDCONTACTO'];
  $buffer.= $rs['IDDIRECCION'];
  $buffer.= $rs['CALLE'];
  $buffer.= $rs['NUMERO'];
  $buffer.= $rs['PISO'];
  $buffer.= $rs['DEPTO'];
  $buffer.= $rs['LOCALIDAD'];
  $buffer.= $rs['CODIGOPOSTAL'];
  $buffer.= $rs['FECHAALTA'];
  $buffer.= $rs['FECHABAJA'];
  $buffer.= $rs['ESTADO'];
  $buffer.= "\n";
  // write line buffer to file
  fwrite($fp, $buffer);
}
// close file
fclose($fp);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/******** DIRECCIONES ********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/********** EMPLEOS **********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// open file
$fp = fopen($PATH.$FILE['EMPLEOS'], "w");
savelog(((!$fp) ? 'ERR FILE: ':'OK FILE: ').$PATH.$FILE['EMPLEOS']);
// get query
$SQL = "SELECT DISTINCT
               LPAD(IFNULL(e.IDCONTACTO,''),11,' ') IDCONTACTO,
               LPAD(IFNULL(e.IDEMPLEO,''),11,' ') IDEMPLEO,
               RPAD(SUBSTRING(IFNULL(e.EMPRESA,''),1,255),255,' ') EMPRESA,
               RPAD(SUBSTRING(IFNULL(e.SECCION,''),1,255),255,' ') SECCION,
               RPAD(SUBSTRING(IFNULL(e.DIRCALLE,''),1,255),255,' ') DIRCALLE,
               RPAD(SUBSTRING(IFNULL(e.DIRNUMERO,''),1,255),255,' ') DIRNUMERO,
               LPAD(IFNULL(e.DIRPISO,''),11,' ') DIRPISO,
               RPAD(SUBSTRING(IFNULL(e.DIRDEPTO,''),1,255),255,' ') DIRDEPTO,
               RPAD(SUBSTRING(IFNULL(e.DIRLOCALIDAD,''),1,255),255,' ') DIRLOCALIDAD,
               RPAD(SUBSTRING(IFNULL(e.DIRCODIGOPOSTAL,''),1,255),255,' ') DIRCODIGOPOSTAL,
               LPAD(IFNULL(e.TELCODIGOAREA,''),11,' ') TELCODIGOAREA,
               LPAD(IFNULL(e.TELNUMERO,''),11,' ') TELNUMERO,
               LPAD(IFNULL(e.TELINTERNO,''),11,' ') TELINTERNO,
               LPAD(IFNULL(e.FECHAALTA,''),19,' ') FECHAALTA,
               LPAD(IFNULL(e.FECHABAJA,''),19,' ') FECHABAJA,
               IFNULL(e.ESTADO,'0') ESTADO
        FROM EMPLEOS e
        JOIN EXPORT_CALLEXT_".$DATE." tmp ON tmp.IDMOROSO = e.IDCONTACTO
       WHERE e.EXT != '".$EXT."'";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." QUERY: EMPLEOS".(($result) ? "" : "\n".mysql_error($conn)));
while($rs = mysql_fetch_array($result)) {
  // clear line buffer
  $buffer = "";
  $buffer.= $rs['IDCONTACTO'];
  $buffer.= $rs['IDEMPLEO'];
  $buffer.= $rs['EMPRESA'];
  $buffer.= $rs['SECCION'];
  $buffer.= $rs['DIRCALLE'];
  $buffer.= $rs['DIRNUMERO'];
  $buffer.= $rs['DIRPISO'];
  $buffer.= $rs['DIRDEPTO'];
  $buffer.= $rs['DIRLOCALIDAD'];
  $buffer.= $rs['DIRCODIGOPOSTAL'];
  $buffer.= $rs['TELCODIGOAREA'];
  $buffer.= $rs['TELNUMERO'];
  $buffer.= $rs['TELINTERNO'];
  $buffer.= $rs['FECHAALTA'];
  $buffer.= $rs['FECHABAJA'];
  $buffer.= $rs['ESTADO'];
  $buffer.= "\n";
  // write line buffer to file
  fwrite($fp, $buffer);
}
// close file
fclose($fp);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/********** EMPLEOS **********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/********** CREDITOS *********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// open file
$fp = fopen($PATH.$FILE['CREDITOS'], "w");
savelog(((!$fp) ? 'ERR FILE: ':'OK FILE: ').$PATH.$FILE['CREDITOS']);
// get query
$SQL = "SELECT LPAD(IFNULL(ca.IDCARTERA,''),11,' ') IDCARTERA,
               LPAD(IFNULL(ca.SUCURSAL,''),11,' ') SUCURSAL,
               LPAD(IFNULL(ca.NUMCREDITO,''),7,' ') NUMCREDITO,
               RPAD(SUBSTR(IFNULL(pe.PERFIL,''),1,50),50,' ') PERFIL,
               LPAD(IFNULL((-DATEDIFF(DATE_ADD(FECHA, INTERVAL CASE WHEN TIPOCREDITO IN ('BI','BY') THEN ULTIMACUOTA*2 ELSE ULTIMACUOTA END MONTH),CURRENT_DATE())),''),11,' ') DIASC,
               LPAD(SUBSTR(IFNULL((DATE_FORMAT(DATE_ADD(FECHA, INTERVAL CASE WHEN TIPOCREDITO IN ('BI','BY') THEN ULTIMACUOTA*2 ELSE ULTIMACUOTA END MONTH),'%d/%m/%y')),''),1,8),8,' ') FECVEN,
               LPAD(IFNULL(ca.CUOTAS,''),2,' ') CUOTAS,
               LPAD(IFNULL(ca.ULTIMACUOTA,''),2,' ') ULTIMACUOTA,
               LPAD(IFNULL(ca.IMPORTE,''),13,' ') IMPORTE,
               LPAD(IFNULL((ca.SALDO+ca.SALDOBCO+ca.SALDOPESOS),''),13,' ') SALDOTOMAR,
               LPAD(IFNULL((CASE WHEN TIPOCREDITO='BI' OR TIPOCREDITO = 'BY' OR TIPOCREDITO = 'AP' THEN '' ELSE ROUND(pr.PORCENTAJE*ca.IMPORTE/100,2) END),''),13,' ') PRORROGA,
               LPAD(IFNULL((CASE WHEN (-DATEDIFF(DATE_ADD(FECHA, INTERVAL CASE WHEN TIPOCREDITO IN ('BI','BY') THEN ULTIMACUOTA*2 ELSE ULTIMACUOTA END MONTH),CURRENT_DATE())) < 31 THEN (CASE WHEN (CEIL(((-DATEDIFF(DATE_ADD(FECHA, INTERVAL CASE WHEN TIPOCREDITO IN ('BI','BY') THEN ULTIMACUOTA*2 ELSE ULTIMACUOTA END MONTH),CURRENT_DATE()))-1)*0.005*IMPORTE)) < 3 THEN 3 ELSE (CEIL(((-DATEDIFF(DATE_ADD(FECHA, INTERVAL CASE WHEN TIPOCREDITO IN ('BI','BY') THEN ULTIMACUOTA*2 ELSE ULTIMACUOTA END MONTH),CURRENT_DATE()))-1)*0.005*IMPORTE)) + 3 END) ELSE (CEIL(((-DATEDIFF(DATE_ADD(FECHA, INTERVAL CASE WHEN TIPOCREDITO IN ('BI','BY') THEN ULTIMACUOTA*2 ELSE ULTIMACUOTA END MONTH),CURRENT_DATE()))-1)*0.005*IMPORTE)) + 15 END),''),10,' ') PUNITORIO,
               RPAD(SUBSTR(IFNULL(ce.DETALLE,''),1,50),50,' ') ESTADOCE
          FROM CARTERA ca 
          JOIN EXPORT_CALLEXT_".$DATE." tmp ON tmp.IDCARTERA = ca.IDCARTERA
          JOIN PERFILES pe ON ca.TIPOCARTERA=pe.TIPOCARTERA AND ca.TIPOPROCESO=pe.TIPOPROCESO
          LEFT JOIN DISTRIBUCION di ON di.IDCARTERA=ca.IDCARTERA
          LEFT JOIN USUARIOS us ON us.IDUSUARIO=di.IDUSUARIO
          JOIN PRORROGAS pr ON ca.AUXNUEVO=pr.PRORROGAS
          JOIN CARTERAESTADOS ce ON ce.ESTADO = (CASE WHEN ca.FECHAPROCESO < ((SELECT MAX(FECHAPROCESO)FECHAPROCESO FROM CARTERA)) OR di.IDPERFIL<>pe.IDPERFIL THEN 5 ELSE ca.ESTADO END)
           AND (CASE WHEN ca.FECHAPROCESO < ((SELECT MAX(FECHAPROCESO)FECHAPROCESO FROM CARTERA)) OR di.IDPERFIL<>pe.IDPERFIL THEN 5 ELSE ca.ESTADO END) <> 5
        UNION
        SELECT LPAD(IFNULL(ca.IDCARTERA,''),11,' ') IDCARTERA,
               LPAD(IFNULL(ca.SUCURSAL,''),11,' ') SUCURSAL,
               LPAD(IFNULL(ca.NUMCREDITO,''),7,' ') NUMCREDITO,
               RPAD(SUBSTR(IFNULL(pe.PERFIL,''),1,50),50,' ') PERFIL,
               LPAD(IFNULL((-DATEDIFF(DATE_ADD(FECHA, INTERVAL CASE WHEN TIPOCREDITO IN ('BI','BY') THEN ULTIMACUOTA*2 ELSE ULTIMACUOTA END MONTH),CURRENT_DATE())),''),11,' ') DIASC,
               RPAD(SUBSTR(IFNULL((DATE_FORMAT(DATE_ADD(FECHA, INTERVAL CASE WHEN TIPOCREDITO IN ('BI','BY') THEN ULTIMACUOTA*2 ELSE ULTIMACUOTA END MONTH),'%d/%m/%y')),''),1,8),8,' ') FECVEN,
               LPAD(IFNULL(ca.CUOTAS,''),2,' ') CUOTAS,
               LPAD(IFNULL(ca.ULTIMACUOTA,''),2,' ') ULTIMACUOTA,
               LPAD(IFNULL(ca.IMPORTE,''),13,' ') IMPORTE,
               LPAD(IFNULL((ca.SALDO + ca.SALDOBCO + ca.SALDOPESOS),''),13,' ') SALDOTOMAR,
               LPAD(IFNULL((CASE WHEN TIPOCREDITO='BI' OR TIPOCREDITO = 'BY' OR TIPOCREDITO = 'AP' THEN '-' ELSE ROUND(pr.PORCENTAJE*ca.IMPORTE/100,2) END),''),13,' ') PRORROGA,
               LPAD(IFNULL((CASE WHEN (-DATEDIFF(DATE_ADD(FECHA, INTERVAL CASE WHEN TIPOCREDITO IN ('BI','BY') THEN ULTIMACUOTA*2 ELSE ULTIMACUOTA END MONTH),CURRENT_DATE())) < 31 THEN (CASE WHEN (CEIL(((-DATEDIFF(DATE_ADD(FECHA, INTERVAL CASE WHEN TIPOCREDITO IN ('BI','BY') THEN ULTIMACUOTA*2 ELSE ULTIMACUOTA END MONTH),CURRENT_DATE()))-1)*0.005*IMPORTE)) < 3 THEN 3 ELSE (CEIL(((-DATEDIFF(DATE_ADD(FECHA, INTERVAL CASE WHEN TIPOCREDITO IN ('BI','BY') THEN ULTIMACUOTA*2 ELSE ULTIMACUOTA END MONTH),CURRENT_DATE()))-1)*0.005*IMPORTE)) + 3 END) ELSE (CEIL(((-DATEDIFF(DATE_ADD(FECHA, INTERVAL CASE WHEN TIPOCREDITO IN ('BI','BY') THEN ULTIMACUOTA*2 ELSE ULTIMACUOTA END MONTH),CURRENT_DATE()))-1)*0.005*IMPORTE)) + 15 END),''),10,' ') PUNITORIO,
               RPAD(SUBSTR(IFNULL(ce.DETALLE,''),1,50),50,' ') ESTADOCE
        FROM (SELECT ca.* 
                FROM CARTERA ca
                JOIN DISTRIBUCION di ON ca.IDCARTERA=di.IDCARTERA
                JOIN LOTES lt ON lt.IDLOTE=di.IDLOTE AND lt.ACTUAL=1
                JOIN EXPORT_CALLEXT_".$DATE." tmp ON tmp.IDCARTERA <> ca.IDCARTERA AND tmp.IDMOROSO = ca.IDMOROSO) ca
        JOIN PERFILES pe ON ca.TIPOCARTERA=pe.TIPOCARTERA AND ca.TIPOPROCESO=pe.TIPOPROCESO
        JOIN PRORROGAS pr ON ca.AUXNUEVO=pr.PRORROGAS
        LEFT JOIN DISTRIBUCION di ON di.IDCARTERA=ca.IDCARTERA
        LEFT JOIN USUARIOS us ON us.IDUSUARIO=di.IDUSUARIO
          JOIN CARTERAESTADOS ce ON ce.ESTADO = (CASE WHEN ca.FECHAPROCESO < ((SELECT MAX(FECHAPROCESO)FECHAPROCESO FROM CARTERA)) OR di.IDPERFIL<>pe.IDPERFIL THEN 5 ELSE ca.ESTADO END)
           AND (CASE WHEN ca.FECHAPROCESO < ((SELECT MAX(FECHAPROCESO)FECHAPROCESO FROM CARTERA)) OR di.IDPERFIL<>pe.IDPERFIL THEN 5 ELSE ca.ESTADO END) <> 5";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." QUERY: CREDITOS".(($result) ? "" : "\n".mysql_error($conn)));
while($rs = mysql_fetch_array($result)) {
  // clear line buffer
  $buffer = "";
  $buffer.= $rs['IDCARTERA'];
  $buffer.= $rs['SUCURSAL'];
  $buffer.= $rs['NUMCREDITO'];
  $buffer.= $rs['PERFIL'];
  $buffer.= $rs['DIASC'];
  $buffer.= $rs['FECVEN'];
  $buffer.= $rs['CUOTAS'];
  $buffer.= $rs['ULTIMACUOTA'];
  $buffer.= $rs['IMPORTE'];
  $buffer.= $rs['SALDOTOMAR'];
  $buffer.= $rs['PRORROGA'];
  $buffer.= $rs['PUNITORIO'];
  $buffer.= $rs['ESTADOCE'];
  $buffer.= "\n";
  // write line buffer to file
  fwrite($fp, $buffer);
}
// close file
fclose($fp);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/********** CREDITOS *********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/******** PRODUCTOS **********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// open file
$fp = fopen($PATH.$FILE['PRODUCTOS'], "w");
savelog(((!$fp) ? 'ERR FILE: ':'OK FILE: ').$PATH.$FILE['PRODUCTOS']);
// get query
$SQL = "SELECT LPAD(IFNULL(p.IDCARTERA,''),11,' ') IDCARTERA,
               LPAD(IFNULL(p.IDPRODUCTO,''),11,' ') IDPRODUCTO,
               RPAD(SUBSTR(IFNULL(p.PRODUCTO,''),1,255),255,' ') PRODUCTO
        FROM PRODUCTOS p
        JOIN EXPORT_CALLEXT_".$DATE." tmp ON tmp.IDCARTERA = p.IDCARTERA";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." QUERY: PRODUCTOS".(($result) ? "" : "\n".mysql_error($conn)));
while($rs = mysql_fetch_array($result)) {
  // clear line buffer
  $buffer = "";
  $buffer.= $rs['IDCARTERA'];
  $buffer.= $rs['IDPRODUCTO'];
  $buffer.= $rs['PRODUCTO'];
  $buffer.= "\n";
  // write line buffer to file
  fwrite($fp, $buffer);
}
// close file
fclose($fp);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/******** PRODUCTOS **********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/******* SEGUIMIENTO *********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// open file
$fp = fopen($PATH.$FILE['SEGUIMIENTO'], "w");
savelog(((!$fp) ? 'ERR FILE: ':'OK FILE: ').$PATH.$FILE['SEGUIMIENTO']);
// get query
$SQL = "SELECT LPAD(IFNULL(s.IDCARTERA,''),11,' ') IDCARTERA,
               LPAD(IFNULL(s.IDSEGUIMIENTO,''),11,' ') IDSEGUIMIENTO,
               RPAD(SUBSTR(IFNULL(s.TELEFONO,''),1,255),255,' ') TELEFONO,
               RPAD(SUBSTR(IFNULL(s.COMENTARIOS,''),1,8000),8000,' ') COMENTARIOS,
               LPAD(IFNULL(ca.NUMCREDITO,''),7,' ') NUMCREDITO,
               LPAD(IFNULL(s.FECHAALTA,''),19,' ') FECHAALTA,
               LPAD(IFNULL(s.FECHABAJA,''),19,' ') FECHABAJA,
               IFNULL(s.ESTADO,'0') ESTADO
        FROM SEGUIMIENTO s
        JOIN CARTERA ca ON s.IDCARTERA=ca.IDCARTERA
        JOIN CARTERA ce ON ca.IDMOROSO=ce.IDMOROSO AND ce.IDCARTERA=s.IDCARTERA
        JOIN EXPORT_CALLEXT_".$DATE." tmp ON tmp.IDCARTERA = s.IDCARTERA
       WHERE s.ESTADO=1
         AND s.EXT != '".$EXT."'";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." QUERY: SEGUIMIENTO".(($result) ? "" : "\n".mysql_error($conn)));
while($rs = mysql_fetch_array($result)) {
  // clear line buffer
  $buffer = "";
  $buffer.= $rs['IDCARTERA'];
  $buffer.= $rs['IDSEGUIMIENTO'];
  $buffer.= $rs['TELEFONO'];
  $buffer.= $rs['COMENTARIOS'];
  $buffer.= $rs['NUMCREDITO'];
  $buffer.= $rs['FECHAALTA'];
  $buffer.= $rs['FECHABAJA'];
  $buffer.= $rs['ESTADO'];
  $buffer.= "\n";
  // write line buffer to file
  fwrite($fp, $buffer);
}
// close file
fclose($fp);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/******* SEGUIMIENTO *********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/********** TABLA TEMPORAL **********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
$SQL = "DROP TABLE EXPORT_CALLEXT_".$DATE;
mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." DROP: EXPORT_CALLEXT_".$DATE.(($result) ? "" : "\n".mysql_error($conn)));
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/********** TABLA TEMPORAL **********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/*********** TAR *************/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// tar/gzip files
shell_exec("cd ".$PATH."; tar --remove-files -zcvf ".$DATE.".tar.gz  *_".$DATE.".txt");
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/*********** tar *************/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/*********** SFTP ************/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// connect to SFTP server (port 22)
if ($SFTP) {
  $sftp = new Net_SFTP($SFTPSERVER, $SFTPPORT);
  if (!$sftp->login($SFTPUSER, $SFTPPASS)) {
    savelog('ERR SFTP CONNECT: '.$SFTPSERVER);
    $SFTP = 0;
  } else {
    savelog('OK SFTP CONNECT: '.$SFTPSERVER);
    $sftp->chdir($SFTPPATH);
    savelog(((!$sftp->put($DATE.$ZIPEXT, $PATH.$DATE.$ZIPEXT, NET_SFTP_LOCAL_FILE)) ? 'ERR SFTP: ':'OK SFTP:').$DATE.$ZIPEXT);
  }
}
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/*********** SFTP ************/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/************ FTP ************/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// connect to FTP server (port 21)
if (!$SFTP) {
  $conn_id = ftp_connect($FTPSERVER, 21);
  savelog(((!$conn_id) ? 'ERR FTP: ':'OK FTP: ').$FTPSERVER);
  if ($conn_id) {
    // send access parameters
    ftp_login($conn_id, $FTPUSER, $FTPPASS);
    // turn on passive mode transfers
    ftp_pasv ($conn_id, true);
    $arr_size=count($FILE);
    // perform file upload
    $upload = ftp_put($conn_id, $FTPPATH.$DATE.$ZIPEXT, $PATH.$DATE.$ZIPEXT, FTP_BINARY);
    // check upload status:
    savelog(((!$upload) ? 'ERR FTP: ':'OK FTP: ').$DATE.$ZIPEXT);
    // close the FTP stream
    ftp_close($conn_id);
  }
}
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/************ FTP ************/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/*********** MOVE ************/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// move files to sent folder
savelog(((!rename($PATH.$DATE.$ZIPEXT, $SENTPATH.$DATE.$ZIPEXT)) ? 'ERR MOVE: ':'OK MOVE: ').$DATE.$ZIPEXT);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/*********** MOVE ************/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/

?>

