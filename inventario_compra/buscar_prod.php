<?php

include_once('../../Include/config.inc.php');
include_once(path(DIR_INCLUDE) . 'conexiones/db_conexion.php');
include_once(path(DIR_INCLUDE) . 'comun.lib.php');

if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
global $DSN_Ifx, $DSN;

$oIfx = new Dbo;
$oIfx->DSN = $DSN_Ifx;
$oIfx->Conectar();

//varibales de sesion
$idempresa = $_SESSION['U_EMPRESA'];
$idsucursal = $_SESSION['U_SUCURSAL'];
$bodega = $_GET['bodega'];
$prod_nom = $_GET['prod_nom'];

if (isset($_REQUEST['bodega_pro'])) {
    $bodega_pro = $_REQUEST['bodega_pro'];
} else {
    $bodega_pro = '';
}





//lectura sucia
//////////////

$tabla = '';

$sql = "select un.unid_nom_unid, tp.tpro_des_tpro, b.bode_nom_bode, pr.prbo_cod_prod, p.prod_nom_prod, pr.prbo_dis_prod, pr.prbo_cta_inv, pr.prbo_cta_ideb,
	pr.prbo_uco_prod, pr.prbo_iva_porc, prod_lot_sino, prod_ser_prod
	from saeprbo pr, saeprod p, saebode b, saetpro tp, saeunid un
	where
	p.prod_cod_prod     = pr.prbo_cod_prod and
	pr.prbo_cod_bode     = b.bode_cod_bode and
	tp.tpro_cod_tpro     = p.prod_cod_tpro and
	un.unid_cod_unid     = pr.prbo_cod_unid and
	p.prod_cod_empr     = $idempresa and
	p.prod_cod_sucu     = $idsucursal and
	pr.prbo_cod_empr    = $idempresa and
	pr.prbo_cod_bode    = '$bodega_pro'
	order by  2 limit 50";

	$sql = "select *from sp_obtener_todos_productos($idempresa , $idsucursal,$bodega_pro,500,'$producto');";



			
	$i = 1;
	unset($_SESSION['U_PROD_RSC']);
    unset($array_tmp);
if ($oIfx->Query($sql)) {
	//echo ($oIfx->NumFilas());
	//exit;

	if ($oIfx->NumFilas() > 0) {
		do {

			$prbo_cod_prod     = ($oIfx->f('prbo_cod_prod'));
            $nom_bode     = ($oIfx->f('bode_nom_bode'));
            $tipo_prod     = ($oIfx->f('tpro_des_tpro'));
            $detalle_prod     = ($oIfx->f('prod_det_prod'));
            $prod_nom_prod     = htmlentities($oIfx->f('prod_nom_prod'));
            $prbo_dis_prod     = $oIfx->f('prbo_dis_prod');
            $prbo_cta_inv     = $oIfx->f('prbo_cta_inv');
            $prbo_cta_ideb     = $oIfx->f('prbo_cta_ideb');
            $prbo_uco_prod     = $oIfx->f('prbo_uco_prod');
            $prbo_iva_porc     = $oIfx->f('prbo_iva_porc');
            $unidad_prod     = $oIfx->f('unid_nom_unid');
            $lote             = $oIfx->f('prod_lot_sino');
            $serie             = $oIfx->f('prod_ser_prod');

			$array_tmp [$i] = array(  $prbo_cod_prod ,  $prod_nom_prod   , $prbo_cta_inv    ,
                                          $prbo_cta_ideb ,  $prbo_uco_prod   , $prbo_iva_porc   ,
                                          $lote ,           $serie );

			if($lote == 1){
				$lote = 'S';
			}else{
				$lote = 'N';
			}

			if($serie == 1){
				$serie = 'S';
			}else{
				$serie = 'N';
			}

		$img = '<div align=\"center\"> <div class=\"btn btn-success btn-sm\" onclick=\"seleccionaItem(\'' . $prod_nom_prod . '\')\"><span class=\"glyphicon glyphicon-ok\"><span></div></div>';
			

			$tabla .= '{
				  "No":"' . $i . '",
				  "Bodega":"' . $nom_bode . '",
				  "Codigo":"' . $prbo_cod_prod . '",
				  "Producto":"' . $prod_nom_prod . '",
				  "Referencia":"' . $detalle_prod . '",
				  "Tipo":"' . $tipo_prod . '",
				  "Unidad_Medida":"' . $unidad_prod . '",
				  "lotes":"' . $lote . '",
				  "Series":"' . $serie . '",
				  "Stock":"' . $prbo_dis_prod . '",
				  "selecciona":"' . $img . '"
				},';

				$i++;
		} while ($oIfx->SiguienteRegistro());
	}

}
$_SESSION['U_PROD_RSC'] = $array_tmp;
$oIfx->Free();

//eliminamos la coma que sobra
$tabla = substr($tabla, 0, strlen($tabla) - 1);

echo '{"data":[' . $tabla . ']}';
