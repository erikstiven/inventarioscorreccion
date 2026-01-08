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
<link rel="stylesheet" type = "text/css" href="css/estilo.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>MOVIMIENTO INVENTARIO</title>
<style type="text/css">
<!--
.Estilo1 {
	font-size: 12px;
	font-family: Georgia, "Times New Roman", Times, serif;
	color: #000000;
	font-weight: bold;
}
.Estilo2 {font-size: 10px; font-family: Georgia, "Times New Roman", Times, serif; color: #000000; font-weight: bold; }
.Estilo3 {font-family: Verdana, Arial, Helvetica, sans-serif}
.Estilo4 {
	font-size: 16px;
	font-weight: bold;
	color:#000000;
}
.fecha {
	font-family: Tahoma, Arial, sans-serif;
	font-size: 34px;
	font-weight: bold;
	color:#000000;
}
-->
</style>

<script>
	function formato(){
		document.getElementById('dos').style.display= "none"; 
		window.print();	
	}
</script>
</head>

<body>

<?
	$oCnx = new Dbo ( );
	$oCnx->DSN = $DSN_Ifx;
	$oCnx->Conectar ();
	
	$oIfx = new Dbo;
	$oIfx -> DSN = $DSN_Ifx;
	$oIfx -> Conectar();
	
	$serial_minv = $_GET['codigo'];
	$id_empresa  = $_SESSION['U_EMPRESA'];
	$id_sucursal = $_GET['sucu'];
	
	        
	// USAUURO
	$sql 		= "select empr_nom_empr from saeempr where empr_cod_empr = $id_empresa ";
    $empr_nom 	= consulta_string($sql, 'empr_nom_empr', $oIfx, '');
        
	if($serial_minv>0){
?> 

<div id="uno">

<table width="98%" height="95%" border="0" align="center">
  <tr>
    <td height="5">&nbsp;</td>
    <td height="20" colspan="2"><div align="center" class="fecha_balance"><? echo $empr_nom; ?></div></td>
  </tr>
  <tr>
      <td colspan="4" class="Estilo2" align="left" width="90%">
	
		<?
			$sql_des = "select mi.minv_fmov,     mi.minv_cod_clpv,  minv_cm1_minv, minv_usu_minv, 
								mi.minv_fac_prov, mi.minv_cod_tran, 
								( select tran_des_tran  from saetran where 
									tran_cod_tran = mi.minv_cod_tran and
									tran_cod_empr = $id_empresa ) as tran,
								mi.minv_num_sec, mi.minv_tot_minv, mi.minv_num_comp,
								dmov_cod_prod, dmov_cod_bode , dmov_can_dmov , dmov_cun_dmov, dmov_cod_unid,
								( select prod_cod_barra from saeprod where
									prod_cod_empr = $id_empresa and
									prod_cod_sucu = $id_sucursal and
									prod_cod_prod = dmov_cod_prod ) as prod_cod_barra,
								( select prod_nom_prod from saeprod where
									prod_cod_empr = $id_empresa and
									prod_cod_sucu = $id_sucursal and
									prod_cod_prod = dmov_cod_prod ) as dmov_nom_prod,
								( select bode_nom_bode from saebode where
									bode_cod_empr = $id_empresa and
									bode_cod_bode = dmov_cod_bode ) as 	 dmov_nom_bode,
								( select unid_nom_unid from saeunid where
									unid_cod_empr = $id_empresa and
									unid_cod_unid = dmov_cod_unid ) as dmov_nom_unid,
								( select clpv_nom_clpv from saeclpv where
									clpv_cod_empr = $id_empresa and
									clpv_cod_clpv = minv_cod_clpv ) as clpv_nom	
								from saeminv mi, saedmov where
								minv_num_comp = dmov_num_comp and
								mi.minv_cod_empr = $id_empresa and
								mi.minv_cod_sucu = $id_sucursal and
								minv_num_comp    = $serial_minv ";
			//echo $sql_des;
			echo '<fieldset style="border:#999999 1px solid; padding:2px; text-align:center; width:98%; background-color:#FFFFFF ">';
			echo '<table align="center" border="0" cellpadding="2" cellspacing="1" width="99%" class="footable">';
                //   echo $sql_des;    
			if($oCnx->Query($sql_des)){
				echo '<tr>
							<td class="fecha_balance" scope="row" colspan="7">N.- '.$oCnx->f('tran').' '.$oCnx->f('minv_num_sec').'</td>
					  </tr>';
				echo '<tr>
							<td class="fecha_balance" scope="row" align="left">PROVEEDOR:</td>
							<td class="fecha_balance" colspan="3" align="left">'.$oCnx->f('clpv_nom').'</td>
							<td class="fecha_balance" colspan="4" align="left">FECHA: '.fecha_mysql_func($oCnx->f('minv_fmov')).'</td>
					  </tr>';
				echo '<tr>
							<td class="fecha_balance" scope="row" align="left">OBSERVACION:</td>
							<td colspan="7" align="left"> '.$oCnx->f('minv_cm1_minv').'</td>
					  </tr>';
				echo '<tr>
							<td class="fecha_balance" scope="row" align="left">USUARIO:</td>
							<td class="fecha_balance" colspan="3" align="left">'.$oCnx->f('minv_usu_minv').'</td>
							<td class="fecha_balance" colspan="4" align="left">FACTURA: '.($oCnx->f('minv_fac_prov')).'</td>
					  </tr>';
				echo '<tr>
							<td colspan="16"></td>
					  </tr>';
				echo '<tr height="25">
							<th class="diagrama">N.-     </th>
							<th class="diagrama">CODIGO BARRA </th>
							<th class="diagrama">CODIGO  </th>
							<th class="diagrama">PRODUCTO</th>
							<th class="diagrama">BODEGA  </th>                                                                               
							<th class="diagrama">CANTIDAD</th>
							<th class="diagrama">COSTO   </th>
							<th class="diagrama">TOTAL   </th>
					  </tr>';
				$total = 0;
				$i 	   = 1;
				if($oCnx->NumFilas()>0){
					do{
							echo '<tr>';
							echo '<td align="right">'.$i.'</td>';
							echo '<td align="left" >'.$oCnx->f('prod_cod_barra').'</td>';
							echo '<td align="left" >'.$oCnx->f('dmov_cod_prod').'</td>';
							echo '<td align="left" >'.htmlentities($oCnx->f('dmov_nom_prod')).'</td>';                                                
							echo '<td align="left" >'.$oCnx->f('dmov_nom_bode').'</td>';                                               
							echo '<td align="right">'.$oCnx->f('dmov_can_dmov').'</td>';                                                                     
							echo '<td align="right">'.$oCnx->f('dmov_cun_dmov').'</td>';
							echo '<td align="right">'.($oCnx->f('dmov_can_dmov') * $oCnx->f('dmov_cun_dmov')).'</td>';
							echo '</tr>';
												
							$total    += ($oCnx->f('dmov_can_dmov') * $oCnx->f('dmov_cun_dmov'));
							$i++;					
					}while($oCnx->SiguienteRegistro());
                            echo '<tr>';
							echo '<td align="left"></td>';
							echo '<td align="left"></td>';
							echo '<td align="left"></td>';
							echo '<td align="left"></td>';
							echo '<td align="left"></td>';
							echo '<td align="left"></td>';
							echo '<td align="right" class="fecha_letra">TOTAL:</td>';
							echo '<td align="right" class="fecha_letra">$ '.round($total,2).'</td>';
							echo '</tr>';
				}else{
					echo 'Sin Productos...';
				}
			}
			$oCnx->Free();
			echo '</table>';
		?>	</td>
    </tr>
  <tr>
    <td colspan="4" class="Estilo2" align="left">&nbsp;</td>
  </tr>
 
  <tr>
    <td colspan="4" class="Estilo2" align="left">&nbsp;</td>
  </tr>
  
</table>

</div>



<div id="dos">

<table width="464" border="0" align="center">
  <tr>
    <td align="center"><label>
      <input name="Submit2" type="submit" class="Estilo2" value="Imprimir" onclick="formato();" />
    </label></td>
  </tr>
</table>
<?

	}else{
	
		echo '<div align="center" class="Estilo1">ERROR!!!! AUN NO INGRESA EL MOVIMIENTO.... </div>';
	}

?>

</div>
</body>
</html>