SELECT
CODIGO,
GRUPO,
NOMBRE,
CANTIDADPENDIENTE,
ROUND(PENDIENTE) PENDIENTE,
CANTIDADCANCELADO,
ROUND(CANCELADO) CANCELADO,
ROUND(TOTAL) TOTAL,
TOTALCARTERA,
CANCELADO/TOTAL*100 RECUPERO,
PENDIENTE/TOTALCARTERA*100 Eficiencia,
TOTAL/TOTALCARTERA*100 EFICIENCIA_INICIAL
 FROM HSTREPORTE1 WHERE PERIODO=CONCAT(MONTH(CURRENT_DATE()),YEAR(CURRENT_DATE()));