<?php

include_once('../../Include/config.inc.php');
include_once(path(DIR_INCLUDE) . 'conexiones/db_conexion.php');
include_once(path(DIR_INCLUDE) . 'comun.lib.php');

if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
global $DSN_Ifx, $DSN;

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
$idempresa = $_SESSION['U_EMPRESA'];
$idsucursal = $_SESSION['U_SUCURSAL'];


if (isset($_REQUEST['producto'])) {
	$producto = strtoupper ($_REQUEST['producto']);
	$con_sql=" and (prod_cod_prod like ('%$producto%') or prod_nom_prod like ('%$producto%'))";
} else {
	$producto = null;
	$con_sql="";
}
if($producto==''){
	$con_sql="";
}
$bodega    =  ($_REQUEST['bodega']);
//lectura sucia
//////////////

$sql="select 
			prod_cod_prod,
			prod_nom_prod
		from saeprod 
		inner join saeprbo on prbo_cod_prod=prod_cod_prod
		where 
			prod_cod_empr=prbo_cod_empr and
			prod_cod_sucu=prbo_cod_sucu and
			prod_cod_prod is not null and 
			prod_cod_empr='$idempresa' and
			prod_cod_sucu='$idsucursal' and
			prbo_cod_bode='$bodega' and
			prbo_cod_empr='$idempresa' and
			prbo_cod_sucu='$idsucursal'  $con_sql";
//echo $sql;exit;
if ($oIfx->Query($sql)) {
	if ($oIfx->Numfilas() > 0) {
		unset($data);
		do {
			$codigo   = trim($oIfx->f('prod_cod_prod')); 
			$nombre   = trim($oIfx->f('prod_nom_prod')); 
			$iva_porc = $oIfx->f('prbo_iva_porc'); 
			$costo    = $oIfx->f('prbo_uco_prod'); 
			$nombre   = str_replace('"', " ", $nombre);
			$nombre   = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $nombre);
			$codigo   = str_replace('"', " ", $codigo);
			$codigo   = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $codigo);
			if($codigo!='' && $nombre!=''){
				$img = '<div align="center"> 
						<div class="btn btn-primary btn-sm" onclick="seleccion_prod(\''.$codigo.'\', \''.$nombre.'\')">
							<span class="glyphicon glyphicon-ok"><span>
						</div> 
					</div>';

				$data[] = array(
						"codigo" => $codigo,
						"nombre" => $nombre,
						"seleccionar" => $img
					);
			
			}
			
			
		} while ($oIfx->SiguienteRegistro());
	}else {
			
		$data = array();
	}
}else {
			
		$data = array();
}


$json_data = array(
	
	"data"            => $data   // data del array
);
	
echo json_encode($json_data);  // Enviando data en forma de Json
	
   
	

	
