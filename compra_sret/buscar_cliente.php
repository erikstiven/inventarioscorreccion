<?
include_once('../../Include/config.inc.php');
include_once(path(DIR_INCLUDE) . 'conexiones/db_conexion.php');
include_once(path(DIR_INCLUDE) . 'comun.lib.php');

if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="stylesheet" type = "text/css" href="<?=$_COOKIE["JIREH_INCLUDE"]?>css/general.css">
            <link href="<?=$_COOKIE["JIREH_INCLUDE"]?>Clases/Formulario/Css/Formulario.css" rel="stylesheet" type="text/css"/>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>LISTA DE DESCRIPCION</title>
            <!--CSS-->
			<link rel="stylesheet" type="text/css" href="<?=$_COOKIE["JIREH_INCLUDE"]?>css/bootstrap-3.3.7-dist/css/bootstrap.css" media="screen">
			<link rel="stylesheet" type="text/css" href="<?=$_COOKIE["JIREH_INCLUDE"]?>css/bootstrap-3.3.7-dist/css/bootstrap.min.css" media="screen">
			<link rel="stylesheet" type="text/css" href="<?=$_COOKIE["JIREH_INCLUDE"]?>js/treeview/css/bootstrap-treeview.css" media="screen">
			<link rel="stylesheet" href="<?=$_COOKIE["JIREH_INCLUDE"]?>css/dataTables/dataTables.bootstrap.min.css">
			<!--JavaScript-->
			<script type="text/javascript" language="JavaScript" src="<?=$_COOKIE["JIREH_INCLUDE"]?>js/treeview/js/bootstrap-treeview.js"></script>
			<script type="text/javascript" language="javascript" src="<?=$_COOKIE["JIREH_INCLUDE"]?>js/Webjs.js"></script>
			<script type="text/javascript" src="<?=$_COOKIE["JIREH_INCLUDE"]?>js/dataTables/jquery.dataTables.min.js"></script>
			<script type="text/javascript" src="<?=$_COOKIE["JIREH_INCLUDE"]?>js/dataTables/dataTables.bootstrap.min.js"></script>

			
			<script src="js/jquery.min.js" type="text/javascript"></script>
			
			 <!--Javascript-->  
			<script type="text/javascript" src="js/jquery.min.js"></script>
			<script type="text/javascript" src="js/jquery.js"></script>  
			<script src="media/js/jquery-1.10.2.js"></script>
			<script src="media/js/jquery.dataTables.min.js"></script>
			<script src="media/js/dataTables.bootstrap.min.js"></script>          
			<script src="media/js/bootstrap.js"></script>
			<script type="text/javascript" language="javascript" src="<?=$_COOKIE["JIREH_INCLUDE"]?>css/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>

            <script>
                function datos(cod, cli, ruc, dir, tel, cel, vend, cont, pre, fpago, tpago, fec, auto, serie, fec_venc, dia, contr, ini, fin, cuenta, correo) {
                    window.opener.document.form1.cliente.value 			= cod;
                    window.opener.document.form1.cliente_nombre.value 	= cli;
                    window.opener.document.form1.ruc.value 				= ruc;
                    window.opener.document.form1.tipo_pago.value 		= tpago;
                    window.opener.document.form1.forma_pago1.value 		= fpago;
                    //window.opener.document.form1.auto_prove.value = auto;
                    //window.opener.document.form1.fecha_validez.value = fec;
                    //window.opener.document.form1.serie_prove.value = serie;
                    window.opener.document.form1.fecha_entrega.value 	= fec_venc;
					window.opener.document.form1.fecha_final.value 		= fec_venc;
                    window.opener.document.form1.plazo.value 			= dia;
                    window.opener.document.form1.dias_fp.value 			= dia;
                    window.opener.document.form1.contri_prove.value 	= contr;
                    window.opener.document.form1.cuenta_prove.value 	= cuenta;
                    window.opener.document.form1.dir_prove.value 		= dir;
                    window.opener.document.form1.tel_prove.value 		= tel;
                    window.opener.document.form1.correo_prove.value = correo;
                    window.opener.cargar_lista_correo();
                    //window.opener.document.getElementById("fac_ini").innerHTML = 'FACT. INI: ' + ini;
                    //window.opener.document.getElementById("fac_fin").innerHTML = 'FACT. FIN: ' + fin;
                    close();
                }
            </script>
    </head>

    <body>

        <?
        if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

        $oIfx = new Dbo;
        $oIfx->DSN = $DSN_Ifx;
        $oIfx->Conectar();

        $oIfxA = new Dbo;
        $oIfxA->DSN = $DSN_Ifx;
        $oIfxA->Conectar();

        $idempresa = $_SESSION['U_EMPRESA'];
        $cliente_nom = $_GET['cliente'];

        //  LECTURA SUCIA
        //////////////

       $sql = "select first 50 c.clpv_cod_clpv, c.clpv_nom_clpv,  c.clpv_ruc_clpv,
                       c.clpv_cod_vend, c.clpv_cot_clpv, c.clpv_pre_ven, min(dire_dir_dire) as direccion,
                       min(t.tlcp_tlf_tlcp) as telefono, clpv_etu_clpv, clpv_cod_tpago, 
					   clpv_cod_fpagop, clpv_pro_pago, C.clpv_cod_cuen, clpv_ret_sn
						from saeclpv c, saedire d, saetlcp t
						where c.clpv_cod_clpv = t.tlcp_cod_clpv and
						c.clpv_cod_clpv = d.dire_cod_clpv and
						d.dire_cod_empr = $idempresa and
						c.clpv_cod_empr = $idempresa and
						c.clpv_clopv_clpv = 'PV' and
						(c.clpv_nom_clpv like upper('%$cliente_nom%') OR c.clpv_ruc_clpv like upper('%$cliente_nom%') or clpv_cod_char='$cliente_nom')
						group by 1,2,3,4,5,6, 9, 10, 11, 12,13,14 order by 2";
		//echo $sql;
       //$oReturn->alert($sql);
        ?> 
    </body>
    <div id="contenido">
        <?
        $cont = 1;
		echo '<div class="table-responsive">';
		echo '<table class="table table-bordered table-hover" align="center" style="width: 98%;">';
		echo '<tr><td colspan="7" align="center" class="bg-primary">LISTA PROVEEDORES</td></tr>';
		echo '<tr>
				<td align="center" class="bg-primary" style="width: 10%;">ID</td>
				<td align="center" class="bg-primary" style="width: 5%;">CODIGO ITEM</td>
				<td align="center" class="bg-primary" style="width: 15%;">PROVEEDOR</td>
				<td align="center" class="bg-primary" style="width: 10%;">IDENTIFICACION</td>
				<td align="center" class="bg-primary" style="width: 10%;">CONTRIBUYENTE ESPECIAL</td>
				<td align="center" class="bg-primary" style="width: 10%;">APLICA RETENCION</td>
			 </tr>';

        if ($oIfx->Query($sql)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    $codigo             = ($oIfx->f('clpv_cod_clpv'));
                    $nom_cliente        = htmlentities($oIfx->f('clpv_nom_clpv'));
                    $ruc                = ($oIfx->f('clpv_ruc_clpv'));
                    $dire               = htmlentities($oIfx->f('direccion'));
                    $telefono           = $oIfx->f('telefono');
                    $celular            = $oIfx->f('celular');
                    $vendedor           = $oIfx->f('clpv_cod_vend');
                    $contacto           = $oIfx->f('clpv_cot_clpv');
                    $precio             = round($oIfx->f('clpv_pre_ven'), 0);

                    $fpago              = $oIfx->f('clpv_cod_fpagop');
                    $tpago              = $oIfx->f('clpv_cod_tpago');
                    $prove_dia          = $oIfx->f('clpv_pro_pago');
                    $clpv_etu_clpv      = $oIfx->f('clpv_etu_clpv');
                    $clpv_cod_cuen      = $oIfx->f('clpv_cod_cuen');
                    $correo             = $oIfx->f('email');
					$clpv_ret_sn        = $oIfx->f('clpv_ret_sn');
					
					
                    if ($clpv_etu_clpv == 1) {
                        $clpv_etu_clpv = 'S';
                    } else {
                        $clpv_etu_clpv = 'N';
                    }

                    if (empty($prove_dia)) {
                        $prove_dia = 0;
                    }
                    // correo
                    $sql = "select min( emai_ema_emai ) as correo from saeemai where
                                    emai_cod_empr = $idempresa and
                                    emai_cod_clpv = $codigo ";
                    $correo = acento_func(consulta_string_func($sql, 'correo', $oIfxA, ''));
                    
                    //echo $correo;

                    // FECHA DE VENCIMIENTO
                    $fecha_venc = (sumar_dias_func(date("Y-m-d"), $prove_dia)); //  Y/m/d
                    list($a, $b, $c) = explode('/',$fecha_venc);
                    $fecha_venc = $a.'-'.$b.'-'.$c;
            
                    // AUTORIZACION PROVE
                    $sql = "select  max(coa_fec_vali) as coa_fec_vali, coa_aut_usua, coa_seri_docu, coa_fact_ini, coa_fact_fin
                                    from saecoa where
                                    clpv_cod_empr = $idempresa and
                                    clpv_cod_clpv = $codigo group by coa_fec_vali,2,3,4,5 ";
                    $fec_cadu_prove = '';
                    $auto_prove = '';
                    $serie_prove = '';
                    $ini_prove = '';
                    $fin_prove = '';
                    if ($oIfxA->Query($sql)) {
                        if ($oIfxA->NumFilas() > 0) {
                            $fec_cadu_prove = fecha_mysql_func2($oIfxA->f('coa_fec_vali'));
                            $auto_prove = $oIfxA->f('coa_aut_usua');
                            $serie_prove = $oIfxA->f('coa_seri_docu');
                            $ini_prove = $oIfxA->f('coa_fact_ini');
                            $fin_prove = $oIfxA->f('coa_fact_fin');
                        }
                    }
                    $oIfxA->Free();

                    if ($sClass == 'off')
                        $sClass = 'on';
                    else
                        $sClass = 'off';
                    echo '<tr height="20" class="' . $sClass . '"
                                        onMouseOver="javascript:this.className=\'link\';"
                                        onMouseOut="javascript:this.className=\'' . $sClass . '\';">';
                    echo '<td>' . $cont . '</td>';
                    echo '<td>';
                    ?>
                    <a href="#" onclick="datos('<? echo $codigo; ?>', '<? echo $nom_cliente; ?>', '<? echo $ruc ?>', '<? echo $dire ?>', '<? echo $telefono ?>',
                                    '<? echo $celular ?>', '<? echo $vendedor ?>', '<? echo $contacto ?>', '<? echo $precio ?>', '<? echo $fpago ?>',
                                    '<? echo $tpago ?>', '<? echo $fec_cadu_prove ?>', '<? echo $auto_prove ?>', '<? echo $serie_prove ?>',
                                    '<? echo $fecha_venc ?>', '<? echo $prove_dia ?>', '<? echo $clpv_etu_clpv ?>',
                                    '<? echo $ini_prove ?>', '<? echo $fin_prove ?>', '<? echo $clpv_cod_cuen ?>', '<? echo $correo ?>')">
                    <? echo $codigo; ?></a>
                    <?
                    echo '</td>';
                    echo '<td>'
                    ?>
                    <a href="#" onclick="datos('<? echo $codigo; ?>', '<? echo $nom_cliente; ?>', '<? echo $ruc ?>', '<? echo $dire ?>', '<? echo $telefono ?>',
                                    '<? echo $celular ?>', '<? echo $vendedor ?>', '<? echo $contacto ?>', '<? echo $precio ?>', '<? echo $fpago ?>',
                                    '<? echo $tpago ?>', '<? echo $fec_cadu_prove ?>', '<? echo $auto_prove ?>', '<? echo $serie_prove ?>',
                                    '<? echo $fecha_venc ?>', '<? echo $prove_dia ?>', '<? echo $clpv_etu_clpv ?>',
                                    '<? echo $ini_prove ?>', '<? echo $fin_prove ?>', '<? echo $clpv_cod_cuen ?>', '<? echo $correo ?>')">
                    <? echo $nom_cliente; ?></a>
                    <?
                    echo '</td>';
                    echo '<td>';
                    ?>
                    <a href="#" onclick="datos('<? echo $codigo; ?>', '<? echo $nom_cliente; ?>', '<? echo $ruc ?>', '<? echo $dire ?>', '<? echo $telefono ?>',
                                    '<? echo $celular ?>', '<? echo $vendedor ?>', '<? echo $contacto ?>', '<? echo $precio ?>', '<? echo $fpago ?>',
                                    '<? echo $tpago ?>', '<? echo $fec_cadu_prove ?>', '<? echo $auto_prove ?>', '<? echo $serie_prove ?>',
                                    '<? echo $fecha_venc ?>', '<? echo $prove_dia ?>', '<? echo $clpv_etu_clpv ?>',
                                    '<? echo $ini_prove ?>', '<? echo $fin_prove ?>', '<? echo $clpv_cod_cuen ?>', '<? echo $correo ?>')">
                    <? echo $ruc; ?></a>
                       <? echo '<td>';
                       ?>
                    <a href="#" onclick="datos('<? echo $codigo; ?>', '<? echo $nom_cliente; ?>', '<? echo $ruc ?>', '<? echo $dire ?>', '<? echo $telefono ?>',
                                    '<? echo $celular ?>', '<? echo $vendedor ?>', '<? echo $contacto ?>', '<? echo $precio ?>', '<? echo $fpago ?>',
                                    '<? echo $tpago ?>', '<? echo $fec_cadu_prove ?>', '<? echo $auto_prove ?>', '<? echo $serie_prove ?>',
                                    '<? echo $fecha_venc ?>', '<? echo $prove_dia ?>', '<? echo $clpv_etu_clpv ?>',
                                    '<? echo $ini_prove ?>', '<? echo $fin_prove ?>', '<? echo $clpv_cod_cuen ?>', '<? echo $correo ?>')">
                    <? echo $clpv_etu_clpv; ?></a>
                    <?
                    echo '</td>';
					echo '<td>';
                    ?>
					<a href="#" onclick="datos('<? echo $codigo; ?>', '<? echo $nom_cliente; ?>', '<? echo $ruc ?>', '<? echo $dire ?>', '<? echo $telefono ?>',
                                    '<? echo $celular ?>', '<? echo $vendedor ?>', '<? echo $contacto ?>', '<? echo $precio ?>', '<? echo $fpago ?>',
                                    '<? echo $tpago ?>', '<? echo $fec_cadu_prove ?>', '<? echo $auto_prove ?>', '<? echo $serie_prove ?>',
                                    '<? echo $fecha_venc ?>', '<? echo $prove_dia ?>', '<? echo $clpv_etu_clpv ?>',
                                    '<? echo $ini_prove ?>', '<? echo $fin_prove ?>', '<? echo $clpv_cod_cuen ?>', '<? echo $correo ?>')">
                    <? echo $clpv_ret_sn; ?></a>
                    <?
                    echo '</td>';
                    ?>
                    <?
                    echo '</tr>';
                    echo '<tr>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '</tr>';
                    $cont++;
                }while ($oIfx->SiguienteRegistro());
            }else {
                echo '<span class="fecha_letra">Sin Datos....</span>';
            }
        }
        $oIfx->Free();
        echo '<tr><td colspan="3">Se mostraron ' . ($cont - 1) . ' Registros</td></tr>';
        echo '</table>';

        //echo $cod_producto;
        function fecha_mysql_func2($fecha) {
            $fecha_array = explode('/', $fecha);
            $m = $fecha_array[0];
            $y = $fecha_array[2];
            $d = $fecha_array[1];

            return ( $y . '/' . $m . '/' . $d );
        }
        ?>    
    </div>
</html>

