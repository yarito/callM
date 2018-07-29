<?
date_default_timezone_set ('America/Argentina/Buenos_Aires');

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/******** CUSTOM DATA ********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// EXT: Código de Call Center Externo
$EXT = "1";


// local path
$PATH = "/home/callcenter/interfaz/cc_externo/contactogarantido/in/";
$SENTPATH = "/home/callcenter/interfaz/cc_externo/contactogarantido/in/processed/";
$TARFILE = "fvg_";
// log
$LOG = "/home/callcenter/interfaz/cc_externo/contactogarantido/log/in.log";
// sftp
$SFTP = 1;
$SFTPSERVER = "201.216.221.82";
$SFTPPORT = "18024";
$SFTPUSER = "fvgdatos";
$SFTPPASS = "M0ntevide0";
$SFTPPATH = "/outbox/";
// ftp
$FTPSERVER = "ftp.contactogarantido.com";
$FTPUSER = "fravega";
$FTPPASS = "GestionCG2010";
$FTPPATH = "/outbox/";
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
$DATEINV = $DATE; //date("dmY",time());
// files
$FILE['TELEFONOS'] = "TELEFONOS_".$DATE.".txt";
$FILE['REFERENCIAS'] = "REFERENCIAS_".$DATE.".txt";
$FILE['DIRECCIONES'] = "DIRECCIONES_".$DATE.".txt";
$FILE['EMPLEOS'] = "EMPLEOS_".$DATE.".txt";
$FILE['SEGUIMIENTO'] = "SEGUIMIENTO_".$DATE.".txt";
// line length
$FILELENGTH['TELEFONOS'] = 605;
$FILELENGTH['REFERENCIAS'] = 572;
$FILELENGTH['DIRECCIONES'] = 1348;
$FILELENGTH['EMPLEOS'] = 1891;
$FILELENGTH['SEGUIMIENTO'] = 8324;


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
/*********** SFTP ************/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// connect to SFTP server (port 22)
if ($SFTP) {
  $sftp = new Net_SFTP($SFTPSERVER, $SFTPPORT);
  if (!$sftp->login($SFTPUSER, $SFTPPASS)) {
    savelog('ERR SFTP CONNECT: '.$SFTPSERVER);
    //$SFTP = 0;
  } else {
    savelog('OK SFTP CONNECT: '.$SFTPSERVER);
    $sftp->chdir($SFTPPATH);
    savelog(((!$sftp->get($TARFILE.$DATEINV.$ZIPEXT, $PATH.$TARFILE.$DATEINV.$ZIPEXT)) ? 'ERR SFTP: ':'OK SFTP:').$TARFILE.$DATEINV.$ZIPEXT);
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
    // change ftp dir
    ftp_chdir($conn_id,$FTPPATH);
    // change local dir
    $CURDIR = getcwd();
    chdir($PATH);
    // perform file download
    $upload = ftp_get($conn_id, $TARFILE.$DATEINV.$ZIPEXT, $TARFILE.$DATEINV.$ZIPEXT, FTP_BINARY);
    // check upload status:
    savelog(((!$upload) ? 'ERR FTP: ':'OK FTP: ').$TARFILE.$DATEINV.$ZIPEXT);
    // close the FTP stream
    ftp_close($conn_id);
    // change local dir
    chdir($CURDIR); 
  }
}
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/************ FTP ************/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/*********** TAR *************/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
shell_exec("rm -fR ".$PATH.$TARFILE.$DATEINV.";");
if (file_exists($PATH.$TARFILE.$DATEINV.$ZIPEXT)) {
  savelog("OK FILE EXISTS: ".$PATH.$TARFILE.$DATEINV.$ZIPEXT);
  // tar/gzip files
  shell_exec("cd ".$PATH."; mkdir ".$TARFILE.$DATEINV."; tar -xvf ".$TARFILE.$DATEINV.$ZIPEXT." -C ".$TARFILE.$DATEINV);
  shell_exec("mv -f ".$PATH.$TARFILE.$DATEINV.$ZIPEXT." ".$PATH."/proc;");
} else {
  savelog("ERR FILE NOT EXISTS: ".$PATH.$TARFILE.$DATEINV.$ZIPEXT);
  return;
}
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/*********** TAR *************/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/******* FILES EXIST *********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/*
foreach($FILE as $i => $v) {  
  if (file_exists($PATH.$TARFILE.$DATEINV."/".$FILE[$i])) {
    savelog("OK FILE EXISTS: ".$PATH.$TARFILE.$DATEINV."/".$FILE[$i]);
  } else {
    savelog("ERR FILE EXISTS: ".$PATH.$TARFILE.$DATEINV."/".$FILE[$i]);
    return;
  }
}
*/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/******* FILES EXIST *********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/****** LINES LENGTH *********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
foreach($FILE as $i => $v) {
  $tmpfile = $PATH.$TARFILE.$DATEINV."/".$FILE[$i];
  $tmphandle = @fopen($tmpfile, "r");
  $tmperror[$i] = 0;
  if ($tmphandle) {
    $cnt = 0;
    while (!feof($tmphandle) && !$tmperror[$i]) {
      $cnt++;
      $tmpline = str_replace(array("\n","\r\n"), '', fgets($tmphandle));
      if ($tmpline) {
        if (strlen($tmpline) != $FILELENGTH[$i]) $tmperror[$i] = strlen($tmpline);
      }
    }
  }
  fclose($tmphandle);
  if (!$tmperror[$i]) {
    savelog("OK LINES LENGHT [".$FILELENGTH[$i]."]: ".$PATH.$TARFILE.$DATEINV."/".$FILE[$i]);
  } else {
    savelog("ERR LINES LENGHT [".$FILELENGTH[$i]."/".$tmperror[$i]." - ".$cnt."]: ".$PATH.$TARFILE.$DATEINV."/".$FILE[$i]);
  }
}
foreach($tmperror as $i => $v) {
  if ($tmperror[$i]) {
    savelog("EXIT");
    return;
  }
}
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/****** LINES LENGTH *********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/************** TELEFONOS **************/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
if (file_exists($PATH.$TARFILE.$DATEINV."/".$FILE['TELEFONOS'])) {
  savelog("OK FILE EXISTS: ".$PATH.$TARFILE.$DATEINV."/".$FILE['TELEFONOS']);

// create temporary table
$SQL = "CREATE TEMPORARY TABLE IMPORT_CALLEXT_TELEFONOS_".$DATE." (
          IDTELEFONO int(11) unsigned NOT NULL,
          IDCONTACTO int(11) unsigned NOT NULL DEFAULT '0',
          TIPO int(11) unsigned NOT NULL DEFAULT '0',
          CODIGOAREA int(11) unsigned DEFAULT NULL,
          NUMERO int(11) unsigned DEFAULT NULL,
          INTERNO int(11) unsigned DEFAULT NULL,
          HORARIO varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          COMENTARIOS varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          FECHAALTA datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
          FECHABAJA datetime DEFAULT NULL,
          ESTADO int(1) unsigned NOT NULL DEFAULT '1',
          EXT int(1) unsigned NOT NULL DEFAULT '0',
          IDTELEFONOEXT int(11) unsigned NOT NULL DEFAULT '0',
          KEY TELEFONOESTADO (IDTELEFONO,ESTADO,EXT),
          KEY TELEFONOEXTESTADO (IDTELEFONOEXT,ESTADO,EXT),
          KEY CODIGOAREA (IDCONTACTO,CODIGOAREA,NUMERO),
          KEY IDCONTACTO (IDCONTACTO,FECHAALTA)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." CREATE TABLE: IMPORT_CALLEXT_TELEFONOS_".$DATE.(($result) ? "" : "\n".mysql_error($conn)));

