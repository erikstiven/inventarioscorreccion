<?
include_once('../../Include/config.inc.php');
include_once(path(DIR_INCLUDE) . 'conexiones/db_conexion.php');
include_once(path(DIR_INCLUDE) . 'comun.lib.php');

session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>


	<!-- Bootstrap 3.3.7 -->
	<link rel="stylesheet" href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/dataTables/dataTables.buttons.min.css" media="screen">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>bower_components/font-awesome/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>bower_components/Ionicons/css/ionicons.min.css">
	<!-- Select2 -->
	<link rel="stylesheet" href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>bower_components/select2/dist/css/select2.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>dist/css/AdminLTE.min.css">
	<!-- AdminLTE Skins. Choose a skin from the css/skinsfolder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>dist/css/skins/_all-skins.min.css">
	<link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/dataTables/dataTables.bootstrap.min.css" media="screen">


	<!--JavaScript-->
	<script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.bootstrap.min.js"></script>
	<script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.buttons.flash.min.js"></script>
	<script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.jszip.min.js"></script>
	<script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.pdfmake.min.js"></script>
	<script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.vfs_fonts.js"></script>
	<script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.buttons.html5.min.js"></script>
	<script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.buttons.print.min.js"></script>

	<!-- Select2 -->
	<script src="<?= $_COOKIE["JIREH_COMPONENTES"] ?>bower_components/select2/dist/js/select2.full.min.js"></script>

	<!-- AdminLTE App -->
	<script src="<?= $_COOKIE["JIREH_COMPONENTES"] ?>dist/js/adminlte.min.js"></script>

	<!--CSS-->
	<link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/bootstrap-3.3.7-dist/css/bootstrap.css" media="screen">
	<link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/bootstrap-3.3.7-dist/css/bootstrap.min.css" media="screen">
	<link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/treeview/css/bootstrap-treeview.css" media="screen">
	<link rel="stylesheet" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/dataTables/dataTables.bootstrap.min.css">


	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>FICHA ACTIVO FIJO</title>

	<script>
		function formato() {
			document.getElementById('dos').style.display = "none";
			window.print();
		}
	</script>
</head>

<style type="text/css">
	#cabecera {
		border-spacing: 0;
		border-collapse: collapse;
	}

	#table-striped>tbody>tr:nth-of-type(odd) {
		background-color: #f9f9f9;
	}

	.table-condensed>thead>tr>th,
	.table-condensed>tbody>tr>th,
	.table-condensed>tfoot>tr>th,
	.table-condensed>thead>tr>td,
	.table-condensed>tbody>tr>td,
	.table-condensed>tfoot>tr>td {
		padding: 5px;
	}
</style>

