<?
include_once('../../Include/config.inc.php');
include_once(path(DIR_INCLUDE).'conexiones/db_conexion.php');
include_once(path(DIR_INCLUDE).'comun.lib.php');

if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link rel="stylesheet" type = "text/css" href="<?=$_COOKIE["JIREH_INCLUDE"]?>css/general.css">
    <link href="<?=$_COOKIE["JIREH_INCLUDE"]?>Clases/Formulario/Css/Formulario.css" rel="stylesheet" type="text/css"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ORDEN DE COMPRA - DETALLE</title>
<style type="text/css">
<!--
.Estilo1 {
	font-size: 12px;
	font-family: Georgia, "Times New Roman", Times, serif;
	color: #000000;
}
-->
</style>

<script>
</script>
</head>

<body>

<?
    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $oIfx = new Dbo;
	$oIfx -> DSN = $DSN_Ifx;
	$oIfx -> Conectar();

    $oIfxA = new Dbo;
	$oIfxA -> DSN = $DSN_Ifx;
	$oIfxA -> Conectar();

	$id_empresa    = $_GET['empresa'];
	$id_sucursal   = $_GET['sucursal'];
    $preimp        = $_GET['serial'];

	$sql = "select dmov_cod_prod, dmov_cod_bode, dmov_cod_unid,
				dmov_can_dmov, dmov_cun_dmov, dmov_cto_dmov
				from saedmov where
				dmov_cod_empr = $id_empresa and
				dmov_cod_sucu = $id_sucursal and
				dmov_num_comp = $preimp ";
//        echo $sql;
?>
</body>
<div id="contenido">
<?
	$cont=1;
	echo '<table align="center" border="0" cellpadding="2" cellspacing="1" width="98%" style="border:#999999 1px solid">';
	echo '<tr><th colspan="6" align="center" class="titulopedido">PRODUCTOS</th></tr>';
	echo '<tr>
			<th align="left" bgcolor="#EBF0FA" class="titulopedido">ID</th>
			<th align="left" bgcolor="#EBF0FA" class="titulopedido">CODIGO ITEM</th>
			<th align="left" bgcolor="#EBF0FA" class="titulopedido">PRODUCTO</th>
            <th align="left" bgcolor="#EBF0FA" class="titulopedido">CANTIDAD</th>
			<th align="left" bgcolor="#EBF0FA" class="titulopedido">COSTO</th>
			<th align="left" bgcolor="#EBF0FA" class="titulopedido">TOTAL</th>
		  </tr>';

	$total = 0;
    if ($oIfx->Query($sql)){
        if( $oIfx->NumFilas() > 0 ){
		do {
			$codigo    = ($oIfx->f('dmov_cod_prod'));
			$sql = "select prod_nom_prod from saeprod where prod_cod_empr = $id_empresa and prod_cod_prod = '$codigo' ";
			if ($oIfxA->Query($sql)){
				if( $oIfxA->NumFilas() > 0 ){
					$nom_prod  = htmlentities($oIfxA->f('prod_nom_prod'));
				}
			}			
			
			$cant      = $oIfx->f('dmov_can_dmov');
			$costo     = $oIfx->f('dmov_cun_dmov');
			$subt      = $oIfx->f('dmov_cto_dmov');

            if ($sClass=='off') $sClass='on'; else $sClass='off';
			echo '<tr height="20" class="'.$sClass.'"
                                        onMouseOver="javascript:this.className=\'link\';"
                                        onMouseOut="javascript:this.className=\''.$sClass.'\';">';
			echo '<td align="right">'.$cont.'</td>';
			echo '<td>'.$codigo.'</td>';
			echo '<td>'.$nom_prod.'</td>';
			echo '<td align="right">'.$cant.'</td>';
			echo '<td align="right">'.$costo.'</td>';
			echo '<td align="right">'.$subt.'</td>';
			echo '</tr>';
			echo '<tr>'; echo '</tr>'; 		echo '<tr>'; echo '</tr>';
			echo '<tr>'; echo '</tr>'; 		echo '<tr>'; echo '</tr>';
			$cont++;
			$total += $subt;
		}while($oIfx->SiguienteRegistro());
		
			echo '<tr height="20" class="'.$sClass.'"
						onMouseOver="javascript:this.className=\'link\';"
						onMouseOut="javascript:this.className=\''.$sClass.'\';">';
			echo '<td></td>';
			echo '<td></td>';
			echo '<td></td>';
			echo '<td></td>';
			echo '<td class="fecha_letra">TOTAL:</td>';
			echo '<td class="fecha_letra" align="right">'.$total.'</td>';
			echo '</tr>';
			
		}else{
			echo '<span class="fecha_letra">Sin Datos....</span>';
		}
	}
	$oIfx->Free();
	echo '<tr><td colspan="3">Se mostraron '.($cont-1).' Registros</td></tr>';
	echo '</table>';
	//echo $cod_producto;
?>
</div>
</html>

