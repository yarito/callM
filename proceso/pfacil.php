<?php
include('../../htdocs/conndb/conndb.php');
//Inicializa Variables de URL
$serviceName="Pago Facil";
$protocol="https://";
$ipUrl="www.e-pagofacil.com";
$port=443;
$call="/2130/index.php";
$URLsola = $protocol.$ipUrl;	  

$SELECT= "select * from pago_facil_queue pe where pe.status=0 ";
$result = mysql_query($SELECT, $conn ); 
 while ($row = mysql_fetch_array($result)){
  
  $txtURL = $protocol.$ipUrl.$call;
  $txtURL .="?p_id_emec=".$row['p_id_emec'];
  $txtURL .="&p_id_operacion=".$row['p_id_operacion'];
  $txtURL .="&p_id_mone_ecom=".$row['p_id_mone_ecom'];
  $txtURL .="&p_va_monto=".$row['p_va_monto'];
  $txtURL .="&p_fe_transaccion=".$row['p_fe_transaccion'];
  $txtURL .="&p_fe_hora=".$row['p_fe_hora'];
  $txtURL .="&p_dias_vigencia=".$row['p_dias_vigencia'];
  $txtURL .="&p_direccion_email_usua=".$row['p_direccion_email_usua'];
  $txtURL .="&p_nombre_usuario=".$row['p_nombre_usuario'];
  $txtURL .="&p_apellido_usuario=".$row['p_apellido_usuario'];
  $txtURL .="&p_domicilio_usuario=".$row['p_domicilio_usuario'];
  $txtURL .="&p_localidad_usuario=".$row['p_localidad_usuario'];
  $txtURL .="&p_provincia_usuario=".$row['p_provincia_usuario'];
  $txtURL .="&p_pais_usuario=".$row['p_pais_usuario'];
  $txtURL .="&p_direccion_email_alter=".$row['p_direccion_email_alter'];
echo $txtURL;
  exec ('wget -O salida --no-check-certificate "'.$txtURL.'"');
  $sql="update pago_facil_queue set status=1 where idPFacil=".$row['idPFacil'];
   mysql_query($sql, $conn );
}
?>
