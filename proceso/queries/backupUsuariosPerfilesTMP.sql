insert into USUARIOPERFILES_TMP_Backup select IDUSUARIOPERFILES,IDUSUARIO,IDGRUPO,IDPERFIL,IDSUCURSAL,FACTOR,
TOPEPTF,TOPE,VETRAMOCOMPLETO,FECHAALTA,FECHABAJA,ESTADO,now()
from USUARIOPERFILES_TMP;