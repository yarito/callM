<?
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/******** CUSTOM DATA ********/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// EXT: Código de Call Center Externo
$EXT = "1";
// idusuario
$IDUSUARIO = "GAR%";
// local path
$PATH = "/home/callcenter/interfaz/cc_externo/contactogarantido/out/";
$SENTPATH = "/home/callcenter/interfaz/cc_externo/contactogarantido/out/sent/";
// log
$LOG = "/home/callcenter/interfaz/cc_externo/contactogarantido/log/send.log";
// sftp
$SFTP = 1;
$SFTPSERVER = "201.216.221.82";
$SFTPPORT = "18024";
$SFTPUSER = "fvgdatos";
$SFTPPASS = "M0ntevide0";
$SFTPPATH = "/inbox/";
// ftp
$FTPSERVER = "ftp.contactogarantido.com";
$FTPUSER = "fravega";
$FTPPASS = "GestionCG2010";
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
/************ atrsucMCV *************/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
//$execOut = shell_exec('cp /home/callcenter/interfaz/data/'.$FILE['ATRSUCMCV'].' '.$PATH.'');
//savelog('atrsucMCV: '.$execOut);
$atrsucMCVfile = trim(shell_exec('ls -ABrt1 /home/callcenter/interfaz/data/atrsucMCV*| tail -n1'));
$FILE['ATRSUCMCV'] = trim(shell_exec('basename '.$atrsucMCVfile));
savelog('ATRSUCMCV FILE: '.$FILE['ATRSUCMCV']);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/************ atrsucMCV ** **********/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/*********** SEGUIMIENTO ************/
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// if file does not exist, join files into single large file
if (!file_exists($PATH.$FILE['SEGUIMIENTO'])) {
  $execOut = exec("cat ".$PATH."tmp/".$FILE['SEGUIMIENTO'].".".str_pad($i,5,"0", STR_PAD_LEFT)." > ".$PATH.$FILE['SEGUIMIENTO'] ,$execOut);
  savelog('JOIN: '.$execOut[0]);
}
$execOut = exec("rm -rf  ".$PATH."tmp/*".$DATE.".* ");
savelog('DEL tmp: '.$execOut);
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/*********** SEGUIMIENTO ************/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/*********** TAR *************/    // tar --remove-files
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
// tar/gzip files
passthru('/bin/tar -cvf '.$PATH.$DATE.'.tar --files-from /dev/null',$execOut);
savelog('TAR CREATE FILE '.$execOut);
passthru('/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['CARTERA'],$execOut);
savelog('TAR CARTERA (/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['CARTERA'].'):'.$execOut);
passthru('/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['MOROSOS'],$execOut);
savelog('TAR MOROSOS (/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['MOROSOS'].'):'.$execOut);
passthru('/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['TELEFONOS'],$execOut);
savelog('TAR TELEFONOS (/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['TELEFONOS'].'):'.$execOut);
passthru('/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['REFERENCIAS'],$execOut);
savelog('TAR REFERENCIAS (/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['REFERENCIAS'].'):'.$execOut);
passthru('/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['DIRECCIONES'],$execOut);
savelog('TAR DIRECCIONES (/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['DIRECCIONES'].'):'.$execOut);
passthru('/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['EMPLEOS'],$execOut);
savelog('TAR EMPLEOS (/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['EMPLEOS'].'):'.$execOut);
passthru('/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['CREDITOS'],$execOut);
savelog('TAR CREDITOS (/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['CREDITOS'].'):'.$execOut);
passthru('/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['PRODUCTOS'],$execOut);
savelog('TAR PRODUCTOS (/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['PRODUCTOS'].'):'.$execOut);
passthru('/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['SEGUIMIENTO'],$execOut);
savelog('TAR SEGUIMIENTO (/bin/tar -rvf '.$PATH.$DATE.'.tar -C'.$PATH.' '.$FILE['SEGUIMIENTO'].'):'.$execOut);
passthru('/bin/tar -rvf '.$PATH.$DATE.'.tar -C/home/callcenter/interfaz/data/ '.$FILE['ATRSUCMCV'],$execOut);
savelog('TAR ATRSUCMCV (/bin/tar -rvf '.$PATH.$DATE.'.tar -C/home/callcenter/interfaz/data/ '.$FILE['ATRSUCMCV'].'): '.$execOut);

exec("/bin/gzip ".$PATH.$DATE.".tar",$execOut);
savelog('GZIP: '.$execOut[0]);

$execOut = exec("rm -rf  ".$PATH."*".$DATE.".txt ");
savelog('DEL TXT: '.$execOut);

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
//savelog(((!rename($PATH.$DATE.$ZIPEXT, $SENTPATH.$DATE.$ZIPEXT)) ? 'ERR MOVE: ':'OK MOVE: ').$DATE.$ZIPEXT);
exec("mv ".$PATH.$DATE.".tar.gz ".$SENTPATH.$DATE.".tar.gz",$execOut);
savelog('MOVE: '.$execOut[0]);

/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/*********** MOVE ************/
/*<<<<<<<<<<<<<<<<<<<<<<<<<<<*/

?>

