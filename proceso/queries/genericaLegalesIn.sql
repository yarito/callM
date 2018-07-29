SELECT      ca.SUCURSAL AS SUCURSAL,
            ca.NUMCREDITO AS NRO_CREDITO,
            grp.GRUPO as GRUPO,
            (ca.SALDO+ca.SALDOBCO+ca.SALDOPESOS) AS SALDOTOMAR,
            pe.PERFIL as PERFIL,
            ce.DETALLE AS ESTADO_DESC,
            ce.ESTADO AS  ESTADO_COD,
            ca.FECHAPROCESO AS FECHAPROCESO
FROM CARTERA ca
JOIN (SELECT MAX(FECHAPROCESO)FECHAPROCESO FROM CARTERA) maca ON 1=1
JOIN CONTACTOS co ON co.IDCONTACTO=ca.IDMOROSO 
JOIN PERFILES pe ON ca.TIPOCARTERA=pe.TIPOCARTERA AND ca.TIPOPROCESO=pe.TIPOPROCESO
JOIN SUCURSALES su ON ca.SUCURSAL=su.SUCURSAL
JOIN DISTRIBUCION di ON ca.IDCARTERA=di.IDCARTERA
JOIN USUARIOS us ON us.IDUSUARIO=di.IDUSUARIO
JOIN GRUPOS grp ON grp.IDGRUPO=di.IDGRUPO
JOIN CARTERAESTADOS ce ON ce.ESTADO = (CASE WHEN ca.FECHAPROCESO<maca.FECHAPROCESO OR di.IDPERFIL<>pe.IDPERFIL THEN 5
                                         WHEN ca.ESTADO=2 AND DATE(ca.FECHAAGENDA)<=CURRENT_DATE() THEN 6
                                         WHEN ca.ESTADO=3 AND ca.FECHAAGENDA<ca.FECHAPROCESO  AND ca.FECHAPROCESO=maca.FECHAPROCESO THEN 4
                                         WHEN ca.ESTADO=14 AND ca.FECHAAGENDA<ca.FECHAPROCESO  AND ca.FECHAPROCESO=maca.FECHAPROCESO THEN 15
                                         WHEN ca.ESTADO=7 AND ca.FECHAAGENDA<ca.FECHAPROCESO  AND ca.FECHAPROCESO=maca.FECHAPROCESO THEN 9
                                         WHEN ca.ESTADO=10 AND ca.FECHAAGENDA<ca.FECHAPROCESO  AND ca.FECHAPROCESO=maca.FECHAPROCESO THEN 11
                                         ELSE ca.ESTADO END) 
WHERE CAST(CONCAT(CAST(CONCAT(ca.SUCURSAL,CAST(ca.NUMCREDITO AS CHAR)) AS SIGNED),2) AS SIGNED) IN (SELECT CAST(NUMERO AS SIGNED) FROM legales_registros_copy); 