// read file into temporary table
$file = $PATH.$TARFILE.$DATEINV."/".$FILE['TELEFONOS'];
$handle = @fopen($file, "r");
if ($handle) {
  $cnt = 0;
  $tot = 0;
  $imp = 0;
  while (!feof($handle)) {
    $cnt++;
    $line = str_replace("'","´",fgets($handle));
    if ($line) {
      $tot++;
      $TMP = null;
      $TMP['IDCONTACTO'] = substr($line,0,11);
      $TMP['IDTELEFONO'] = substr($line,11,11);
      $TMP['CODIGOAREA'] = substr($line,22,11);
      $TMP['NUMERO'] = substr($line,33,11);
      $TMP['INTERNO'] = substr($line,44,11);
      $TMP['HORARIO'] = substr($line,55,255);
      $TMP['COMENTARIOS'] = substr($line,310,255);
      $TMP['FECHAALTA'] = substr($line,565,19);
      $TMP['FECHABAJA'] = substr($line,584,19);
      $TMP['ESTADO'] = substr($line,603,1);
      $TMP['EXT'] = substr($line,604,1);
      // copy new files into temporary table
      if (!$TMP['ESTADO'] || $TMP['EXT'] == $EXT) { // only BAJA or EXT==$EXT 
        $SQL = "INSERT INTO IMPORT_CALLEXT_TELEFONOS_".$DATE." (IDCONTACTO,IDTELEFONO,CODIGOAREA,NUMERO,INTERNO,HORARIO,COMENTARIOS,FECHAALTA,FECHABAJA,ESTADO,EXT,IDTELEFONOEXT)
                                                        values ('".$TMP['IDCONTACTO']."',
                                                                '".((!$TMP['EXT']) ? $TMP['IDTELEFONO'] : 0)."',
                                                                '".$TMP['CODIGOAREA']."',
                                                                '".$TMP['NUMERO']."',
                                                                '".$TMP['INTERNO']."',
                                                                '".$TMP['HORARIO']."',
                                                                '".$TMP['COMENTARIOS']."',
                                                                str_to_date('".$TMP['FECHAALTA']."','%Y-%m-%d %H:%i:%s'),
                                                                ".(($TMP['FECHABAJA']) ? "str_to_date('".$TMP['FECHABAJA']."','%Y-%m-%d %H:%i:%s')" : NULL).",
                                                                '".$TMP['ESTADO']."',
                                                                '".$TMP['EXT']."',
                                                                '".(($TMP['EXT']) ? $TMP['IDTELEFONO'] : 0)."')";
        $result = mysql_query($SQL, $conn);
        if (!$result) savelog("ERR LOAD RECORDS: IMPORT_CALLEXT_TELEFONOS_".$DATE."\n".mysql_error($conn)."\nLINE: ".$cnt."\n"); else $imp++;
      }
    }
  }
  fclose($handle);
  savelog("OK LOAD RECORDS: IMPORT_CALLEXT_TELEFONOS_".$DATE." [".$imp."/".$tot."]");
}