<body>
	<?
	$oCnx = new Dbo();
	$oCnx->DSN = $DSN;
	$oCnx->Conectar();

	$oIfx = new Dbo;
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oIfxA = new Dbo;
	$oIfxA->DSN = $DSN_Ifx;
	$oIfxA->Conectar();
	$empresa      = $_SESSION['U_EMPRESA'];
	$sucursal      = $_SESSION['U_SUCURSAL'];
	$html_impresion      = $_SESSION['ImpresionDetalleBalanza'];

	$codigoActivo       = $_GET['codigo'];
	if (strstr($codigoActivo, ",") != false) {
		$codigoActivo = substr($codigoActivo, 0, -1);
	}

	//////////

	$html = '';
	$sql = "select empr_nom_empr, empr_ruc_empr , empr_dir_empr, empr_conta_sn, empr_num_resu, empr_path_logo, empr_iva_empr
            from saeempr where empr_cod_empr = $empresa ";
	if ($oIfx->Query($sql)) {
		if ($oIfx->NumFilas() > 0) {
			$razonSocial = trim($oIfx->f('empr_nom_empr'));
			$ruc_empr = $oIfx->f('empr_ruc_empr');
			$dirMatriz = trim($oIfx->f('empr_dir_empr'));
			$empr_path_logo = $oIfx->f('empr_path_logo');
			if ($oIfx->f('empr_conta_sn') == 'S')
				$empr_conta_sn = 'SI';
			else
				$empr_conta_sn = 'NO';

			$empr_num_resu = $oIfx->f('empr_num_resu');
			$empr_iva_empr = $oIfx->f('empr_iva_empr');
		}
	}
	//echo $empr_path_logo; exit;
	$oIfx->Free();

	//  AMBIENTE - EMISION
	$sql = "select sucu_tip_ambi, sucu_tip_emis  from saesucu where sucu_cod_empr = $empresa and sucu_cod_sucu = $sucursal ";
	if ($oIfx->Query($sql)) {
		if ($oIfx->NumFilas() > 0) {
			$ambiente_sri = $oIfx->f('sucu_tip_ambi');
			$emision_sri = $oIfx->f('sucu_tip_emis');
		}
	}
	$oIfx->Free();

	if ($ambiente_sri == 1) {
		$ambiente_sri = 'PRUEBAS';
	} elseif ($ambiente_sri == 2) {
		$ambiente_sri = 'PRODUCCION';
	}

	if ($emision_sri == 1) {
		$emision_sri = 'NORMAL';
	} elseif ($emision_sri == 2) {
		$emision_sri = 'POR INDISPONIBLIDAD DEL SISTEMA';
	}

	$html .= '<div id="uno">';
	$html .= '<table align="center"  width="100%" cellspacing="1" cellpadding="0" border="0">';
	$html .= '<tr>';
	$html .= '<b><td style="font:Brandon Grotesque Regular, sans-serif; font-size:24px; height:25px; text-align:center;">';
	$html .= '<table align="center" style="border-collapse:collapse;border-color:#ddd;" width="100%" cellspacing="1" cellpadding="0" bordercolor="#000000" border="0">';
	$html .= '<tr><td style="font:Brandon Grotesque Regular, sans-serif; font-size:24px; height:25px; text-align:center;">' . $razonSocial . '</td></tr>';
	$html .= '<tr><td align="center" style="font-size: 16px;">RUC : ' . $ruc_empr . '</td></tr>';

	//selecciona sucursales y direcciones
	$sql_sucu = "select sucu_nom_sucu, sucu_dir_sucu from saesucu where sucu_cod_empr = $empresa and sucu_cod_sucu = $sucursal ";
	if ($oIfx->Query($sql_sucu)) {
		if ($oIfx->NumFilas() > 0) {
			do {
				$sucu_nom_sucu = $oIfx->f('sucu_nom_sucu');
				$sucu_dir_sucu = $oIfx->f('sucu_dir_sucu');

				$html .= '<tr><td align="center" style="font-size: 16px">' . htmlentities($sucu_dir_sucu) . '</td></tr>';
			} while ($oIfx->SiguienteRegistro());
		}
	}
	$hoy = date("Y-m-d H:i:s");
	//$html .= '<tr><td align="center" style="font-size: 12px;">Contribuyente Especial #:' . $empr_num_resu . '</td></tr>';
	$html .= ' </table>';
	$html .= '</td>';
	$html .= '</tr>';
	$html .= ' </table>';
	$html .= ' <br/>';
	$html .= ' <div align="center">Fecha Impresi&oacuten: ' . $hoy . '</div>';
	//$html .= '<hr size="30px">';
	$html .= ' <br/>';
	//echo $html; 
	// LISTA DE INDICES DE ACTIVOS


	$html .= $html_impresion;
	$html .= '<br/>';



	//////////
	//arma pdf
	// $table.= '<page>';
	// $table.= $html;
	// $table.= '</page>';
	$html .= '</div>';

	$html .= '<div id="dos">
				<table width="464" border="0" align="center">
				  <tr>
					<td align="center"><label>
					  <input name="Submit2" type="submit" class="Estilo2" value="Imprimir" onclick="formato();" />
					</label></td>
				  </tr>
				</table>
		  </div>';
	echo $html;

	?>

</body>
<script>
	formato();
</script>

</html>