<?
include_once('../../Include/config.inc.php');
include_once(path(DIR_INCLUDE) . 'conexiones/db_conexion.php');
include_once(path(DIR_INCLUDE) . 'comun.lib.php');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>

<?
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$oIfx = new Dbo;
$oIfx->DSN = $DSN_Ifx;
$oIfx->Conectar();

$oIfxA = new Dbo;
$oIfxA->DSN = $DSN_Ifx;
$oIfxA->Conectar();

$oCon = new Dbo;
$oCon->DSN = $DSN;
$oCon->Conectar();

//varibales de sesion
$empresa_id = $_SESSION['U_EMPRESA'];
$sucursal_id = $_SESSION['U_SUCURSAL'];
$usuario_id = $_SESSION['U_ID'];
$para_num_dec_muestra = $_SESSION['para_num_dec_muestra'];
$para_num_dec = $_SESSION['para_num_dec'];

if (isset($_REQUEST['codigo_balanza'])) {
    $codigo_balanza_config = trim($_REQUEST['codigo_balanza']);
} else {
    $codigo_balanza_config = 0;
}

if (isset($_REQUEST['codigo_tmp'])) {
    $codigo_tmp = trim($_REQUEST['codigo_tmp']);
} else {
    $codigo_tmp = 0;
}

if (isset($_REQUEST['bodega'])) {
    $codigo_bodega = trim($_REQUEST['bodega']);
} else {
    $codigo_bodega = 0;
}

if (isset($_REQUEST['producto'])) {
    $codigo_producto = trim($_REQUEST['producto']);
} else {
    $codigo_producto = '';
}


$bandera = 0;
$url_api_result = "";
$unid_result = "";


$id_usuario = $_SESSION['U_USER_INFORMIX'];
$sql_balanza_seleccionada = "SELECT 
                                        cb.id as codigo_balanza_config 
                                from config_balanza as cb
                                    inner join balanza_usuario as bu
                                    on cb.id = bu.id_balanza
                                    where modulo = 'COMP'
                                    and bu.usuario_ingr = $id_usuario
                                    ";
$codigo_balanza_config = consulta_string($sql_balanza_seleccionada, 'codigo_balanza_config', $oIfx, 0);

if ($codigo_balanza_config > 0 || !empty($codigo_balanza_config)) {

    $sql_info_balanza = "SELECT url_api from config_balanza where id = $codigo_balanza_config";
    $url_api = consulta_string($sql_info_balanza, 'url_api', $oIfx, '');


    if ($url_api) {

        /**
         * ACTUALIZAR CANTIDAD TABLA TEMPORAL
         */
       
        if ($codigo_bodega && $codigo_producto) {
            $sql_sg_unid = "select TRIM(unid_sigl_unid) as unid_sigl_unid
                                    from saeprbo po
                                    inner join saeunid uni on uni.unid_cod_empr = po.prbo_cod_empr and uni.unid_cod_unid = po.prbo_cod_unid
                                    where po.prbo_cod_empr = $empresa_id
                                    and po.prbo_cod_sucu = $sucursal_id 
                                    and po.prbo_cod_bode = $codigo_bodega
                                    and po.prbo_cod_prod = '$codigo_producto';";
            $siglas_unidad = consulta_string($sql_sg_unid, 'unid_sigl_unid', $oIfx, '');

            if ($siglas_unidad) {
                $bandera = 1;
                $url_api_result = $url_api;
                $unid_result = $siglas_unidad;
            }
        }
    }
}


echo (json_encode(['bandera' => $bandera, 'url_api_result' => $url_api_result, 'unid_result' => $unid_result]));