// update bajas EXT = 0
$SQL  = "UPDATE TELEFONOS t, IMPORT_CALLEXT_TELEFONOS_".$DATE." i
            SET t.FECHABAJA = i.FECHABAJA,
                t.ESTADO = i.ESTADO
          WHERE t.IDTELEFONO = i.IDTELEFONO
            AND i.ESTADO = 0
            AND i.EXT = 0";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." LOAD RECORDS: TELEFONOS (UPDATE BAJAS) [".mysql_affected_rows($conn)."]".(($result) ? "" : "\n".mysql_error($conn)));

// insert where EXT = $EXT
$SQL  = "INSERT INTO TELEFONOS (IDCONTACTO,TIPO,CODIGOAREA,NUMERO,INTERNO,HORARIO,COMENTARIOS,FECHAALTA,FECHABAJA,ESTADO,EXT,IDTELEFONOEXT)
  SELECT i.IDCONTACTO,i.TIPO,i.CODIGOAREA,i.NUMERO,i.INTERNO,i.HORARIO,i.COMENTARIOS,i.FECHAALTA,i.FECHABAJA,i.ESTADO,i.EXT,i.IDTELEFONOEXT
          FROM IMPORT_CALLEXT_TELEFONOS_".$DATE." i
          WHERE i.EXT = ".$EXT."
          AND NOT EXISTS (SELECT 1 FROM TELEFONOS t WHERE t.EXT = i.EXT AND t.IDTELEFONOEXT = i.IDTELEFONOEXT)";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." LOAD RECORDS: TELEFONOS (INSERT EXT) [".mysql_affected_rows($conn)."]".(($result) ? "" : "\n".mysql_error($conn)));

// update bajas EXT = $EXT
$SQL  = "UPDATE TELEFONOS t, IMPORT_CALLEXT_TELEFONOS_".$DATE." i
            SET t.FECHABAJA = i.FECHABAJA,
                t.ESTADO = 0
          WHERE t.IDTELEFONOEXT = i.IDTELEFONOEXT
            AND i.ESTADO = 0
            AND i.EXT = ".$EXT;
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." LOAD RECORDS: TELEFONOS (UPDATE BAJAS EXT) [".mysql_affected_rows($conn)."]".(($result) ? "" : "\n".mysql_error($conn)));

} else {
  savelog("ERR FILE EXISTS: ".$PATH.$TARFILE.$DATEINV."/".$FILE['TELEFONOS']);
}

/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/************** TELEFONOS **************/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/************* REFERENCIAS *************/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
if (file_exists($PATH.$TARFILE.$DATEINV."/".$FILE['REFERENCIAS'])) {
  savelog("OK FILE EXISTS: ".$PATH.$TARFILE.$DATEINV."/".$FILE['REFERENCIAS']);

// create temporary table
$SQL = "CREATE TEMPORARY TABLE IMPORT_CALLEXT_REFERENCIAS_".$DATE." (
          IDREFERENCIA int(11) unsigned NOT NULL,

          IDCONTACTO int(11) unsigned NOT NULL DEFAULT '0',
          REFERENCIA varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          TELEFONO varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          FECHAALTA datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
          FECHABAJA datetime DEFAULT NULL,
          ESTADO int(1) unsigned NOT NULL DEFAULT '1',
          EXT int(1) unsigned NOT NULL DEFAULT '0',
          IDREFERENCIAEXT int(11) unsigned NOT NULL DEFAULT '0',
          KEY REFERENCIAESTADO (IDREFERENCIA,ESTADO,EXT),
          KEY REFERENCIAEXTESTADO (IDREFERENCIAEXT,ESTADO,EXT),
          KEY IDCONTACTO (IDCONTACTO,FECHAALTA)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." CREATE TABLE: IMPORT_CALLEXT_REFERENCIAS_".$DATE.(($result) ? "" : "\n".mysql_error($conn)));

// read file into temporary table
$file = $PATH.$TARFILE.$DATEINV."/".$FILE['REFERENCIAS'];
$handle = @fopen($file, "r");
if ($handle) {
  $cnt = 0;
  $tot = 0;
  $imp = 0;
  while (!feof($handle)) {
    $cnt++;
    $line = str_replace("'","´",fgets($handle));
    if ($line) {
      $tot++;
      $TMP = null;
      $TMP['IDCONTACTO'] = substr($line,0,11);
      $TMP['IDREFERENCIA'] = substr($line,11,11);
      $TMP['REFERENCIA'] = substr($line,22,255);
      $TMP['TELEFONO'] = substr($line,277,255);
      $TMP['FECHAALTA'] = substr($line,532,19);
      $TMP['FECHABAJA'] = substr($line,551,19);
      $TMP['ESTADO'] = substr($line,570,1);
      $TMP['EXT'] = substr($line,571,1);
      // copy new files into temporary table
      if (!$TMP['ESTADO'] || $TMP['EXT'] == $EXT) { // only BAJA or EXT==$EXT
        $SQL = "INSERT INTO IMPORT_CALLEXT_REFERENCIAS_".$DATE." (IDCONTACTO,IDREFERENCIA,REFERENCIA,TELEFONO,FECHAALTA,FECHABAJA,ESTADO,EXT,IDREFERENCIAEXT)
                                                        values ('".$TMP['IDCONTACTO']."',
                                                                '".((!$TMP['EXT']) ? $TMP['IDREFERENCIA'] : 0)."',
                                                                '".$TMP['REFERENCIA']."',
                                                                '".$TMP['TELEFONO']."',
                                                                str_to_date('".$TMP['FECHAALTA']."','%Y-%m-%d %H:%i:%s'),
                                                                ".(($TMP['FECHABAJA']) ? "str_to_date('".$TMP['FECHABAJA']."','%Y-%m-%d %H:%i:%s')" : NULL).",
                                                                '".$TMP['ESTADO']."',
                                                                '".$TMP['EXT']."',
                                                                '".(($TMP['EXT']) ? $TMP['IDREFERENCIA'] : 0)."')";
        $result = mysql_query($SQL, $conn);
        if (!$result) savelog("ERR LOAD RECORDS: IMPORT_CALLEXT_REFERENCIAS_".$DATE."\n".mysql_error($conn)."\nLINE: ".$cnt."\n"); else $imp++;
      }
    }
  }
  fclose($handle);
  savelog("OK LOAD RECORDS: IMPORT_CALLEXT_REFERENCIAS_".$DATE." [".$imp."/".$tot."]");
}

// update bajas EXT = 0
$SQL  = "UPDATE REFERENCIAS t, IMPORT_CALLEXT_REFERENCIAS_".$DATE." i
            SET t.FECHABAJA = i.FECHABAJA,
                t.ESTADO = i.ESTADO
          WHERE t.IDREFERENCIA = i.IDREFERENCIA
            AND i.ESTADO = 0
            AND i.EXT = 0";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." LOAD RECORDS: REFERENCIAS (UPDATE BAJAS) [".mysql_affected_rows($conn)."]".(($result) ? "" : "\n".mysql_error($conn)));

// insert where EXT = $EXT
$SQL  = "INSERT INTO REFERENCIAS (IDCONTACTO,REFERENCIA,TELEFONO,FECHAALTA,FECHABAJA,ESTADO,EXT,IDREFERENCIAEXT)
  SELECT i.IDCONTACTO,i.REFERENCIA,i.TELEFONO,i.FECHAALTA,i.FECHABAJA,i.ESTADO,i.EXT,i.IDREFERENCIAEXT
          FROM IMPORT_CALLEXT_REFERENCIAS_".$DATE." i
          WHERE i.EXT = ".$EXT."
          AND NOT EXISTS (SELECT 1 FROM REFERENCIAS t WHERE t.EXT = i.EXT AND t.IDREFERENCIAEXT = i.IDREFERENCIAEXT)";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." LOAD RECORDS: REFERENCIAS (INSERT EXT) [".mysql_affected_rows($conn)."]".(($result) ? "" : "\n".mysql_error($conn)));

// update bajas EXT = $EXT
$SQL  = "UPDATE REFERENCIAS t, IMPORT_CALLEXT_REFERENCIAS_".$DATE." i
            SET t.FECHABAJA = i.FECHABAJA,
                t.ESTADO = 0
          WHERE t.IDREFERENCIAEXT = i.IDREFERENCIAEXT
            AND i.ESTADO = 0
            AND i.EXT = ".$EXT;
//mysql_query($SQL, $conn);
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." LOAD RECORDS: REFERENCIAS (UPDATE BAJAS EXT) [".mysql_affected_rows($conn)."]".(($result) ? "" : "\n".mysql_error($conn)));

} else {
  savelog("ERR FILE EXISTS: ".$PATH.$TARFILE.$DATEINV."/".$FILE['REFERENCIAS']);
}

/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/************* REFERENCIAS *************/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/************* DIRECCIONES *************/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
if (file_exists($PATH.$TARFILE.$DATEINV."/".$FILE['DIRECCIONES'])) {
  savelog("OK FILE EXISTS: ".$PATH.$TARFILE.$DATEINV."/".$FILE['DIRECCIONES']);

// create temporary table
$SQL = "CREATE TEMPORARY TABLE IMPORT_CALLEXT_DIRECCIONES_".$DATE." (
          IDDIRECCION int(11) unsigned NOT NULL,
          IDCONTACTO int(11) unsigned NOT NULL DEFAULT '0',
          CALLE varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          NUMERO varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          PISO int(11) unsigned DEFAULT NULL,
          DEPTO varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          LOCALIDAD varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          PROVINCIA varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          CODIGOPOSTAL varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          PLANO varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          COORDENADAS varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          FECHAALTA datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
          FECHABAJA datetime DEFAULT NULL,
          ESTADO int(1) unsigned NOT NULL DEFAULT '1',
          EXT int(1) unsigned NOT NULL DEFAULT '0',
          IDDIRECCIONEXT int(11) unsigned NOT NULL DEFAULT '0',
          KEY DIRECCIONESTADO (IDDIRECCION,ESTADO,EXT),
          KEY DIRECCIONEXTESTADO (IDDIRECCIONEXT,ESTADO,EXT),
          KEY IDCONTACTO (IDCONTACTO,FECHAALTA)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." CREATE TABLE: IMPORT_CALLEXT_DIRECCIONES_".$DATE.(($result) ? "" : "\n".mysql_error($conn)));

// read file into temporary table
$file = $PATH.$TARFILE.$DATEINV."/".$FILE['DIRECCIONES'];
$handle = @fopen($file, "r");
if ($handle) {
  $cnt = 0;
  $tot = 0;
  $imp = 0;
  while (!feof($handle)) {
    $cnt++;
    $line = str_replace("'","´",fgets($handle));
    if ($line) {
      $tot++;
      $TMP = null;
      $TMP['IDCONTACTO'] = substr($line,0,11);
      $TMP['IDDIRECCION'] = substr($line,11,11);
      $TMP['CALLE'] = substr($line,22,255);
      $TMP['NUMERO'] = substr($line,277,255);
      $TMP['PISO'] = substr($line,532,11);
      $TMP['DEPTO'] = substr($line,543,255);
      $TMP['LOCALIDAD'] = substr($line,798,255);
      $TMP['CODIGOPOSTAL'] = substr($line,1053,255);
      $TMP['FECHAALTA'] = substr($line,1308,19);
      $TMP['FECHABAJA'] = substr($line,1327,19);
      $TMP['ESTADO'] = substr($line,1346,1);
      $TMP['EXT'] = substr($line,1347,1);
      // copy new files into temporary table
      if (!$TMP['ESTADO'] || $TMP['EXT']) { // only BAJA or EXT 
        $SQL = "INSERT INTO IMPORT_CALLEXT_DIRECCIONES_".$DATE." (IDCONTACTO,IDDIRECCION,CALLE,NUMERO,PISO,DEPTO,LOCALIDAD,CODIGOPOSTAL,FECHAALTA,FECHABAJA,ESTADO,EXT,IDDIRECCIONEXT)
                                                        values ('".$TMP['IDCONTACTO']."',
                                                                '".((!$TMP['EXT']) ? $TMP['IDDIRECCION'] : 0)."',
                                                                '".$TMP['CALLE']."',
                                                                '".$TMP['NUMERO']."',
                                                                '".$TMP['PISO']."',
                                                                '".$TMP['DEPTO']."',
                                                                '".$TMP['LOCALIDAD']."',
                                                                '".$TMP['CODIGOPOSTAL']."',
                                                                str_to_date('".$TMP['FECHAALTA']."','%Y-%m-%d %H:%i:%s'),
                                                                ".(($TMP['FECHABAJA']) ? "str_to_date('".$TMP['FECHABAJA']."','%Y-%m-%d %H:%i:%s')" : NULL).",
                                                                '".$TMP['ESTADO']."',
                                                                '".$TMP['EXT']."',
                                                                '".(($TMP['EXT']) ? $TMP['IDDIRECCION'] : 0)."')";
        $result = mysql_query($SQL, $conn);
        if (!$result) savelog("ERR LOAD RECORDS: IMPORT_CALLEXT_DIRECCIONES_".$DATE."\n".mysql_error($conn)."\nLINE: ".$cnt."\n"); else $imp++;
      }
    }
  }
  fclose($handle);
  savelog("OK LOAD RECORDS: IMPORT_CALLEXT_DIRECCIONES_".$DATE." [".$imp."/".$tot."]");
}

// update bajas EXT = 0
$SQL  = "UPDATE DIRECCIONES t, IMPORT_CALLEXT_DIRECCIONES_".$DATE." i
            SET t.FECHABAJA = i.FECHABAJA,
                t.ESTADO = i.ESTADO
          WHERE t.IDDIRECCION = i.IDDIRECCION
            AND i.ESTADO = 0
            AND i.EXT = 0";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." LOAD RECORDS: DIRECCIONES (UPDATE BAJAS) [".mysql_affected_rows($conn)."]".(($result) ? "" : "\n".mysql_error($conn)));

// insert where EXT = 1
$SQL  = "INSERT INTO DIRECCIONES (IDCONTACTO,CALLE,NUMERO,PISO,DEPTO,LOCALIDAD,CODIGOPOSTAL,FECHAALTA,FECHABAJA,ESTADO,EXT,IDDIRECCIONEXT)
  SELECT i.IDCONTACTO,i.CALLE,i.NUMERO,i.PISO,i.DEPTO,i.LOCALIDAD,i.CODIGOPOSTAL,i.FECHAALTA,i.FECHABAJA,i.ESTADO,i.EXT,i.IDDIRECCIONEXT
          FROM IMPORT_CALLEXT_DIRECCIONES_".$DATE." i
          WHERE i.EXT = 1
          AND NOT EXISTS (SELECT 1 FROM DIRECCIONES t WHERE t.EXT = 1 AND t.IDDIRECCIONEXT = i.IDDIRECCIONEXT)";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." LOAD RECORDS: DIRECCIONES (INSERT EXT) [".mysql_affected_rows($conn)."]".(($result) ? "" : "\n".mysql_error($conn)));

// update bajas EXT = 1
$SQL  = "UPDATE DIRECCIONES t, IMPORT_CALLEXT_DIRECCIONES_".$DATE." i
            SET t.FECHABAJA = i.FECHABAJA,
                t.ESTADO = 0
          WHERE t.IDDIRECCIONEXT = i.IDDIRECCIONEXT
            AND i.ESTADO = 0
            AND i.EXT = 1";
//mysql_query($SQL, $conn);
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." LOAD RECORDS: DIRECCIONES (UPDATE BAJAS EXT) [".mysql_affected_rows($conn)."]".(($result) ? "" : "\n".mysql_error($conn)));

} else {
  savelog("ERR FILE EXISTS: ".$PATH.$TARFILE.$DATEINV."/".$FILE['DIRECCIONES']);
}

/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/************* DIRECCIONES *************/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/*************** EMPLEOS ***************/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
if (file_exists($PATH.$TARFILE.$DATEINV."/".$FILE['EMPLEOS'])) {
  savelog("OK FILE EXISTS: ".$PATH.$TARFILE.$DATEINV."/".$FILE['EMPLEOS']);

// create temporary table
$SQL = "CREATE TEMPORARY TABLE IMPORT_CALLEXT_EMPLEOS_".$DATE." (
          IDEMPLEO int(11) unsigned NOT NULL,
          IDCONTACTO int(11) unsigned NOT NULL DEFAULT '0',
          EMPRESA varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          SECCION varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          DIRCALLE varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          DIRNUMERO varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          DIRPISO int(11) unsigned DEFAULT NULL,
          DIRDEPTO varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          DIRLOCALIDAD varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          DIRCODIGOPOSTAL varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          TELCODIGOAREA int(11) unsigned DEFAULT NULL,
          TELNUMERO int(11) unsigned DEFAULT NULL,
          TELINTERNO int(11) unsigned DEFAULT NULL,
          FECHAALTA datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
          FECHABAJA datetime DEFAULT NULL,
          ESTADO int(1) unsigned NOT NULL DEFAULT '1',
          EXT int(1) unsigned NOT NULL DEFAULT '0',
          IDEMPLEOEXT int(11) unsigned NOT NULL DEFAULT '0',
          KEY EMPLEOESTADO (IDEMPLEO,ESTADO,EXT),
          KEY EMPLEOEXTESTADO (IDEMPLEOEXT,ESTADO,EXT),
          KEY IDCONTACTO (IDCONTACTO,FECHAALTA)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." CREATE TABLE: IMPORT_CALLEXT_EMPLEOS_".$DATE.(($result) ? "" : "\n".mysql_error($conn)));

// read file into temporary table
$file = $PATH.$TARFILE.$DATEINV."/".$FILE['EMPLEOS'];
$handle = @fopen($file, "r");
if ($handle) {
  $cnt = 0;
  $tot = 0;
  $imp = 0;
  while (!feof($handle)) {
    $cnt++;
    $line = str_replace("'","´",fgets($handle));
    if ($line) {
      $tot++;
      $TMP = null;
      $TMP['IDCONTACTO'] = substr($line,0,11);
      $TMP['IDEMPLEO'] = substr($line,11,11);
      $TMP['EMPRESA'] = substr($line,22,255);
      $TMP['SECCION'] = substr($line,277,255);
      $TMP['DIRCALLE'] = substr($line,532,255);
      $TMP['DIRNUMERO'] = substr($line,787,255);
      $TMP['DIRPISO'] = substr($line,1042,11);
      $TMP['DIRDEPTO'] = substr($line,1053,255);
      $TMP['DIRLOCALIDAD'] = substr($line,1308,255);
      $TMP['DIRCODIGOPOSTAL'] = substr($line,1563,255);
      $TMP['TELCODIGOAREA'] = substr($line,1818,11);
      $TMP['TELNUMERO'] = substr($line,1829,11);
      $TMP['TELINTERNO'] = substr($line,1840,11);
      $TMP['FECHAALTA'] = substr($line,1851,19);
      $TMP['FECHABAJA'] = substr($line,1870,19);
      $TMP['ESTADO'] = substr($line,1889,1);
      $TMP['EXT'] = substr($line,1890,1);
      // copy new files into temporary table
      if (!$TMP['ESTADO'] || $TMP['EXT']) { // only BAJA or EXT 
        $SQL = "INSERT INTO IMPORT_CALLEXT_EMPLEOS_".$DATE." (IDCONTACTO,IDEMPLEO,EMPRESA,SECCION,DIRCALLE,DIRNUMERO,DIRPISO,DIRDEPTO,DIRLOCALIDAD,DIRCODIGOPOSTAL,TELCODIGOAREA,TELNUMERO,TELINTERNO,FECHAALTA,FECHABAJA,ESTADO,EXT,IDEMPLEOEXT)
                                                        values ('".$TMP['IDCONTACTO']."',
                                                                '".((!$TMP['EXT']) ? $TMP['IDEMPLEO'] : 0)."',
                                                                '".$TMP['EMPRESA']."',
                                                                '".$TMP['SECCION']."',
                                                                '".$TMP['DIRCALLE']."',
                                                                '".$TMP['DIRNUMERO']."',
                                                                '".$TMP['DIRPISO']."',
                                                                '".$TMP['DIRDEPTO']."',
                                                                '".$TMP['DIRLOCALIDAD']."',
                                                                '".$TMP['DIRCODIGOPOSTAL']."',
                                                                '".$TMP['TELCODIGOAREA']."',
                                                                '".$TMP['TELNUMERO']."',
                                                                '".$TMP['TELINTERNO']."',
                                                                str_to_date('".$TMP['FECHAALTA']."','%Y-%m-%d %H:%i:%s'),
                                                                ".(($TMP['FECHABAJA']) ? "str_to_date('".$TMP['FECHABAJA']."','%Y-%m-%d %H:%i:%s')" : NULL).",
                                                                '".$TMP['ESTADO']."',
                                                                '".$TMP['EXT']."',
                                                                '".(($TMP['EXT']) ? $TMP['IDEMPLEO'] : 0)."')";
        $result = mysql_query($SQL, $conn);
        if (!$result) savelog("ERR LOAD RECORDS: IMPORT_CALLEXT_EMPLEOS_".$DATE."\n".mysql_error($conn)."\nLINE: ".$cnt."\n"); else $imp++;
      }
    }
  }
  fclose($handle);
  savelog("OK LOAD RECORDS: IMPORT_CALLEXT_EMPLEOS_".$DATE." [".$imp."/".$tot."]");
}

// update bajas EXT = 0
$SQL  = "UPDATE EMPLEOS t, IMPORT_CALLEXT_EMPLEOS_".$DATE." i
            SET t.FECHABAJA = i.FECHABAJA,
                t.ESTADO = i.ESTADO
          WHERE t.IDEMPLEO = i.IDEMPLEO
            AND i.ESTADO = 0
            AND i.EXT = 0";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." LOAD RECORDS: EMPLEOS (UPDATE BAJAS) [".mysql_affected_rows($conn)."]".(($result) ? "" : "\n".mysql_error($conn)));

// insert where EXT = 1
$SQL  = "INSERT INTO EMPLEOS (IDCONTACTO,EMPRESA,SECCION,DIRCALLE,DIRNUMERO,DIRPISO,DIRDEPTO,DIRLOCALIDAD,DIRCODIGOPOSTAL,TELCODIGOAREA,TELNUMERO,TELINTERNO,FECHAALTA,FECHABAJA,ESTADO,EXT,IDEMPLEOEXT)
  SELECT i.IDCONTACTO,i.EMPRESA,i.SECCION,i.DIRCALLE,i.DIRNUMERO,i.DIRPISO,i.DIRDEPTO,i.DIRLOCALIDAD,i.DIRCODIGOPOSTAL,i.TELCODIGOAREA,i.TELNUMERO,i.TELINTERNO,i.FECHAALTA,i.FECHABAJA,i.ESTADO,i.EXT,i.IDEMPLEOEXT
          FROM IMPORT_CALLEXT_EMPLEOS_".$DATE." i
          WHERE i.EXT = 1
          AND NOT EXISTS (SELECT 1 FROM EMPLEOS t WHERE t.EXT = 1 AND t.IDEMPLEOEXT = i.IDEMPLEOEXT)";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." LOAD RECORDS: EMPLEOS (INSERT EXT) [".mysql_affected_rows($conn)."]".(($result) ? "" : "\n".mysql_error($conn)));

// update bajas EXT = 1
$SQL  = "UPDATE EMPLEOS t, IMPORT_CALLEXT_EMPLEOS_".$DATE." i
            SET t.FECHABAJA = i.FECHABAJA,
                t.ESTADO = 0
          WHERE t.IDEMPLEOEXT = i.IDEMPLEOEXT
            AND i.ESTADO = 0
            AND i.EXT = 1";
//mysql_query($SQL, $conn);
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." LOAD RECORDS: EMPLEOS (UPDATE BAJAS EXT) [".mysql_affected_rows($conn)."]".(($result) ? "" : "\n".mysql_error($conn)));

} else {
  savelog("ERR FILE EXISTS: ".$PATH.$TARFILE.$DATEINV."/".$FILE['EMPLEOS']);
}

/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/*************** EMPLEOS ***************/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/************* SEGUIMIENTO *************/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
if (file_exists($PATH.$TARFILE.$DATEINV."/".$FILE['SEGUIMIENTO'])) {
  savelog("OK FILE EXISTS: ".$PATH.$TARFILE.$DATEINV."/".$FILE['SEGUIMIENTO']);

// create temporary table
$SQL = "CREATE TEMPORARY TABLE IMPORT_CALLEXT_SEGUIMIENTO_".$DATE." (
          IDSEGUIMIENTO int(11) unsigned NOT NULL,
          IDUSUARIO int(11) unsigned NOT NULL DEFAULT '0',
          IDCARTERA int(11) unsigned NOT NULL DEFAULT '0',
          TELEFONO varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
          RESULTADO int(11) unsigned NOT NULL DEFAULT '0',
          COMENTARIOS varchar(8000) COLLATE latin1_spanish_ci DEFAULT NULL,
          NUMCREDITO varchar(7) COLLATE latin1_spanish_ci DEFAULT NULL,
          FECHAALTA datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
          FECHABAJA datetime DEFAULT NULL,
          ESTADO int(1) unsigned NOT NULL DEFAULT '1',
          EXT int(1) unsigned NOT NULL DEFAULT '0',
          IDSEGUIMIENTOEXT int(11) unsigned NOT NULL DEFAULT '0',
          KEY SEGUIMIENTOESTADO (IDSEGUIMIENTO,ESTADO,EXT),
          KEY SEGUIMIENTOEXTESTADO (IDSEGUIMIENTOEXT,ESTADO,EXT),
          KEY IDCARTERA (IDCARTERA,FECHAALTA)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." CREATE TABLE: IMPORT_CALLEXT_SEGUIMIENTO_".$DATE.(($result) ? "" : "\n".mysql_error($conn)));

// read file into temporary table
$file = $PATH.$TARFILE.$DATEINV."/".$FILE['SEGUIMIENTO'];
$handle = @fopen($file, "r");
if ($handle) {
  $cnt = 0;
  $tot = 0;
  $imp = 0;
  while (!feof($handle)) {
    $cnt++;
    $line = str_replace("'","´",fgets($handle));
    if ($line) {
      $tot++;
      $TMP = null;
      $TMP['IDCARTERA'] = substr($line,0,11);
      $TMP['IDSEGUIMIENTO'] = substr($line,11,11);
      $TMP['TELEFONO'] = substr($line,22,255);
      $TMP['COMENTARIOS'] = substr($line,277,8000);
      $TMP['NUMCREDITO'] = substr($line,8277,7);
      $TMP['FECHAALTA'] = substr($line,8284,19);
      $TMP['FECHABAJA'] = substr($line,8303,19);
      $TMP['ESTADO'] = substr($line,8322,1);
      $TMP['EXT'] = substr($line,8323,1);
      // copy new files into temporary table
      if (!$TMP['ESTADO'] || $TMP['EXT']) { // only BAJA or EXT 
        $SQL = "INSERT INTO IMPORT_CALLEXT_SEGUIMIENTO_".$DATE." (IDCARTERA,IDSEGUIMIENTO,IDUSUARIO,TELEFONO,COMENTARIOS,NUMCREDITO,FECHAALTA,FECHABAJA,ESTADO,EXT,IDSEGUIMIENTOEXT)
                                                        values ('".$TMP['IDCARTERA']."',
                                                                '".((!$TMP['EXT']) ? $TMP['IDSEGUIMIENTO'] : 0)."',
                                                                (SELECT IDUSUARIO FROM DISTRIBUCION WHERE IDCARTERA = '".$TMP['IDCARTERA']."'),
                                                                '".$TMP['TELEFONO']."',
                                                                '".trim($TMP['COMENTARIOS'])."',
                                                                '".$TMP['NUMCREDITO']."',
                                                                str_to_date('".$TMP['FECHAALTA']."','%Y-%m-%d %H:%i:%s'),
                                                                ".(($TMP['FECHABAJA']) ? "str_to_date('".$TMP['FECHABAJA']."','%Y-%m-%d %H:%i:%s')" : NULL).",
                                                                '".$TMP['ESTADO']."',
                                                                '".$TMP['EXT']."',
                                                                '".(($TMP['EXT']) ? $TMP['IDSEGUIMIENTO'] : 0)."')";
        $result = mysql_query($SQL, $conn);
        if (!$result) savelog("ERR LOAD RECORDS: IMPORT_CALLEXT_SEGUIMIENTO_".$DATE."\n".mysql_error($conn)."\nLINE: ".$cnt."\n"); else $imp++;
      }
    }
  }
  fclose($handle);
  savelog("OK LOAD RECORDS: IMPORT_CALLEXT_SEGUIMIENTO_".$DATE." [".$imp."/".$tot."]");
}

// update bajas EXT = 0
$SQL  = "UPDATE SEGUIMIENTO t, IMPORT_CALLEXT_SEGUIMIENTO_".$DATE." i
            SET t.FECHABAJA = i.FECHABAJA,
                t.ESTADO = i.ESTADO
          WHERE t.IDSEGUIMIENTO = i.IDSEGUIMIENTO
            AND i.ESTADO = 0
            AND i.EXT = 0";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." LOAD RECORDS: SEGUIMIENTO (UPDATE BAJAS) [".mysql_affected_rows($conn)."]".(($result) ? "" : "\n".mysql_error($conn)));

// insert where EXT = 1
$SQL  = "INSERT INTO SEGUIMIENTO (IDCARTERA,IDSEGUIMIENTO,IDUSUARIO,TELEFONO,COMENTARIOS,FECHAALTA,FECHABAJA,ESTADO,EXT,IDSEGUIMIENTOEXT)
  SELECT i.IDCARTERA,i.IDSEGUIMIENTO,(SELECT dd.IDUSUARIO FROM DISTRIBUCION dd WHERE dd.IDCARTERA = i.IDCARTERA) IDUSUARIO,i.TELEFONO,i.COMENTARIOS,i.FECHAALTA,i.FECHABAJA,i.ESTADO,i.EXT,i.IDSEGUIMIENTOEXT
          FROM IMPORT_CALLEXT_SEGUIMIENTO_".$DATE." i
          WHERE i.EXT = 1
          AND NOT EXISTS (SELECT 1 FROM SEGUIMIENTO t WHERE t.EXT = 1 AND t.IDSEGUIMIENTOEXT = i.IDSEGUIMIENTOEXT)";
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." LOAD RECORDS: SEGUIMIENTO (INSERT EXT) [".mysql_affected_rows($conn)."]".(($result) ? "" : "\n".mysql_error($conn)));

// update bajas EXT = 1
$SQL  = "UPDATE SEGUIMIENTO t, IMPORT_CALLEXT_SEGUIMIENTO_".$DATE." i
            SET t.FECHABAJA = i.FECHABAJA,
                t.ESTADO = 0
          WHERE t.IDSEGUIMIENTOEXT = i.IDSEGUIMIENTOEXT
            AND i.ESTADO = 0
            AND i.EXT = 1";
//mysql_query($SQL, $conn);
$result = mysql_query($SQL, $conn);
savelog((($result) ? "OK" : "ERR")." LOAD RECORDS: SEGUIMIENTO (UPDATE BAJAS EXT) [".mysql_affected_rows($conn)."]".(($result) ? "" : "\n".mysql_error($conn)));

} else {
  savelog("ERR FILE EXISTS: ".$PATH.$TARFILE.$DATEINV."/".$FILE['SEGUIMIENTO']);
}

/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/************* SEGUIMIENTO *************/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/

shell_exec("rm -fR ".$PATH.$TARFILE.$DATEINV.";");

?>
