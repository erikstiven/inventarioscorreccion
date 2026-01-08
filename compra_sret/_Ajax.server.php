<?php

require("_Ajax.comun.php"); // No modificar esta linea
include_once './mayorizacion.inc.php';
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  // S E R V I D O R   A J A X //
  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

/**
  Herramientas de apoyo
 */

function envio_correo_sret($aForm = '')
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oCon = new Dbo;
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $idempresa  = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];

    $oReturn = new xajaxResponse();
    $user_web       = $_SESSION['U_ID'];

    //CONSULTA DEL CORREO
    $sqlcorreo = "select usuario_email from comercial.usuario where usuario_id=$user_web";
    $mail    = consulta_string_func($sqlcorreo, 'usuario_email', $oCon, '');


    $correos = $aForm['correo'];
    $adj = $aForm['archivoCorreo'];
    $archivos = substr($adj, 3);


    $mensaje = $aForm['mensaje'];

    $mensaje = strtolower($mensaje);
    $mensaje = ucwords($mensaje);


    $asunto = strtoupper($aForm['asunto']);

    $msj = logo_mensaje_correo($mensaje, "");

    $arrayc = explode(';', $correos);

    $docu = '';
    $comp = '';
    $repdj = '';


    foreach ($arrayc as $aVal) {
        $correo = trim($aVal);
        if (!empty($correo)) {

            $correoMsj = envio_correo_csret($mail, $correo, $msj, $archivos, $asunto);

            $oReturn->alert($correoMsj);
        }
    }

    //$oReturn->script("jsRemoveWindowLoad();");
    $oReturn->script("cerrarModalcorreo();");
    return $oReturn;
}

function modal_correo($aForm = '')
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oReturn = new xajaxResponse();

    $sHtml = correo_compras_pago();

    $oReturn->assign("divCorreos", "innerHTML", $sHtml);
    $oReturn->script("abre_modal_correo();");

    return $oReturn;
}

function genera_gridtmp($aData = null, $aLabel = null, $sTitulo = 'Reporte', $iAncho = '400', $aAccion = null, $Totales = null, $aOrden = null)
{
    if (is_array($aData) && is_array($aLabel)) {
        $iLabel = count($aLabel);
        $iData  = count($aData);
        $sClass = 'on';
        $sStyle = 'border:#999999 1px solid; padding:2px; width:' . $iAncho . '%';
        $sHtml  = '';

        $sHtml .= '<form id="DataGrid">';
        $sHtml .= '<table align="center" border="0" class="table table-hover table-bordered table-striped table-condensed" style="width: 98%; margin-bottom: 0px;">';
        $sHtml .= '<tr class="warning" ><td colspan="' . $iLabel . '">Su consulta genero ' . $iData . ' registros de resultado</td></tr>';
        $sHtml .= '<tr>';
        // Genera Columnas de Grid
        for ($i = 0; $i < $iLabel; $i++) {
            $sLabel = explode('|', $aLabel[$i]);
            if ($sLabel[1] == '')
                //				$sHtml .= '<th class="diagrama" align="center">'.$sLabel[0].'</th>';
                if ($i == 130) {
                    $sHtml .= '<td class="info" align="center" style="display:none">' . $sLabel[0] . '</th>';
                } else {
                    $sHtml .= '<td class="info" align="center">' . $sLabel[0] . '</th>';
                }
            else {
                if ($sLabel[1] == $aOrden[0]) {
                    if ($aOrden[1] == 'ASC') {
                        $sLabel[1] .= '|DESC';
                        $sImg = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/ico_down.png" align="absmiddle" />';
                    } else {
                        $sLabel[1] .= '|ASC';
                        $sImg = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/ico_up.png" align="absmiddle" />';
                    }
                } else {
                    $sImg = '';
                    $sLabel[1] .= '|ASC';
                }

                $sHtml .= '<th onClick="xajax_' . $sLabel[2] . '(xajax.getFormValues(\'form1\'),\'' . $sLabel[1] . '\')" 
								style="cursor: hand !important; cursor: pointer !important;" >' . $sLabel[0] . ' ';
                $sHtml .= $sImg;
                $sHtml .= '</th>';
            }
        }
        $sHtml .= '</tr>';
        // Genera Filas de Grid

        for ($i = 0; $i < $iData; $i++) {
            if ($sClass == 'off')
                $sClass = 'on';
            else
                $sClass = 'off';

            $sHtml .= '<tr>';
            for ($j = 0; $j < $iLabel; $j++)
                if (is_float($aData[$i][$aLabel[$j]]))
                    $sHtml .= '<td align="right">' . number_format($aData[$i][$aLabel[$j]], 2, ',', '.') . '</td>';
                else
                    //				$sHtml .= '<td align="left">'.$aData[$i][$aLabel[$j]].'</td>';
                    if ($j == 130) {
                        $sHtml .= '<td align="left" style="display:none">' . $aData[$i][$aLabel[$j]] . '</td>';
                    } else {
                        $sHtml .= '<td align="left">' . $aData[$i][$aLabel[$j]] . '</td>';
                    }
            $sHtml .= '</tr>';
        }

        //Totales 
        $sHtml .= '<tr>';
        if (is_array($Totales)) {
            for ($i = 0; $i < $iLabel; $i++) {
                if ($i == 0)
                    $sHtml .= '<th class="total_reporte">Totales</th>';
                else {
                    if ($Totales[$i] == '')
                        if ($Totales[$i] == '0.00')
                            $sHtml .= '<th align="right" class="total_reporte">' . number_format($Totales[$i], 2, ',', '.') . '</th>';
                        else
                            $sHtml .= '<th align="right"></th>';
                    else
                        $sHtml .= '<th align="right" class="total_reporte">' . number_format($Totales[$i], 2, ',', '.') . '</th>';
                }
            }
        }

        $sHtml .= '</tr></table>';
        $sHtml .= '</form>';
    }
    return $sHtml;
}

function genera_grid($aData = null, $aLabel = null, $sTitulo = 'Reporte', $iAncho = '400', $aAccion = null, $Totales = null, $aOrden = null)
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }


    $arrayaDataGridVisible[0] = 'S';
    $arrayaDataGridVisible[1] = 'S';
    $arrayaDataGridVisible[2] = 'S';
    $arrayaDataGridVisible[3] = 'S';
    $arrayaDataGridVisible[4] = 'S';
    $arrayaDataGridVisible[5] = 'S';
    $arrayaDataGridVisible[6] = 'S';
    $arrayaDataGridVisible[7] = 'S';
    $arrayaDataGridVisible[8] = 'S';
    $arrayaDataGridVisible[9] = 'S';
    $arrayaDataGridVisible[10] = 'S';
    $arrayaDataGridVisible[11] = 'S';
    $arrayaDataGridVisible[12] = 'S';
    $arrayaDataGridVisible[13] = 'S';
    $arrayaDataGridVisible[14] = 'N';
    $arrayaDataGridVisible[15] = 'N';
    $arrayaDataGridVisible[16] = 'N';
    $arrayaDataGridVisible[17] = 'S';
    $arrayaDataGridVisible[18] = 'N';
    $arrayaDataGridVisible[19] = 'N';
    $arrayaDataGridVisible[20] = 'S';
    $arrayaDataGridVisible[21] = 'S';
    $arrayaDataGridVisible[22] = 'S';
    $arrayaDataGridVisible[23] = 'N';
    $arrayaDataGridVisible[24] = 'S';
    $arrayaDataGridVisible[25] = 'N';
    $arrayaDataGridVisible[26] = 'S';
    $arrayaDataGridVisible[27] = 'S';

    if (is_array($aData) && is_array($aLabel)) {
        $iLabel = count($aLabel);
        $iData = count($aData);
        $sClass = 'on';
        $sStyle = 'border:#999999 1px solid; padding:2px; width:' . $iAncho . '%';
        $sHtml = '';
        $sHtml .= '<form id="DataGrid">';
        $sHtml .= '<table class="table table-striped table-condensed table-bordered table-hover" style="width: 98%; margin-top: 20px;" align="center">';
        $sHtml .= '<tr class="warning" ><td colspan="' . $iLabel . '">Su consulta genero ' . $iData . ' registros de resultado</td></tr>';
        $sHtml .= '<tr>';

        // Genera Columnas de Grid
        for ($i = 0; $i < $iLabel; $i++) {
            $sLabel = explode('|', $aLabel[$i]);
            if ($sLabel[1] == '') {

                $aDataVisible = $arrayaDataGridVisible[$i];
                if ($aDataVisible == 'S') {
                    $aDataVisible = '';
                } else {
                    $aDataVisible = 'none;';
                }

                $sHtml .= '<td class="fecha_letra" align="center" style="display: ' . $aDataVisible . '">' . $sLabel[0] . '</th>';
            } else {
                if ($sLabel[1] == $aOrden[0]) {
                    if ($aOrden[1] == 'ASC') {
                        $sLabel[1] .= '|DESC';
                        $sImg = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/ico_down.png" align="absmiddle" />';
                    } else {
                        $sLabel[1] .= '|ASC';
                        $sImg = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/ico_up.png" align="absmiddle" />';
                    }
                } else {
                    $sImg = '';
                    $sLabel[1] .= '|ASC';
                }

                $sHtml .= '<th onClick="xajax_' . $sLabel[2] . '(xajax.getFormValues(\'form1\'),\'' . $sLabel[1] . '\')"
								style="cursor: hand !important; cursor: pointer !important;" >' . $sLabel[0] . ' ';
                $sHtml .= $sImg;
                $sHtml .= '</td>';
            }
        }
        $sHtml .= '</tr>';
        // Genera Filas de Grid

        for ($i = 0; $i < $iData; $i++) {
            if ($sClass == 'off')
                $sClass = 'on';
            else
                $sClass = 'off';

            $sHtml .= '<tr class="' . $sClass . '"
                            onMouseOver="javascript:this.className=\'link\';" 
                            onMouseOut="javascript:this.className=\'' . $sClass . '\';">';
            for ($j = 0; $j < $iLabel; $j++) {
                $campo = $aData[$i][$aLabel[$j]];

                $aDataVisible = $arrayaDataGridVisible[$j];
                if ($aDataVisible == 'S') {
                    $aDataVisible = '';
                } else {
                    $aDataVisible = 'none;';
                }

                if (is_numeric($campo) && $j != 2) {
                    $redondeo = 2;
                    if ($j == 11 || $j == 12) {
                        $redondeo = 4;
                    }
                    $campo = number_format($campo, $redondeo, '.', '');
                    $sHtml .= '<td align="right" style="display: ' . $aDataVisible . '">' . $campo . '</td>';
                } else {
                    $sHtml .= '<td align="left" style="display: ' . $aDataVisible . '">' . $campo . '</td>';
                }
            } //fin for

            $sHtml .= '</tr>';
        }

        //Totales
        $sHtml .= '<tr>';
        if (is_array($Totales)) {
            for ($i = 0; $i < $iLabel; $i++) {
                if ($i == 0)
                    $sHtml .= '<th class="total_reporte">Totales</th>';
                else {
                    if ($Totales[$i] == '')
                        if ($Totales[$i] == '0.00')
                            $sHtml .= '<th align="right" class="total_reporte">' . number_format($Totales[$i], 2, ',', '.') . '</th>';
                        else
                            $sHtml .= '<th align="right"></th>';
                    else
                        $sHtml .= '<th align="right" class="total_reporte">' . number_format($Totales[$i], 2, ',', '.') . '</th>';
                }
            }
        }

        $sHtml .= '</tr></table>';
        $sHtml .= '</form>';
    }
    return $sHtml;
}

function genera_grid_ret($aData = null, $aLabel = null, $sTitulo = 'Reporte', $iAncho = '400', $aAccion = null, $Totales = null, $aOrden = null)
{
    if (is_array($aData) && is_array($aLabel)) {
        $iLabel = count($aLabel);
        $iData = count($aData);
        $sClass = 'on';
        $sStyle = 'border:#999999 1px solid; padding:2px; width:' . $iAncho . '%';
        $sHtml = '';

        $sHtml .= '<form id="DataGrid">';
        $sHtml .= '<table align="left" border="0" class="table table-hover table-bordered table-striped table-condensed" style="width: 60%; margin-bottom: 0px;">';
        $sHtml .= '<tr class="warning" ><td colspan="' . $iLabel . '">Su consulta genero ' . $iData . ' registros de resultado</td></tr>';
        $sHtml .= '<tr>';
        // Genera Columnas de Grid
        for ($i = 0; $i < $iLabel; $i++) {
            $sLabel = explode('|', $aLabel[$i]);
            if ($sLabel[1] == '')
                //				$sHtml .= '<th class="diagrama" align="center">'.$sLabel[0].'</th>';
                if ($i == 13) {
                    $sHtml .= '<td  class="info" align="center" style="display:none">' . $sLabel[0] . '</th>';
                } else {
                    $sHtml .= '<td  class="info" align="center">' . $sLabel[0] . '</th>';
                }
            else {
                if ($sLabel[1] == $aOrden[0]) {
                    if ($aOrden[1] == 'ASC') {
                        $sLabel[1] .= '|DESC';
                        $sImg = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/ico_down.png" align="absmiddle" />';
                    } else {
                        $sLabel[1] .= '|ASC';
                        $sImg = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/ico_up.png" align="absmiddle" />';
                    }
                } else {
                    $sImg = '';
                    $sLabel[1] .= '|ASC';
                }

                $sHtml .= '<th onClick="xajax_' . $sLabel[2] . '(xajax.getFormValues(\'form1\'),\'' . $sLabel[1] . '\')"
								style="cursor: hand !important; cursor: pointer !important;" >' . $sLabel[0] . ' ';
                $sHtml .= $sImg;
                $sHtml .= '</th>';
            }
        }
        $sHtml .= '</tr>';
        // Genera Filas de Grid

        for ($i = 0; $i < $iData; $i++) {
            if ($sClass == 'off')
                $sClass = 'on';
            else
                $sClass = 'off';

            $sHtml .= '<tr class="' . $sClass . '"
							onMouseOver="javascript:this.className=\'link\';"
							onMouseOut="javascript:this.className=\'' . $sClass . '\';">';
            for ($j = 0; $j < $iLabel; $j++)
                if (is_float($aData[$i][$aLabel[$j]]))
                    $sHtml .= '<td align="right">' . number_format($aData[$i][$aLabel[$j]], 2, ',', '.') . '</td>';
                else
                    //				$sHtml .= '<td align="left">'.$aData[$i][$aLabel[$j]].'</td>';
                    if ($j == 13) {
                        $sHtml .= '<td align="left" style="display:none">' . $aData[$i][$aLabel[$j]] . '</td>';
                    } else {
                        $sHtml .= '<td align="left">' . $aData[$i][$aLabel[$j]] . '</td>';
                    }
            $sHtml .= '</tr>';
        }

        //Totales
        $sHtml .= '<tr>';
        if (is_array($Totales)) {
            for ($i = 0; $i < $iLabel; $i++) {
                if ($i == 0)
                    $sHtml .= '<th class="total_reporte">Totales</th>';
                else {
                    if ($Totales[$i] == '')
                        if ($Totales[$i] == '0.00')
                            $sHtml .= '<th align="right" class="total_reporte">' . number_format($Totales[$i], 2, ',', '.') . '</th>';
                        else
                            $sHtml .= '<th align="right"></th>';
                    else
                        $sHtml .= '<th align="right" class="total_reporte">' . number_format($Totales[$i], 2, ',', '.') . '</th>';
                }
            }
        }

        $sHtml .= '</tr></table>';
        $sHtml .= '</form>';
    }
    return $sHtml;
}

function genera_grid_detalle_fp($aData = null, $aLabel = null, $sTitulo = 'Reporte', $iAncho = '400', $aAccion = null, $Totales = null, $aOrden = null)
{
    if (is_array($aData) && is_array($aLabel)) {
        $iLabel = count($aLabel);
        $iData = count($aData);
        $sClass = 'on';
        $sStyle = 'border:#999999 1px solid; padding:2px; width:' . $iAncho . '%';

        $sHtml = '';
        $sHtml .= '<form id="DataGrid">';
        $sHtml .= '<table align="left" border="0" class="table table-hover table-bordered table-striped table-condensed" style="width: 60%; margin-bottom: 0px;">';
        $sHtml .= '<tr class="warning" ><td colspan="' . $iLabel . '">Su consulta genero ' . $iData . ' registros de resultado</td></tr>';
        $sHtml .= '<tr>';
        // Genera Columnas de Grid
        for ($i = 0; $i < $iLabel; $i++) {
            $sLabel = explode('|', $aLabel[$i]);
            if ($sLabel[1] == '')
                if ($i == 7 || $i == 8 || $i == 9 || $i == 10) {
                    $sHtml .= '<td class="info" align="center" style="display:none">' . $sLabel[0] . '</th>';
                } else {
                    $sHtml .= '<td class="info" align="center">' . $sLabel[0] . '</th>';
                }
            else {
                if ($sLabel[1] == $aOrden[0]) {
                    if ($aOrden[1] == 'ASC') {
                        $sLabel[1] .= '|DESC';
                        $sImg = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/ico_down.png" align="absmiddle" />';
                    } else {
                        $sLabel[1] .= '|ASC';
                        $sImg = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/ico_up.png" align="absmiddle" />';
                    }
                } else {
                    $sImg = '';
                    $sLabel[1] .= '|ASC';
                }

                $sHtml .= '<th onClick="xajax_' . $sLabel[2] . '(xajax.getFormValues(\'form1\'),\'' . $sLabel[1] . '\')"
                                                                    style="cursor: hand !important; cursor: pointer !important;" >' . $sLabel[0] . ' ';
                $sHtml .= $sImg;
                $sHtml .= '</td>';
            }
        }
        $sHtml .= '</tr>';
        // Genera Filas de Grid

        for ($i = 0; $i < $iData; $i++) {
            if ($sClass == 'off')
                $sClass = 'on';
            else
                $sClass = 'off';

            $sHtml .= '<tr class="' . $sClass . '"
							onMouseOver="javascript:this.className=\'link\';"
							onMouseOut="javascript:this.className=\'' . $sClass . '\';">';
            for ($j = 0; $j < $iLabel; $j++)
                if (is_float($aData[$i][$aLabel[$j]]))
                    $sHtml .= '<td align="right" style="display:none">' . number_format($aData[$i][$aLabel[$j]], 2, ',', '.') . '</td>';
                else
                if ($j == 7 || $j == 8 || $j == 9 || $j == 10) {
                    $sHtml .= '<td align="left" style="display:none">' . $aData[$i][$aLabel[$j]] . '</td>';
                } else {
                    $sHtml .= '<td align="left" >' . $aData[$i][$aLabel[$j]] . '</td>';
                }
            $sHtml .= '</tr>';
        }

        //Totales
        $sHtml .= '<tr>';
        if (is_array($Totales)) {
            for ($i = 0; $i < $iLabel; $i++) {
                if ($i == 0)
                    $sHtml .= '<th class="total_reporte">Totales</th>';
                else {
                    if ($Totales[$i] == '')
                        if ($Totales[$i] == '0.00')
                            $sHtml .= '<th align="right" class="total_reporte">' . number_format($Totales[$i], 2, ',', '.') . '</th>';
                        else
                            $sHtml .= '<th align="right"></th>';
                    else
                        $sHtml .= '<th align="right" class="total_reporte">' . number_format($Totales[$i], 2, ',', '.') . '</th>';
                }
            }
        }

        $sHtml .= '</tr></table>';
        $sHtml .= '</form>';
    }
    return $sHtml;
}

/* * **************************************************************** */
/* DF01 :: G E N E R A    F O R M U L A R I O    P E D I D O       */
/* * **************************************************************** */

function genera_formulario_pedido($tmp = 0, $sAccion = 'nuevo', $aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo;
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse();

    $idempresa          = $_SESSION['U_EMPRESA'];
    $idsucursal         = $_SESSION['U_SUCURSAL'];
    $idbodega_s         = $_SESSION['U_BODEGA'];
    $usuario_informix   = $_SESSION['U_USER_INFORMIX'];
    unset($_SESSION['U_OTROS']);
    unset($_SESSION['Print']);
    unset($_SESSION['U_PROF_APROB_RECO']);
    unset($_SESSION['aDataGirdAdj']);

    // D E T A L L E     D E S C R I P C I O N
    unset($_SESSION['aDataGird_INV_MRECO']);
    unset($_SESSION['aLabelGirdProd_INV_MRECO']);
    $aDataGrid  = $_SESSION['aDataGird_INV_MRECO'];

    //CAMPO PARA ATAR ORDENES DE COMPRA A UNA FACTURA
    $sqlgein = "SELECT count(*) as conteo
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE COLUMN_NAME = 'minv_comp_ord' AND TABLE_NAME = 'saeminv'";
    $ctralter = consulta_string($sqlgein, 'conteo', $oIfx, 0);
    if ($ctralter == 0) {
        $sqlalter = "alter table saeminv add  minv_comp_ord int4;";
        $oIfx->QueryT($sqlalter);
    }

    $_SESSION['aLabelGirdProd_INV_MRECO'] = array(
        'Id',
        'Bodega',
        'Codigo Item',
        'Descripcion',
        'Unidad',
        'Cantidad',
        'Costo',
        'Impuesto',
        'Dscto 1',
        'Dscto 2',
        'Dscto Gral',
        'Total',
        'Total Con Impuesto',
        'Serie',
        'Fecha Ela',
        'Fecha Cad',
        'Detalle',
        'Precio',
        'Cuenta',
        'Cuenta Impuesto',
        'Modificar',
        'Eliminar',
        'Dmov',
        'CodPedi',
        'Evaluacion',
        'datos_evaluacion',
        'Recep C.U.',
        'MAC'
    );

    $sql = "select mone_sgl_mone from saemone where
                    mone_cod_empr = $idempresa and
                    mone_cod_mone in ( select pcon_mon_base from saepcon where  pcon_cod_empr = $idempresa  ) ";
    $mone_sgl_mone = consulta_string_func($sql, 'mone_sgl_mone', $oIfx, '');
    unset($_SESSION['U_MONE_SIGLA']);
    $_SESSION['U_MONE_SIGLA'] = $mone_sgl_mone;

    switch ($sAccion) {
        case 'nuevo':
            // EMPRESA
            $sql = "select empr_cod_empr, empr_nom_empr from saeempr where empr_cod_empr=$idempresa ";
            $lista_empr = lista_boostrap_func($oIfx, $sql, $idempresa, 'empr_cod_empr',  'empr_nom_empr');


            // Serie lote campos
            ///CHECK serie
            $ifu->AgregarCampoTexto('serie', "Serie |LEFT", true, '', 120, 120);
            ///CHECK lote
            $ifu->AgregarCampoTexto('lote', "Lote |LEFT", true, '', 120, 120);


            $ifu->AgregarCampoTexto('ruc', 'Identificacion|left', true, '', 120, 120);
            $ifu->AgregarCampoTexto('cliente_nombre', 'Suplidor|left', true, '', 250, 200);
            $ifu->AgregarComandoAlEscribir('cliente_nombre', 'autocompletar(' . $idempresa . ', event ); form1.cliente_nombre.value=form1.cliente_nombre.value.toUpperCase();');
            $lista_cliente = '<select class= "CampoFormulario" name="select" size="5" id="select" style="width: auto;display:none" onclick="envio_autocompletar();">
                                          </select>';
            $ifu->AgregarCampoTexto('cliente', 'Proveedor|left', true, '', 50, 50);
            $ifu->AgregarComandoAlPonerEnfoque('cliente', 'this.blur()');
            $ifu->AgregarComandoAlCambiarValor('cliente', 'cargar_datos()');
            $ifu->AgregarCampoTexto('cuenta_prove', 'Cuenta Prove|left', true, '', 50, 50);
            $ifu->AgregarCampoTexto('dir_prove', 'Direccion Prove|left', true, '', 250, 150);
            $ifu->AgregarCampoTexto('tel_prove', 'Telefono Prove|left', true, '', 250, 150);

            $ifu->AgregarCampoListaSQL('correo_prove', 'Email|left', "select emai_ema_emai from saeemai where 
                                            emai_cod_empr=$idempresa and emai_cod_sucu =$idsucursal and emai_cod_clpv = '$cliente'", false, 150);
            $ifu->AgregarCampoNumerico('codMinv', '|left', false, '', 70, 10);
            $ifu->AgregarComandoAlPonerEnfoque('codMinv', 'this.blur()');
            $ifu->AgregarCampoTexto('nota_compra', 'No. SECU|right', false, '', 100, 200);
            $ifu->AgregarComandoAlPonerEnfoque('nota_compra', 'this.blur()');

            $ifu->AgregarCampoFecha('fecha_pedido', 'Fecha Compra|left', true, date('Y') . '/' . date('m') . '/' . date('d'));
            $ifu->AgregarCampoFecha('fecha_entrega', 'Fecha Pago|left', true, date('Y') . '/' . date('m') . '/' . date('d'));

            $sql = "select t.tran_cod_tran, t.tran_des_tran  from saetran t, saedefi d  where
                                    t.tran_cod_tran = d.defi_cod_tran and
                                    t.tran_cod_empr = $idempresa and
                                    t.tran_cod_sucu = $idsucursal and
                                    t.tran_cod_modu = 10 and
                                    d.defi_cod_empr = $idempresa and
                                    d.defi_tip_defi = '0' and
                                    d.defi_cod_modu = 10  order by 2";
            $ifu->AgregarCampoLista('tran', 'Tipo|left', true, 170, 150);
            $lista_tran = lista_boostrap($oIfx, $sql, $tran_cod_tran, 'tran_cod_tran',  'tran_des_tran');

            $sql      = "select pcon_mon_base, pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa ";
            $mone_cod = consulta_string_func($sql, 'pcon_mon_base', $oIfx, '');
            $ifu->AgregarCampoListaSQL('moneda', 'Moneda|left', "select  mone_cod_mone , mone_des_mone  from saemone where
                                                                                mone_cod_empr = $idempresa ", true, 80);
            $sql = "select  mone_cod_mone , mone_des_mone  from saemone where  mone_cod_empr = $idempresa";
            $lista_mone = lista_boostrap($oIfx, $sql, $mone_cod, 'mone_cod_mone',  'mone_des_mone');

            $ifu->AgregarCampoLista('tipo_factura', 'Tipo Factura|left', true, 180, 100);
            $ifu->AgregarOpcionCampoLista('tipo_factura', 'ELECTRONICA', 1);
            $ifu->AgregarOpcionCampoLista('tipo_factura', 'PREIMPRESA', 2);
            $ifu->AgregarComandoAlCambiarValor('tipo_factura', 'cargar_factura()');

            $ifu->AgregarCampoLista('tipo_retencion', 'Tipo Retencion|left', true, 180, 100);
            $ifu->AgregarOpcionCampoLista('tipo_retencion', 'ELECTRONICA', 'S');
            $ifu->AgregarOpcionCampoLista('tipo_retencion', 'PREIMPRESA', 'N');
            $ifu->AgregarComandoAlCambiarValor('tipo_retencion', 'cargar_secuencial_rete()');

            $ifu->AgregarCampoTexto('observaciones', 'Observaciones|left', false, '', 500, 1000);
            $ifu->AgregarCampoListaSQL('sucursal', 'Sucursal|left', "select sucu_cod_sucu, sucu_nom_sucu from saesucu where 
                                                sucu_cod_empr = $idempresa ", true, 'auto');


            // ---------------------------------------------------------------------------------------------------------
            // CONTROL CLPV POR USUARIO, SUCURSALES
            // ---------------------------------------------------------------------------------------------------------
            $id_usuario_comercial = $_SESSION['U_ID'];
            $bloqueo_sucu_sn = 'N';
            $sucursales_usuario = '';
            $sql_data_usuario_sucu = "SELECT bloqueo_sucu_sn, sucursales_usuario from comercial.usuario where usuario_id = $id_usuario_comercial";
            if ($oIfx->Query($sql_data_usuario_sucu)) {
                if ($oIfx->NumFilas() > 0) {
                    do {
                        $bloqueo_sucu_sn = $oIfx->f('bloqueo_sucu_sn');
                        $sucursales_usuario = $oIfx->f('sucursales_usuario');
                    } while ($oIfx->SiguienteRegistro());
                }
            }
            $sql_adicional_sucu = "";
            $oIfx->Free();
            if ($bloqueo_sucu_sn == 'S') {
                if (!empty($sucursales_usuario)) {
                    $sql_adicional_sucu = ' and sucu_cod_sucu in (' . $sucursales_usuario . ')';
                }
            }
            // ---------------------------------------------------------------------------------------------------------
            // FIN CONTROL CLPV POR USUARIO, SUCURSALES
            // ---------------------------------------------------------------------------------------------------------


            $sql = "select sucu_cod_sucu, sucu_nom_sucu from saesucu where sucu_cod_empr = $idempresa $sql_adicional_sucu";
            $lista_sucu = lista_boostrap($oIfx, $sql, $idsucursal, 'sucu_cod_sucu',  'sucu_nom_sucu');

            $ifu->AgregarComandoAlCambiarValor('sucursal', 'cargar_tran();cargar_bode();cargar_fpago();');

            $ifu->AgregarCampoNumerico('plazo', 'No Plazo|left', true, '', 35, 50);
            $ifu->AgregarCampoTexto('contri_prove', 'Contribuyente Especial|left', true, '', 50, 100);

            // AUTORIZACION DEL PROVEEDOR
            /* $ifu->AgregarCampoTexto('auto_prove', 'No Autorizacion|left', true, '', 250, 100);
              $ifu->AgregarCampoTexto('serie_prove', 'Serie|left', true, '', 50, 100);
              $ifu->AgregarComandoAlEscribir('serie_prove', 'auto_proveedor(' . $idempresa . ', event)');
              $ifu->AgregarCampoTexto('fecha_validez', 'Fecha Validez|left', true, date('Y') . '/' . date('m') . '/' . date('d'), 70, 100); */

            $ifu->AgregarCampoListaSQL('tipo_pago', 'Tipo Pago|left', "select tpago_cod_tpago,
                                                                                        (saetpago.tpago_cod_tpago||' '||saetpago.tpago_des_tpago) as tipo_pago
                                                                                        from saetpago where
                                                                                        tpago_cod_empr = $idempresa ", true, '130');

            $ifu->AgregarCampoListaSQL('forma_pago1', 'Forma de Pago|left', "SELECT saefpagop.fpagop_cod_fpagop,
                                                                                             (saefpagop.fpagop_cod_fpagop||' '||saefpagop.fpagop_des_fpagop) as fpagop
                                                                                             FROM saefpagop   where
                                                                                             fpagop_cod_empr = $idempresa ", true, '120');

            //
            // PRODUCTO
            $ifu->AgregarCampoTexto('producto', 'Producto|LEFT', false, '', 250, 200);
            $ifu->AgregarComandoAlEscribir('producto', 'autocompletar_producto(' . $idempresa . ', event, 1 )');
            $ifu->AgregarCampoTexto('codigo_producto', 'Cod. Prod|left', false, '', 120, 100);
            $ifu->AgregarComandoAlEscribir('codigo_producto', 'autocompletar_producto(' . $idempresa . ', event, 2)');
            $ifu->AgregarCampoTexto('codigo_barra', 'Cod. Barra|left', false, '', 120, 100);
            $ifu->AgregarComandoAlEscribir('codigo_barra', 'autocompletar_producto(' . $idempresa . ', event, 3)');

            $ifu->AgregarCampoNumerico('cantidad', 'Cantidad|LEFT', true, 1, 50, 40);
            $ifu->AgregarCampoNumerico('costo', 'Costo|LEFT', true, 0, 90, 40);
            $ifu->AgregarCampoNumerico('iva', 'Impuesto|LEFT', true, 0, 50, 40);
            $ifu->AgregarCampoListaSQL('bodega', 'Bodega|left', "select  b.bode_cod_bode, b.bode_nom_bode from saebode b, saesubo s where
                                                                                b.bode_cod_bode = s.subo_cod_bode and
                                                                                b.bode_cod_empr = $idempresa and
                                                                                s.subo_cod_empr = $idempresa and
                                                                                s.subo_cod_sucu = $idsucursal ", true, '130');
            $sql = "select  b.bode_cod_bode, b.bode_nom_bode from saebode b, saesubo s where
                            b.bode_cod_bode = s.subo_cod_bode and
                            b.bode_cod_empr = $idempresa and
                            s.subo_cod_empr = $idempresa and
                            s.subo_cod_sucu = $idsucursal";
            $lista_bode = lista_boostrap($oIfx, $sql, $idbodega_s, 'bode_cod_bode',  'bode_nom_bode');

            $ifu->AgregarCampoTexto('cuenta_inv', 'Cuenta|LEFT', false, '', 100, 100);
            $ifu->AgregarCampoTexto('cuenta_iva', 'Cuenta Iva|LEFT', false, '', 100, 100);
            $ifu->AgregarCampoNumerico('desc1', 'Descto1|LEFT', true, 0, 50, 40);


            $op = '';
            unset($_SESSION['aDataGird_INV_MRECO']);
            unset($_SESSION['aDataGird_Pago']);
            $cont = count($aDataGird);
            if ($cont > 0) {
                $sHtml2 = mostrar_grid();
            } else {
                $sHtml2 = "";
            }

            $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml2);
            $oReturn->assign("divFormularioDetalle_FP", "innerHTML", $sHtml2);
            $oReturn->assign("divFormularioDetalleRET", "innerHTML", $sHtml2);
            $oReturn->assign("divTotal", "innerHTML", "");

            // control
            $fu->AgregarCampoOculto('ctrl', 'Control');
            $fu->cCampos["ctrl"]->xValor = 1;
            $ifu->cCampos["sucursal"]->xValor = $idsucursal;
            $ifu->cCampos["moneda"]->xValor = 1;


            // F O R M A    D E    P A G O
            unset($_SESSION['aDataGird_Pago']);
            $aDataGrid_Pago = $_SESSION['aDataGird_Pago'];
            $cont = count($aDataGrid_Pago);
            if ($cont > 0) {
                $sHtml2 = mostrar_grid_fp();
            } else {
                $sHtml2 = "";
            }

            $oReturn->assign("divFormularioDetalle_FP", "innerHTML", $sHtml2);
            $oReturn->assign("divFormularioDetalleFP_DET", "innerHTML", "");
            $oReturn->assign("divTotalFP", "innerHTML", "");

            $ifu->AgregarCampoListaSQL('forma_pago_prove', 'Forma de Pago|LEFT', "select  fpag_cod_fpag, fpag_des_fpag  from saefpag where
                                                                                            fpag_cod_empr = $idempresa and
                                                                                            fpag_cod_modu = 10 and
                                                                                            fpag_cod_sucu = $idsucursal	", false, 'auto');

            $sql = "select  fpag_cod_fpag, fpag_des_fpag  from saefpag where
                        fpag_cod_empr = $idempresa and
                        fpag_cod_modu = 10 and
                        fpag_cod_sucu = $idsucursal	 ";
            $lista_fp = lista_boostrap($oIfx, $sql, '', 'fpag_cod_fpag',  'fpag_des_fpag');

            $ifu->AgregarComandoAlCambiarValor('forma_pago_prove', 'tipo_fp();');
            $fu->AgregarCampoFecha('fecha_inicio', 'Fecha|left', true, date('Y') . '/' . date('m') . '/' . date('d'));
            $fu->AgregarCampoNumerico('dias_fp', 'No- Dias|left', true, 0, 25, 4);
            $fu->AgregarComandoAlCambiarValor('dias_fp', 'calculo_fecha_fp()');
            $fu->AgregarCampoTexto('fecha_final', 'Fecha Final|left', true, date('Y') . '/' . date('m') . '/' . date('d'), 70, 20);
            $fu->AgregarComandoAlPonerEnfoque('fecha_final', 'this.blur()');
            $fu->AgregarCampoNumerico('porcentaje', 'Porcentaje|left', true, 100, 40, 3);
            $fu->AgregarCampoNumerico('valor', 'Valor|left', true, 0, 100, 10);
            $fu->AgregarCampoNumerico('ingreso', 'Ingreso|left', true, 0, 100, 10);
            $fu->AgregarCampoTexto('tipo_fp_tmp', 'tipo_fp_tmp', false, '', 80, 10);
            $fu->AgregarCampoTexto('total_fact_fp', 'Total FP|left', false, 0, 100, 10);
            $fu->AgregarComandoAlPonerEnfoque('total_fact_fp', 'this.blur()');

            $ifu->AgregarCampoListaSQL('ccosn', 'Centro de Costo|left', "select ccosn_cod_ccosn,  ccosn_nom_ccosn
                                                                from saeccosn where
                                                                ccosn_cod_empr = $idempresa and
                                                                ccosn_mov_ccosn = 1 order by 2", false, 120);
            $sql = "select ccosn_cod_ccosn,  ccosn_nom_ccosn
                        from saeccosn where
                        ccosn_cod_empr = $idempresa and
                        ccosn_mov_ccosn = 1 order by 2";
            $lista_ccosn = lista_boostrap($oIfx, $sql, '', 'ccosn_cod_ccosn',  'ccosn_nom_ccosn');

            $diaHoy = date("Y-m-d");
            //$oReturn->alert($cliente);
            $sHtml_Fp = '<table align="left" class="table table-striped table-condensed" style="width: 60%; margin-bottom: 0px;">
                                   <tr><td colspan="4" align="center" class="bg-primary">FORMAS DE PAGO ONLINE</td></tr>';
            $sHtml_Fp .= '<tr>
                                <td class="total_fact"  bgcolor="#EBEBEB" height="25px">TOTAL: </td>
                                <td colspan="2" class="total_fact">
                                        <input type="text" class="form-control input-sm" id="total_fact_fp" name="total_fact_fp" style="width:150px; text-align:right"  readonly/> 
                                </td>
                          </tr>';
            $sHtml_Fp .= '<tr>
                                            <td class="labelFrm" >' . $ifu->ObjetoHtmlLBL('forma_pago_prove') . '</td>
                                            <td colspan="3">
                                                <select id="forma_pago_prove" name="forma_pago_prove" class="form-control input-sm" onchange="tipo_fp();">
                                                    <option value="0">Seleccione una opcion..</option>
                                                    ' . $lista_fp . '
                                                </select>
                                            </td>
                                   </tr>';

            $S_PAIS_API_SRI = $_SESSION['S_PAIS_API_SRI'];

            $sHtml_Fp .= '<tr>
                                            <td class="labelFrm">' . $fu->ObjetoHtmlLBL('fecha_inicio') . '</td>
                                            <td colspan="3">
                                                <table width="99%">
                                                    <tr>
                                                        <td><input type="date" name="fecha_inicio" id="fecha_inicio" step="1" value="' . $diaHoy . '">    &nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                        <td>*No Dias:</td>
                                                        <td>
                                                            <input type="number" class="form-control input-sm" id="dias_fp" name="dias_fp" style="width:110px; text-align:right"  onchange="recalcular_fpago(2);" /> 
                                                        </td>';

            if ($S_PAIS_API_SRI == '51') {

                $sHtml_Fp .= '                          <td>*Dias Cuota:</td>
                                                        <td>
                                                            <input type="number" class="form-control input-sm" id="dias_cuotas_fp" name="dias_cuotas_fp" style="width:110px; text-align:right"/> 
                                                        </td>
                                                        <td>*Cuotas:</td>
                                                        <td>
                                                            <input type="number" class="form-control input-sm" id="cuotas_fp" name="cuotas_fp" style="width:110px; text-align:right" onchange="mostrar_boton_amort();" /> 
                                                        </td>';
            }


            $sHtml_Fp .= '                              <td>Fecha Final:</td>
                                                        <td><input type="date" name="fecha_final"  id="fecha_final" step="1" value="' . $diaHoy . '" onchange="fecha_pago(2);"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                    </tr>';
            $sHtml_Fp .= '<tr>
                                            <td class="labelFrm">' . $fu->ObjetoHtmlLBL('valor') . '</td>
                                            <td colspan="3">
                                                <table  width="99%" border="0">
                                                    <tr>
                                                        <td width="43%"><input type="number" class="form-control input-sm" id="valor" name="valor" style="width:150px; text-align:right"  /></td>
                                                        <td class="labelFrm" width="29%">' . $fu->ObjetoHtmlLBL('porcentaje') . '</td>
                                                        <td>
                                                        <input type="number" class="form-control input-sm" id="porcentaje" name="porcentaje" style="width:150px; text-align:right" value="100" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                    </tr>';
            $sHtml_Fp .= '<tr style="display:none">
                                            <td colspan="2">' . $fu->ObjetoHtml('tipo_fp_tmp') . '</td>
                                   </tr>';
            $sHtml_Fp .= '<tr>
                                            <td colspan="4" align="right">    
                                                    <div class="row">                                                        
                                                    <div class="col-md-7" style="float: rigth">                                                        
                                                        <div class="btn btn-primary btn-sm"onclick="javascript:anadir_detalle_fp(' . $idsucursal . ')">
                                                                <span class="glyphicon glyphicon-th-list"></span>
                                                                A&ntilde;adir
                                                        </div>	
                                                    </div>	
                                                    <div class="col-md-5">                                                        
                                                        <div class="btn btn-success btn-sm"onclick="javascript:generar_tabla_amortizacion()" id="div_boton_amort" name="div_boton_amort" style="display: none">
                                                                <span class="glyphicon glyphicon-th-list"></span>
                                                                Tabla Amortizacion
                                                        </div>						
                                                    </div>						
                                            </td>
                                   </tr>';


            $sHtml_Fp .= '<tr>
                            <td colspan="4" align="right">
                                <div id="div_tabla_amortizacion" name="div_tabla_amortizacion"></div>
                            </td>
                        </tr';


            $sHtml_Fp .= '</table>';


            // OTROS
            $sql = "select  rcgo_cod_rcgo, rcgo_des_rcgo, rcgo_cta_debi ,
                                    ( select  cuen_nom_cuen  from saecuen where
                                            cuen_cod_empr = $idempresa and
                                            cuen_cod_cuen = rcgo_cta_debi ) as cuenta
                                    from saercgo where
                                    rcgo_cod_empr = $idempresa ";
            unset($array_otros);
            if ($oIfx->Query($sql)) {
                if ($oIfx->NumFilas() > 0) {
                    do {
                        $array_otros[] = array($oIfx->f('rcgo_cod_rcgo'), $oIfx->f('rcgo_des_rcgo'), $oIfx->f('rcgo_cta_debi'), $oIfx->f('cuenta'));
                    } while ($oIfx->SiguienteRegistro());
                }
            }
            $oIfx->Free();

            $_SESSION['U_OTROS'] = $array_otros;

            $fu->AgregarCampoTexto('lote', 'Lote - Serie', false, '', 180, 100);
            $fu->AgregarCampoFecha('fecha_ela', 'Fecha Elaboracion|left', false, date('Y') . '/' . date('m') . '/' . date('d'));
            $fu->AgregarCampoFecha('fecha_cad', 'Fecha Caducidad|left', false, date('Y') . '/' . date('m') . '/' . date('d'));

            $fu->cCampos["fecha_ela"]->xValor = '';
            $fu->cCampos["fecha_cad"]->xValor = '';

            // RETENCION
            // DATOS RETENCION EMPRESA
            $sql = "select sucu_fac_elec from saesucu where sucu_cod_sucu = $idsucursal ";
            $sucu_fac_elec = consulta_string($sql, 'sucu_fac_elec', $oIfx, 'N');

            if ($sucu_fac_elec == 'S') {
                $tmp = " and retp_elec_sn = 'S'";
            } else {
                $tmp = " and retp_elec_sn = 'N'";
            }

            $sql = "select retp_sec_retp, retp_num_seri, retp_fech_cadu , retp_num_auto
							from saeretp where 
							retp_cod_empr = $idempresa and
							retp_cod_sucu = $idsucursal and
							retp_act_retp = '1' $tmp";
            //$oReturn->alert($sql);
            $num_rete     = consulta_string($sql, 'retp_sec_retp', $oIfx, '');
            $num_rete     = secuencial(2, '', $num_rete, 9);
            $seri_rete       = consulta_string($sql, 'retp_num_seri', $oIfx, '');
            $ret_fec_auto = fecha_mysql_func_(consulta_string($sql, 'retp_fech_cadu', $oIfx, date("Y-m-d")));
            $rete_auto    = consulta_string($sql, 'retp_num_auto', $oIfx, '');


            // $ifu->AgregarCampoTexto('num_rete', 'Retencion|left', true, $num_rete, 100, 100);			
            $fu->AgregarCampoSi_No('electronica', 'Electronica|left', $sucu_fac_elec);
            $fu->AgregarComandoAlCambiarValor('electronica', 'cargar_electronica();');
            //$ifu->AgregarComandoAlCambiarValor('num_rete', 'num_digito(1)');


            $ifu->AgregarCampoTexto('serie_rete', 'Serie|left', true, $seri_rete, 50, 100);
            $ifu->AgregarCampoTexto('auto_rete', 'Autorizacion|left', true, $rete_auto, 200, 100);
            $ifu->AgregarCampoTexto('cad_rete', 'Caducidad|left', true, $ret_fec_auto, 100, 100);

            /*$sql = "select retp_sec_retp, retp_num_seri, retp_fech_cadu from saeretp where 
							retp_cod_empr = $idempresa and
							retp_cod_sucu = $idsucursal and
							retp_act_retp = '1' ";
			//$oReturn->alert($sql);
            $num_rete = consulta_string($sql, 'retp_sec_retp', $oIfx, '');
            $num_rete = secuencial(2, '', $num_rete, 9);
			*/

            $ifu->AgregarCampoTexto('cod_ret', 'Cta Ret.|left', false, '', 100, 200);
            $ifu->AgregarComandoAlEscribir('cod_ret', 'cod_retencion(' . $idempresa . ', event );');
            $ifu->AgregarCampoNumerico('ret_porc', 'Porc.(%)|left', false, '', 50, 50);
            $ifu->AgregarCampoNumerico('ret_base', 'Base Imponible|left', false, '', 100, 200);
            $ifu->AgregarCampoNumerico('ret_val', 'Valor|left', false, '', 50, 200);
            $ifu->AgregarCampoNumerico('ret_num', 'N.- Retencion|left', false, $num_rete, 100, 200);
            $ifu->AgregarComandoAlCambiarValor('ret_num', 'cargar_digito_ret();');

            $ifu->AgregarCampoTexto('ejercicio', 'Ejercicio|right', false, '', 100, 200);
            $ifu->AgregarCampoTexto('periodo', 'Periodo|right', false, '', 100, 200);
            $ifu->AgregarCampoTexto('asiento', 'Asiento|right', false, '', 100, 200);

            $ifu->AgregarCampoSi_No('ret_asumido', 'Retencion Asumida|left', 'N');


            // moneda
            $ifu->AgregarCampoNumerico('cotizacion', 'Tipo Cambio|left', false, 1, 70, 9);

            $ifu->AgregarCampoNumerico('cotizacion_ext', 'Tipo Cambio Ext.|left', false, 1, 70, 9);
            $ifu->AgregarComandoAlPonerEnfoque('cotizacion_ext', 'this.blur()');

            $ifu->AgregarCampoListaSQL('moneda', 'Moneda|left', "select mone_cod_mone, mone_des_mone  from saemone where mone_cod_empr = $idempresa ", true, 150, 150);
            $ifu->AgregarComandoAlCambiarValor('moneda', 'cargar_coti();');


            $ifu->cCampos["moneda"]->xValor = $mone_cod;

            // COTIZACION MONEDA EXTRANJERA
            $sql      = "select pcon_mon_base, pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa ";
            $mone_extr = consulta_string_func($sql, 'pcon_seg_mone', $oIfx, '');
            $sql = "select tcam_val_tcam from saetcam where
						mone_cod_empr = $idempresa and
						tcam_cod_mone = $mone_extr and
						tcam_fec_tcam in (
											select max(tcam_fec_tcam)  from saetcam where
													mone_cod_empr = $idempresa and
													tcam_cod_mone = $mone_extr
										)  ";

            $coti = consulta_string($sql, 'tcam_val_tcam', $oIfx, 0);
            $ifu->cCampos["cotizacion_ext"]->xValor = $coti;

            break;
    }

    $diaHoy = date("Y-m-d");
    $ultimo_dia_mes = date("Y-m-t", strtotime($diaHoy));

    $array_imp = $_SESSION['U_EMPRESA_IMPUESTO'];
    $sHtml .= '<table class="table table-condensed table-striped" style="width: 99%; margin:0px;" align="center">
                    <tr>
                            <td>							
									<div class="btn btn-primary btn-sm" onclick="genera_formulario();">
										<span class="glyphicon glyphicon-file"></span>
										Nuevo
									</div>
									
                                    <div id ="imagen1" class="btn btn-primary btn-sm" onclick="guardar_precios(' . $opcion_tmp . ');">
										<span class="glyphicon glyphicon-floppy-disk"></span>
										Guardar
									</div>


									<div class="btn btn-primary btn-sm"onclick="javascript:impresion_mov();">
										<span class="glyphicon glyphicon-print"></span>
										Impresion Movimiento
									</div>
									
									<div class="btn btn-primary btn-sm"onclick="javascript:impresion_asto();">
										<span class="glyphicon glyphicon-print"></span>
										Comprobante
									</div>									
									
									<div class="btn btn-primary btn-sm"onclick="javascript:formulario_etiqueta();">
										<span class="glyphicon glyphicon-print"></span>
										Etiquetas
									</div>							
									
									<div class="btn btn-primary btn-sm"onclick="javascript:orden_compra_consulta();">
										<span class="glyphicon glyphicon-tag"></span>
										Orden de Compra
									</div>	
									
									<div class="btn btn-primary btn-sm" onclick="archivosAdjuntos();">
										<span class="glyphicon glyphicon-folder-open"></span>
										Adjuntos
									</div>

                                    
                            </td>
                            
                            <td valing="top">
                                    <div class="form-inline">
                                        <label>Clave Acceso:</label>
                                        
                                        <input type="text" class="form-control input-sm" id="clave_acceso_" name="clave_acceso_" 
                                        value="" style="width:200px; text-align:right; height:25px"/>
                                        <div class="btn btn-success btn-sm" onclick="clave_acceso_sri(1);">
											<span class="glyphicon glyphicon-retweet"></span>
											Generar
										</div>
                                    </div>							
						    </td>
							
							<td align="right">
								<div class="btn btn-danger btn-sm"onclick="javascript:cancelar_pedido();">
									<span class="glyphicon glyphicon-remove"></span>
									Cancelar
								</div>
							</td>
                    </tr>
              </table>';

    $sHtml .= '<table class="table table-condensed table-striped" style="width: 99%; margin: 0px;" align="center">
				<tr>
					<td colspan="8" align="center" class="bg-primary">INVENTARIO COMPRA ONLINE</td>
				</tr>
				<tr class="msgFrm">
					<td colspan="8" align="center">Los campos con * son de ingreso obligatorio</td>
				</tr>';
    $sHtml .= '<tr>						
                    <td class="pedido" align="center" class="fecha_letra" style="color: red; font-size: 13px; margin:0px;" colspan="8">
                        <table>
                            <tr>
                                <td style="color: red; font-size: 12px; font-weight: bold;">
                                    ' . $ifu->ObjetoHtmlLBL('nota_compra') . '                                        
                                </td>
                                <td>
                                    <input type="text" class="form-control input-sm" id="codMinv" name="codMinv" size="0" readonly style="width:80px; text-align:right" />
                                </td>
                                <td>
                                    <input type="text" class="form-control input-sm" id="nota_compra" name="nota_compra" size="0" readonly/>
                                </td>
                            </tr>
                        </table>
                    </td>	
			   </tr>';
    $sHtml .= '<tr>
					<td>' . $ifu->ObjetoHtmlLBL('cliente_nombre') . '</td>
					<td colspan="7">
						<table class="table table-striped table-condensed" style="width: 98%; margin:0px;" align="center">
							<tr>
                                <td>
                                    <input type="text" class="form-control input-sm" id="cliente" name="cliente" style="width:50px; text-align:rigth" readonly/>
                                </td>
                                <td>
                                    <input type="text" class="form-control input-sm" id="cliente_nombre" name="cliente_nombre" onkeyup="autocompletar(' . $idempresa . ', event );" style="width:250px; text-align:left"/>
                                </td>
								<td>' . $ifu->ObjetoHtmlLBL('sucursal') . '</td>
                                <td>
                                    <select id="sucursal" name="sucursal" class="form-control input-sm" onchange="cargar_bodega();">
                                        <option value="0">Seleccione una opcion..</option>
                                        ' . $lista_sucu . '
                                    </select>
                                </td>
								<td>' . $ifu->ObjetoHtmlLBL('tran') . '</td>
                                <td>
                                    <select id="tran" name="tran" class="form-control input-sm" style="width:180px;" requerid>
                                        <option value="">Seleccione una opcion..</option>
                                        ' . $lista_tran . '
                                    </select>
                                </td>
								<td>' . $ifu->ObjetoHtmlLBL('moneda') . '</td>
                                <td>
                                        <select id="moneda" name="moneda" class="form-control input-sm" onchange="cotizacion();" style="width:180px;">
                                            <option value="0">Seleccione una opcion..</option>
                                            ' . $lista_mone . '
                                        </select>
                                </td>
								<td>' . $ifu->ObjetoHtmlLBL('cotizacion') . '</td>
                                <td>
                                    <input type="text" class="form-control input-sm" id="cotizacion" name="cotizacion" value="' . $coti . '" style="width:80px; text-align:right"/>
                                </td>
								<td style="display:none">' . $ifu->ObjetoHtml('cotizacion_ext') . '</td>
							</tr>
						</table>
					</td>					
				</tr>';
    $sHtml .= '<tr>
                    <td>' . $ifu->ObjetoHtmlLBL('ruc') . '</td>
                    <td colspan="7">
						<table class="table table-striped table-condensed" style="width: 98%; margin:0px;" align="center" >
                            <tr>
                                <td><input type="text" class="form-control input-sm" id="ruc" name="ruc" style="width:150px; height:25px; text-align:right" /></td>
                                <td>' . $ifu->ObjetoHtmlLBL('correo_prove') . '</td>
                                <td>
                                    <select id="correo_prove" name="correo_prove" class="form-control input-sm">
                                        <option value="0">Seleccione una opcion..</option>
                                    </select>
                                </td>
                                <td>' . $ifu->ObjetoHtmlLBL('fecha_pedido') . '</td>
                                <td> <input type="date" name="fecha_pedido" id="fecha_pedido" step="1" value="' . $diaHoy . '"></td>
                                <td>' . $ifu->ObjetoHtmlLBL('plazo') . '</td>
                                <td><input type="text" class="form-control input-sm" id="plazo" name="plazo" style="width:70px; height:25px; text-align:right" />  </td>
                                <td>' . $ifu->ObjetoHtmlLBL('fecha_entrega') . '</td>
                                <td> <input type="date" name="fecha_entrega" id="fecha_entrega" step="1" value="' . $diaHoy . '"></td>   
                            </tr>
                        </table>
                    </td>					
				</tr>';
    $sHtml .= '<tr>		
                    <td>' . $ifu->ObjetoHtmlLBL('tipo_factura') . '</td>
                    <td colspan="7">
                        <table class="table table-striped table-condensed" style="width: 98%; margin:0px;" align="center" >
                            <tr>
                                <td>
                                    <select id="tipo_factura" name="tipo_factura" class="form-control input-sm" onchange="cargar_factura();">
                                        <option value="0">Seleccione una opcion..</option>
                                        <option value="1">ELECTRONICA</option>
                                        <option value="2">PREIMPRESA</option>
                                    </select>
                                </td>
                                <td style="display:none">' . $ifu->ObjetoHtmlLBL('tipo_pago') . '</td>
                                <td style="display:none">' . $ifu->ObjetoHtml('tipo_pago') . '</td>
                                <td style="display:none">' . $ifu->ObjetoHtmlLBL('forma_pago1') . '</td>
                                <td style="display:none">' . $ifu->ObjetoHtml('forma_pago1') . '</td>      
                                <td>
                                    <table id="divFactura" class="table table-striped table-condensed" style="width: 100%; margin:0px;"></table>
                                </td>                          
                            </tr>
                        </table>
                    </td>
			   </tr>';
    $sHtml .= '<tr>
					<td>' . $ifu->ObjetoHtmlLBL('observaciones') . '</td>
                    <td colspan="7">
						<table class="table table-striped table-condensed" style="width: 98%; margin:0px;" align="center" >
                            <tr>
                                <td>
                                    <input type="text" class="form-control input-sm" id="observaciones" name="observaciones" style="width:80%; height:25px; text-align:left !important" />
                                </td>
                                <td>
                                    <div class="btn btn-primary btn-sm"onclick="javascript:cargar_oc();">
										<span class="glyphicon glyphicon-tag"></span>
										Orden de Compra
									</div>
                                </td>
                            </tr>
                        </table>
					</td>
				</tr>';

    $sHtml .= '<tr>
					<td style="display:none">' . $ifu->ObjetoHtml('cuenta_prove') . '</td>
					<td style="display:none">' . $ifu->ObjetoHtml('dir_prove') . '</td>
					<td style="display:none">' . $ifu->ObjetoHtml('tel_prove') . '</td>
					<td style="display:none">' . $fu->ObjetoHtml('ctrl') . '</td>
					<td style="display:none">' . $ifu->ObjetoHtml('contri_prove') . '</td>
				</tr>';
    $sHtml .= '</table>';


    $sHtml .= '<table class="table table-striped table-condensed" style="width: 99%; margin:0px;" align="center">
					<tr>
                        <td>' . $ifu->ObjetoHtmlLBL('bodega') . '</td>
                        <td>
                            <select id="bodega" name="bodega" class="form-control input-sm">
                                <option value="0">Seleccione una opcion..</option>
                                ' . $lista_bode . '
                            </select>
                        </td>
                        <td>' . $ifu->ObjetoHtmlLBL('producto') . '</td>
                        <td>
                            <input class="form-control input-sm" type="text" placeholder="producto"  style="width: 200px; height:25px;"
                            id="producto" name="producto" onkeyup="autocompletar_producto(' . $idempresa . ', event, 1 );">
                        </td>
                        <td>' . $ifu->ObjetoHtmlLBL('codigo_producto') . '</td>
                        <td>
                            <input class="form-control input-sm" type="text" placeholder="CODIGO"  style="width: 100px; height:25px; "
                            id="codigo_producto" name="codigo_producto" onkeyup="autocompletar_producto(' . $idempresa . ', event, 2 );">
                        </td>
						<td>' . $ifu->ObjetoHtmlLBL('codigo_barra') . '</td>
                        <td>
                            <input class="form-control input-sm" type="text" placeholder="CODIGO BARRAS"  style="width: 100px; height:25px; "
                            id="codigo_barra" name="codigo_barra" onkeyup="autocompletar_producto(' . $idempresa . ', event, 3 );">
                        </td>						
                        <td>' . $ifu->ObjetoHtmlLBL('cantidad') . '</td>
                        <td>
                            <input class="form-control input-sm" type="text" placeholder="Cantidad" id="cantidad" name="cantidad" style="width:80px; height:25px; text-align:right">
                        </td>
                        <td><a href="#" onclick="generaReporteCompras();">' . $ifu->ObjetoHtmlLBL('costo') . '</a></td>
                        <td>
                            <input class="form-control input-sm" type="text" placeholder="Costo" id="costo" name="costo" style="width:80px; height:25px; text-align:right">
                        </td>
                        <td>' . $ifu->ObjetoHtmlLBL('iva') . '</td>
                        <td>
                            <input class="form-control input-sm" type="text" placeholder="Impuesto" id="iva" name="iva" style="width:50px; height:25px; text-align:right">
                        </td>
						<td>' . $ifu->ObjetoHtmlLBL('desc1') . '</td>
                        <td>
                            <input class="form-control input-sm" type="text" placeholder="Dscto" id="desc1" name="desc1" style="width:50px; height:25px; text-align:right">
                        </td>
                        <td style="display:none">' . $ifu->ObjetoHtml('cuenta_inv') . '</td>
                        <td style="display:none">' . $ifu->ObjetoHtml('cuenta_iva') . '</td>
						<td style="display:none">' . $ifu->ObjetoHtml('ejercicio') . '</td>
						<td style="display:none">' . $ifu->ObjetoHtml('periodo') . '</td>
						<td style="display:none">' . $ifu->ObjetoHtml('asiento') . '</td>
					</tr>
				</table>';

    $sHtml .= '<table class="table table-striped table-condensed" style="width: 98%; margin:0px;" align="center">
					<tr>
						<td>' . $ifu->ObjetoHtmlLBL('ccosn') . '</td>
                        <td>
                            <select id="ccosn" name="ccosn" class="form-control input-sm" style="width:140px;">
                                <option value="0">Seleccione una opcion..</option>
                                ' . $lista_ccosn . '
                            </select>
                        </td>
                        <td><div id="lote_etiq">' . $fu->ObjetoHtmlLBL('lote') . '</div></td>
                        <td><div id="lote_txt"><input class="form-control input-sm" type="text" placeholder="Serie" id="lote" name="lote" style="width:150px; height:25px; text-align:right"></div></td>
                        <td><div id="fela_etiq">' . $fu->ObjetoHtmlLBL('fecha_ela') . '                                 </div></td>
                        <td><div id="fela_txt" ><input type="date" name="fecha_ela" step="1" value="' . $diaHoy . '">       </div></td>
                        <td><div id="fcad_txt" >' . $fu->ObjetoHtmlLBL('fecha_cad') . '                                    </div></td>
                        <td><div id="fcad_etiq"><input type="date" name="fecha_cad" step="1" value="' . $diaHoy . '">       </div></td>                        
                        <td> 
							<div class="btn btn-success btn-sm"onclick="javascript:cargar_producto();">
								<span class="glyphicon glyphicon-plus-sign"></span>
								Agregar Producto
							</div>
                        </td>
					</tr>
				</table>';
    // RETENCION
    $sHtml_ret .= '<table class="table table-striped table-condensed" style="width: 90%;  margin:0px;">
	               <tr><td colspan="4" align="center" class="bg-primary">RETENCIONES</td></tr>';
    $sHtml_ret .= '<tr>
						<td colspan="4" width="95%">
							<table class="table table-striped table-condensed" style="width: 98%;  margin:0px;">
								<tr>
									<td>' . $fu->ObjetoHtmlLBL('electronica') . '</td>
									<td>' . $fu->ObjetoHtml('electronica') . '</td>
									<td>' . $ifu->ObjetoHtmlLBL('serie_rete') . '</td>
                                    <td>
                                        <input class="form-control input-sm" type="text" placeholder="SERIE" id="serie_rete" name="serie_rete" style="width:120px; height:25px; text-align:right" value="' . $seri_rete . '">
                                    </td>
									<td>' . $ifu->ObjetoHtmlLBL('cad_rete') . '</td>
                                    <td>
                                        <input class="form-control input-sm" type="text" placeholder="CADUCIDAD" id="cad_rete" name="cad_rete" style="width:120px; height:25px; text-align:right" value="' . $ret_fec_auto . '">
                                    </td>  
									<td>' . $ifu->ObjetoHtmlLBL('auto_rete') . '</td>
                                    <td>
                                        <input class="form-control input-sm" type="text" placeholder="AUTORIZACION" id="auto_rete" name="auto_rete" style="width:150px; height:25px; text-align:right" value="' . $rete_auto . '">
                                    </td>
									<td>' . $ifu->ObjetoHtmlLBL('ret_num') . '</td>
                                    <td>
                                        <input class="form-control input-sm" type="text" placeholder="RETENCION" id="ret_num" name="ret_num" style="width:150px; height:25px; text-align:right" value="' . $num_rete . '" onchange="cargar_digito_ret();">
                                    </td>
								</tr>
								<tr>
									<td>' . $ifu->ObjetoHtmlLBL('cod_ret') . '</td>
                                    <td>
                                        <input class="form-control input-sm" type="text" placeholder="CODIGO" id="cod_ret" name="cod_ret" style="width:120px; height:25px; text-align:right"  onkeyup="cod_retencion(' . $idempresa . ', event );">
                                    </td>   
									<td>' . $ifu->ObjetoHtmlLBL('ret_porc') . '</td>
                                    <td>
                                        <input class="form-control input-sm" type="text" placeholder="PORCENTAJE" id="ret_porc" name="ret_porc" style="width:120px; height:25px; text-align:right" >
                                    </td>
									<td>' . $ifu->ObjetoHtmlLBL('ret_base') . '</td>
                                    <td>
                                        <input class="form-control input-sm" type="text" placeholder="BASE" id="ret_base" name="ret_base" style="width:120px; height:25px; text-align:right" >    
                                    </td>
									<td colspan="2">' . $ifu->ObjetoHtmlLBL('ret_asumido') . '</td>
									<td>' . $ifu->ObjetoHtml('ret_asumido') . '</td>
									<td align="center">												
											<div class="btn btn-success btn-sm"onclick="javascript:anadir_ret();">
												<span class="glyphicon glyphicon-plus-sign"></span>
												Agregar
											</div>
									</td>
								</tr>
						</td>
			   </tr>';
    $sHtml_ret .= '</table>';


    $ifu->AgregarCampoSi_No('sec_automatico', 'Secuencial Factura Auto.|left', 'N');

    // FORM NUEVO
    $sHtml_cab .= '<div class="row">
                        <div class="col-md-12">
                            <div class="btn-group">
                                <div class="btn btn-primary btn-sm" onclick="genera_formulario();">
                                    <span class="glyphicon glyphicon-file"></span>
                                    Nuevo
                                </div>
                                
                                <div id ="imagen1" class="btn btn-primary btn-sm" onclick="guardar_precios(' . $opcion_tmp . ');">
                                    <span class="glyphicon glyphicon-floppy-disk"></span>
                                    Guardar
                                </div>

                                <div class="btn btn-primary btn-sm"onclick="javascript:impresion_mov();">
                                    <span class="glyphicon glyphicon-print"></span>
                                    Impresion Movimiento
                                </div>
                                
                                <div class="btn btn-primary btn-sm"onclick="javascript:impresion_asto();">
                                    <span class="glyphicon glyphicon-print"></span>
                                    Comprobante
                                </div>									
                                
                                <div class="btn btn-primary btn-sm"onclick="javascript:formulario_etiqueta();">
                                    <span class="glyphicon glyphicon-print"></span>
                                    Etiquetas
                                </div>							
                                
                                <div class="btn btn-primary btn-sm"onclick="javascript:orden_compra_consulta();">
                                    <span class="glyphicon glyphicon-tag"></span>
                                    Orden de Compra
                                </div>	
                                
                                <div class="btn btn-primary btn-sm" onclick="archivosAdjuntos();">
                                    <span class="glyphicon glyphicon-folder-open"></span>
                                    Adjuntos
                                </div>
                                <div class="btn btn-primary btn-sm" onclick="procesoSerie()">
                                    <span class="glyphicon glyphicon-th-list"></span>
                                    Proceso/Serie
                                </div> 
                                <div class="btn btn-primary btn-sm" onclick="envioCorreo();">
                                    <span class="glyphicon glyphicon-envelope"></span>
                                    Correo
                                </div>
                                 
                                <div class="btn btn-primary btn-sm" onclick="cerrar_anticipo_modulo()">
                                    <span class="glyphicon glyphicon-th-list"></span>
                                    Cerrar Anticipo
                                </div>  
									                               
                            </div> 

                            <div class="col-md-2" style="float: right !important">
                                <label style="font-size: 10px; font-weight: 900 !important;">' . $ifu->ObjetoHtmlLBL('sec_automatico') . '</label>
                                <label style="font-size: 9px;">' . $ifu->ObjetoHtml('sec_automatico') . '</label>
                            </div>

                            
                        </div><br><br>';
    $sHtml_cab .= '<div class="col-md-12">
                        <div class="form-group">
                            <label for="clave_acceso_" class="col-sm-2 control-label">Clave de Acceso:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control input-sm" id="clave_acceso_" name="clave_acceso_" value="" text-align:right; />
                            </div>
                            <div class="col-sm-2">
                                <div class="btn btn-success btn-sm" onclick="clave_acceso_sri(1);" style="width:100%">
                                    <span class="glyphicon glyphicon-retweet"></span>
                                    Generar
                                </div>
                            </div>
                        </div>                
                    </div>
                    <br><br><br><br>';

    $sHtml_cab .= '<div class="col-md-12">
                        <div class="form-group">
                            <input type="hidden" class="form-control input-sm" id="cliente" name="cliente" style="width:50px; text-align:rigth"/>
                            <input type="hidden" id="codigo_producto"      name="codigo_producto"      value="">

                            <label for="REQUISICION" class="col-sm-2 control-label">No. Movimiento:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-sm" id="codMinv" name="codMinv" size="0" readonly style="text-align:right" />
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control input-sm" id="nota_compra" name="nota_compra" size="0" readonly style="text-align:right"/>
                            </div>
                        </div>                
                    </div>';
    $sHtml_cab .= '<div class="col-md-12">
                        <div class="form-row">
                            <div class="col-md-2">
                                <label for="empresa">* Empresa:</label>
                                <select id="empresa" name="empresa" class="form-control input-sm" onchange="cargar_sucursal();">
                                    <option value="0">Seleccione una opcion..</option>
                                    ' . $lista_empr . '
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sucursal">* Sucursal:</label>
                                <select id="sucursal" name="sucursal" class="form-control input-sm" onchange="cargar_bode();" required>
                                    <option value="">Seleccione una opcion..</option>
                                    ' . $lista_sucu . '
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label  for="tran">* Tipo:</label>
                                <select id="tran" name="tran" class="form-control input-sm" required>
                                    <option value="">Seleccione una opcion..</option>
                                    ' . $lista_tran . '
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label  for="moneda">* Moneda:</label>
                                <select id="moneda" name="moneda" class="form-control input-sm" required onclick="cargar_coti();">
                                    <option value="">Seleccione una opcion..</option>
                                    ' . $lista_mone . '
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label  for="cotizacion">* Tipo de Cambio:</label>
                                <input type="text" class="form-control input-sm" id="cotizacion" name="cotizacion" value="1.0" text-align:right"  />
                            </div>
                        </div>
                    </div>';
    $sHtml_cab .= '<div class="col-md-12">
                        <div class="form-row">
                            <div class="col-md-3">
                                <label class="control-label" for="cliente_nombre">* Suplidor:</label>                                
                                <div class="input-group">
                                    <input type="text" class="form-control input-sm" placeholder="ESCRIBA SUPLIDOR Y PRESIONE ENTER" id="cliente_nombre" 
                                    name="cliente_nombre" onkeyup="autocompletar( ' . $idempresa . ', event ); form1.cliente_nombre.value=form1.cliente_nombre.value.toUpperCase();"/>
                                    <span class="input-group-addon primary" style="cursor: pointer;" onClick="autocompletar_btn(' . $idempresa . ' );"><i class="fa fa-search"></i></span>
                                </div>                                
                            </div>                            
                            <div class="col-md-2">
                                <label for="ruc" class="control-label">* Identificacion:</label>
                                <input class="form-control input-sm" type="text" id="ruc" name="ruc">
                            </div>
                            <div class="col-md-2">
                                <label for="correo_prove" class="control-label">* Correo:</label>
                                <input class="form-control input-sm" type="text" id="correo_prove" name="correo_prove">
                            </div>
                            <div class="col-md-2">
                                <label for="fecha_pedido" class="control-label">* Fecha Registro Contable:</label>
                                <!--
                                <input type="date" name="fecha_pedido" id="fecha_pedido" step="1" value="' . date("Y-m-d") . '" class="form-control input-sm" >
                                -->       
                                <input type="date" name="fecha_pedido" id="fecha_pedido" step="1" value="' . date("Y-m-d") . '" class="form-control input-sm" onchange="anio_fecha_abierto();" >
                                </div>  
                            <div class="col-md-2">
                                <label for="fecha_entrega" class="control-label">* Fecha Pago:</label>
                                <input type="date" name="fecha_entrega"  id="fecha_entrega" step="1" value="' . date("Y-m-d") . '" class="form-control input-sm" onchange="fecha_pago(1);">   
                            </div>  

                        </div>  
                        <div class="form-row">
                            <div class="col-md-2">
                                <label for="fecha_pedido" class="control-label">* Fecha Compra:</label>      
                                    <input type="date" name="fecha_regc" id="fecha_regc" step="1" value="' . date("Y-m-d") . '" class="form-control input-sm" onchange="anio_fecha_abierto();" >
                            </div>  
                            <div class="col-md-1">
                                <label for="plazo" class="control-label">* N. Plazo:</label>
                                <input type="number" class="form-control input-sm" id="plazo" name="plazo" style="text-align:right" onchange="recalcular_fpago(1);" />                            </div>
                        </div>
                    </div>';
    $sHtml_cab .= '<div class="col-md-12">
                        <div class="form-row">
                            <div class="col-md-2">
                                <label for="tipo_factura">* Tipo Factura:</label>
                                <select id="tipo_factura" name="tipo_factura" class="form-control input-sm" onchange="cargar_factura();">
                                    <option value="0">Seleccione una opcion..</option>
                                    <option value="1">ELECTRONICA</option>
                                    <option value="2">PREIMPRESA</option>
                                </select>
                            </div>
                            <div class="col-md-10">
                                <div id="divFactura"  class="table-responsive"></div>
                            </div>
                            <div style="display: none">
                                <td>' . $ifu->ObjetoHtmlLBL('tipo_pago') . '</td>
                                <td>' . $ifu->ObjetoHtml('tipo_pago') . '</td>
                                <td>' . $ifu->ObjetoHtmlLBL('forma_pago1') . '</td>
                                <td>' . $ifu->ObjetoHtml('forma_pago1') . '</td>   
                                <td>' . $ifu->ObjetoHtml('cuenta_prove') . '</td>
                                <td>' . $ifu->ObjetoHtml('dir_prove') . '</td>
                                <td>' . $ifu->ObjetoHtml('tel_prove') . '</td>
                                <td>' . $fu->ObjetoHtml('ctrl') . '</td>
                                <td>' . $ifu->ObjetoHtml('contri_prove') . '</td>
                            </div>
                        </div>
                    </div>';
    $sHtml_cab .= '<div class="col-md-12">
                        <div class="form-row">
                            <div class="col-md-10">
                                <label for="observaciones">* Observaciones:</label>
                                <input type="text" class="form-control input-sm" id="observaciones" name="observaciones" style="text-align:left !important" />
                            </div>
                            <div class="col-md-2">
                                <div><label class="control-label">&nbsp;&nbsp;.</label></div>
                                <div class="btn btn-success" onclick="javascript:cargar_recepcion();">
                                    <span class="glyphicon glyphicon-th-list"></span>
                                    Orden Compra
                                </div>
                            </div>
                        </div>
                   </div>';
    $sHtml_cab .= '<div class="col-md-12">
                        <div class="form-row">
                            <div class="col-md-2">
                                <label for="observaciones">* Bodega:</label>
                                <select id="bodega" name="bodega" class="form-control input-sm">
                                    <option value="0">Seleccione una opcion..</option>
                                    ' . $lista_bode . '
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="cliente_nombre">Producto:</label>                                
                                <div class="input-group">
                                    <input type="text" class="form-control input-sm" placeholder="ESCRIBA PRODUCTO Y PRESIONE ENTER" id="producto" 
                                    name="producto" onkeyup="autocompletar_producto( ' . $idempresa . ', event, 1 ); form1.producto.value=form1.producto.value.toUpperCase();"/>
                                    <span class="input-group-addon primary" style="cursor: pointer;" onClick="autocompletar_producto_btn( ' . $idempresa . ' );"><i class="fa fa-search"></i></span>
                                </div>   
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="cliente_nombre">Cantidad:</label>    
                                <div class="input-group">
                                    <input type="text" class="form-control input-sm" placeholder="Cantidad" id="cantidad" name="cantidad" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = 0; "/>
                                                                                                   
                                    <span class="input-group-addon primary" style="cursor: pointer;" onClick="obtener_balanza_api();"><i class="fa fa-balance-scale"></i></span>
                                    
                                </div> 
                            </div> 
                            <div class="col-md-2">
                                <label class="control-label" for="costo">Costo:</label>    
                                <input type="text" class="form-control input-sm" placeholder="Costo" id="costo" name="costo" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = 0; " />
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="iva">Impuesto:</label>     
                                <input type="text" class="form-control input-sm" placeholder="Impuesto" id="iva" name="iva" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = 0; " />                              
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="desc1">Dscto %:</label>     
                                <input type="text" class="form-control input-sm" placeholder="Descuento%" id="desc1" name="desc1" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = 0; " />                              
                            </div>                            
                        </div>
                    </div>';

    $sHtml_cab .= '<div class="col-md-12">
                        <div class="form-row">
                            <div class="col-md-3">
                                <label for="observaciones">* Centro Costo:</label>
                                <select id="ccosn" name="ccosn" class="form-control input-sm"  >
                                    <option value="">Seleccione una opcion..</option>
                                    ' . $lista_ccosn . '
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="observaciones">* Tasa Efectiva:</label><br>
                                <input class="form-check-input" type="checkbox" value="S" id="tasa_efectiva_sn" name="tasa_efectiva_sn">
                            </div>
                            <div class="col-md-3">
                                <label class="control-label" id="serie_prod_txt" style="display:none">' . $ifu->ObjetoHtmlLBL('serie') . '</label>
                                <input type="text" style="display:none" class="form-control input-sm" placeholder="Serie" id="serie_prod" name="serie_prod" />                              
                            </div>
                            
                            <div class="col-md-3">
                                <label class="control-label" id="mac_prod_txt" style="display:none">MAC</label>
                                <input type="text" style="display:none" class="form-control input-sm" placeholder="Serie" id="mac_ad_prod" name="mac_ad_prod" />                              
                            </div>

                            <div class="col-md-3">
                                <label class="control-label" id="lote_prod_txt" style="display:none">' . $ifu->ObjetoHtmlLBL('lote') . '</label>
                                <input type="text" style="display:none" class="form-control input-sm" placeholder="Lote" id="lote_prod" name="lote_prod" />     
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="fecha_ela"><div style="display:none"  id="fela_etiq">' . $fu->ObjetoHtmlLBL('fecha_ela') . '</div></label>    
                                <div style="display:none" id="fela_txt" ><input type="date" name="fecha_ela" id="fecha_ela" step="1" onchange="validar_fecha_elaboracion();" class="form-control input-sm"></div>                                                                
                            </div> 
                        </div> 
                    </div> 
                    <div class="col-md-12">
                        <div class="form-row">
                            <div class="col-md-2">
                                <label class="control-label" for="fecha_cad"><div style="display:none"  id="fcad_txt" >' . $fu->ObjetoHtmlLBL('fecha_cad') . '</div></label>    
                                <div style="display:none" id="fcad_etiq"><input type="date" name="fecha_cad" id="fecha_cad" step="1" onchange="validar_fecha_caducidad();" class="form-control input-sm"></div>
                            </div>

                            <div class="col-md-2"> 
                                <label for="observaciones">* Evaluacion:</label>
                                <div id="fcad_etiq">
                                    <div class="btn btn-success btn-sm" onclick="abrir_evaluacion()">
                                        <span class="glyphicon glyphicon-check"></span> Iniciar
                                    </div>
                                </div>                                
                            </div>

                            <div class="col-md-8 text-center">                                
                                <div><label class="control-label">&nbsp;&nbsp;</label></div>
                                <div class="btn btn-success" onclick="javascript:cargar_producto();">
                                    <span class="glyphicon glyphicon-plus-sign"></span>
                                    Agregar Producto
                                </div>
                            </div>  
                            <div class="col-md-12 text-center" style="margin-top: 50px; border: 2px solid black !important; padding: 30px; border-style: dotted !important;">
                                <div class="row justify-content-md-center">
                                    <div class="col-md-12" style="margin-bottom: 10px;">
                                        <label for="archivo">* Cargar Archivo con ordenes de compra:</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="file" name="archivo" id="archivo" onchange="upload_image(id);" required>
                                        <div class="upload-msg"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="S" id="iva_sn" name="iva_sn">
                                            <label class="form-check-label" for="iva_sn">
                                                Incluye ' . $array_imp['IVA'] . '
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="text-align: center; align-content: center;">
                                        <div><label class="control-label"> Ejemplo:</label> </div>
                                        <div class="btn btn-sm">
                                            <span class="glyphicon glyphicon-file" style="text-align:left;"></span>
                                            <div style="text-align:left;">
                                                <a href="ejemplo.txt" download="Archivo Ejemplo Compra.txt" id="txt">
                                                    Ejemplo Archivo
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="btn btn-primary btn-sm" onclick="consultar();" style="width: 100%">
                                            <span class="glyphicon glyphicon-search"></span>
                                            Consultar
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                    </div>
                                </div>
                            </div> 
                            <div style="display: none">
                                <td style="display:none">' . $ifu->ObjetoHtml('cuenta_inv') . '</td>
                                <td style="display:none">' . $ifu->ObjetoHtml('cuenta_iva') . '</td>
                                <td style="display:none">' . $ifu->ObjetoHtml('ejercicio') . '</td>
                                <td style="display:none">' . $ifu->ObjetoHtml('periodo') . '</td>
                                <td style="display:none">' . $ifu->ObjetoHtml('asiento') . '</td>
                            </div>
                        </div>
                    </div>';

    $sHtml_cab .= '</div>';


    $oReturn->assign("divFormularioCabecera", "innerHTML", $sHtml_cab);
    //$oReturn->assign("nota_pedido", "disabled", true);
    $oReturn->assign("divReporte", "innerHTML", "");
    $oReturn->assign("divAbono", "innerHTML", "");
    $oReturn->assign("cliente_nombre", "placeholder", "ESCRIBA EL CLIENTE O RUC Y PRESIONE F4 O ENTER...");
    $oReturn->assign("producto", "placeholder", "ESCRIBA EL PROD. Y PRESIONE F4 ....");
    $oReturn->assign("divFormularioFp", "innerHTML", $sHtml_Fp);
    $oReturn->assign("cliente_nombre", "focus()", "");
    $oReturn->assign("divFormularioRET", "innerHTML", $sHtml_ret);
    $oReturn->script("modal_balanza()");


    $clave_acceso = $_SESSION['claveAccesoExterno'];
    if (!empty($clave_acceso)) {

        $oIfxA = new Dbo;
        $oIfxA->DSN = $DSN_Ifx;
        $oIfxA->Conectar();

        $sql_tran = "SELECT t.tran_cod_tran, t.tran_des_tran 
                        from saetran t, saedefi d 
                        where t.tran_cod_tran = d.defi_cod_tran 
                        and t.tran_cod_empr = 1 
                        and t.tran_cod_sucu = 1 
                        and t.tran_cod_modu = 10 
                        and d.defi_cod_empr = 1 
                        and d.defi_tip_defi = '0' 
                        and d.defi_cod_modu = 10 
                        and tran_cod_tran like '%002%' 
                        order by 2 
                        limit 1
        ";
        $tipo_transaccion = consulta_string($sql_tran, 'tran_cod_tran', $oIfxA, '0');
        $oReturn->assign("tran", "value", $tipo_transaccion);
        $oReturn->assign("clave_acceso_", "value", $clave_acceso);
        $oReturn->script("clave_acceso_sri(1)");
    }


    return $oReturn;
}


function obtener_balanza_api($aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    //Definiciones
    $oReturn = new xajaxResponse();

    $codigo_producto = $aForm['codigo_producto'];



    $id_usuario = $_SESSION['U_USER_INFORMIX'];
    $sql_balanza_seleccionada = "SELECT * from balanza_usuario where usuario_ingr = $id_usuario";

    $codigo_balanza_config = 0;
    if ($oIfx->Query($sql_balanza_seleccionada)) {
        if ($oIfx->NumFilas() > 0) {
            do {
                $id_balanza = $oIfx->f('id_balanza');
                $modulo = $oIfx->f('modulo');
                if ($modulo == 'COMP') {
                    $codigo_balanza_config = $id_balanza;
                }
            } while ($oIfx->SiguienteRegistro());
        } else {
            $oReturn->alert('Debes configurar la balanza dentro de: Lado Derecho/Comercial/Configuracion Balanzas y seleccionar la balanza de compras');
        }
    }

    if ($codigo_balanza_config > 0 || !empty($codigo_balanza_config)) {

        $sql_info_balanza = "SELECT * from config_balanza where id = $codigo_balanza_config";
        $url_api = consulta_string($sql_info_balanza, 'url_api', $oIfx, '');


        $url = $url_api;
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($crl);
        if ($response) {
            $data_balanza = json_decode($response);
            $peso = $data_balanza->peso; // Valor que tiene de peso en la balanza
            $medida = strtoupper($data_balanza->medida); // Medida que esta usando para el pesaje LB, KG, G, ETC 

            $sql_prbo = "SELECT prbo_cod_unid, unid_sigl_unid from saeprbo, saeunid where prbo_cod_unid = unid_cod_unid and prbo_cod_prod = '$codigo_producto'";
            $siglas_unidad = consulta_string($sql_prbo, 'unid_sigl_unid', $oIfx, 0);

            if ($siglas_unidad == $medida) {
                $oReturn->assign('cantidad', "value", $peso);
            } else {
                if ($medida == 'KG') {
                    // Convertir de kilos a Libras
                    $peso_conversor = $peso * 2.20462;
                } else if ($medida == 'LB') {
                    // Convertir de Libras a Kilos
                    $peso_conversor = $peso * 0.453592;
                } else {
                    $peso_conversor = 0;
                    $oReturn->alert('La medida de la balanza debe ser entre KG y LB');
                }
                $oReturn->assign('cantidad', "value", $peso_conversor);
            }
            // $oReturn->script('cargar_producto()');
        } else {
            $oReturn->alert('Sin Conexion Balanza: "' . curl_error($crl) . '" - Code: ' . curl_errno($crl));
        }

        curl_close($crl);
    }


    return $oReturn;
}


function recalcular_fpago($aForm = '', $tipo = 0)
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN_Ifx;

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    if ($tipo == 1) {
        $fecha_compra = $aForm['fecha_pedido'];
        $clpv_pro_pago = $aForm['plazo'];
        if (empty($clpv_pro_pago)) {
            $clpv_pro_pago = 1;
        }
        $fecha_final = date("Y-m-d", strtotime($fecha_compra . "+ " . $clpv_pro_pago . " days"));
        $oReturn->assign('fecha_entrega', 'value', $fecha_final);


        // Asignamos las fechas a las demas opciones
        $oReturn->assign('fecha_inicio', 'value', $fecha_compra);
        $oReturn->assign('dias_fp', 'value', $clpv_pro_pago);
        $oReturn->assign('fecha_final', 'value', $fecha_final);
    } else {
        $fecha_compra = $aForm['fecha_inicio'];
        $clpv_pro_pago = $aForm['dias_fp'];
        if (empty($clpv_pro_pago)) {
            $clpv_pro_pago = 1;
        }
        $fecha_final = date("Y-m-d", strtotime($fecha_compra . "+ " . $clpv_pro_pago . " days"));
        $oReturn->assign('fecha_final', 'value', $fecha_final);


        // Asignamos las fechas a las demas opciones
        $oReturn->assign('fecha_pedido', 'value', $fecha_compra);
        $oReturn->assign('plazo', 'value', $clpv_pro_pago);
        $oReturn->assign('fecha_entrega', 'value', $fecha_final);
    }



    return $oReturn;
}


function cargar_tran($aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $idempresa = $_SESSION['U_EMPRESA'];

    $sucursal = $aForm['sucursal'];

    $sql = "select t.tran_cod_tran, t.tran_des_tran  from saetran t, saedefi d  where
			t.tran_cod_tran = d.defi_cod_tran and
			t.tran_cod_empr = $idempresa and
			t.tran_cod_sucu = $sucursal and
			t.tran_cod_modu = 10 and
			d.defi_cod_empr = $idempresa and
			d.defi_tip_defi = '0' and
			d.defi_cod_modu = 10 order by 2";
    $i = 1;
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            $oReturn->script('eliminar_lista_tran();');
            do {
                $detalle = $oIfx->f('tran_cod_tran') . ' - ' . $oIfx->f('tran_des_tran');
                $oReturn->script(('anadir_elemento_tran(' . $i++ . ',\'' . $oIfx->f('tran_cod_tran') . '\', \'' . $detalle . '\' )'));
            } while ($oIfx->SiguienteRegistro());
        }
    }
    $oIfx->Free();

    return $oReturn;
}


function cargar_bode($aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $idempresa = $_SESSION['U_EMPRESA'];

    $sucursal = $aForm['sucursal'];

    $sql = "select  b.bode_cod_bode, b.bode_nom_bode from saebode b, saesubo s where
			b.bode_cod_bode = s.subo_cod_bode and
			b.bode_cod_empr = $idempresa and
			s.subo_cod_empr = $idempresa and
			s.subo_cod_sucu = $sucursal";
    $i = 1;
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            $oReturn->script('eliminar_lista_bode();');
            do {
                $detalle = $oIfx->f('bode_nom_bode');
                $oReturn->script(('anadir_elemento_bode(' . $i++ . ',\'' . $oIfx->f('bode_cod_bode') . '\', \'' . $detalle . '\' )'));
            } while ($oIfx->SiguienteRegistro());
        }
    }
    $oIfx->Free();

    return $oReturn;
}


function cargar_fpago($aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $idempresa = $_SESSION['U_EMPRESA'];

    $sucursal = $aForm['sucursal'];

    $sql = "select  fpag_cod_fpag, fpag_des_fpag  from saefpag where
			fpag_cod_empr = $idempresa and
			fpag_cod_modu = 10 and
			fpag_cod_sucu = $sucursal	";
    $i = 1;
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            $oReturn->script('eliminar_lista_fpago();');
            do {
                $detalle = $oIfx->f('fpag_des_fpag');
                $oReturn->script(('anadir_elemento_fpago(' . $i++ . ',\'' . $oIfx->f('fpag_cod_fpag') . '\', \'' . $detalle . '\' )'));
            } while ($oIfx->SiguienteRegistro());
        }
    }
    $oIfx->Free();

    return $oReturn;
}

/* * ************************************* */
/* DF01 :: Etiquetas */
/* * ************************************* */
function formulario_etiqueta($id)
{
    //Definiciones
    global $DSN, $DSN_Ifx;
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

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse();

    //variables de session
    unset($_SESSION['ARRAY_ETIQUETAS']);
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];

    $fu->AgregarCampoListaSQL('etiquetam', 'Medidas|left', "select id,nombre from comercial.medida_etiq", true, 170, 10);

    $sHtml = '<fieldset style="border:#999999 1px solid; padding:2px; text-align:center; width:98%; margin-top:1px;" align="center">
                <legend class="Titulo" style="font-size: 9px;">Generar Etiquetas</legend>
                <table style="width:98%" align="center">
					<tr>
						<td align="left" colspan="2">
						<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/full_page24.png"
						title = "Presione para Cerrar";
						style="cursor: pointer;"
						onclick="genera_formulario();"
						alt="Imprimir"
						align="bottom" />
					   
						<td align="right">
						<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/ico-salir.png"
						title = "Presione para Cerrar";
						style="cursor: pointer;"
						onclick="parent.cerrar_ventana();"
						alt="Imprimir"
						align="bottom" />
						</td>
					</tr>
                <tr>
					<td class="labelFrm" align="left">' . $fu->ObjetoHtmlLBL('etiquetam') . '</td>
					<td  align="left">' . $fu->ObjetoHtml('etiquetam') . '</td>
					<td align="right">
						<input type="button" value="GENERAR"
						onClick="javascript:procesar( )"
						class="myButton_BT"
						style="width:100px; height: 25px;"/> 
					</td>
				</tr>
				</table>
				
                <table style="width: 98%;" align="center">
                </tr>
					<th class="diagrama">#</th>
					<th class="diagrama">CODIGO</th>
					<th class="diagrama">PRODUCTO</th>
					<th class="diagrama">MARCA</th>
					<th class="diagrama">COD. BARRAS</th>
					<th class="diagrama">TALLA</th>
					<th class="diagrama">COLOR</th>
					<th class="diagrama">PRECIO</th>
					<th class="diagrama">CANTIDAD</th>
					<th class="diagrama" style="width: 80px;">CHECK</th>
                <tr>';

    $sql1 = "select minv_num_comp, minv_num_sec, dmov_can_dmov, prod_cod_barra, 
			prbo_cod_bode, prbo_dis_prod, prbo_uco_prod, prod_cod_colr,
			dmov_cod_prod, prod_nom_prod, prod_cod_talla, prod_cod_marc, dmov_cod_dmov
			from saeminv, saedmov, saeprod, saeprbo 
			where minv_num_comp = dmov_num_comp and 
			minv_cod_empr = dmov_cod_empr and
			minv_cod_sucu = dmov_cod_sucu and
			prod_cod_prod = prbo_cod_prod and
			prod_cod_prod = dmov_cod_prod and
			dmov_cod_bode = prbo_cod_bode and
			dmov_cod_empr = prbo_cod_empr and
			minv_num_comp = $id and
			minv_cod_empr = $idempresa and
			minv_cod_sucu = $idsucursal and
			minv_est_minv <> '0'";
    if ($oIfx->Query($sql1)) {
        if ($oIfx->NumFilas() > 0) {
            $i = 1;
            unset($arrayEtiqueta);
            do {
                $minv_num_comp  = $oIfx->f('minv_num_comp');
                $prod_cod_prod  = $oIfx->f('dmov_cod_prod');
                $prod_nom_prod  = $oIfx->f('prod_nom_prod');
                $prod_cod_barra = $oIfx->f('prod_cod_barra');
                $prbo_cod_bode  = $oIfx->f('prbo_cod_bode');
                $prod_cod_talla = $oIfx->f('prod_cod_talla');
                $prbo_dis_prod  = $oIfx->f('prbo_dis_prod');
                $prbo_uco_prod  = $oIfx->f('prbo_uco_prod');
                $dmov_can_dmov  = $oIfx->f('dmov_can_dmov');
                $dmov_cod       = $oIfx->f('dmov_cod_dmov');

                //query precio
                $sql = "select ppr_pre_raun from saeppr where ppr_cod_bode = $prbo_cod_bode and ppr_cod_prod = '$prod_cod_prod' and ppr_cod_nomp = 1";
                $ppr_pre_raun = consulta_string_func($sql, 'ppr_pre_raun', $oIfxA, 0);

                if (empty($prod_cod_talla)) {
                    $nomtalla = '';
                } else {
                    $sqltalla = "select talla_cod_talla,talla_nom_talla from saetalla where talla_cod_talla= $prod_cod_talla  ";
                    $nomtalla = consulta_string_func($sqltalla, 'talla_nom_talla', $oIfxA, '');
                }

                $prod_cod_colr = $oIfx->f('prod_cod_colr');

                if (empty($prod_cod_colr)) {
                    $nomcolor = '';
                } else {
                    $sqlcolor = "select color_cod_serial,color_nom_color from saecolor where color_cod_serial = $prod_cod_colr  ";
                    $nomcolor = consulta_string_func($sqlcolor, 'color_nom_color', $oIfxA, '');
                }

                $prod_cod_marc = $oIfx->f('prod_cod_marc');
                $sqlmacr = "select marc_cod_marc,marc_des_marc from saemarc where marc_cod_marc = $prod_cod_marc";
                $marca = consulta_string_func($sqlmacr, 'marc_des_marc', $oIfxA, '');

                // SERIALES
                $serial = $minv_num_comp . '_' . $dmov_cod;

                $ifu->AgregarCampoCheck($serial . '_check', 'S/N',   false, 'N');
                $ifu->AgregarCampoNumerico($serial . '_stock', '',     false, $dmov_can_dmov, 100, 10);

                $arrayEtiqueta[] = array($prod_cod_prod, $marca, $nomcolor, $nomtalla, $ppr_pre_raun, $serial);

                if ($sClass == 'on')
                    $sClass = 'off';
                else
                    $sClass = 'on';

                $sHtml .= '<tr height="20"  class="' . $sClass . '"
                                onMouseOver="javascript:this.className=\'link\';"
                                onMouseOut="javascript:this.className=\'' . $sClass . '\';">
                                 <td width="10px;" class="fecha_letra">' . $i . '</td>                              
                                 <td align="left">' . $prod_cod_prod . '</td>
                                 <td align="left">' . $prod_nom_prod . '</td>
                                 <td align="left">' . $marca . '</td>
                                 <td align="left">' . $prod_cod_barra . '</td>
                                 <td align="left">' . $nomtalla . '</td>
                                 <td align="left">' . $nomcolor . '</td>
								 <td align="right">' . $ppr_pre_raun . '</td>
                                 <td align="right">' . $ifu->ObjetoHtml($serial . '_stock') . '</td>
                                 <td align="center">' . $ifu->ObjetoHtml($serial . '_check') . '</td>                             
                            </tr>';
                $i++;
            } while ($oIfx->SiguienteRegistro());
        }
    }
    $oIfx->Free();

    $sHtml .= '</table>';
    $sHtml .= '</fieldset>';

    $_SESSION['ARRAY_ETIQUETAS'] = $arrayEtiqueta;

    $oReturn->assign("dive", "innerHTML", $sHtml);

    return $oReturn;
}

function enviar_etiquetas($aForm = '')
{

    global $DSN_Ifx, $DSN;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo;
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $oCon = new Dbo();
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oReturn = new xajaxResponse();

    //variables de session
    unset($_SESSION['LIST_CHECK_ETIQUETAS']);
    $id_empresa = $_SESSION['U_EMPRESA'];
    $id_sucursal = $_SESSION['U_SUCURSAL'];
    $array_etiq = $_SESSION['ARRAY_ETIQUETAS'];

    $desde = $aForm['desde'];
    $cant = $aForm['cantidad'];
    $etiquetam = $aForm['etiquetam'];

    unset($etiqueta);
    if (count($array_etiq) > 0) {
        foreach ($array_etiq as $val) {
            $serial = $val[5];
            $check = $aForm[$serial . '_check'];
            if (!empty($check)) {
                $prod = $val[0];
                $marca = $val[1];
                $color = $val[2];
                $talla = $val[3];
                $preci = $val[4];
                $can = $aForm[$serial . '_stock'];

                $etiqueta[] = array($marca, $color, $talla, $preci, $can, $prod);
            } // fin check


        } // fin foreach

        $_SESSION['LIST_CHECK_ETIQUETAS'] = $etiqueta;

        $oReturn->script('etiquetasPrint();');
    } else {
        $oReturn->alert('Por favor realice una Busqueda...');
    }

    return $oReturn;
}



/* * ************************************* */
/* DF01 :: G U A R D A      P E D I D O */
/* * ************************************* */
function guarda_pedido($opcion_tmp, $aForm = '')
{
    //Definiciones
    global $DSN, $DSN_Ifx;
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

    $oConA = new Dbo;
    $oConA->DSN = $DSN;
    $oConA->Conectar();

    $oReturn = new xajaxResponse();
    //      VARIABLES
    $idempresa         = $_SESSION['U_EMPRESA'];
    $sucursal         = $aForm['sucursal'];
    $tipo_retencion = $aForm['tipo_retencion'];
    $aDataGrid         = $_SESSION['aDataGird_INV_MRECO'];

    $aDataGrid_FP     = $_SESSION['aDataGird_Pago'];
    $contdata         = count($aDataGrid);
    $contdatafp     = count($aDataGrid_FP);
    $array_otros     = $_SESSION['U_OTROS'];

    $cliente         = $aForm['cliente'];
    $sql = "select clpv_ret_sn from saeclpv where
				clpv_cod_empr = $idempresa and
				clpv_cod_clpv = $cliente ";
    $clpv_ret_sn = consulta_string($sql, 'clpv_ret_sn', $oIfx, 'N');


    // SIN RETENCION
    $inv_ctrl = 1;


    if ($contdata > 0 && $contdatafp > 0 && $inv_ctrl == 1) {


        // TRANSACCIONALIDAD
        try {
            // commit
            $oIfx->QueryT('BEGIN WORK;');
            // transaccion de informix
            /*             * *********************************************************************** */
            /* F E C H A     D E     P E D I D O     Y     V E N C I M I E N T O      */
            /*             * *********************************************************************** */
            $cliente         = $aForm['cliente'];
            $cliente_nom     = $aForm['cliente_nombre'];
            $ruc             = $aForm['ruc'];
            $fecha_pedido     = $aForm['fecha_pedido'];
            $fecha_entrega     = $aForm['fecha_entrega'];
            $plazo             = $aForm['plazo'];
            $tran             = $aForm['tran'];
            $moneda         = $aForm['moneda'];
            $factura         = $aForm['factura'];
            $serie_prove     = $aForm['serie_prove'];
            $auto_prove     = $aForm['auto_prove'];
            $factura_inicio = $aForm['factura_inicio'];

            $factura_fin     = $aForm['factura_fin'];

            $fecha_prove     = $aForm['fecha_validez'];
            $tipo_pago         = $aForm['tipo_pago'];
            $fpago_prove     = $aForm['forma_pago1'];
            $detalle         = $aForm['observaciones'];
            $anio             = substr($aForm['fecha_pedido'], 0, 4);
            $idprdo         = (substr($aForm['fecha_pedido'], 5, 2)) * 1;
            //$fecha_ejer     = '31-12-'.$anio;
            $fecha_ejer = $anio . '-12-31';

            $sql             = "select ejer_cod_ejer from saeejer where ejer_fec_finl = '$fecha_ejer' and ejer_cod_empr = $idempresa ";
            $idejer         = consulta_string($sql, 'ejer_cod_ejer', $oIfx, 1);
            $fecha_servidor = date("Y-m-d");
            $nombre_cliente = $aForm['cliente_nombre'];
            $usuario_informix = $_SESSION['U_USER_INFORMIX'];
            $usuario_web     = $_SESSION['U_ID'];
            $sql2            = "SELECT usua_cod_empl FROM SAEUSUA WHERE USUA_COD_USUA = $usuario_informix";
            $empleado         = consulta_string($sql2, 'usua_cod_empl', $oIfx, '');
            $sql_tcambio     = "select tcam_fec_tcam, tcam_cod_tcam, tcam_val_tcam from saetcam where
                                                tcam_cod_mone = $moneda and
                                                mone_cod_empr = $idempresa and
                                                tcam_fec_tcam = (select max(tcam_fec_tcam) from saetcam where
                                                                        tcam_cod_mone = $moneda and
                                                                        mone_cod_empr = $idempresa) ";
            $tcambio         = consulta_string($sql_tcambio, 'tcam_cod_tcam', $oIfx, 1);
            $val_tcambio     = //consulta_string($sql_tcambio, 'tcam_val_tcam', $oIfx, 0);

                $desc_general     = $aForm['descuento_general'];
            $desc_valor     = $aForm['descuento_valor'];
            $iva_total         = $aForm['iva_total'];
            $con_iva         = $aForm['con_iva'];
            $sin_iva         = $aForm['sin_iva'];
            $anticipo         = $aForm['anticipo'];
            $fact_tot         = $aForm['total_fac'];
            $fac_ini         = $aForm['fac_ini'];
            $fac_fin         = $aForm['fac_fin'];
            //$cuenta_prove 	= $aForm['cuenta_prove'];
            $dir_prove         = $aForm['dir_prove'];
            $tel_prove         = $aForm['tel_prove'];
            $tel_prove         = $aForm['tel_prove'];
            $hora             = date("H:i:s") . '.00000';
            $usua_nom_usua  = $_SESSION['U_USER'];

            // CUENTA PROVEEDOR
            $sql = "select grpv_cta_grpv from saegrpv where
                            grpv_cod_empr = $idempresa and
                            grpv_cod_grpv in (
                                                select grpv_cod_grpv from saeclpv where 
                                                    clpv_cod_empr = $idempresa and
                                                    clpv_cod_clpv = $cliente	
                                            ) ";
            $cuenta_prove     = consulta_string_func($sql, 'grpv_cta_grpv', $oIfx, '');



            // --------------------------------------------------------------------------
            // CUENTAS ADICIONALES PERU
            // --------------------------------------------------------------------------
            $class = new mayorizacion_class();

            $empr_cod_pais = $_SESSION['U_PAIS_COD'];
            $sql_codInter_pais = "SELECT pais_codigo_inter from saepais where pais_cod_pais = $empr_cod_pais;";
            $codigo_pais = consulta_string_func($sql_codInter_pais, 'pais_codigo_inter', $oIfx, 0);

            $sql_pcon = "SELECT pcon_cue_niif from saepcon WHERE pcon_cod_empr = $idempresa;";
            $pcon_cue_niif = consulta_string_func($sql_pcon, 'pcon_cue_niif', $oIfx, 0);
            // --------------------------------------------------------------------------
            // FIN CUENTAS ADICIONALES PERU
            // --------------------------------------------------------------------------



            //OTROS PARAMETROS XML SRI
            $codDocSustento = "01";
            $numDocSustento = $serie_prove . $factura;
            $codDoc         = '07';
            $fechaEmision     = fecha_mysql($fecha_pedido);

            //CORREO ELECTRONICO
            $minv_email_clpv = $aForm['correo_prove'];

            $sql_control = "select count(*) as contador from saeminv where minv_cod_empr = $idempresa and
                                    minv_cod_sucu = $sucursal and
                                    minv_ser_docu = '$serie_prove' and  
                                    minv_cod_clpv = $cliente and	
									minv_est_minv <> '0' and 
									minv_fac_prov = '$factura' ";
            $contador_ = consulta_string($sql_control, 'contador', $oIfx, '');

            $sql_control_ = "select count(*) as cont   from saefprv      where 
                                fprv_cod_empr     =$idempresa
                                and fprv_cod_sucu = $sucursal
                                and fprv_cod_clpv = $cliente
                                and fprv_num_fact = '$factura'
                                and fprv_num_seri = '$serie_prove' ";
            //$oReturn->alert($sql_control_);
            $contador_1 = consulta_string($sql_control_, 'contador', $oIfx, '');

            //$oReturn->alert($contador_);

            if ($contador_ > 0 || $contador_1 > 0) {
                $oReturn->alert('Factura numero ' . $factura . ' ya ingresada...');
                $oReturn->assign("ctrl", "value", 1);
            } else {
                if ($factura_inicio != '' && $factura_fin != '') {
                    $sql_update = "update saecoa set coa_fact_ini = '$factura_inicio', coa_fact_fin = '$factura_fin', coa_fec_vali = '$fecha_prove' 
                                where clpv_cod_empr = $idempresa and clpv_cod_clpv = $cliente";

                    $oIfx->QueryT($sql_update);
                    $controles = ($factura >= $factura_inicio && $factura <= $factura_fin);
                } else {
                    $fecha_prove = $fecha_pedido;
                    $controles = ($factura != '');
                }

                if ($controles) {
                    $sql = "select tran_des_tran from saetran where
                                        tran_cod_tran = '$tran' and
                                        tran_cod_empr = $idempresa and
                                        tran_cod_sucu = $sucursal ";
                    $des_tran = consulta_string($sql, 'tran_des_tran', $oIfx, '');

                    // OTROS
                    $total_otros = 0;
                    if (count($array_otros) > 0) {
                        $txt = '';
                        $total_otros = 0;
                        foreach ($array_otros as $val) {
                            $id_otro    = $val[0];
                            $det_otro   = $val[1];
                            $txt        = $id_otro . '_OTRO';
                            $val_txt    = $aForm[$txt];
                            if (empty($val_txt)) {
                                $val_txt = 0;
                            }
                            $total_otros += $val_txt;
                        } // fin foreach
                    } // fin otros

                    $total_compra = $fact_tot - $desc_valor + $iva_total + $total_otros;

                    // ASIENTO CONTABLE
                    // TIDU
                    $sql = "select  defi_cod_tidu  from saedefi where
                                    defi_cod_empr = $idempresa and
                                    defi_cod_sucu = $sucursal and
                                    defi_cod_tran = '$tran' ";

                    $tidu = consulta_string($sql, 'defi_cod_tidu', $oIfx, '');

                    // SECUENCIAL DEL ASIENTO
                    $sql = "select  secu_dia_comp, secu_asi_comp from saesecu where
                                    secu_cod_empr = $idempresa and
                                    secu_cod_sucu = $sucursal and
                                    secu_cod_tidu = '$tidu' and
                                    secu_cod_modu = 10 and
                                    secu_cod_ejer = $idejer and
                                    secu_num_prdo = $idprdo ";



                    if ($oIfx->Query($sql)) {
                        if ($oIfx->NumFilas() > 0) {
                            $secu_dia = $oIfx->f('secu_dia_comp');
                            $secu_asto = $oIfx->f('secu_asi_comp');
                        }
                    }
                    $oIfx->Free();

                    $secu_dia_tmp     = substr($secu_dia, 5);
                    $secu_asto_tmp     = substr($secu_asto, 5);
                    $ini_secu_dia     = substr($secu_dia, 0, 5);
                    $ini_secu_asto     = substr($secu_asto, 0, 5);

                    $secu_dia         = $ini_secu_dia . secuencial(2, '', $secu_dia_tmp, 8);
                    $secu_asto         = $ini_secu_asto . secuencial(2, '', $secu_asto_tmp, 8);

                    // UPDATE SECUENCIA SAESECU
                    $sql = "update saesecu set secu_dia_comp = '$secu_dia', 
									secu_asi_comp = '$secu_asto' where
									secu_cod_empr = $idempresa and
									secu_cod_sucu = $sucursal and
									secu_cod_tidu = '$tidu' and
									secu_cod_modu = 10 and
									secu_cod_ejer = $idejer and
									secu_num_prdo = $idprdo ";
                    $oIfx->QueryT($sql);

                    // FACTURA ADICIONAL
                    $dgui                = $aForm['dgui'];

                    if (empty($detalle)) {
                        $detalle_asto = $des_tran . ' ' . $serie_prove . '-' . $factura . '-000' . ' - ' . $dgui;
                    } else {
                        $detalle_asto = $detalle . ' ' . $serie_prove . '-' . $factura . '-000' . ' - ' . $dgui;
                    }


                    // COTIZCION
                    $mone_cod = $aForm["moneda"];
                    $sql      = "select pcon_mon_base from saepcon where pcon_cod_empr = $idempresa ";
                    $mone_base = consulta_string_func($sql, 'pcon_mon_base', $oIfx, '');
                    $coti     = $aForm["cotizacion"];

                    $coti_ext = 0;
                    if ($moneda == $mone_base) {
                        $fil_coti = '';
                        $S_PAIS_API_SRI = $_SESSION['S_PAIS_API_SRI'];
                        if ($S_PAIS_API_SRI == '51') {
                            //PERU: PARA EL CMABIO DE MONEDA SE CONSIDERA LA FECHA DE EMISION DEL COMPORBANTE
                            $fech  = $aForm['fecha_pedido'];

                            $fil_coti = "and tcam_fec_tcam<='$fech'";
                        }


                        // MONEDA LOCAL
                        $sql = "select tcam_val_tcam   from saetcam where
                                    mone_cod_empr = $idempresa and 
                                    tcam_cod_mone in ( select pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa  ) and
                                    tcam_fec_tcam in ( select  max(tcam_fec_tcam)   from saetcam where
                                                                mone_cod_empr = $idempresa and 
                                                                tcam_cod_mone in ( select pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa )  $fil_coti
                                                    ) ";
                        $val_camb = consulta_string_func($sql, 'tcam_val_tcam', $oIfx, '0');
                        $coti_ext = $val_camb;
                    } else {
                        $coti_ext = $coti;
                    }


                    // -------------------------------------------------------------------------------------------------------------------------------------
                    // Metodo para recepcionar la compra en moneda secundaria Adrian47
                    // -------------------------------------------------------------------------------------------------------------------------------------
                    if ($moneda != $mone_base) {
                        $cotizacion_ad = $aForm["cotizacion"];
                        $total_compra = round($total_compra * $cotizacion_ad, 2);
                    }
                    // -------------------------------------------------------------------------------------------------------------------------------------
                    // FIN Metodo para recepcionar la compra en moneda secundaria Adrian47
                    // -------------------------------------------------------------------------------------------------------------------------------------



                    // SAEASTO
                    $sql = "insert into saeasto (  asto_cod_asto,       	asto_cod_empr,      asto_cod_sucu,      asto_cod_ejer,
												   asto_num_prdo,       	asto_cod_mone,      asto_cod_usua,      asto_cod_modu,
												   asto_cod_tdoc,       	asto_ben_asto,      asto_vat_asto,      asto_fec_asto,
												   asto_det_asto,       	asto_est_asto,      asto_num_mayo,      asto_fec_emis,
												   asto_tipo_mov,       	asto_cot_asto,      asto_for_impr,      asto_cod_tidu,
												   asto_usu_asto,       	asto_fec_serv,      asto_user_web  )
                                          values(  '$secu_asto',            $idempresa,         $sucursal,          $idejer,
                                                    $idprdo,                $moneda,            $usuario_informix,  10,
                                                   '$tran',                '$cliente_nom',      $total_compra,      '$fecha_pedido',
                                                   '$detalle_asto',        'MY',                '$secu_dia',        '$fecha_pedido',
                                                   'DI',                    $coti,              8,                  '$tidu',
                                                   '$usua_nom_usua',        CURRENT_DATE,            $usuario_web  )";
                    $oIfx->QueryT($sql);


                    $S_PAIS_API_SRI = $_SESSION['S_PAIS_API_SRI'];  // 593 --- 51 
                    // Adrian47
                    if ($S_PAIS_API_SRI == '51') {

                        $dias_cuotas_fp = $aForm['dias_cuotas_fp_input'];
                        $cuotas_fp = $aForm['cuotas_fp_input'];
                        $fecha_inicio = $aForm['fecha_inicio_input'];
                        $valor_peru = $aForm['valor_input'];

                        $fecha_dias = $fecha_inicio;
                        $porcentaje_cuotas = round(100 / $cuotas_fp, 2);
                        $valor_cuota = round($valor_peru / $cuotas_fp, 2);

                        // -------------------------------------------------------------------------------------------------------------------------------------
                        // Metodo para recepcionar la compra en moneda secundaria Adrian47
                        // -------------------------------------------------------------------------------------------------------------------------------------
                        if ($moneda != $mone_base) {
                            $cotizacion_ad = $aForm["cotizacion"];
                            $valor_cuota = round($valor_cuota * $cotizacion_ad, 2);
                            $valor_peru = round($valor_peru * $cotizacion_ad, 2);
                        }
                        // -------------------------------------------------------------------------------------------------------------------------------------
                        // FIN Metodo para recepcionar la compra en moneda secundaria Adrian47
                        // -------------------------------------------------------------------------------------------------------------------------------------


                        $suma_dias = 0;
                        $suma_porcentaje = 0;
                        $suma_valor_pagar = 0;

                        $suma_porcentaje2 = 0;
                        $suma_valor_pagar2 = 0;

                        for ($x = 1; $x <= $cuotas_fp; $x++) {

                            $suma_dias += $dias_cuotas_fp;
                            $suma_porcentaje += $porcentaje_cuotas;
                            $suma_valor_pagar += $valor_cuota;

                            if ($x == $cuotas_fp) {
                                if ($suma_porcentaje > 100) {
                                    $diferencia = $suma_porcentaje - 100;
                                    $porcentaje_cuotas = round($porcentaje_cuotas - $diferencia, 2);
                                } else if ($suma_porcentaje < 100) {
                                    $diferencia = 100 - $suma_porcentaje;
                                    $porcentaje_cuotas = round($porcentaje_cuotas + $diferencia, 2);
                                }

                                if ($suma_valor_pagar > $valor_peru) {
                                    $diferencia_val = $suma_valor_pagar - $valor_peru;
                                    $valor_cuota = round($valor_cuota - $diferencia_val, 2);
                                } else if ($suma_valor_pagar < $valor_peru) {
                                    $diferencia_val = $valor_peru - $suma_valor_pagar;
                                    $valor_cuota = round($valor_cuota + $diferencia_val, 2);
                                }
                            }


                            $suma_porcentaje2 += $porcentaje_cuotas;
                            $suma_valor_pagar2 += $valor_cuota;


                            $fecha_dias = date("Y-m-d", strtotime($fecha_dias . "+ $dias_cuotas_fp days"));

                            /*
                            $sHtml .= '<tr>
                                           <td style="width: 1.5%;">' . $x . '</td> 
                                           <td style="width: 1.5%;">' . $fecha_inicio . '</td>
                                           <td style="width: 1.5%;">' . $dias_cuotas_fp . '</td>
                                           <td style="width: 1.5%;">' . $fecha_dias . '</td>
                                           <td style="width: 1.5%;">' . $fpag_des_fpag . '</td>
                                           <td style="width: 1.5%;">' . $porcentaje_cuotas . '%</td>
                                           <td style="width: 1.5%;">' . $valor_cuota . '</td>
                                           
                                       </tr>';
                                       */


                            // --------------------------------------------
                            // Insertamos la dir con los datos de credito
                            // --------------------------------------------

                            $total_compra_ext = 0;
                            if ($moneda == $mone_base) {
                                // MONEDA LOCAL
                                $sql = "select tcam_val_tcam   from saetcam where
                                    mone_cod_empr = $idempresa and 
                                    tcam_cod_mone in ( select pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa  ) and
                                    tcam_fec_tcam in ( select  max(tcam_fec_tcam)   from saetcam where
                                                                mone_cod_empr = $idempresa and 
                                                                tcam_cod_mone in ( select pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa )
                                                    ) ";
                                $val_camb = consulta_string_func($sql, 'tcam_val_tcam', $oIfx, '0');
                                $total_compra_ext = round(($valor_cuota / $val_camb), 2);
                            } else {
                                $total_compra_ext = round(($valor_cuota / $coti), 2);
                            }

                            // SAEDIR FACTURA COMPRA
                            $secuencial_dir_ad = str_pad($x, 3, "0", STR_PAD_LEFT);
                            $fact_dir = $serie_prove . '-' . $factura . '-' . $secuencial_dir_ad;
                            $sql = "insert into saedir ( dir_cod_dir,          dire_cod_asto,     dire_cod_empr,       dire_cod_sucu,
                                                  asto_cod_ejer,        asto_num_prdo,     dir_cod_cli,         tran_cod_modu,
                                                  dir_cod_tran,         dir_num_fact,      dir_fec_venc,
                                                  dir_detalle,          dire_tip_camb,     dir_deb_ml,          dir_cre_ml,
                                                  dir_deb_mex,          dir_cred_mex,      bandera_cr,          dir_aut_usua,
                                                  dir_aut_impr,         dir_fac_inic,      dir_fac_fina,        dir_ser_docu,
                                                  dir_fec_vali,         dire_suc_clpv,     dir_user_web  )
                                         values(  $x,                    '$secu_asto',     $idempresa,          $sucursal,
                                                  $idejer,              $idprdo,          $cliente,            10,
                                                  '$tran',              '$fact_dir',      '$fecha_dias',
                                                  '$detalle_asto',       $coti,            0,                  $valor_cuota,
                                                   0,                   $total_compra_ext, 'CR',                '$auto_prove',
                                                  '$auto_prove',        '$fac_ini',       '$fac_fin' ,         '$serie_prove',
                                                  '$fecha_prove',       $sucursal,        $usuario_web  ); ";

                            $oIfx->QueryT($sql);

                            // --------------------------------------------
                            // FIN Insertamos la dir con los datos de credito
                            // --------------------------------------------


                        }
                    } else {
                        $total_compra_ext = 0;
                        if ($moneda == $mone_base) {
                            // MONEDA LOCAL
                            $sql = "select tcam_val_tcam   from saetcam where
                                    mone_cod_empr = $idempresa and 
                                    tcam_cod_mone in ( select pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa  ) and
                                    tcam_fec_tcam in ( select  max(tcam_fec_tcam)   from saetcam where
                                                                mone_cod_empr = $idempresa and 
                                                                tcam_cod_mone in ( select pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa )
                                                    ) ";
                            $val_camb = consulta_string_func($sql, 'tcam_val_tcam', $oIfx, '0');
                            $total_compra_ext = round(($total_compra / $val_camb), 2);
                        } else {
                            $total_compra_ext = round(($total_compra / $coti), 2);
                        }

                        // SAEDIR FACTURA COMPRA
                        $fact_dir = $serie_prove . '-' . $factura . '-000';
                        $sql = "insert into saedir ( dir_cod_dir,          dire_cod_asto,     dire_cod_empr,       dire_cod_sucu,
                                                  asto_cod_ejer,        asto_num_prdo,     dir_cod_cli,         tran_cod_modu,
                                                  dir_cod_tran,         dir_num_fact,      dir_fec_venc,
                                                  dir_detalle,          dire_tip_camb,     dir_deb_ml,          dir_cre_ml,
                                                  dir_deb_mex,          dir_cred_mex,      bandera_cr,          dir_aut_usua,
                                                  dir_aut_impr,         dir_fac_inic,      dir_fac_fina,        dir_ser_docu,
                                                  dir_fec_vali,         dire_suc_clpv,     dir_user_web  )
                                         values(  1,                    '$secu_asto',     $idempresa,          $sucursal,
                                                  $idejer,              $idprdo,          $cliente,            10,
                                                  '$tran',              '$fact_dir',      '$fecha_entrega',
                                                  '$detalle_asto',       $coti,            0,                  $total_compra,
                                                   0,                   $total_compra_ext, 'CR',                '$auto_prove',
                                                  '$auto_prove',        '$fac_ini',       '$fac_fin' ,         '$serie_prove',
                                                  '$fecha_prove',       $sucursal,        $usuario_web  ); ";

                        $oIfx->QueryT($sql);
                    }




                    // SAEDASI PROVEEDOR COMPRA
                    // NOMBRE PROVEEDOR
                    $sql = "select  cuen_nom_cuen  from saecuen where
                                    cuen_cod_empr = $idempresa and
                                    cuen_cod_cuen = '$cuenta_prove' ";
                    $cuen_prove_nom = consulta_string($sql, 'cuen_nom_cuen', $oIfx, '');

                    if (empty($cuen_prove_nom)) {
                        throw new Exception('La cuenta contable: ' . $cuenta_prove . ' del PROVEEDOR no existe, No existe. ...');
                    }

                    $sql = "insert into saedasi    (  asto_cod_asto,        asto_cod_empr,      asto_cod_sucu,      dasi_num_prdo,
                                                      asto_cod_ejer,        dasi_cod_cuen,      dasi_dml_dasi,      dasi_cml_dasi,
                                                      dasi_dme_dasi,        dasi_cme_dasi,      dasi_tip_camb,      dasi_det_asi,
                                                      dasi_nom_ctac,        dasi_cod_clie,      dasi_cod_tran,      dasi_user_web )
                                            values  ( '$secu_asto',         $idempresa,         $sucursal,          $idprdo,
                                                       $idejer,            '$cuenta_prove',     0,                  $total_compra,
                                                       0,                   $total_compra_ext,  $coti,              '$detalle_asto' ,
                                                      '$cuen_prove_nom',    $cliente,           '$tran',            $usuario_web   ); ";
                    $oIfx->QueryT($sql);



                    // --------------------------------------------------------------------------
                    // CUENTAS ADICIONALES PERU
                    // --------------------------------------------------------------------------


                    if ($codigo_pais == 51 && $pcon_cue_niif == 'S') {

                        // Cuando es Peru baja dos cuentas adicionales: cuan_ana_deb y cuen_anal_cre
                        $sql_cuen = "SELECT cuen_ana_deb, cuen_ana_cre from saecuen where cuen_cod_cuen = '$cuenta_prove';";
                        $cuen_ana_deb = consulta_string_func($sql_cuen, 'cuen_ana_deb', $oIfx, '');
                        $cuen_ana_cre = consulta_string_func($sql_cuen, 'cuen_ana_cre', $oIfx, '');

                        if (!empty($cuen_ana_deb) && !empty($cuen_ana_cre)) {
                            $existe_cuenta = 'N';
                        } else {
                            $existe_cuenta = 'S';
                        }

                        if ($existe_cuenta == 'N') {


                            // --------------------------------------
                            // DEBITO
                            // --------------------------------------


                            $detalle_asto_db = 'CUENTA ADICIONAL DEBITO';
                            $class->saedasi(
                                $oIfx,
                                $idempresa,
                                $sucursal,
                                $cuen_ana_deb,
                                $idprdo,
                                $idejer,
                                '',
                                $total_compra,
                                0,
                                $total_compra_ext,
                                0,
                                $coti,
                                $detalle_asto_db,
                                $cliente,
                                $tran,
                                $usuario_web,
                                $secu_asto
                            );

                            // --------------------------------------
                            // CREDITO
                            // --------------------------------------

                            $detalle_asto_cr = 'CUENTA ADICIONAL CREDITO';
                            $class->saedasi(
                                $oIfx,
                                $idempresa,
                                $sucursal,
                                $cuen_ana_cre,
                                $idprdo,
                                $idejer,
                                '',
                                0,
                                $total_compra,
                                0,
                                $total_compra_ext,
                                $coti,
                                $detalle_asto_cr,
                                $cliente,
                                $tran,
                                $usuario_web,
                                $secu_asto
                            );
                        }
                    }

                    // --------------------------------------------------------------------------
                    // FIN CUENTAS ADICIONALES PERU
                    // --------------------------------------------------------------------------



                    // SECUENCIAL MINV INGRESO COMPRA
                    $sql_defi = "SELECT DEFI_COD_MODU, DEFI_TRS_DEFI  , DEFI_TIP_DEFI, DEFI_FOR_DEFI FROM SAEDEFI WHERE
                                        DEFI_COD_EMPR = $idempresa AND
                                        DEFI_COD_SUCU = $sucursal and
                                        defi_cod_modu = 10 and
                                        defi_tip_defi = '0' and
                                        defi_cod_tran = '$tran' ";
                    $secu_minv = '';
                    $formato = 0;
                    if ($oIfx->Query($sql_defi)) {
                        if ($oIfx->NumFilas() > 0) {
                            $secu_minv = $oIfx->f('defi_trs_defi');
                            $formato = $oIfx->f('defi_for_defi');
                        }
                    }

                    if (empty($formato)) {
                        $formato = 0;
                    }

                    $oIfx->Free();
                    $secu_minv = secuencial(2, '0', $secu_minv, 8);

                    $ret_electronica = $aForm['electronica'];

                    // FACTURA ADICIONAL
                    $dgui                = $aForm['dgui'];
                    $minv_fec_ncf        = $aForm['minv_fec_ncf'];
                    if (!empty($minv_fec_ncf)) {
                        $minv_fec_ncf  = fecha_informix_func($minv_fec_ncf);
                    }

                    ///
                    //INGRESO ALMACEN
                    $array_oc = $_SESSION['U_PROF_APROB_RECO'];

                    $msn_reco = '';
                    if (count($array_oc) > 0) {
                        foreach ($array_oc as $val) {
                            $clpv_cod_oc = $val[0];
                            $serial_oc   = $val[2];

                            $sql = "update saeminv set  minv_cer_sn         = 'S' ,
                                                minv_fech_modi      = CURRENT_DATE,
                                                minv_usua_modi      = $usuario_web where
                                                minv_cod_empr       = $idempresa and
                                                minv_cod_sucu       = $sucursal and
                                                minv_num_comp       = $serial_oc ";
                            $oIfx->QueryT($sql);


                            //$sql = "update saedmov set dmov_can_entr = dmov_can_dmov  where dmov_num_comp = $serial_oc and dmov_cod_empr = $idempresa and dmov_cod_sucu = $sucursal ";
                            //$oIfx->QueryT($sql);
                        }
                    } // FIN RECO

                    //SECUENCIAL ORDEN DE COMPRA 
                    if (empty($serial_oc)) {
                        $serial_oc = 'NULL';
                    }


                    $minv_nom_clpv = $aForm['cliente_nombre'];
                    $minv_ruc_clpv = $aForm['ruc'];

                    if (empty($fact_tot)) {
                        $fact_tot = 'null';
                    }

                    if (empty($desc_valor)) {
                        $desc_valor = 'null';
                    }

                    if (empty($iva_total)) {
                        $iva_total = 'null';
                    }

                    $sql_maxminv = "select max(minv_num_comp) as maximo from saeminv";
                    $minv_num_comp = consulta_string($sql_maxminv, 'maximo', $oIfx, 0);

                    $fecha_regc = $aForm['fecha_regc'];
                    //INGRESO DEL MOVIMIENTO  SAEMINV 
                    $sql_minv = "insert into saeminv(	minv_num_comp,      minv_num_plaz,  	minv_num_sec,       	minv_cod_tcam,
                                                        minv_cod_mone,  	minv_cod_empr,      	minv_cod_sucu,
                                                        minv_cod_tran,  	minv_cod_modu,      	minv_cod_empl,
                                                        minv_cod_ftrn,  	minv_fmov,          	minv_dege_minv,
                                                        minv_cod_usua,  	minv_num_prdo,      	minv_cod_ejer,
                                                        minv_fac_prov,  	minv_fec_entr,      	minv_fec_ser,   
                                                        minv_est_minv,  	minv_tot_minv,      	minv_con_iva,
                                                        minv_sin_iva,   	minv_dge_valo,      	minv_iva_valo,
                                                        minv_otr_valo,  	minv_fle_minv,      	minv_aut_usua,
                                                        minv_aut_impr,  	minv_fac_inic,      	minv_fac_fina,
                                                        minv_ser_docu,  	minv_fec_valo,      	minv_sucu_clpv,
                                                        minv_sno_esta,  	minv_usu_minv ,     	minv_cm1_minv,
                                                        minv_fec_regc,  	minv_cod_fpagop,    	minv_cod_tpago,
                                                        minv_ani_minv,  	minv_mes_minv,      	minv_user_web,
                                                        minv_comp_cont, 	minv_tran_minv,     	minv_cod_clpv,
                                                        minv_email_clpv, 	minv_elec_sn,			minv_num_dgi ,
														minv_val_tcam ,     minv_cm6_minv,          minv_nom_clpv,
														minv_ruc_clpv,      minv_comp_ord	)
                                                  values( ($minv_num_comp + 1),   0,             	'$secu_minv',       	$tcambio,
                                                          $moneda,        	$idempresa,         	$sucursal,
                                                          '$tran',        	10,               		'$empleado',
                                                          '$formato',     	'$fecha_pedido',    	0,
                                                          $usuario_informix, $idprdo,         		$idejer,
                                                          '$factura',     	'$fecha_entrega',   	CURRENT_DATE,        
                                                          'M',             	$fact_tot,         		0,
                                                          0,              	$desc_valor,        	$iva_total,
                                                          $total_otros,   	0,                 		'$auto_prove',
                                                          '',             	'$fac_ini',         	'$fac_fin',
                                                          '$serie_prove', 	'$fecha_prove',      	$sucursal,
                                                          0,              	'$usua_nom_usua',   	'$detalle',
                                                          '$fecha_regc',        	'$fpago_prove',     	'$tipo_pago',
                                                          $anio,           	$idprdo,            	$usuario_web,
                                                          '$secu_asto' ,  	'$secu_asto',        	$cliente,
                                                          '$minv_email_clpv', '$ret_electronica',   '$dgui' ,
														  $coti_ext,         '$msn_reco' ,			'$minv_nom_clpv',
														  '$minv_ruc_clpv'	, $serial_oc) ";
                    $oIfx->QueryT($sql_minv);



                    // ---------------------------------------------------------------------
                    // Actualizar secuecial automatico de factura de gasto
                    // ---------------------------------------------------------------------
                    $sec_automatico = $aForm['sec_automatico'];
                    if ($sec_automatico == 'S') {
                        $factura_secuencial = trim($aForm['factura']);
                        $sql_update_para_pv = "UPDATE comercial.parametro_inv set secuencial_factura = '$factura_secuencial'
															where empr_cod_empr = $idempresa
										";
                        $oIfx->QueryT($sql_update_para_pv);
                    }

                    // ---------------------------------------------------------------------
                    // Actualizar secuecial automatico de factura de gasto
                    // ---------------------------------------------------------------------


                    //UPDATE AL SECUENCIAL SAEDEFI
                    $sql_update = "UPDATE SAEDEFI SET DEFI_TRS_DEFI = '$secu_minv' WHERE
                                            DEFI_COD_EMPR = $idempresa AND
                                            DEFI_COD_SUCU = $sucursal and
                                            defi_cod_modu = 10 and
                                            defi_tip_defi = '0' and
                                            defi_cod_tran = '$tran' ";
                    $oIfx->QueryT($sql_update);

                    //SERIAL DEL SAEDMIV
                    $serial_minv = 0;
                    $sql_serial = "select minv_num_comp from saeminv where
                                            minv_num_sec = '$secu_minv' and
                                            minv_cod_empr = $idempresa and
                                            minv_cod_sucu = $sucursal and
											minv_cod_clpv = $cliente and
                                            minv_cod_tran = '$tran' ";
                    $serial_minv = consulta_string($sql_serial, 'minv_num_comp', $oIfx, 0);

                    // OTROS
                    if (count($array_otros) > 0) {
                        $txt = '';
                        $total_otros = 0;
                        $x = 1;
                        foreach ($array_otros as $val) {
                            $id_otro    = $val[0];
                            $det_otro   = $val[1];
                            $cuen_otro  = $val[2];
                            $cuen_nom   = $val[3];
                            $txt        = $id_otro . '_OTRO';
                            $val_txt    = $aForm[$txt];
                            if (empty($val_txt)) {
                                $val_txt = 0;
                            }
                            if ($val_txt > 0) {
                                // insertar otros
                                $sql = "insert into saemvre ( mvre_cod_rcgo, mvre_num_comp, mvre_cod_empr, mvre_cod_sucu,
                                                                  mvre_num_prdo, mvre_cod_ejer, mvre_val_mvca, mvre_por_mvca, mvre_ban_decr )
                                                        values (  $x,            $serial_minv, $idempresa,     $sucursal,
                                                                  $idprdo,       $idejer,      $val_txt,       0,            'N' )";
                                $oIfx->QueryT($sql);
                                $x++;


                                // -------------------------------------------------------------------------------------------------------------------------------------
                                // Metodo para recepcionar la compra en moneda secundaria Adrian47
                                // -------------------------------------------------------------------------------------------------------------------------------------
                                if ($moneda != $mone_base) {
                                    $cotizacion_ad = $aForm["cotizacion"];
                                    $val_txt = round($val_txt * $cotizacion_ad, 2);
                                }
                                // -------------------------------------------------------------------------------------------------------------------------------------
                                // FIN Metodo para recepcionar la compra en moneda secundaria Adrian47
                                // -------------------------------------------------------------------------------------------------------------------------------------



                                $val_txt_ext = 0;
                                if ($moneda == $mone_base) {
                                    // MONEDA LOCAL
                                    $sql = "select tcam_val_tcam   from saetcam where
                                                mone_cod_empr = $idempresa and 
                                                tcam_cod_mone in ( select pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa  ) and
                                                tcam_fec_tcam in ( select  max(tcam_fec_tcam)   from saetcam where
                                                                            mone_cod_empr = $idempresa and 
                                                                            tcam_cod_mone in ( select pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa )
                                                                ) ";
                                    $val_camb = consulta_string_func($sql, 'tcam_val_tcam', $oIfx, '0');
                                    $val_txt_ext = round(($val_txt / $val_camb), 2);
                                } else {
                                    $val_txt_ext = round(($val_txt / $coti), 2);
                                }

                                // SAEDASI OTROS
                                $sql = "insert into saedasi (asto_cod_asto,        asto_cod_empr,      asto_cod_sucu,      dasi_num_prdo,
                                                                  asto_cod_ejer,        dasi_cod_cuen,      dasi_dml_dasi,      dasi_cml_dasi,
                                                                  dasi_dme_dasi,        dasi_cme_dasi,      dasi_tip_camb,      dasi_det_asi,
                                                                  dasi_nom_ctac,        dasi_cod_clie,      dasi_cod_tran,      dasi_user_web )
                                                        values  ( '$secu_asto',         $idempresa,         $sucursal,          $idprdo,
                                                                   $idejer,            '$cuen_otro',        $val_txt,           0,
                                                                   $val_txt_ext,        0,                  $coti,             '$detalle_asto' ,
                                                                  '$cuen_nom',          $cliente,           '$tran',            $usuario_web   ); ";
                                $oIfx->QueryT($sql);

                                // --------------------------------------------------------------------------
                                // CUENTAS ADICIONALES PERU
                                // --------------------------------------------------------------------------


                                if ($codigo_pais == 51 && $pcon_cue_niif == 'S') {

                                    // Cuando es Peru baja dos cuentas adicionales: cuan_ana_deb y cuen_anal_cre
                                    $sql_cuen = "SELECT cuen_ana_deb, cuen_ana_cre from saecuen where cuen_cod_cuen = '$cuen_otro';";
                                    $cuen_ana_deb = consulta_string_func($sql_cuen, 'cuen_ana_deb', $oIfx, '');
                                    $cuen_ana_cre = consulta_string_func($sql_cuen, 'cuen_ana_cre', $oIfx, '');

                                    if (!empty($cuen_ana_deb) && !empty($cuen_ana_cre)) {
                                        $existe_cuenta = 'N';
                                    } else {
                                        $existe_cuenta = 'S';
                                    }

                                    if ($existe_cuenta == 'N') {


                                        // --------------------------------------
                                        // DEBITO
                                        // --------------------------------------


                                        $detalle_asto_db = 'CUENTA ADICIONAL DEBITO';
                                        $class->saedasi(
                                            $oIfx,
                                            $idempresa,
                                            $sucursal,
                                            $cuen_ana_deb,
                                            $idprdo,
                                            $idejer,
                                            '',
                                            $val_txt,
                                            0,
                                            $val_txt_ext,
                                            0,
                                            $coti,
                                            $detalle_asto_db,
                                            $cliente,
                                            $tran,
                                            $usuario_web,
                                            $secu_asto
                                        );

                                        // --------------------------------------
                                        // CREDITO
                                        // --------------------------------------

                                        $detalle_asto_cr = 'CUENTA ADICIONAL CREDITO';
                                        $class->saedasi(
                                            $oIfx,
                                            $idempresa,
                                            $sucursal,
                                            $cuen_ana_cre,
                                            $idprdo,
                                            $idejer,
                                            '',
                                            0,
                                            $val_txt,
                                            0,
                                            $val_txt_ext,
                                            $coti,
                                            $detalle_asto_cr,
                                            $cliente,
                                            $tran,
                                            $usuario_web,
                                            $secu_asto
                                        );
                                    }
                                }

                                // --------------------------------------------------------------------------
                                // FIN CUENTAS ADICIONALES PERU
                                // --------------------------------------------------------------------------


                            }
                        } // fin foreach
                    } // fin otros
                    //




                    //   DETALLE SAEDMOV
                    $x = 1;


                    $j = 0;
                    unset($arrray_dmov);
                    //SECUENCIAL DMOV



                    $array_oc = $_SESSION['U_PROF_APROB_RECO'];
                    $msn_reco = '';
                    if (count($array_oc) > 0) {
                        foreach ($array_oc as $val) {
                            $clpv_cod_oc = $val[0];
                            $serial_oc   = $val[2];

                            $sql_update_saeminv_oc = "UPDATE saeminv set  minv_cer_sn = 'N' ,
                                                                minv_fech_modi      = CURRENT_DATE,
                                                                minv_usua_modi      = $usuario_web where
                                                                minv_cod_empr       = $idempresa and
                                                                minv_cod_sucu       = $sucursal and
                                                                minv_num_comp       = $serial_oc ";
                            $oIfx->QueryT($sql_update_saeminv_oc);
                        }
                    }




                    foreach ($aDataGrid as $aValues) {

                        $sql = "select max(dmov_cod_dmov) as ultimo_id from saedmov";
                        $codmov = consulta_string($sql, 'ultimo_id', $oIfx, 0) + 1;


                        // ----------------------------------------------------------------
                        // Tipo de moneda para realizar las compras y el costo salga con moneda local
                        // ----------------------------------------------------------------
                        $moneda = $aForm['moneda'];
                        $cotizacion = $aForm['cotizacion'];
                        $sql = "SELECT pcon_mon_base, pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa ";
                        $mone_base = consulta_string_func($sql, 'pcon_mon_base', $oIfx, '');
                        $mone_secu = consulta_string_func($sql, 'pcon_seg_mone', $oIfx, '');
                        // ----------------------------------------------------------------
                        // FIN Tipo de moneda para realizar las compras y el costo salga con moneda local
                        // ----------------------------------------------------------------


                        $sql_d = 'insert into saedmov(dmov_cod_dmov,   dmov_cod_prod,     dmov_cod_sucu,
                                                    dmov_cod_empr,   dmov_cod_bode,     dmov_cod_unid,
                                                    dmov_cod_ejer,   dmov_num_comp,     dmov_num_prdo,
                                                    dmov_can_dmov,   dmov_can_entr,     dmov_cun_dmov,
                                                    dmov_cto_dmov,   dmov_pun_dmov,     dmov_pto_dmov,
                                                    dmov_ds1_dmov,   dmov_ds2_dmov,     dmov_ds3_dmov,
                                                    dmov_ds4_dmov,   dmov_des_tota,     dmov_imp_dmov,
                                                    dmov_est_dmov,   dmov_iva_dmov,     dmov_iva_porc,
                                                    dmov_dis_dmov,   dmov_ice_dmov,     dmov_hor_crea,
                                                    dmov_cod_tran,   dmov_fac_prov,     dmov_cod_clpv,
                                                    dmov_fmov,       dmov_pto1_dmov,    dmov_cod_lote,  dmov_cod_serie,
                                                    dmov_ela_lote,   dmov_cad_lote,     dmov_cod_ccos	)
                                                values ';

                        //$oReturn->alert($codmov);
                        $aux = 0;
                        $total = 0;
                        $pedf_iva = 0;



                        $sql_d .= "(";


                        foreach ($aValues as $aVal) {


                            if ($aux == 0) {
                                $sql_d .= " " . $codmov . ",";                 //dmov cod dmov
                            } elseif ($aux == 1) {
                                $bod = $aVal;
                            } elseif ($aux == 2) {
                                $prod = $aVal;
                            } elseif ($aux == 4) {
                                $sql_d .= " '" . $prod . "',";
                                $sql_d .= " '" . $sucursal . "',";
                                $sql_d .= " '" . $idempresa . "',";
                                $sql_d .= " '" . $bod . "',";
                                $sql_d .= " " . $aVal . ",";                     //dpef_cod_unid   		UNIDAD
                                $sql_d .= " '" . $idejer . "',";
                                $sql_d .= " '" . $serial_minv . "',";
                                $sql_d .= " '" . $idprdo . "',";
                            } elseif ($aux == 5) {
                                $cant = $aVal;
                            } elseif ($aux == 6) {
                                $costo = $aVal;
                            } elseif ($aux == 7) {                                  //IVA
                                $iva = $aVal;
                            } elseif ($aux == 8) {                                  //DESCUENTO 1
                                $descuento = $aVal;
                            } elseif ($aux == 9) {                                  //DESCUENTO 2
                                $descuento_2 = $aVal;
                            } elseif ($aux == 10) {
                                $desc_gral = $aVal;
                            } elseif ($aux == 11) {                                 //SUB TOTAL
                                $total = $aVal;
                                $costo_real = round(($total / $cant), 6);
                            } elseif ($aux == 12) {                                 //TOTAL CON IVA
                                $total_iva = $aVal;
                            } elseif ($aux == 13) {
                                $lote = $aVal;
                            } elseif ($aux == 19) {
                                $cuenta_iva = $aVal;
                            } elseif ($aux == 14) {
                                $fec_ela = $aVal;
                                if (!empty($fec_ela)) {
                                    $fec_ela = $aVal;
                                }
                            } elseif ($aux == 15) {
                                $fec_cad = $aVal;
                                if (!empty($fec_cad)) {
                                    $fec_cad = $aVal;
                                }
                            } elseif ($aux == 18) {
                                $cuenta_prod = $aVal;
                            } elseif ($aux == 23) {
                                //$codpedi = $aVal;

                                $serie = $aVal;

                                // ----------------------------------------------------------------
                                // Tipo de moneda para realizar las compras y el costo salga con moneda local
                                // ----------------------------------------------------------------
                                if ($moneda != $mone_base) {
                                    $costo = $costo * $cotizacion;
                                    $costo_real = $costo_real * $cotizacion;
                                }
                                // ----------------------------------------------------------------
                                // FIN Tipo de moneda para realizar las compras y el costo salga con moneda local
                                // ----------------------------------------------------------------




                                // PORTAFOLIO DE PRODCUTOS
                                $sql = "select count(*) as cont from saeppvpr where
											ppvpr_cod_empr = $idempresa and
											ppvpr_cod_sucu = $sucursal and
											ppvpr_cod_clpv = $cliente and
											ppvpr_cod_prod = '$prod' and
											ppvpr_cod_bode = $bod ";
                                $ru = consulta_string($sql, 'cont', $oIfx, 0);
                                if ($ru == 0) {
                                    // INSRETAR
                                    $sql = "select prod_nom_prod from saeprod where 
													prod_cod_empr = $idempresa and 
													prod_cod_sucu = $sucursal and
													prod_cod_prod = '$prod' ";
                                    $prod_nom = consulta_string($sql, 'prod_nom_prod', $oIfx, '');
                                    /*$sql = "insert into saeppvpr  ( ppvpr_cod_sucu,		ppvpr_cod_empr,			ppvpr_cod_clpv,	
																	ppvpr_cod_prod,		ppvpr_nom_prod,			ppvpr_pre_pac,
																	ppvpr_cod_bode	)
															values ( $sucursal, 		$idempresa,				$cliente,
																	 '$prod',			'$prod_nom',			$costo,
																	 $bod
																	)";
									$oIfx->QueryT($sql);		*/
                                }

                                // ARRAY DE CUENTA PROD Y IVA
                                $arrray_dmov[$cuenta_prod] += $total;
                                $arrray_dmov[$cuenta_iva]   = $iva_total;

                                // dmov_can_dmov,   dmov_can_entr,     dmov_cun_dmov,
                                // dmov_cto_dmov,   dmov_pun_dmov,     dmov_pto_dmov,

                                //$fec_ela = date("Y-m-d", strtotime($fec_ela));
                                //$fec_cad = date("Y-m-d", strtotime($fec_cad));


                                /*



                                $fec_ela = date("Y-m-d", strtotime($fec_ela));
                                $fec_cad = date("Y-m-d", strtotime($fec_cad));

                               // 1969-12-31
                               $data_fecha_ela = explode('-', $fec_ela);
                               $quitar_coma_ela = str_replace("'", "", $data_fecha_ela);
                               if (empty($fec_ela) || $quitar_coma_ela[2] < 2000) {
                                   $fec_ela = 'NULL';
                               }else{
                                   $fec_ela = "'".$fec_ela."'";
                               }

                               $data_fecha_cad = explode('-', $fec_cad);
                               $quitar_coma_cad = str_replace("'", "", $data_fecha_cad);
                               if (empty($fec_cad) || $quitar_coma_cad[2] < 2000) {
                                   $fec_cad = 'NULL';
                               }else{
                                   $fec_cad = "'".$fec_cad."'";
                               }



                               */

                                if (empty($fec_ela)) {
                                    $fec_ela = 'NULL';
                                } else {
                                    $fec_ela = "'" . $fec_ela . "'";
                                }


                                if (empty($fec_cad)) {
                                    $fec_cad = 'NULL';
                                } else {
                                    $fec_cad = "'" . $fec_cad . "'";
                                }

                                // Centro de costo Adn
                                $ccosn_costo     = $aDataGrid[$j]["ccosn"];
                                $mac_producto = $aDataGrid[$j]["MAC"];



                                // -------------------------------------------------------------------------------------------------------
                                // VALIDAMOS SI TIENE SERIE Y VERIFICAMOS QUE NO EXISTA EN NINGUN BODEGA DE TODAS LAS SUCURSALES DE LA EMPRESA (SERIE UNICO)
                                // -------------------------------------------------------------------------------------------------------
                                $lote = strtoupper($lote);
                                if (!empty($lote) && $fec_cad == 'NULL' && $fec_ela == 'NULL') {
                                    $existe_data_array = verifica_serie_prod($idempresa, $prod, $lote);
                                    if (count($existe_data_array)) {
                                        $mensaje = 'Esto que lo escriba en la 1ª línea, \n y esto en la 2ª';
                                        $mensaje = 'Serie ya existe: \n \n';
                                        foreach ($existe_data_array as $key47 => $existe_data) {
                                            $bodega_serie = $existe_data['bodega'];
                                            $producto_serie = $existe_data['producto'];
                                            $lote_serie = $existe_data['lote'];
                                            $cantidad_serie = $existe_data['cantidad'];
                                            $mensaje .= 'BODEGA: ' . $bodega_serie . ' \n PRODUCTO: ' . $producto_serie . ' \n SERIE: ' . $lote_serie . ' \n CANTIDAD: ' . round($cantidad_serie, 4) . ' \n \n';
                                        }
                                        throw new Exception($mensaje);
                                    }
                                }
                                // -------------------------------------------------------------------------------------------------------
                                // FIN VALIDAMOS SI TIENE SERIE Y VERIFICAMOS QUE NO EXISTA EN NINGUN BODEGA DE TODAS LAS SUCURSALES DE LA EMPRESA (SERIE UNICO)
                                // -------------------------------------------------------------------------------------------------------







                                $cero = 0;
                                $estado = 1;
                                $dis = 'N';
                                $sql_d .= " " . $cant . ",";                      //
                                $sql_d .= " '" . $cero . "',";     //
                                $sql_d .= " '" . $costo . "',";             //
                                $sql_d .= " " . $total . ",";
                                $sql_d .= " " . $costo_real . ",";        //
                                $sql_d .= " " . ($costo_real * $cant) . ",";                     //dpef_por_iva		IVA
                                $sql_d .= " '" . $descuento . "',";       //desc1
                                $sql_d .= " '" . $descuento_2 . "',";       //dsc2
                                $sql_d .= " '" . $cero . "',";       //dsc3
                                $sql_d .= " '" . $cero . "',";       //dsc4
                                $sql_d .= " '" . $desc_gral . "',";       //dsc general
                                $sql_d .= " '" . $cero . "',";       //imp
                                $sql_d .= " '" . $estado . "',";       //estado
                                $sql_d .= " '" . $cero . "',";       //iva
                                $sql_d .= " '" . $iva . "',";       //dsc1
                                $sql_d .= " '" . $dis . "',";       //dis
                                $sql_d .= " '" . $cero . "',";       //ic
                                $sql_d .= " '" . date('Y-m-d') . ' ' . $hora . "',";       //hora
                                $sql_d .= " '" . $tran . "',";       //tran
                                $sql_d .= " '" . $factura . "',";       //fac prov
                                $sql_d .= " '" . $cliente . "',";       //cliente
                                $sql_d .= " '" . $fecha_servidor . "',";       //fecha server
                                $sql_d .= " '" . $cero . "', ";       //pto1
                                $sql_d .= " '" . $lote . "', ";
                                $sql_d .= " '" . $mac_producto . "', ";
                                $sql_d .= " " . $fec_ela . ", ";
                                $sql_d .= " " . $fec_cad . ", ";
                                $sql_d .= " '" . $ccosn_costo . "' ";

                                // hasta aqui
                                // COSTO
                                // COSTO PROMEDIO
                                $costo_ult_tmp  = ultimo_costo_func($idempresa, $sucursal, $prod, $bod, $fecha_pedido, $oIfx);
                                $cant_ult_tmp   = ultimo_cant_func($idempresa, $sucursal, $prod, $bod, $fecha_pedido, $oIfx);
                                $costo_real_tot = ($costo_real * $cant) + ($costo_ult_tmp * $cant_ult_tmp);
                                $cant_real      = $cant_ult_tmp + $cant;
                                if ($cant_real > 0) {
                                    $cost_act   = round(($costo_real_tot / $cant_real), 6);
                                } else {
                                    $cost_act   = 0;
                                }

                                if ($cost_act < 0) {
                                    $cost_act   = $costo_real;
                                    $cant_real  = $cant;
                                }
                                // sctok bodega
                                $sql = "select prbo_dis_prod, prbo_uco_prod from saeprbo where
                                                            prbo_cod_empr = $idempresa and
                                                            prbo_cod_sucu = $sucursal and
                                                            prbo_cod_bode = $bod and
                                                            prbo_cod_prod = '$prod' ";
                                $stock = consulta_string($sql, 'prbo_dis_prod', $oIfx, 0);

                                // actualiza stock en bodega                                                
                                $sql = "update saeprbo set prbo_dis_prod = ($stock+$cant), prbo_uco_prod = $costo_real, prbo_fec_ucom = '$fecha_pedido' where
                                                                prbo_cod_empr = $idempresa and
                                                                prbo_cod_sucu = $sucursal and
                                                                prbo_cod_bode = $bod and
                                                                prbo_cod_prod = '$prod' ";
                                $oIfx->QueryT($sql);


                                // -------------------------------------------------------------------------------------------------------------------
                                // CONTROL DE CANTIDAD RECIBIDA EN LA RECEPCION DE COMPRA
                                // -------------------------------------------------------------------------------------------------------------------
                                if (count($array_oc) > 0) {
                                    foreach ($array_oc as $val) {
                                        $clpv_cod_oc = $val[0];
                                        $serial_oc   = $val[2];

                                        $sql_select_saedmov = "SELECT dmov_can_entr from saedmov where dmov_num_comp = $serial_oc and dmov_cod_empr = $idempresa and dmov_cod_sucu = $sucursal and dmov_cod_prod = '$prod' ";
                                        $cant_entr = consulta_string($sql_select_saedmov, 'dmov_can_entr', $oIfx, 0) + $cant;

                                        $sql_update_saedmov = "UPDATE saedmov set dmov_can_entr = $cant_entr  where dmov_num_comp = $serial_oc and dmov_cod_empr = $idempresa and dmov_cod_sucu = $sucursal and dmov_cod_prod = '$prod' ";
                                        $oIfx->QueryT($sql_update_saedmov);
                                    }
                                }

                                // -------------------------------------------------------------------------------------------------------------------
                                // FIN CONTROL DE CANTIDAD RECIBIDA EN LA RECEPCION DE COMPRA
                                // -------------------------------------------------------------------------------------------------------------------





                                $informacion_evaluacion = $aDataGrid[$j]['datos_evaluacion'];

                                foreach ($informacion_evaluacion as $key2 => $value) {
                                    $id_evaluacion_parametro = $value['id_evaluacion_parametros'];
                                    $sneval = $value['sneval'];
                                    $observ_eval = $value['observ_eval'];

                                    if (empty($sneval)) {
                                        $sneval = 'N';
                                    }

                                    $usuario_ifx = $_SESSION['U_USER_INFORMIX'];

                                    $sql = "SELECT nombre_parametro from recepcion_parametros where id = $id_evaluacion_parametro";
                                    $nombre_parametro = consulta_string_func($sql, 'nombre_parametro', $oIfx, '');


                                    $responsable_eval = '';
                                    if ($nombre_parametro == 'NOMBRE_RECIBE') {
                                        $sql = " INSERT INTO recepcion_compra_eval 
                                                    (id_empresa, id_sucursal, minv_num_comp, cod_prod, id_recepcion_parametro, sn_estado, observacion, fecha_server, lote_prod, user_system) 
                                                    VALUES ($idempresa, $sucursal, $serial_minv, '$prod', $id_evaluacion_parametro, '', '$observ_eval',  now(), '$lote', '$usuario_ifx');";
                                        $oIfx->QueryT($sql);
                                    } else if ($nombre_parametro == 'NOMBRE_ENTREGA') {
                                        $sql = " INSERT INTO recepcion_compra_eval 
                                                    (id_empresa, id_sucursal, minv_num_comp, cod_prod, id_recepcion_parametro, sn_estado, observacion, fecha_server, lote_prod, user_system)  
                                                    VALUES ($idempresa, $sucursal, $serial_minv, '$prod', $id_evaluacion_parametro, '', '$observ_eval', now(), '$lote', '$usuario_ifx');";
                                        $oIfx->QueryT($sql);
                                    } else if ($nombre_parametro == 'MOTIVO_NOVEDAD') {
                                        $sql = " INSERT INTO recepcion_compra_eval 
                                                    (id_empresa, id_sucursal, minv_num_comp, cod_prod, id_recepcion_parametro, sn_estado, observacion, fecha_server, lote_prod, user_system)  
                                                    VALUES ($idempresa, $sucursal, $serial_minv, '$prod', $id_evaluacion_parametro, '', '$observ_eval', now(), '$lote', '$usuario_ifx');";
                                        $oIfx->QueryT($sql);
                                    } else if ($nombre_parametro == 'DESCRIPCION_NOVEDAD') {
                                        $sql = " INSERT INTO recepcion_compra_eval 
                                                    (id_empresa, id_sucursal, minv_num_comp, cod_prod, id_recepcion_parametro, sn_estado, observacion, fecha_server, lote_prod, user_system)  
                                                    VALUES ($idempresa, $sucursal, $serial_minv, '$prod', $id_evaluacion_parametro, '', '$observ_eval', now(), '$lote', '$usuario_ifx');";
                                        $oIfx->QueryT($sql);
                                    } else if ($nombre_parametro == 'SN_DEVOLUCION') {
                                        $sql = " INSERT INTO recepcion_compra_eval 
                                                    (id_empresa, id_sucursal, minv_num_comp, cod_prod, id_recepcion_parametro, sn_estado, observacion, fecha_server, lote_prod, user_system)  
                                                    VALUES ($idempresa, $sucursal, $serial_minv, '$prod', $id_evaluacion_parametro, '$sneval', '', now(), '$lote', '$usuario_ifx');";
                                        $oIfx->QueryT($sql);
                                    } else {
                                        $sql = " INSERT INTO recepcion_compra_eval 
                                                    (id_empresa, id_sucursal, minv_num_comp, cod_prod, id_recepcion_parametro, sn_estado, observacion, fecha_server, lote_prod, user_system)  
                                                    VALUES ($idempresa, $sucursal, $serial_minv, '$prod', $id_evaluacion_parametro, '$sneval', '$observ_eval', now(), '$lote', '$usuario_ifx');";
                                        $oIfx->QueryT($sql);
                                    }
                                }
                            }
                            $aux++;
                        }
                        $sql_d .= ");";

                        $oIfx->QueryT($sql_d);



                        // -----------------------------------------------------------------------------------------
                        // NUEVO PROCESO CALCULO COSTO PROMEDIO ADRIAN47
                        // -----------------------------------------------------------------------------------------
                        //actualizar_costo_promedio_ponderado_prod($oIfx, $idempresa, $sucursal, $bod, $prod);
                        // -----------------------------------------------------------------------------------------
                        // FIN NUEVO PROCESO CALCULO COSTO PROMEDIO ADRIAN47
                        // -----------------------------------------------------------------------------------------

                        $x++;



                        $j++;
                    } // fin foreach dmov


                    //ACTUALIZACION SAEMINV PROCESO SOLICITUDES DE COMPRA CON ORDEN DE COMPRA

                    /*if (!empty($codpedi)) {
                        $sqlp = "update saeminv set minv_cod_pedi=$codpedi where minv_num_comp=$serial_minv";
                        $oIfx->QueryT($sqlp);
                    }*/


                    // SAEDASI CUENTA PROD I IVA
                    if (count($arrray_dmov) > 0) {
                        $suma = 0;
                        foreach (array_keys($arrray_dmov) as $key) {
                            $suma = $arrray_dmov[$key];

                            // SAEDASI PRODUCTOS
                            // NOMBRE CUENTA PRODUCTOS
                            $sql = "select  cuen_nom_cuen  from saecuen where
                                            cuen_cod_empr = $idempresa and
                                            cuen_cod_cuen = '$key' ";
                            $cuen_prod_nom = consulta_string($sql, 'cuen_nom_cuen', $oIfx, '');

                            // -------------------------------------------------------------------------------------------------------------------------------------
                            // Metodo para recepcionar la compra en moneda secundaria Adrian47
                            // -------------------------------------------------------------------------------------------------------------------------------------
                            if ($moneda != $mone_base) {
                                $cotizacion_ad = $aForm["cotizacion"];
                                $suma = round($suma * $cotizacion_ad, 2);
                            }
                            // -------------------------------------------------------------------------------------------------------------------------------------
                            // FIN Metodo para recepcionar la compra en moneda secundaria Adrian47
                            // -------------------------------------------------------------------------------------------------------------------------------------


                            $suma_ext = 0;
                            if ($moneda == $mone_base) {
                                // MONEDA LOCAL
                                $sql = "select tcam_val_tcam   from saetcam where
                                            mone_cod_empr = $idempresa and 
                                            tcam_cod_mone in ( select pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa  ) and
                                            tcam_fec_tcam in ( select  max(tcam_fec_tcam)   from saetcam where
                                                                        mone_cod_empr = $idempresa and 
                                                                        tcam_cod_mone in ( select pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa )
                                                            ) ";
                                $val_camb = consulta_string_func($sql, 'tcam_val_tcam', $oIfx, '0');
                                $suma_ext = round(($suma / $val_camb), 2);
                            } else {
                                $suma_ext = round(($suma / $coti), 2);
                            }



                            if (empty($cuen_prod_nom)) {
                                throw new Exception('La cuenta contable: ' . $key . ' del PRODUCTO, No existe. ...');
                            }


                            $sql = "insert into saedasi (asto_cod_asto,        asto_cod_empr,      asto_cod_sucu,      dasi_num_prdo,
                                                              asto_cod_ejer,        dasi_cod_cuen,      dasi_dml_dasi,      dasi_cml_dasi,
                                                              dasi_dme_dasi,        dasi_cme_dasi,      dasi_tip_camb,      dasi_det_asi,
                                                              dasi_nom_ctac,        dasi_cod_clie,      dasi_cod_tran,      dasi_user_web )
                                                    values  ( '$secu_asto',         $idempresa,         $sucursal,          $idprdo,
                                                               $idejer,            '$key',              $suma,              0,
                                                               $suma_ext,           0,                  $coti,              '$detalle_asto' ,
                                                              '$cuen_prod_nom',     $cliente,           '$tran',            $usuario_web   ); ";
                            $oIfx->QueryT($sql);



                            // --------------------------------------------------------------------------
                            // CUENTAS ADICIONALES PERU
                            // --------------------------------------------------------------------------


                            if ($codigo_pais == 51 && $pcon_cue_niif == 'S') {

                                // Cuando es Peru baja dos cuentas adicionales: cuan_ana_deb y cuen_anal_cre
                                $sql_cuen = "SELECT cuen_ana_deb, cuen_ana_cre from saecuen where cuen_cod_cuen = '$key';";
                                $cuen_ana_deb = consulta_string_func($sql_cuen, 'cuen_ana_deb', $oIfx, '');
                                $cuen_ana_cre = consulta_string_func($sql_cuen, 'cuen_ana_cre', $oIfx, '');

                                if (!empty($cuen_ana_deb) && !empty($cuen_ana_cre)) {
                                    $existe_cuenta = 'N';
                                } else {
                                    $existe_cuenta = 'S';
                                }

                                if ($existe_cuenta == 'N') {


                                    // --------------------------------------
                                    // DEBITO
                                    // --------------------------------------


                                    $detalle_asto_db = 'CUENTA ADICIONAL DEBITO';
                                    $class->saedasi(
                                        $oIfx,
                                        $idempresa,
                                        $sucursal,
                                        $cuen_ana_deb,
                                        $idprdo,
                                        $idejer,
                                        '',
                                        $suma,
                                        0,
                                        $suma_ext,
                                        0,
                                        $coti,
                                        $detalle_asto_db,
                                        $cliente,
                                        $tran,
                                        $usuario_web,
                                        $secu_asto
                                    );

                                    // --------------------------------------
                                    // CREDITO
                                    // --------------------------------------

                                    $detalle_asto_cr = 'CUENTA ADICIONAL CREDITO';
                                    $class->saedasi(
                                        $oIfx,
                                        $idempresa,
                                        $sucursal,
                                        $cuen_ana_cre,
                                        $idprdo,
                                        $idejer,
                                        '',
                                        0,
                                        $suma,
                                        0,
                                        $suma_ext,
                                        $coti,
                                        $detalle_asto_cr,
                                        $cliente,
                                        $tran,
                                        $usuario_web,
                                        $secu_asto
                                    );
                                }
                            }

                            // --------------------------------------------------------------------------
                            // FIN CUENTAS ADICIONALES PERU
                            // --------------------------------------------------------------------------


                        } // fin for
                    } // fin if
                    // F O R M A     D E    P A G O
                    $x = 1;
                    foreach ($aDataGrid_FP as $aValues) {
                        $sql_d = 'insert into saemxfp(mxfp_cod_mxfp,   mxfp_num_comp,      mxfp_cod_sucu,
                                                              mxfp_cod_empr,   mxfp_cod_fpag,      
                                                              mxfp_num_prdo,   mxfp_cod_ejer,      mxfp_num_dias,
                                                              mxfp_poc_mxfp,   mxfp_val_mxfp,      mxfp_fec_mxfp,
                                                              mxfp_fec_fin )
                                                values ';
                        $aux = 0;
                        $sql_d .= "(";
                        foreach ($aValues as $aVal) {
                            if ($aux == 0) {
                                $sql_d .= " " . $x . ",";                 //mxfp cod 
                            } elseif ($aux == 1) {
                                $fecha_ini = $aVal;
                            } elseif ($aux == 2) {
                                $dia = $aVal;
                            } elseif ($aux == 3) {
                                $fecha_fin = $aVal;
                            } elseif ($aux == 4) {
                                $fp = $aVal;
                            } elseif ($aux == 5) {
                                $porc = $aVal;
                            } elseif ($aux == 6) {                                  //
                                $valor = $aVal;
                                $cero = 0;
                                $sql_d .= " " . $serial_minv . ",";             //
                                $sql_d .= " '" . $sucursal . "',";     //
                                $sql_d .= " '" . $idempresa . "',";             //
                                $sql_d .= " " . $fp . ",";
                                $sql_d .= " " . $idprdo . ",";                  //
                                $sql_d .= " " . $idejer . ", ";
                                $sql_d .= " " . $dia . ", ";
                                $sql_d .= " " . $porc . ", ";
                                $sql_d .= " " . $valor . ", ";
                                $sql_d .= " '" . $fecha_ini . "', ";
                                $sql_d .= " '" . $fecha_fin . "' ";
                            }
                            $aux++;
                        }
                        $sql_d .= ");";
                        $oIfx->QueryT($sql_d);
                        $x++;
                    } // fin foreach saemxfp




                    // UPDATE SAEDMCP
                    $sql = "update saedmcp set dmcp_cod_fact = $serial_minv , dmcp_est_dcmp = 'MY' where
                                        dmcp_cod_empr = $idempresa and
                                        dmcp_cod_sucu = $sucursal and
                                        dmcp_cod_asto = '$secu_asto' and
                                        dmcp_cod_ejer = $idejer and
                                        dmcp_cod_modu = 10 and
                                        clpv_cod_clpv = $cliente  ";
                    $oIfx->QueryT($sql);

                    //archivos adjuntos
                    $aDataGirdAdj = $_SESSION['aDataGirdAdj'];
                    if (count($aDataGirdAdj) > 0) {
                        foreach ($aDataGirdAdj as $aValues) {
                            $aux = 0;
                            foreach ($aValues as $aVal) {
                                if ($aux == 0) {
                                    $idAdj = $aVal;
                                } elseif ($aux == 1) {
                                    $titulo = $aVal;
                                } elseif ($aux == 2) {
                                    $adjunto = $aVal;

                                    $sql = "insert into comercial.adjuntos (id_empresa, id_sucursal, id_clpv, id_ejer, id_prdo,
																tipo_doc, asto, documento, titulo, ruta, estado,
																fecha_server, user_web)
														values($idempresa, $sucursal, $cliente, $idejer, $idprdo,
																'INVEN', '$secu_asto', '$factura', '$titulo', '$adjunto', 'A',
																now(), $usuario_web)";
                                    $oCon->QueryT($sql);
                                    //$oReturn->alert($sql);
                                }
                                $aux++;
                            } //fin foreach
                        } //fin foreach
                    } //fin if


                    // COA
                    $tipo_factura = $aForm['tipo_factura'];
                    $auto_prove   = $aForm['auto_prove'];
                    $serie_prove  = $aForm['serie_prove'];
                    $fecha_val    = $aForm['fecha_validez'];
                    $fact_ini     = $aForm['factura_inicio'];
                    $fact_fin     = $aForm['factura_fin'];

                    /*if($tipo_factura==2){
						// 
						$sql = "insert into saecoa ( clpv_cod_sucu, clpv_cod_empr , clpv_cod_clpv, coa_aut_usua, coa_aut_impr,  coa_fact_ini,
													 coa_fact_fin,  coa_seri_docu,  coa_fec_vali,  coa_est_coa  ) 
											values(  $sucursal,     $idempresa,     $cliente,      '$auto_prove', '',		'$fact_ini',
													 '$fact_fin' ,   '$serie_prove', '$fecha_val',  '1'  );";
						$oIfx->QueryT($sql);							 
					}*/



                    //SACAR DATOS PARA EL XML SRI
                    $sql = "select  sucu_tip_ambi, sucu_tip_emis, sucu_fac_elec  from saesucu where sucu_cod_sucu = $sucursal ";
                    if ($oIfx->Query($sql)) {
                        if ($oIfx->NumFilas() > 0) {
                            $ambiente = $oIfx->f('sucu_tip_ambi');
                            $tipoEmision = $oIfx->f('sucu_tip_emis');
                            $sucu_fac_elec = $oIfx->f('sucu_fac_elec');
                        }
                    }
                    $oIfx->Free();





                    // Guardar los precios del producto Adrian



                    $numero_registros = count($aDataGrid);

                    $sql = "select nomp_cod_nomp, nomp_nomb_nomp from saenomp where nomp_cod_empr = $idempresa ";
                    unset($array_nomp);
                    if ($oIfx->Query($sql)) {
                        if ($oIfx->NumFilas() > 0) {
                            do {
                                $nomp_cod_nomp  = $oIfx->f('nomp_cod_nomp');
                                $nomp_nomb_nomp = $oIfx->f('nomp_nomb_nomp');

                                $array_nomp[] = array($nomp_cod_nomp, $nomp_nomb_nomp);
                            } while ($oIfx->SiguienteRegistro());
                        }
                    }
                    $oIfx->Free();


                    for ($i = 0; $i < $numero_registros; $i++) {
                        $prod_cod     = $aDataGrid[$i]['Codigo Item'];
                        $bode_cod     = $aDataGrid[$i]['Bodega'];

                        // Actualizar campo del FOB en la saeprod
                        $fob_real     = $aDataGrid[$i]["fob_real"];
                        if (!empty($fob_real)) {
                            $sql_update_fob = "update saeprod set prod_fob_prod = '$fob_real' where prod_cod_prod = '$prod_cod' and prod_cod_sucu = $sucursal and prod_cod_empr = $idempresa";
                            $oIfx->QueryT($sql_update_fob);
                        }

                        $num_nomp = 1;
                        foreach ($array_nomp as $val) {
                            $nomp_cod_nomp  = $val[0];
                            $nomp_nomb_nomp = $val[1];

                            $sql = "select ppr_cod_ppr, ppr_cod_prod, ppr_pre_raun, ppr_cod_nomp, ppr_imp_ppr
                                        from saeppr where
                                        ppr_cod_empr = $idempresa and
                                        ppr_cod_sucu = $sucursal and
                                        ppr_cod_bode = $bode_cod and
                                        ppr_cod_prod = '$prod_cod' and
                                        ppr_cod_nomp = $nomp_cod_nomp ";

                            $ppr_cod_ppr = 0;
                            $ppr_pre_raun = 0;
                            if ($oIfx->Query($sql)) {
                                if ($oIfx->NumFilas() > 0) {
                                    $ppr_cod_ppr  = $oIfx->f('ppr_cod_ppr');
                                    $ppr_pre_raun = $oIfx->f('ppr_pre_raun');
                                }
                            }
                            $oIfx->Free();





                            $precio     = $aDataGrid[$i]["pvp" . $num_nomp];

                            if (empty($precio)) {
                                $precio     = $ppr_pre_raun;
                            }


                            if ($ppr_cod_ppr > 0) {
                                // UPDATE
                                $sql = "update saeppr set ppr_pre_raun = '$precio' where
                                            ppr_cod_empr = $idempresa and
                                            ppr_cod_sucu = $sucursal and
                                            ppr_cod_bode = $bode_cod and
                                            ppr_cod_prod = '$prod_cod' and
                                            ppr_cod_nomp = $nomp_cod_nomp and
                                            ppr_cod_ppr  = $ppr_cod_ppr	";
                            } elseif ($ppr_cod_ppr == 0) {
                                // INGRESO
                                $sql = "select  max(ppr_cod_ppr) ppr_cod
                                            from saeppr where
                                            ppr_cod_empr = $idempresa and
                                            ppr_cod_sucu = $sucursal and
                                            ppr_cod_bode = $bode_cod and
                                            ppr_cod_prod = '$prod_cod' ";
                                $serial    = consulta_string_func($sql, 'ppr_cod', $oIfx, 0) + 1;

                                $sql = "insert into saeppr ( ppr_cod_ppr, 		ppr_cod_prod, 		ppr_cod_bode,		ppr_cod_empr,
                                                             ppr_cod_sucu, 		ppr_pre_raun,		ppr_cod_nomp )
                                                    values ( $serial,			'$prod_cod',		$bode_cod,			$idempresa,
                                                             $sucursal,		$precio,		    $nomp_cod_nomp
                                                           )";
                            }
                            $oIfx->QueryT($sql);
                            $num_nomp++;
                        }
                    }







                    // --------------------------------------------------------------------------------------------------------------
                    // Guardamos en caso de que exista la informacion de la balanza. Este proceso fue desarrollado para pollos campo
                    // --------------------------------------------------------------------------------------------------------------

                    $procesar_sn_bal = $aForm['procesar_sn_bal'];
                    if ($procesar_sn_bal == 1) {

                        $balpc_cod_bode = $aForm['bodega_bal'];
                        $balpc_fec_comp = $aForm['fecha_bal'] . ' ' . date('H:i:s');
                        $balpc_nom_recibe = $aForm['nombre_recibe_bal'];
                        $balpc_cod_clpv = $aForm['codigo_proveedor_bal'];
                        $balpc_nom_clpv = $aForm['cliente_nombre_bal'];
                        $balpc_cod_prod = $aForm['codigo_producto_bal'];
                        $balpc_guia_id = $aForm['guia_id'];
                        $balpc_lote_id = $aForm['lote_id'];
                        $balpc_nom_prod = $aForm['producto_bal'];
                        $balpc_prod_procesa = $aForm['pollos_procesados_bal'];
                        $balpc_prod_pedido = $aForm['pollos_pedidos_bal'];
                        $balpc_peso_promedi = $aForm['peso_promedio_ped_bal'];
                        $balpc_uco_prod = $aForm['precio_bal'];
                        $balpc_hora_balpc = date('Y-m-d') . ' ' . $aForm['hora_bal'];
                        $balpc_nom_transp = strtoupper($aForm['nonbre_transportista']);
                        $balpc_peso_jaula = $aForm['peso_promedio_jaula_bal'];
                        $balpc_peso_bruto = $aForm['peso_bruto_bal'];
                        $balpc_peso_jaulas = $aForm['peso_jaulas_bal'];

                        $balpc_prod_muert = $aForm['numero_muertos_bal'];
                        $balpc_peso_muert = $aForm['peso_muertos_bal'];

                        $balpc_prod_decom = $aForm['numero_decomiso_bal'];
                        $balpc_peso_decom = $aForm['peso_decomiso_bal'];

                        $balpc_merm_alas = $aForm['numero_decomiso_alas'];
                        $balpc_peso_alas = $aForm['peso_decomiso_alas'];

                        $balpc_merm_piern = $aForm['numero_decomiso_piernas'];
                        $balpc_peso_piern = $aForm['peso_decomiso_piernas'];

                        $balpc_merm_ab = $aForm['numero_decomiso_merma_ab'];
                        $balpc_peso_ab = $aForm['peso_decomiso_merma_ab'];

                        $balpc_merm_orga = $aForm['numero_decomiso_organos'];
                        $balpc_peso_orga = $aForm['peso_decomiso_organos'];

                        $balpc_peso_adici = $aForm['peso_buches_mollejas_bal'];
                        $balpc_peso_neto = $aForm['peso_neto_bal'];
                        $balpc_val_efecti = $aForm['valor_efectivo_bal'];
                        $balpc_cod_modu = 'COMPRA_SIN_RETENCION';
                        $balpc_num_comp = $serial_minv;


                        $id_usuario = $_SESSION['U_ID'];
                        $fecha_actual = date('Y-m-d H:i:s');

                        if (empty($balpc_cod_clpv)) {
                            $balpc_cod_clpv = 0;
                        }
                        if (empty($balpc_prod_procesa)) {
                            $balpc_prod_procesa = 0;
                        }
                        if (empty($balpc_prod_pedido)) {
                            $balpc_prod_pedido = 0;
                        }
                        if (empty($balpc_peso_promedi)) {
                            $balpc_peso_promedi = 0;
                        }
                        if (empty($balpc_uco_prod)) {
                            $balpc_uco_prod = 0;
                        }
                        if (empty($balpc_peso_jaula)) {
                            $balpc_peso_jaula = 0;
                        }
                        if (empty($balpc_peso_bruto)) {
                            $balpc_peso_bruto = 0;
                        }
                        if (empty($balpc_peso_jaulas)) {
                            $balpc_peso_jaulas = 0;
                        }
                        if (empty($balpc_prod_muert)) {
                            $balpc_prod_muert = 0;
                        }
                        if (empty($balpc_peso_muert)) {
                            $balpc_peso_muert = 0;
                        }
                        if (empty($balpc_prod_decom)) {
                            $balpc_prod_decom = 0;
                        }
                        if (empty($balpc_peso_decom)) {
                            $balpc_peso_decom = 0;
                        }
                        if (empty($balpc_peso_adici)) {
                            $balpc_peso_adici = 0;
                        }
                        if (empty($balpc_peso_neto)) {
                            $balpc_peso_neto = 0;
                        }
                        if (empty($balpc_val_efecti)) {
                            $balpc_val_efecti = 0;
                        }
                        if (empty($balpc_merm_alas)) {
                            $balpc_merm_alas = 0;
                        }
                        if (empty($balpc_peso_alas)) {
                            $balpc_peso_alas = 0;
                        }
                        if (empty($balpc_merm_piern)) {
                            $balpc_merm_piern = 0;
                        }
                        if (empty($balpc_peso_piern)) {
                            $balpc_peso_piern = 0;
                        }
                        if (empty($balpc_merm_ab)) {
                            $balpc_merm_ab = 0;
                        }
                        if (empty($balpc_peso_ab)) {
                            $balpc_peso_ab = 0;
                        }
                        if (empty($balpc_merm_orga)) {
                            $balpc_merm_orga = 0;
                        }
                        if (empty($balpc_peso_orga)) {
                            $balpc_peso_orga = 0;
                        }

                        $sql_insert_saebalpc = "INSERT into saebalpc ( 
                                                                        balpc_cod_empr,
                                                                        balpc_cod_sucu,
                                                                        balpc_cod_bode,

                                                                        balpc_guia_id,
                                                                        balpc_lote_id,
                                                                        balpc_fec_comp,
                                                                        balpc_nom_recibe,
                                                                        balpc_cod_clpv,
                                                                        balpc_nom_clpv,
                                                                        balpc_cod_prod,
                                                                        balpc_nom_prod,
                                                                        balpc_prod_procesa,
                                                                        balpc_prod_pedido,
                                                                        balpc_peso_promedi,
                                                                        balpc_uco_prod,
                                                                        balpc_hora_balpc,
                                                                        balpc_nom_transp,
                                                                        balpc_peso_jaula,
                                                                        balpc_peso_bruto,
                                                                        balpc_peso_jaulas,

                                                                        balpc_prod_muert,
                                                                        balpc_peso_muert,

                                                                        balpc_prod_decom,
                                                                        balpc_peso_decom,

                                                                        balpc_merm_alas,
                                                                        balpc_peso_alas,

                                                                        balpc_merm_piern,
                                                                        balpc_peso_piern,

                                                                        balpc_merm_ab,
                                                                        balpc_peso_ab,

                                                                        balpc_merm_orga,
                                                                        balpc_peso_orga,

                                                                        balpc_peso_adici,
                                                                        balpc_peso_neto,
                                                                        balpc_val_efecti,
                                                                        balpc_cod_modu,
                                                                        balpc_num_comp,

                                                                        balpc_cod_usua,
                                                                        balpc_fech_ingr,
                                                                        balpc_usua_act,
                                                                        balpc_fech_act
                                                                )
                                                        values ( 
                                                                        $idempresa,
                                                                        $sucursal,
                                                                        $balpc_cod_bode,

                                                                        '$balpc_guia_id',
                                                                        '$balpc_lote_id',
                                                                        '$balpc_fec_comp',
                                                                        '$balpc_nom_recibe',
                                                                        $balpc_cod_clpv,
                                                                        '$balpc_nom_clpv',
                                                                        '$balpc_cod_prod',
                                                                        '$balpc_nom_prod',
                                                                        $balpc_prod_procesa,
                                                                        $balpc_prod_pedido,
                                                                        $balpc_peso_promedi,
                                                                        $balpc_uco_prod,
                                                                        '$balpc_hora_balpc',
                                                                        '$balpc_nom_transp',
                                                                        $balpc_peso_jaula,
                                                                        $balpc_peso_bruto,
                                                                        $balpc_peso_jaulas,

                                                                        $balpc_prod_muert,
                                                                        $balpc_peso_muert,

                                                                        $balpc_prod_decom,
                                                                        $balpc_peso_decom,

                                                                        $balpc_merm_alas,
                                                                        $balpc_peso_alas,

                                                                        $balpc_merm_piern,
                                                                        $balpc_peso_piern,

                                                                        $balpc_merm_ab,
                                                                        $balpc_peso_ab,

                                                                        $balpc_merm_orga,
                                                                        $balpc_peso_orga,

                                                                        $balpc_peso_adici,
                                                                        $balpc_peso_neto,
                                                                        $balpc_val_efecti,
                                                                        '$balpc_cod_modu',
                                                                        $balpc_num_comp,

                                                                        $id_usuario,
                                                                        '$fecha_actual',
                                                                        $id_usuario,
                                                                        '$fecha_actual'
                                                                ) RETURNING balpc_cod_balpc;";
                        $oIfx->QueryT($sql_insert_saebalpc);
                        $balpc_cod_balpc = $oIfx->ResRow['balpc_cod_balpc'];
                        if (!$balpc_cod_balpc) {
                            throw new Exception("No se pudo insertar la informacion Balanza");
                        }

                        $sql_update_saedbalpc = "UPDATE saedbalpc 
                                                    set dbalpc_cod_mbalpc = $balpc_cod_balpc,
                                                    dbalpc_num_comp = $serial_minv
                                                where 
                                                    dbalpc_cod_modu = 'COMPRA_SIN_RETENCION'
                                                    and dbalpc_cod_usua = $id_usuario
                                                    and dbalpc_num_comp is null
                                                ";
                        $oIfx->QueryT($sql_update_saedbalpc);
                    }

                    // --------------------------------------------------------------------------------------------------------------
                    // FIN Guardamos en caso de que exista la informacion de la balanza. Este proceso fue desarrollado para pollos campo
                    // --------------------------------------------------------------------------------------------------------------









                    $oIfx->QueryT('COMMIT WORK;');
                    $oReturn->script("jsRemoveWindowLoad();");

                    $oReturn->alert('Compra Ingresado Correctamente');
                    $oReturn->assign("nota_compra", "value",     $secu_minv);
                    $oReturn->assign("codMinv", "value",         $serial_minv);
                    $oReturn->assign("ejercicio", "value",         $idejer);
                    $oReturn->assign("periodo", "value",         $idprdo);
                    $oReturn->assign("asiento", "value",         $secu_asto);
                    //codMinv

                    $oReturn->script("vista_previa('.$serial_minv.', '.$idempresa.', '.$sucursal.');");

                    $array_print[] = array($claveAcceso, $cliente, $factura, $idejer, $secu_asto, $fechaEmision);

                    $oReturn->script("refreshTablaIn();");
                } else {
                    $oReturn->alert('::.ERROR.:: la factura numero ' . $factura . ' debe estar dentro del intervalo ' . $factura_inicio . ' - ' . $factura_fin . ' ');
                    $oReturn->assign("ctrl", "value", 1);
                }
            }
        } catch (Exception $e) {
            // rollback
            $oIfx->QueryT('ROLLBACK WORK;');
            $oReturn->alert($e->getMessage());
            $oReturn->assign("ctrl", "value", 1);
            $oReturn->script("jsRemoveWindowLoad();");
        }
    } else {
        if ($clpv_ret_sn == 'S') {
            // APLICACION RETENCION
            $oReturn->alert('!!!!....Por favor Seleccionar Productos - Forma de Pago....!!!!!');
        } else {
            // SIN RETENCION
            $oReturn->alert('!!!!....Por favor Seleccionar Productos - Forma de Pago...!!!!!');
        }

        $oReturn->assign("ctrl", "value", 1);
    }

    $_SESSION['Print'] = $array_print;

    $oReturn->script("jsRemoveWindowLoad();");

    return $oReturn;
}



function anio_fecha_abierto($aForm = '')
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN_Ifx;

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $fecha_pedido = $aForm['fecha_pedido'];

    $array_fecha_compra = (explode("-", $fecha_pedido));
    $anio = intval($array_fecha_compra[0]);
    $mes = intval($array_fecha_compra[1]);

    //echo $mes;exit;


    $fecha_compra = $aForm['fecha_pedido'];
    $clpv_pro_pago = $aForm['plazo'];
    if (empty($clpv_pro_pago)) {
        $clpv_pro_pago = 1;
    }
    $fecha_final = date("Y-m-d", strtotime($fecha_compra . "+ " . $clpv_pro_pago . " days"));
    $oReturn->assign('fecha_entrega', 'value', $fecha_final);

    $sql = "select 
    EXTRACT(YEAR FROM ejer_fec_inil) AS anio, ejer_fec_inil, ejer_est_ejer, EXTRACT(MONTH FROM prdo_fec_ini) as mes, prdo_fec_ini, prdo_est_prdo
    from saeejer, saeprdo 
    where
    prdo_cod_ejer = ejer_cod_ejer and
    EXTRACT(YEAR FROM ejer_fec_inil) = $anio and 
		EXTRACT(MONTH FROM prdo_fec_ini) = $mes";


    $estado_anio = '';
    $estado_mes = '';
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            do {
                $estado_anio      = $oIfx->f('ejer_est_ejer');
                $estado_mes     = $oIfx->f('prdo_est_prdo');
            } while ($oIfx->SiguienteRegistro());
        }
    }

    if ($estado_anio != 'A') {
        $oReturn->alert("El periodo " . $anio . " se encuentra cerrado. Consulte con el administrador.");
    } else if ($estado_anio == 'A' && $estado_mes != 'A') {
        $oReturn->alert("El mes " . $mes . " se encuentra cerrado. Consulte con el administrador.");
    }


    //CAMBIOS DE MONEDA - PERU

    //PAIS
    $S_PAIS_API_SRI = $_SESSION['S_PAIS_API_SRI'];

    if ($S_PAIS_API_SRI == '51') {
        $oReturn->script('cargar_coti_ext();');
    }




    return $oReturn;
}

function cargar_coti_ext($aForm = '')
{
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    //variables del formulario
    $idempresa = $_SESSION['U_EMPRESA'];
    $mone_cod  = $aForm['moneda'];
    //PAIS
    $S_PAIS_API_SRI = $_SESSION['S_PAIS_API_SRI'];

    //PERU: PARA EL CMABIO DE MONEDA SE CONSIDERA LA FECHA DE EMISION DEL COMPORBANTE

    $fech  = $aForm['fecha_pedido'];

    //$fech=date('m/d/Y');
    $sql = "select tcam_val_tcam from saetcam where
                mone_cod_empr = $idempresa and
                tcam_cod_mone = $mone_cod and
                tcam_fec_tcam in (
                                    select max(tcam_fec_tcam)  from saetcam where
                                            mone_cod_empr = $idempresa and
                                            tcam_cod_mone = $mone_cod and tcam_fec_tcam<='$fech'
                                )  ";

    $coti = consulta_string_func($sql, 'tcam_val_tcam', $oIfx, 0);
    $oReturn->assign("cotizacion", "value", $coti);

    /*$sql = "select max(mone_cod_mone) as moneda from saemone where mone_cod_empr=$idempresa";
	$moneda = consulta_string_func($sql, 'moneda', $oIfx, 0);
	$sql = "select tcam_val_tcam from saetcam where
                mone_cod_empr = $idempresa and
                tcam_cod_mone = $moneda and
                tcam_fec_tcam in (
                                    select max(tcam_fec_tcam)  from saetcam where
                                            mone_cod_empr = $idempresa and
                                            tcam_cod_mone = $moneda and tcam_fec_tcam<='$fech'
                                )  ";

	$coti_e = consulta_string_func($sql, 'tcam_val_tcam', $oIfx, 0);
	$oReturn->assign("cotizacion_ext", "value", $coti_e);*/
    return $oReturn;
}




function firmar($nombre_archivo = '', $clave_acceso = '', $ruc = '', $id_docu = '', $correo = '', $cliente = '', $factura = '', $idejer = '', $secu_asto = '', $fechaEmision = '')
{
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $sqlUpdate = $_SESSION['sqlUpdate'];
    $sqlRete = $_SESSION['sqlRete'];

    $idEmpresa = $_SESSION['U_EMPRESA'];

    $pathpdf = "/Jireh/comprobantes electronicos/generados/";

    //SETEAMOS EL WEB SERVICE PARA FIRMAR LOS COMPROBANTES
    $clientOptions = array(
        "useMTOM" => FALSE,
        'trace' => 1,
        'stream_context' => stream_context_create(array('http' => array('protocol_version' => 1.0)))
    );

    try {
        $wsdlFirma = new SoapClient("http://localhost:8080/WebServFirma/firmaComprobante?WSDL", $clientOptions);

        //CONSULTAMOS LOS DATOS DEL TOKEN
        $sqlConf = "select sucu_tip_ambi, sucu_tip_toke from saesucu where sucu_cod_empr = $idEmpresa";

        if ($oIfx->Query($sqlConf)) {
            $tipoAmbiente = $oIfx->f("sucu_tip_ambi");
            $tiempoEspera = 3;
            $token = $oIfx->f("sucu_tip_toke");
        }

        $serv = "/Jireh/";
        $ruta = $serv . "Comprobantes Electronicos";

        // CARPETA EMPRESA
        $pathFirmados = $ruta . "/firmados";

        if (!file_exists($ruta)) {
            mkdir($ruta);
        }

        if (!file_exists($pathFirmados)) {
            mkdir($pathFirmados);
        }

        $pathArchivo = "/Jireh/comprobantes electronicos/generados/" . $nombre_archivo;

        $password = null;

        $aFirma = array(
            "ruc" => $ruc,
            "tipoAmbiente" => $tipoAmbiente,
            "tiempoEspera" => $tiempoEspera,
            "token" => $token,
            "pathArchivo" => $pathArchivo,
            "pathFirmados" => $pathFirmados,
            "password" => $password
        );

        $respFirm = $wsdlFirma->FirmarDocumento($aFirma);

        $respFirm = strtoupper($respFirm->return);

        if ($respFirm == null) {
            $oReturn->alert("FIRMA");
            $oReturn->script("validaAutoriza('$nombre_archivo','$clave_acceso','$id_docu', '$correo', $cliente, '$factura', $idejer, '$secu_asto', '$fechaEmision')");
        } else {
            $oReturn->alert("El archivo fue guardado pero no fue firmado : " . $respFirm);

            $sqlError = "update saeminv set minv_erro_sri = '$respFirm' " . $sqlUpdate;
            $oIfx->QueryT($sqlError);
        }
    } catch (SoapFault $e) {
        $oReturn->alert("NO HAY CONECCION CON LA FIRMA");



        $sqlError = "update saeminv set minv_erro_sri = 'NO HUBO CONECCION CON LA FIRMA' " . $sqlUpdate;
        $oIfx->QueryT($sqlError);
    }
    return $oReturn;
}

function validaAutoriza($nombre_archivo = '', $clave_acceso = '', $id_docu = '', $correo = '', $cliente = '', $factura = '', $idejer = '', $secu_asto = '', $fechaEmision = '')
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $sqlUpdate = $_SESSION['sqlUpdate'];
    $idEmpresa = $_SESSION['U_EMPRESA'];
    $idSucursal = $_SESSION['U_SUCURSAL'];

    $oReturn = new xajaxResponse();

    $pathpdf = "/Jireh/comprobantes electronicos/generados/";

    $clientOptions = array(
        "useMTOM" => FALSE,
        'trace' => 1,
        'stream_context' => stream_context_create(array('http' => array('protocol_version' => 1.0)))
    );

    //HACEMOS LA VALIDACION DEL COMPROBANTE SUBIENDO EL ARCHIVO XML YA FIRMADO
    try {

        $sql = "select sucu_tip_ambi from saesucu where sucu_cod_sucu = $idSucursal and sucu_cod_empr = $idEmpresa";
        $sucu_tip_ambi = consulta_string($sql, 'sucu_tip_ambi', $oIfx, 1);

        if ($sucu_tip_ambi == 1) {
            $wsdlValiComp = new SoapClient("https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantes?wsdl", $clientOptions);
        } else {
            $wsdlValiComp = new SoapClient("https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantes?wsdl", $clientOptions);
        }

        $rutaFirm = "/Jireh/Comprobantes Electronicos/firmados/" . $nombre_archivo;
        $xml = file_get_contents($rutaFirm);

        $aArchivo = array("xml" => $xml);

        //fclose($xml);

        $valiComp = new stdClass();
        $valiComp = $wsdlValiComp->validarComprobante($aArchivo);

        $RespuestaRecepcionComprobante = $valiComp->RespuestaRecepcionComprobante;
        $estado = $RespuestaRecepcionComprobante->estado;

        //$oReturn->alert($estado);
        if ($estado == 'RECIBIDA') {
            $oReturn->alert("RECIBIDA");
            $oReturn->script("autorizaComprobante('$clave_acceso', '$id_docu' , '$correo', $cliente, '$factura', $idejer, '$secu_asto', '$fechaEmision')");
        } else {
            $comprobantes = $RespuestaRecepcionComprobante->comprobantes;
            $comprobante = $comprobantes->comprobante;
            $mensajes = $comprobante->mensajes;
            $mensaje = $mensajes->mensaje;
            // $mensaje2 = $mensaje->mensaje;
            $informacionAdicional = strtoupper($mensaje->informacionAdicional);

            $informacionAdicional = substr($informacionAdicional, 0, 30);

            $error = "El archivo fue guardado, pero no fue enviado ni autorizado :" . " \n" . " * " . $informacionAdicional;
            $oReturn->alert($error);

            $sqlError = "update saeminv set minv_erro_sri = '$informacionAdicional' " . $sqlUpdate;

            $oIfx->QueryT($sqlError);
        }
    } catch (SoapFault $e) {
        $oReturn->alert("NO HAY CONECCION AL SRI (VALIDAR) " . $e);



        $sqlError = "update saeminv set minv_erro_sri  = 'NO HUBO CONECCION AL SRI (VALIDAR)' " . $sqlUpdate;
        $oIfx->QueryT($sqlError);
    }

    return $oReturn;
}

function autorizaComprobante($clave_acceso = '', $id_docu = '', $correo = '', $cliente = '', $factura = '', $idejer = '', $secu_asto = '', $fechaEmision = '')
{
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $sqlUpdate = $_SESSION['sqlUpdate'];
    $idEmpresa = $_SESSION['U_EMPRESA'];
    $idSucursal = $_SESSION['U_SUCURSAL'];

    $pathpdf = "/Jireh/Comprobantes Electronicos/generados/";

    try {
        $clientOptions = array(
            "useMTOM" => FALSE,
            'trace' => 1,
            'stream_context' => stream_context_create(array('http' => array('protocol_version' => 1.0)))
        );

        $sql = "select sucu_tip_ambi from saesucu where sucu_cod_sucu = $idSucursal and sucu_cod_empr = $idEmpresa";
        $sucu_tip_ambi = consulta_string($sql, 'sucu_tip_ambi', $oIfx, 1);

        if ($sucu_tip_ambi == 1) {
            $wsdlAutoComp = new SoapClient("https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl", $clientOptions);
        } else {
            $wsdlAutoComp = new SoapClient("https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl", $clientOptions);
        }

        //RECUPERA LA AUTORIZACION DEL COMPROBANTE
        $aClave = array("claveAccesoComprobante" => $clave_acceso);

        $autoComp = new stdClass();
        $autoComp = $wsdlAutoComp->autorizacionComprobante($aClave);

        $RespuestaAutorizacionComprobante = $autoComp->RespuestaAutorizacionComprobante;
        $claveAccesoConsultada = $RespuestaAutorizacionComprobante->claveAccesoConsultada;
        $autorizaciones = $RespuestaAutorizacionComprobante->autorizaciones;
        $autorizacion = $autorizaciones->autorizacion;

        if (count($autorizacion) > 1) {
            $estado = $autorizacion[0]->estado;
            $numeroAutorizacion = $autorizacion[0]->numeroAutorizacion;
            $fechaAutorizacion = $autorizacion[0]->fechaAutorizacion;
            $ambiente = $autorizacion[0]->ambiente;
            $comprobante = $autorizacion[0]->comprobante;
            $mensajes = $autorizacion[0]->mensajes;
            $mensaje = $mensajes->mensaje;
        } else {
            $estado = $autorizacion->estado;
            $numeroAutorizacion = $autorizacion->numeroAutorizacion;
            $fechaAutorizacion = $autorizacion->fechaAutorizacion;
            $ambiente = $autorizacion->ambiente;
            $comprobante = $autorizacion->comprobante;
            $mensajes = $autorizacion->mensajes;
            $mensaje = $mensajes->mensaje;
        }

        if ($estado == 'AUTORIZADO') {
            $oReturn->alert("El comprobante fue autorizado ");
            // $oReturn->script("update_comprobante('$numeroAutorizacion','$fechaAutorizacion','$id_docu')");
            update_comprobante($clave_acceso, $numeroAutorizacion, $fechaAutorizacion, $id_docu);

            $dia = substr($claveAccesoConsultada, 0, 2);
            $mes = substr($claveAccesoConsultada, 2, 2);
            $an = substr($claveAccesoConsultada, 4, 4);

            //CREO LOS DIRECTORIOS DE LOS RIDES
            $serv = "/Jireh";
            $rutaRide = $serv . "/RIDE";
            $rutaComp = $rutaRide . 'RETENCIONES INVENTARIO';
            $rutaAo = $rutaComp . "/" . $an;
            $rutaMes = $rutaAo . "/" . $mes;
            $rutaDia = $rutaMes . "/" . $dia;

            if (!file_exists($rutaRide)) {
                mkdir($rutaRide);
            }

            if (!file_exists($rutaComp)) {
                mkdir($rutaComp);
            }

            if (!file_exists($rutaAo)) {
                mkdir($rutaAo);
            }

            if (!file_exists($rutaMes)) {
                mkdir($rutaMes);
            }

            if (!file_exists($rutaDia)) {
                mkdir($rutaDia);
            }

            $numero = substr($claveAccesoConsultada, 24, 15);
            $nombre = "ReteI_" . $numero . "_" . "$dia-$mes-$an" . ".xml";

            //FORMO EL RIDE
            $ride .= '<?xml version="1.0" encoding="UTF-8"?>';
            $ride .= '<autorizacion>';
            $ride .= "<estado>$estado</estado>";
            $ride .= "<numeroAutorizacion>$numeroAutorizacion</numeroAutorizacion>";
            $ride .= "<fechaAutorizacion>$fechaAutorizacion</fechaAutorizacion>";
            $ride .= "<ambiente>$ambiente</ambiente>";
            $ride .= "<comprobante><![CDATA[$comprobante]]></comprobante>";
            $ride .= '</autorizacion>';

            // ruta del xml
            $archivo_xml = fopen($rutaDia . '/' . $nombre, "w+");
            fwrite($archivo_xml, $ride);
            fclose($archivo_xml);

            $ride = '' . $rutaDia . '/' . $nombre;


            envio_correo_adj($correo, $ride, $rutaPdf);
        } else {
            $mensFina = "COMPROBANTE GUARDADO, FIRMADO, ENVIADO, PERO NO AUTORIZADO:";

            $informacionAdicional = strtoupper($mensaje[0]->informacionAdicional);
            if ($informacionAdicional != '') {
                $posi = strpos($informacionAdicional, ':');
                $mensBDD = substr($informacionAdicional, $posi, -1);
                $mensFina .= " \n " . substr($informacionAdicional, $posi, strlen($informacionAdicional));
            } elseif (is_array($mensaje)) {
                foreach ($mensaje as $fila) {
                    $val = " \n ";
                    $mensFina .= $val . ' * ' . $fila->mensaje;
                    $mensBDD .= preg_quote(strtoupper($fila->mensaje)) . " | ";
                }
            }

            $oReturn->alert($mensFina);

            $sqlError = "update saeminv set minv_erro_sri = '$mensBDD' " . $sqlUpdate;
            $oIfx->QueryT($sqlError);
        }
    } catch (SoapFault $e) {
        $oReturn->alert("'NO HAY CONECCION AL SRI (AUTORIZAR) '");

        $sqlError = "update saeminv set minv_erro_sri = 'NO HUBO CONECCION AL SRI (AUTORIZAR)' " . $sqlUpdate;
        $oIfx->QueryT($sqlError);
    }

    return $oReturn;
}

function update_comprobante($claveAcceso = '', $numeroAutorizacion, $fechaAutorizacion, $id_docu)
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $user_sri = $_SESSION['U_ID'];
    $sqlUpdate = $_SESSION['sqlUpdate'];

    $oReturn = new xajaxResponse();

    $sqlUpdaComp = "update saeminv set 
                    minv_aprob_sri = 'S',
                    minv_auto_sri = '$numeroAutorizacion',
                    minv_user_sri = $user_sri,
                    minv_fech_sri = '$fechaAutorizacion',
                    minv_user_web  = $user_sri,
                    minv_erro_sri = '',
                    minv_clav_sri  = '$claveAcceso' " . $sqlUpdate;

    $oReturn->alert($sqlUpdaComp);

    if (!($oIfx->QueryT($sqlUpdaComp)))
        $oReturn->alert("Error al actualizar el comprobante");

    return $oReturn;
}

/* * ************************************************************************* */
/* DF01 :: G E N E R A    EL   S E C U E N C I A L   D E L    P E D I D O   */
/* * ************************************************************************* */

function secuencial_pedido($op, $serie, $as_codigo_pedido, $ceros_sql)
{
    //string 
    $ls_codigo;
    $ceros;
    $ls_codigos;

    //integer 
    $li_codigo;
    $ceros1;
    $ll_numeros;
    $ll_codigo;

    if (isset($as_codigo_pedido) or $as_codigo_pedido == '') {
        $li_codigo = ($as_codigo_pedido);

        $li_codigo = 0;
    } else {
        $li_codigo = $as_codigo_pedido;
    }

    $li_codigo = $as_codigo_pedido;

    $li_codigo = $li_codigo + 1;
    $ll_numeros = strlen(($li_codigo));
    $ceros = cero_mas('0', $ceros_sql);
    $ceros1 = strlen($ceros);
    $ll_codigo = $ceros1 - $ll_numeros;

    switch ($op) {
        case 1:
            // secuencial user
            $ls_codigos = $serie . '-' . (cero_mas('0', $ll_codigo)) . ($li_codigo);
            break;
        case 2:
            // secuencial normal					
            $ls_codigos = (cero_mas('0', $ll_codigo)) . ($li_codigo);
            break;
    }

    return $ls_codigos;
}

function cero_mas($caracter, $num)
{
    if ($num > 0) {
        for ($i = 1; $i <= $num; $i++) {
            $arreglo[$i] = $caracter;
        }

        while (list($i, $Valor) = each($arreglo)) {
            $cadena .= $Valor;
        }
    } else {
        $cadena = '';
    }

    return $cadena;
}

// ENVIO DE CORREO
function envio_correo($correo, $correo2, $correo3, $pedido, $vendedor, $cliente, $observaciones, $detalle, $usuario)
{
    include("class.phpmailer.php");
    include("class.smtp.php");

    $mail = new PHPMailer();
    $mail->IsSMTP();

    $mail->Host = "mail.sisconti.com.ec";
    //	$mail->From = "ruben.santacruz@sisconti.com.ec";
    //        $mail->Host = "mail.andinanet.net";
    $mail->From = "sistemasalitecno@alitecno.com.ec";
    $mail->FromName = "Sistema Web Alitecno Cliente: $cliente";
    $mail->Subject = "Bienvenidos al Sistema Web Alitecno";
    $mail->AltBody = "Bienvenidos.....";
    $mail->MsgHTML("Hola, Se realizo el siguiente Pedido Web:<br><br><br>
                            Pedido: $pedido <br>                            
                            Vendedor: $vendedor <br><br>
                            Usuario: $usuario <br><br>
                            Cliente: $cliente <br><br>
                            Observaciones: $observaciones <br><br>
                            Detalle: $detalle <br><br><br>
                            Recibe un cordial saludo,<br>
			    El equipo WebMaster Alitecno.<br>");
    $mail->AddAddress($correo, "Ventas");
    $mail->AddAddress($correo2, "Bodega");
    $mail->AddAddress($correo3, "Bodega2");
    //        $mail->AddAddress('ruben.santacruz@sisconti.com.ec',"Bodega2");
    $mail->IsHTML(true);
    $mail->Send();
}


/* * ****************************************** */
/*   M O S T R A R     D A T A    G R I D    */
/* * ***************************************** */
function agrega_modifica_grid($nTipo = 0, $descuento_general = 0, $codigo_prod = '', $aForm = '', $id = '', $cant_update = 0, $costo_update = 0, $iva_up = 0, $desc1_up = 0, $desc2_up = 0, $bode_up = 0, $cuen1 = '', $cuen2 = '')
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN, $DSN_Ifx;

    $oCnx = new Dbo();
    $oCnx->DSN = $DSN;
    $oCnx->Conectar();

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $aDataGrid  = $_SESSION['aDataGird_INV_MRECO'];
    $aDataGridEvaluacion  = $_SESSION['aDataGird_RECEPCION'];
    $aLabelGrid = $_SESSION['aLabelGirdProd_INV_MRECO'];

    $oReturn     = new xajaxResponse();

    $idempresa     = $aForm['empresa'];
    $idsucursal = $aForm['sucursal'];
    $decimal     = 6;

    // P R E C I O S     D E     C A DA      P R O D U C T O     D E     T A B LA
    // S A E P P R      C O N     L A     T A B L A      S A E P R O D
    $cantidad             = $aForm['cantidad'];
    $codigo_barra         = $aForm['codigo_barra'];
    $codigo_producto     = $aForm['codigo_producto'];
    $costo                 = $aForm['costo'];
    $iva                 = $aForm['iva'];
    $idbodega             = $aForm['bodega'];
    $descuento             = vacios($aForm['desc1'], 0);
    $descuento_2         = vacios($aForm['descuento_2'], 0);
    $cuenta_inv         = $aForm['cuenta_inv'];
    $cuenta_iva         = $aForm['cuenta_iva'];
    $lote_prod               = $aForm['lote_prod'];
    $serie_prod               = $aForm['serie_prod'];
    $mac_ad_prod = $aForm['mac_ad_prod'];
    $fecha_ela          = $aForm['fecha_ela'];
    $fecha_cad          = $aForm['fecha_cad'];
    $prod_nom           = $aForm['producto'];
    $ccosn           = $aForm['ccosn'];
    $tasa_efectiva_sn = $aForm['tasa_efectiva_sn'];

    if (!empty($serie_prod)) {
        $lote_prod = $serie_prod;
        $fecha_cad = '';
        $fecha_ela = '';
    }


    try {



        // -------------------------------------------------------------------------------------------------------
        // VALIDAMOS SI TIENE EL CHECK DE TASA EFECTIVA
        // -------------------------------------------------------------------------------------------------------
        if ($tasa_efectiva_sn == 'S') {
            $costo_tasa_efec = $costo;                                                  // 887.55
            $iva_tasa_efec = $iva;                                                      // 13%
            // Calculos para caluclar la tasa efectiva
            $porcentaje_impuesto = (100 - $iva_tasa_efec) / 100;                        // (100 - 13)/100 => 0.87
            $costo_diferencia = $costo_tasa_efec * $porcentaje_impuesto;                // 887.55 * 0.87 => 772.16              >Costo que baja a la tabla
            $costo_calculado = $costo_tasa_efec - $costo_diferencia;                    // 887.55 - 772.16 => 115.38
            $iva_diferencia = ($costo_calculado * 100) / $costo_diferencia;             // (115.38 * 100) / 772.16 => 14.9453   >Iva que baja a la tabla

            $costo = round($costo_diferencia, 4);
            $iva = round($iva_diferencia, 6);
        }
        // -------------------------------------------------------------------------------------------------------
        // FIN VALIDAMOS SI TIENE EL CHECK DE TASA EFECTIVA
        // -------------------------------------------------------------------------------------------------------





        // DEFI
        $tran_cod           = $aForm['tran'];
        $costo                = saetran_costo($oIfx, $idempresa,  $idsucursal, $codigo_producto, $idbodega, $tran_cod, $costo, $cantidad);

        // saeprod
        $sql = "select  
    
                    pr.prbo_cta_inv, 
                    pr.prbo_cta_cven, 
                    pr.prbo_cta_vent, 
                    pr.prbo_cta_desc, 
                    
                    pr.prbo_cta_devo, 
                    pr.prbo_cta_ideb, 
                    pr.prbo_cta_icre,
                    
    
    
                    p.prod_cod_prod,   pr.prbo_cod_unid,  COALESCE(pr.prbo_iva_porc,0) as prbo_iva_porc   ,
                    COALESCE(pr.prbo_ice_porc,0) as prbo_ice_porc,
                    COALESCE( pr.prbo_dis_prod,0 ) as stock, prod_cod_tpro, prod_lot_sino, prod_ser_prod
                    from saeprod p, saeprbo pr where
                    p.prod_cod_prod = pr.prbo_cod_prod and
                    p.prod_cod_empr = $idempresa and
                    p.prod_cod_sucu = $idsucursal and
                    pr.prbo_cod_empr = $idempresa and
                    pr.prbo_cod_bode = $idbodega and
                    p.prod_cod_prod = '$codigo_producto' ";
        if ($oIfx->Query($sql)) {
            if ($oIfx->NumFilas() > 0) {
                $idproducto = $oIfx->f('prod_cod_prod');
                $idunidad   = $oIfx->f('prbo_cod_unid');

                $prbo_cta_inv = $oIfx->f('prbo_cta_inv');
                $prbo_cta_cven = $oIfx->f('prbo_cta_cven');
                $prbo_cta_vent = $oIfx->f('prbo_cta_vent');
                $prbo_cta_desc = $oIfx->f('prbo_cta_desc');
                $prbo_cta_devo = $oIfx->f('prbo_cta_devo');
                $prbo_cta_ideb = $oIfx->f('prbo_cta_ideb');
                $prbo_cta_icre = $oIfx->f('prbo_cta_icre');
                $prod_lot_sino = $oIfx->f('prod_lot_sino');
                $prod_ser_prod = $oIfx->f('prod_ser_prod');
            } else {
                $idproducto = '';
                $idunidad   = '';

                $prbo_cta_inv = null;
                $prbo_cta_cven = null;
                $prbo_cta_vent = null;
                $prbo_cta_desc = null;
                $prbo_cta_devo = null;
                $prbo_cta_ideb = null;
                $prbo_cta_icre = null;
                $prod_lot_sino = null;
                $prod_ser_prod = null;
            }
        }
        $oIfx->Free();




        // -------------------------------------------------------------------------------------------------------
        // VALIDAMOS SI TIENE EL SERIE ELPRODUCTO PARA NO DEJARLO INSERTAR
        // -------------------------------------------------------------------------------------------------------

        if ($prod_ser_prod == 1 || $prod_ser_prod == 'S') {
            $serie_prod_an = 'S';
        } else {
            $serie_prod_an = 'N';
        }

        if ($serie_prod_an == 'S') {

            $id_user = $_SESSION['U_ID'];
            $fecha_ini = '2018-01-01';
            $fecha_fin = '2030-01-01';

            $sql_delete_lotes = "DELETE from tmp_prod_lote_web where user_cod_web = $id_user";
            $oIfx->QueryT($sql_delete_lotes);

            $sql_sp_lotes = "select * from sp_lotes_productos_web( $idempresa, $idsucursal, $idbodega, '$fecha_ini', '$fecha_fin', '$codigo_producto', '$codigo_producto', '2' , $id_user, '$lote_prod') ";
            $oIfx->Query($sql_sp_lotes);


            $sql_lotes_obtenido = "SELECT  sum(cant_lote) as cant, num_lote,  MAX(fecha_ela_lote) as felab, MAX(fecha_cad_lote) as fcad, 
                                        prod_cod_prod, prod_nom_prod, costo
                                        from tmp_prod_lote_web where
                                        user_cod_web  = $id_user and
                                        bode_cod_bode = $idbodega and
                                        empr_cod_empr = $idempresa and
                                        sucu_cod_sucu = $idsucursal
                                        group by 2, 5, 6, 7
                                        having  sum(cant_lote) <> 0
                                        order by fcad 
                                        limit 800
                                        ";
            $cantidad_lote = consulta_string_func($sql_lotes_obtenido, 'cant', $oIfx, 0);
            if ($cantidad_lote  > 0) {
                throw new Exception('La Serie: ' . $lote_prod . ' ya fue ingresada para el Producto:' . $codigo_producto . ', En existencia: ' . $cantidad_lote);
            }
        }
        // -------------------------------------------------------------------------------------------------------
        // FIN VALIDAMOS SI TIENE EL SERIE ELPRODUCTO PARA NO DEJARLO INSERTAR
        // -------------------------------------------------------------------------------------------------------





        if (
            $prbo_cta_inv != null &&
            $prbo_cta_cven != null &&
            $prbo_cta_vent != null &&
            $prbo_cta_desc != null &&
            $prbo_cta_devo != null &&
            $prbo_cta_ideb != null &&
            $prbo_cta_icre  != null
        ) {


            // TOTAL
            $total_fac  = 0;
            $dsc1       = ($costo * $cantidad * $descuento) / 100;
            $dsc2       = ((($costo * $cantidad) - $dsc1) * $descuento_2) / 100;
            if ($descuento_general > 0) {
                // descto general
                $dsc3   = ((($costo * $cantidad) - $dsc1 - $dsc2) * $descuento_general) / 100;
                $total_fact_tmp = ((($costo * $cantidad) - ($dsc1 + $dsc2 + $dsc3)));
                $tmp    = ((($costo * $cantidad) - ($dsc1 + $dsc2)));
            } else {
                // sin descuento general
                $total_fact_tmp = ((($costo * $cantidad) - ($dsc1 + $dsc2)));
                $tmp = $total_fact_tmp;
            }

            $total_fac = round($total_fact_tmp, 2);

            // total con iva
            if ($iva > 0) {
                $total_con_iva = round((($total_fac * $iva) / 100), 2) + $total_fac;
            } else {
                $total_con_iva = $total_fac;
            }

            if ($nTipo == 0) {


                // -------------------------------------------------------------------------------------------------------
                // VALIDAMOS SI TIENE SERIE Y VERIFICAMOS QUE NO EXISTA EN NINGUN BODEGA DE TODAS LAS SUCURSALES DE LA EMPRESA (SERIE UNICO)
                // -------------------------------------------------------------------------------------------------------

                if (!empty($serie_prod)) {
                    $existe_data_array = verifica_serie_prod($idempresa, $codigo_producto, $lote_prod);
                    if (count($existe_data_array)) {
                        $mensaje = 'Esto que lo escriba en la 1ª línea, \n y esto en la 2ª';
                        $mensaje = 'Serie ya existe: \n \n';
                        foreach ($existe_data_array as $key47 => $existe_data) {
                            $bodega_serie = $existe_data['bodega'];
                            $producto_serie = $existe_data['producto'];
                            $lote_serie = $existe_data['lote'];
                            $cantidad_serie = $existe_data['cantidad'];
                            $mensaje .= 'BODEGA: ' . $bodega_serie . ' \n PRODUCTO: ' . $producto_serie . ' \n SERIE: ' . $lote_serie . ' \n CANTIDAD: ' . round($cantidad_serie, 4) . ' \n \n';
                        }
                        throw new Exception($mensaje);
                    }

                    // -------------------------
                    // Validamos en el aDataGrid si la serie existe
                    // -------------------------


                    $existe_serie_aDataGrid = 'N';
                    foreach ($aDataGrid as $key48 => $data_aDataGrid) {
                        $prod_aDataGrid = $data_aDataGrid['Codigo Item'];
                        $serie_aDataGrid = $data_aDataGrid['Serie'];
                        if ($serie_aDataGrid == $lote_prod && $prod_aDataGrid == $codigo_producto) {
                            $existe_serie_aDataGrid = 'S';
                        }
                    }

                    if ($existe_serie_aDataGrid == 'S') {
                        throw new Exception('La Serie: ' . $lote_prod . ' ya fue ingresada para el Producto:' . $codigo_producto);
                    }

                    // -------------------------
                    // Validamos en el aDataGrid si la serie existe
                    // -------------------------



                }

                // -------------------------------------------------------------------------------------------------------
                // FIN VALIDAMOS SI TIENE SERIE Y VERIFICAMOS QUE NO EXISTA EN NINGUN BODEGA DE TODAS LAS SUCURSALES DE LA EMPRESA (SERIE UNICO)
                // -------------------------------------------------------------------------------------------------------








                //GUARDA LOS DATOS DEL DETALLE
                $cont = count($aDataGrid);
                // cantidad
                $fu->AgregarCampoNumerico($cont . '_cantidad', 'Cantidad|LEFT', false, $cantidad, 40, 40);
                $fu->AgregarComandoAlCambiarValor($cont . '_cantidad', 'cargar_update_cant(\'' . $cont . '\');');

                // costo
                $fu->AgregarCampoNumerico($cont . '_costo', 'Costo|LEFT', false, $costo, 80, 40);
                $fu->AgregarComandoAlCambiarValor($cont . '_costo', 'cargar_update_cant(\'' . $cont . '\');');

                // iva
                $fu->AgregarCampoNumerico($cont . '_iva', 'Iva|LEFT', false, $iva, 40, 40);
                $fu->AgregarComandoAlCambiarValor($cont . '_iva', 'cargar_update_cant(\'' . $cont . '\');');

                // descto1
                $fu->AgregarCampoNumerico($cont . '_desc1', 'Descto1|LEFT', false, $descuento, 40, 40);
                $fu->AgregarComandoAlCambiarValor($cont . '_desc1', 'cargar_update_cant(\'' . $cont . '\');');

                // descto2
                $fu->AgregarCampoNumerico($cont . '_desc2', 'Descto2|LEFT', false, 0, 40, 40);
                $fu->AgregarComandoAlCambiarValor($cont . '_desc2', 'cargar_update_cant(\'' . $cont . '\');');

                $aDataGrid[$cont][$aLabelGrid[0]] = floatval($cont);
                $aDataGrid[$cont][$aLabelGrid[1]] = $idbodega;
                $aDataGrid[$cont][$aLabelGrid[2]] = $idproducto;
                $aDataGrid[$cont][$aLabelGrid[3]] = $prod_nom;
                $aDataGrid[$cont][$aLabelGrid[4]] = $idunidad;
                $aDataGrid[$cont][$aLabelGrid[5]] = $cantidad;  //$cantidad;
                $aDataGrid[$cont][$aLabelGrid[6]] = $costo; //costo;
                $aDataGrid[$cont][$aLabelGrid[7]] = $iva; //iva
                $aDataGrid[$cont][$aLabelGrid[8]] = $descuento; // desc1
                $aDataGrid[$cont][$aLabelGrid[9]] = 0; // dec2
                $aDataGrid[$cont][$aLabelGrid[10]] = $descuento_general;
                $aDataGrid[$cont][$aLabelGrid[11]] = $total_fac;
                $aDataGrid[$cont][$aLabelGrid[12]] = $total_con_iva;
                $aDataGrid[$cont][$aLabelGrid[13]] = $lote_prod;
                $aDataGrid[$cont][$aLabelGrid[14]] = $fecha_ela;
                $aDataGrid[$cont][$aLabelGrid[15]] = $fecha_cad;
                $aDataGrid[$cont][$aLabelGrid[16]] = '';
                $aDataGrid[$cont][$aLabelGrid[17]] = '';
                $aDataGrid[$cont][$aLabelGrid[18]] = $cuenta_inv;
                $aDataGrid[$cont][$aLabelGrid[19]] = $cuenta_iva;
                $aDataGrid[$cont][$aLabelGrid[20]] = '';
                $aDataGrid[$cont][$aLabelGrid[21]] = '';
                $aDataGrid[$cont][$aLabelGrid[22]] = 0;
                $aDataGrid[$cont][$aLabelGrid[23]] = $serie_prod;
                $aDataGrid[$cont]['ccosn'] = $ccosn;
                $aDataGrid[$cont][$aLabelGrid[24]] = "
                                                        <div class='btn btn-success btn-sm' id='evaluacion" . $cont . "' onclick='guardar_evaluacion(" . $cont . ")'>
                                                            <span class='glyphicon glyphicon-check'></span> Imprimir
                                                        </div>
                                                        ";
                $aDataGrid[$cont][$aLabelGrid[25]] = $aDataGridEvaluacion;
                $aDataGrid[$cont][$aLabelGrid[26]] = $mac_ad_prod;
                $aDataGrid[$cont][$aLabelGrid[27]] = $mac_ad_prod;
            }

            $_SESSION['aDataGird_INV_MRECO'] = $aDataGrid;
            $sHtml = mostrar_grid();
            $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
            $oReturn->script('habilita(5)');
            $oReturn->script('limpiar_prod();');
            $oReturn->script('totales();');
            $oReturn->script('cerrar_ventana();');
        } else {
            $oReturn->alert('Verificar cuentas contables del producto !!!');
        }
    } catch (Exception $e) {
        $oReturn->script("Swal.fire({
                                width: '800px',
                                position: 'left',
                                type: 'error',
                                title: '" . $e->getMessage() . "',
                                showConfirmButton: true,
                                confirmButtonText: 'Aceptar',
                                timer: 99000
                            })");
    }

    return $oReturn;
}


function agrega_modifica_grid_update($descuento_general, $aForm = '')
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN, $DSN_Ifx;

    $oReturn = new xajaxResponse();

    $aDataGrid = $_SESSION['aDataGird_INV_MRECO'];
    $oReturn = new xajaxResponse();
    $empresa = $_SESSION['U_EMPRESA'];

    $cont = count($aDataGrid);
    $matriz = array();
    unset($matriz);
    if ($cont > 0) {
        $j = 0;
        foreach ($aDataGrid as $aValues) {
            $aux = 0;
            $total_fact = 0;
            $i = 0;
            foreach ($aValues as $aVal) {
                if ($aux == 0) {                    //id
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 1) {              //bodega
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 2) {              //codigo
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 3) {              //codigo
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 4) {              //unidad
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 5) {              //cantidad
                    $cant = $aVal;
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 6) {              //costo
                    $costo = $aVal;
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 7) {              //iva
                    $iva = $aVal;
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 8) {              //desc1
                    $desc1 = $aVal;
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 9) {              //dsc2
                    $desc2 = $aVal;
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 10) {             // desc general
                    $desc3 = $descuento_general;
                    $matriz[$j][$i] = $desc3;
                    $i++;
                } elseif ($aux == 11) {             // total
                    $descuento1 = ($costo * $cant * $desc1) / 100;
                    $descuento2 = ((($costo * $cant) - $descuento1) * $desc2) / 100;
                    $descuento3 = ((($costo * $cant) - $descuento1 - $descuento2) * $desc3) / 100;
                    $total_fact = round((($costo * $cant) - ($descuento1 + $descuento2 + $descuento3)), 2);
                    $matriz[$j][$i] = $total_fact;
                    $i++;
                } elseif ($aux == 12) {             // total iva
                    // total con iva
                    if ($iva > 0) {
                        $total_con_iva = round((($total_fact * $iva) / 100), 2) + $total_fact;
                    } else {
                        $total_con_iva = $total_fact;
                    }
                    $matriz[$j][$i] = $total_con_iva;
                    $i++;
                } elseif ($aux == 13) {             // lote
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 14) {             // fecha elA
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 15) {             // fecha cad
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 16) {             // detalle
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 17) {             // precio
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 18) {             // cuenta inventario
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 19) {             // cuenta impuesto
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 20) {             // modificsar
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 21) {             // eliminar
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 22) {             // dmov cod
                    $matriz[$j][$i] = $aVal;
                    $i++;
                } elseif ($aux == 23) {             // dmov cod pedi
                    $matriz[$j][$i] = $aVal;
                    $i++;
                }

                $aux++;
            }
            $j++;
        }

        unset($_SESSION['aDataGird_INV_MRECO']);
        $aDataGrid  = $_SESSION['aDataGird_INV_MRECO'];
        $aLabelGrid = $_SESSION['aLabelGirdProd_INV_MRECO'];

        for ($x = 0; $x <= ($j - 1); $x++) {
            for ($y = 0; $y <= $i; $y++) {
                $aDataGrid[$x][$aLabelGrid[$y]] = $matriz[$x][$y];
            }
        }

        $_SESSION['aDataGird_INV_MRECO'] = $aDataGrid;
        $sHtml = mostrar_grid();
        $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
        $oReturn->script('totales();');
    }

    return $oReturn;
}

function mostrar_grid()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN, $DSN_Ifx;

    $oCnx = new Dbo();
    $oCnx->DSN = $DSN;
    $oCnx->Conectar();

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $idempresa = $_SESSION['U_EMPRESA'];
    $aDataGrid = $_SESSION['aDataGird_INV_MRECO'];
    $aLabelGrid = $_SESSION['aLabelGirdProd_INV_MRECO'];

    unset($array_bode);
    $sql = "SELECT bode_cod_bode, bode_nom_bode FROM saebode where bode_cod_empr = $idempresa ";
    $array_bode = array_dato($oIfx, $sql, 'bode_cod_bode', 'bode_nom_bode');

    unset($array_unid);
    $sql = "SELECT unid_cod_unid, unid_sigl_unid FROM saeunid where unid_cod_empr = $idempresa ";
    $array_unid = array_dato($oIfx, $sql, 'unid_cod_unid', 'unid_sigl_unid');

    $cont = 0;
    foreach ($aDataGrid as $aValues) {
        $aux = 0;
        foreach ($aValues as $aVal) {
            if ($aux == 0)
                $aDatos[$cont][$aLabelGrid[$aux]] = $cont + 1;
            elseif ($aux == 1) {
                //bodega
                $aDatos[$cont][$aLabelGrid[$aux]] = $array_bode[$aVal];
            } elseif ($aux == 2) {
                $cod_prod = $aVal;
                $aDatos[$cont][$aLabelGrid[$aux]] = $cod_prod;
            } elseif ($aux == 3) {
                $aDatos[$cont][$aLabelGrid[$aux]] = $aVal;
            } elseif ($aux == 4) {
                $aDatos[$cont][$aLabelGrid[$aux]] = $array_unid[$aVal];
            } elseif ($aux == 5) {
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
            } elseif ($aux == 6) {
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
            } elseif ($aux == 7) {
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
            } elseif ($aux == 8) {
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
            } elseif ($aux == 9) {
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
            } elseif ($aux == 10) {
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
            } elseif ($aux == 11) {
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right"><span class="fecha_letra">' . $aVal . '</span></div>';
            } elseif ($aux == 12) {
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right"><span class="fecha_letra">' . $aVal . '</span></div>';
            } elseif ($aux == 20)
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="center">
                                                        <button type="button" class="btn btn-sm btn-warning" onclick="agregar_detalle(1,' . $cont . ');">
                                                            <span class="glyphicon glyphicon-pencil"></span>
                                                        </button>
                                                    </div>';
            elseif ($aux == 21)
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="center">
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="javascript:elimina_detalle(' . $cont . ');">
                                                            <span class="glyphicon glyphicon-remove"></span>
                                                        </button>
                                                    </div>';
            elseif ($aux == 17)
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="center">
														<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/process_accept24.png"
														title = "Presione aqui para Precios"
														style="cursor: hand !important; cursor: pointer !important;"
														onclick="javascript: precio_inv(' . $cont . ');"
														alt="Precios"
														align="bottom" />
													</div>';
            else
                $aDatos[$cont][$aLabelGrid[$aux]] = $aVal;
            $aux++;
        }
        $cont++;
    }
    return genera_grid($aDatos, $aLabelGrid, 'Lista de Productos', 98);
}

function cancelar_pedido()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $aDataGrid = $_SESSION['aDataGird_INV_MRECO'];
    $aDataPrueba = $_SESSION['aDataPrueba'];
    unset($_SESSION['aDataGird_INV_MRECO']);
    unset($_SESSION['aDataPrueba']);
    $sScript = "xajax_genera_formulario_pedido();";
    $oReturn = new xajaxResponse();
    $oReturn->clear("divFormularioDetalle", "innerHTML");
    $oReturn->script($sScript);
    return $oReturn;
}

function elimina_detalle($id = null, $idempresa,  $aForm = '')
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN, $DSN_Ifx;

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse();

    $aLabelGrid = $_SESSION['aLabelGirdProd_INV_MRECO'];
    $aDataGrid  = $_SESSION['aDataGird_INV_MRECO'];
    $contador   = count($aDataGrid);

    if ($contador > 1) {
        unset($aDataGrid[$id]);
        $aDataGrid = array_values($aDataGrid);
        $cont = 0;
        foreach ($aDataGrid as $aValues) {
            $aux = 0;
            foreach ($aValues as $aVal) {
                if ($aux == 0) {
                    $aDatos[$cont][$aLabelGrid[$aux]] = $cont;
                } elseif ($aux == 20) {
                    $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="center">
                                                                <button type="button" class="btn btn-sm btn-warning" onclick="agregar_detalle(1,' . $cont . ');">
                                                                    <span class="glyphicon glyphicon-pencil"></span>
                                                                </button>
                                                        </div>';
                } elseif ($aux == 21)
                    $aDatos[$cont][$aLabelGrid[$aux]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/delete_1.png"
                                                                title = "Presione aqui para Eliminar"
                                                                style="cursor: hand !important; cursor: pointer !important;"
                                                                onclick="javascript:elimina_detalle(' . $cont . ');"
                                                                alt="Eliminar"
                                                                align="bottom" />';
                else
                    $aDatos[$cont][$aLabelGrid[$aux]] = $aVal;
                $aux++;
            }
            $cont++;
        }

        $_SESSION['aDataGird_INV_MRECO'] = $aDatos;

        $sHtml = mostrar_grid();
    } else {
        unset($aDataGrid[0]);
        $_SESSION['aDataGird_INV_MRECO'] = $aDatos;
        $sHtml = "";
        $sHtml = $mostrar_prueba_grid;
    }


    $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
    $oReturn->script('totales();');
    return $oReturn;
}

/* * ********************************************************************* */
/* T O T A L       D E L      P A G O       D E L       P E D I D O     */
/* * ********************************************************************* */

function total_grid($descuento_general_tmp, $flete_tmp, $otro_tmp, $anticipo_tmp, $aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oCon = new Dbo;
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $oReturn = new xajaxResponse();

    $idempresa          = $_SESSION['U_EMPRESA'];
    $usuario_informix   = $_SESSION['U_USER_INFORMIX'];
    $aDataGrid          = $_SESSION['aDataGird_INV_MRECO'];
    $contdata           = count($aDataGrid);
    $sucursal           = $aForm['sucursal'];
    $cod_prove          = $aForm['cliente'];
    $cod_tran           = $aForm['tran'];
    $contri             = $aForm['contri_prove'];
    $array_otros        = $_SESSION['U_OTROS'];

    if ($contdata > 0) {

        $total_iva      = 0;
        $total_sin_iva  = 0;
        $pedf_iva       = 0;
        $total          = 0;
        $con_iva        = 0;
        $sin_iva        = 0;
        $x              = 0;
        foreach ($aDataGrid as $aValues) {
            $aux = 0;
            foreach ($aValues as $aVal) {
                if ($aux == 5) {      //CANTIDAD
                    $cant = $aVal;
                } elseif ($aux == 6) {     //COSTO
                    $costo = $aVal;
                } elseif ($aux == 7) {     //IVA
                    $iva = $aVal;
                } elseif ($aux == 8) {     //DESCUENTO 1
                    $descuento_1 = $aVal;
                } elseif ($aux == 9) {     //DESCUENTO 2
                    $descuento_2 = $aVal;
                } elseif ($aux == 10) {                                     //DESCUENTO GENERAL
                    $descuento_3 = $aVal;
                    $dsc1 = ($costo * $cant * $descuento_1) / 100;
                    $dsc2 = ((($costo * $cant) - $dsc1) * $descuento_2) / 100;
                    if ($descuento_3 > 0) {
                        // descto general
                        $dsc3           = ((($costo * $cant) - $dsc1 - $dsc2) * $descuento_3) / 100;
                        $total_fact_tmp = ((($costo * $cant) - ($dsc1 + $dsc2 + $dsc3)));
                        $tmp            = ((($costo * $cant) - ($dsc1 + $dsc2)));
                    } else {
                        // sin descuento general
                        $total_fact_tmp = ((($costo * $cant) - ($dsc1 + $dsc2)));
                        $tmp            = $total_fact_tmp;
                    }

                    $subtotal   += round($tmp, 2);
                    $total_fac  += round($total_fact_tmp, 2);
                    if ($iva > 0) {
                        //                                        $total_iva += round(((($total_fact_tmp*$iva)/100)),2);
                        $total_iva += (($total_fact_tmp * $iva) / 100);
                        $con_iva   += round($total_fact_tmp, 2);
                    } else {
                        $sin_iva   += round($total_fact_tmp, 2);
                    }
                }
                $aux++;
            }
            $x++;
        }

        //descuento general por usuario
        $sql_desc = "select usua_por_boni  from saeusua where  usua_cod_usua = $usuario_informix ";
        $desc_general = consulta_string_func($sql_desc, 'usua_por_boni', $oIfx, 0);

        // form total
        $fu->AgregarCampoNumerico('descuento_general', 'Descuento General|left', false, 0, 70, 2);
        $fu->AgregarComandoAlCambiarValor('descuento_general', 'cargar_descuento(' . $desc_general . ', ' . $total_fac . ', ' . $total_iva . ' )');
        $fu->AgregarCampoNumerico('descuento_valor', 'Descuento General Valor|left', false, 0, 70, 10);
        $fu->AgregarComandoAlPonerEnfoque('descuento_valor', 'this.blur()');

        $fu->AgregarCampoNumerico('anticipo', 'Anticipo|left', false, 0, 70, 10);
        $fu->AgregarCampoNumerico('iva_total', 'Iva|left', false, 0, 70, 10);
        $fu->AgregarComandoAlPonerEnfoque('iva', 'this.blur()');
        $fu->AgregarCampoNumerico('total_fac', 'Total|left', false, 0, 70, 10);
        $fu->AgregarComandoAlPonerEnfoque('total_fac', 'this.blur()');
        $fu->AgregarCampoNumerico('total_fac', 'Total|left', false, 0, 70, 10);
        $fu->AgregarComandoAlPonerEnfoque('total_fac', 'this.blur()');
        $fu->AgregarCampoNumerico('con_iva', 'Monto con Iva|left', false, 0, 70, 10);
        $fu->AgregarComandoAlPonerEnfoque('con_iva', 'this.blur()');
        $fu->AgregarCampoNumerico('sin_iva', 'Monto sin Iva|left', false, 0, 70, 10);
        $fu->AgregarComandoAlPonerEnfoque('sin_iva', 'this.blur()');

        // OTROS
        if (count($array_otros) > 0) {
            $html_txt = '';
            $txt = '';
            $total_otros = 0;
            foreach ($array_otros as $val) {
                $id_otro = $val[0];
                $det_otro = $val[1];
                $txt = $id_otro . '_OTRO';
                $val_txt = $aForm[$txt];
                if (empty($val_txt)) {
                    $val_txt = 0;
                }
                $fu->AgregarCampoNumerico($txt, $det_otro . '|left', false, $val_txt, 70, 10);
                $fu->AgregarComandoAlCambiarValor($txt, 'totales( )');
                $html_txt .= '<table cellspacing="2" width="100%" border="0">
                                            <tr>
                                                <td  bgcolor="#EBEBEB" class="fecha_grande">' . $fu->ObjetoHtmlLBL($txt) . '</td>
                                                <td  bgcolor="#EBEBEB" class="fecha_grande" align="right">' . $fu->ObjetoHtml($txt) . '</td>
                                            </tr>
                                     </table>';
                $total_otros += $val_txt;
            } // fin foreach
        } // fin otros

        $fu->cCampos["descuento_general"]->xValor = $descuento_general_tmp;
        $fu->cCampos["descuento_valor"]->xValor = round(($subtotal * $descuento_general_tmp / 100), 2);
        $fu->cCampos["anticipo"]->xValor = $anticipo_tmp;
        $fu->cCampos["total_fac"]->xValor = round($subtotal, 2);
        $fu->cCampos["con_iva"]->xValor = round($con_iva, 2);
        $fu->cCampos["sin_iva"]->xValor = round($sin_iva, 2);
        $total_fac_total = round((round($subtotal, 2) - round(($subtotal * $descuento_general_tmp / 100), 2)), 2);
        $fu->cCampos["iva_total"]->xValor = round($total_iva, 2);


        //$sql = "select empr_iva_empr, empr_cod_pais,  * from saeempr where empr_cod_empr = $idempresa ";
        //$iva = round(consulta_string_func($sql, 'empr_iva_empr', $oIfx, 0));
        $empr_cod_pais = $_SESSION['U_PAIS_COD'];

        // IMPUESTOS POR PAIS
        $sql = "select p.impuesto, p.etiqueta, p.porcentaje from comercial.pais_etiq_imp p where
					p.pais_cod_pais = $empr_cod_pais ";
        unset($array_imp);
        if ($oCon->Query($sql)) {
            if ($oCon->NumFilas() > 0) {
                do {
                    $impuesto      = $oCon->f('impuesto');
                    $etiqueta     = $oCon->f('etiqueta');
                    $porcentaje = $oCon->f('porcentaje');
                    $array_imp[$impuesto] = $etiqueta;
                } while ($oCon->SiguienteRegistro());
            }
        }
        $oCon->Free();


        $sql_cod_mone = "select mone_cod_mone from 
            saemone, saepara
            where para_mon_def = mone_cod_mone
            and mone_cod_empr = $idempresa
            and para_cod_empr = $idempresa
            and para_cod_sucu = $sucursal
            ";
        $cod_mone_princ = consulta_string_func($sql_cod_mone, 'mone_cod_mone', $oIfx, '');



        $sigl_mone = $_SESSION['U_MONE_SIGLA'] . '$';
        $moneda    = $aForm['moneda'];
        $val_camb  = $aForm['cotizacion'];
        $sql       = "select pcon_mon_base, pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa ";
        $mone_base = consulta_string_func($sql, 'pcon_mon_base', $oIfx, '');
        $mone_secu = consulta_string_func($sql, 'pcon_seg_mone', $oIfx, '');


        $sql_mone_principal = "select * from saemone
            where mone_cod_empr = $idempresa
            and mone_cod_mone = $cod_mone_princ";

        if ($oIfx->Query($sql_mone_principal)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    //$moneda_principal = $oIfx->f('mone_sgl_mone') . '' . $oIfx->f('mone_smb_mene');
                    //$moneda_principal_ad = $oIfx->f('mone_sgl_mone') . '' . $oIfx->f('mone_smb_mene');
                    $moneda_principal = $oIfx->f('mone_sgl_mone');
                    $moneda_principal_ad = $oIfx->f('mone_sgl_mone');
                } while ($oIfx->SiguienteRegistro());
            }
        }

        if ($moneda == $mone_base) {
            $sql_mone_secundaria = "select * from saemone
                    where mone_cod_empr = $idempresa
                    and mone_cod_mone <> $mone_base
                    and mone_cod_mone = $mone_secu
                    ";
        } else {
            $sql_mone_secundaria = "select * from saemone
                    where mone_cod_empr = $idempresa
                    and mone_cod_mone <> $mone_base
                    and mone_cod_mone = $moneda
                    ";
        }



        if ($oIfx->Query($sql_mone_secundaria)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    //$moneda_secundaria = $oIfx->f('mone_sgl_mone') . '' . $oIfx->f('mone_smb_mene');
                    //$moneda_secundaria_ad = $oIfx->f('mone_sgl_mone') . '' . $oIfx->f('mone_smb_mene');
                    $moneda_secundaria = $oIfx->f('mone_sgl_mone');
                    $moneda_secundaria_ad = $oIfx->f('mone_sgl_mone');
                } while ($oIfx->SiguienteRegistro());
            }
        }





        if ($moneda == $mone_base) {

            $fil_coti = '';
            $S_PAIS_API_SRI = $_SESSION['S_PAIS_API_SRI'];
            if ($S_PAIS_API_SRI == '51') {
                //PERU: PARA EL CMABIO DE MONEDA SE CONSIDERA LA FECHA DE EMISION DEL COMPORBANTE
                $fech  = $aForm['fecha_pedido'];

                $fil_coti = "and tcam_fec_tcam<='$fech'";
            }


            // MONEDA LOCAL
            $sql = "select tcam_val_tcam   from saetcam where
                        mone_cod_empr = $idempresa and 
                        tcam_cod_mone in ( select pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa  ) and
                        tcam_fec_tcam in ( select  max(tcam_fec_tcam)   from saetcam where
                                                    mone_cod_empr = $idempresa and 
                                                    tcam_cod_mone in ( select pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa ) $fil_coti
                                        ) ";
            $val_camb = consulta_string_func($sql, 'tcam_val_tcam', $oIfx, '0');
        }


        $total_usd = round(((round(($total_fac_total + $total_iva + $total_otros), 2)) / $val_camb), 2);


        // -------------------------------------------------------------------------------------------------------------------------------------
        // Cambiamos la moneda principal y secundaria dependiendo de la moneda seleccionada
        // -------------------------------------------------------------------------------------------------------------------------------------
        if ($moneda == $mone_base) {
            $moneda_principal = $moneda_principal_ad;
            $moneda_secundaria = $moneda_secundaria_ad;
        } else {
            $moneda_principal = $moneda_secundaria_ad;
            $moneda_secundaria = $moneda_principal_ad;
            $total_usd = round(((round(($total_fac_total + $total_iva + $total_otros), 2)) * $val_camb), 2);
        }
        // -------------------------------------------------------------------------------------------------------------------------------------
        // FIN Cambiamos la moneda principal y secundaria dependiendo de la moneda seleccionada
        // -------------------------------------------------------------------------------------------------------------------------------------



        $sHtml  = '';
        $sHtml .= '<div class="row">
                    <div class="col-md-12">
                            <div class="form-group">
                                <label for="con_iva" class="col-md-2 control-label" style="font-size:12px; text-align:right">MONTO CON ' . $array_imp['IVA'] . ': ' . $moneda_principal . '</label>                                
                                <div class="col-sm-2">
                                    <strong><input type="text" class="form-control input-sm" id="con_iva" name="con_iva" size="0" readonly value="' . round($con_iva, 2) . '" style="text-align:right"/></strong>
                                </div>
                                <label for="sucursal" class="col-md-2 control-label" style="font-size:12px; text-align:right">DESCUENTO GRAL: %</label>
                                <div class="col-sm-2">
                                    ' . $fu->ObjetoHtml('descuento_general') . '
                                </div>
                                <label for="tipo" class="col-md-2 control-label" style="font-size:12px; text-align:right">SUMA: ' . $moneda_principal . '</label>
                                <div class="col-sm-2">
                                    <strong><input type="text" readonly class="form-control input-sm" id="total_fac" name="total_fac" size="0" readonly value="' . round($subtotal, 2) . '" style="text-align:right"/></strong>
                                </div>
                            </div>                
                    </div>
                    <div class="col-md-12">
                            <div class="form-group">
                                <label for="con_iva" class="col-md-2 control-label" style="font-size:12px; text-align:right">MONTO SIN ' . $array_imp['IVA'] . ': ' . $moneda_principal . '</label>
                                <div class="col-sm-2">
                                    <strong><input type="text" class="form-control input-sm" id="sin_iva" name="sin_iva" size="0" readonly value="' . round($sin_iva, 2) . '" style="text-align:right"/></strong>
                                </div>
                                <label for="sucursal" class="col-md-2 control-label" style="font-size:12px; text-align:right">DSCTO GRAL (VALOR): ' . $moneda_principal . '</label>
                                <div class="col-sm-2">
                                    <strong><input type="text" class="form-control input-sm" id="descuento_valor" name="descuento_valor" size="0"  value="' . round(($subtotal * $descuento_general_tmp / 100), 2) . '" style="text-align:right"/></strong>
                                </div>
                                <label for="tipo" class="col-md-2 control-label" style="font-size:12px; text-align:right;">DSCTO GRAL (VALOR): ' . $moneda_principal . '</label>
                                <div class="col-sm-2">
                                    <strong><input type="text" class="form-control input-sm"  size="0" readonly value="' . round(($subtotal * $descuento_general_tmp / 100), 2) . '" style="text-align:right"/></strong>
                                </div>
                            </div>                
                    </div>
                    <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-md-2 control-label" style="font-size:12px;"></label>
                                <label class="col-md-1 control-label" style="font-size:12px;"></label>
                                <div class="col-sm-1"></div>
                                <label class="col-md-2 control-label" style="font-size:12px; text-align:right">
									' . $html_txt . '
								</label>
                                <label class="col-md-1 control-label" style="font-size:12px;"></label>
                                <div class="col-sm-1"></div>
                                <label class="col-md-2 control-label" style="font-size:12px; text-align:right; height:25px">SUBTOTAL: ' . $moneda_principal . '</label>
                                <div class="col-sm-2" align="right"><strong>' . number_format(round($total_fac_total, 2), 2, '.', ',') . '</strong></div>
                            </div>                
                    </div>
                    <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-md-2 control-label" style="font-size:12px;"></label>
                                <label class="col-md-1 control-label" style="font-size:12px;"></label>
                                <div class="col-sm-1"></div>
                                <label class="col-md-2 control-label" style="font-size:12px;" align="right"></label>
                                <label class="col-md-1 control-label" style="font-size:12px;"></label>
                                <div class="col-sm-1"></div>
                                <label class="col-md-2 control-label" style="font-size:12px; text-align:right; height:25px">' . $array_imp['IVA'] . ': ' . $moneda_principal . '</label>
                                <div class="col-sm-2"><strong><input type="text" readonly class="form-control input-sm" id="iva_total" name="iva_total" size="0" readonly value="' . round($total_iva, 2) . '" style="text-align:right"/></strong></div>
                            </div>                
                    </div>
                    <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-md-2 control-label" style="font-size:12px;"></label>
                                <label class="col-md-1 control-label" style="font-size:12px;"></label>
                                <div class="col-sm-1"></div>
                                <label class="col-md-2 control-label" style="font-size:12px;" align="right"></label>
                                <label class="col-md-1 control-label" style="font-size:12px;"></label>
                                <div class="col-sm-1"></div>
                                <label class="col-md-2 control-label" style="font-size:12px; text-align:right; height:25px">OTROS VALORES: ' . $moneda_principal . '</label>
                                <div class="col-sm-2" align="right"><strong>' . round($total_otros, 2) . '</strong></div>
                            </div>                
                    </div>
                    <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-md-2 control-label" style="font-size:12px;"></label>
                                <label class="col-md-1 control-label" style="font-size:12px;"></label>
                                <div class="col-sm-1"></div>
                                <label class="col-md-2 control-label" style="font-size:12px;" align="right"></label>
                                <label class="col-md-1 control-label" style="font-size:12px;"></label>
                                <div class="col-sm-1"></div>
                                <label class="col-md-2 control-label" style="font-size:12px; text-align:right; height:25px" ><strong><span  class="total_fact">TOTAL: ' . $moneda_principal . '</span></strong></label>
                                
                                <div class="col-sm-2" align="right"><span  class="total_fact"><strong>' . number_format(round($total_fac_total + $total_iva + $total_otros, 2), 2, '.', ',') . '</strong></span></div>
                            </div>                
                    </div>
                    <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-md-2 control-label" style="font-size:12px;"></label>
                                <label class="col-md-1 control-label" style="font-size:12px;"></label>
                                <div class="col-sm-1"></div>
                                <label class="col-md-2 control-label" style="font-size:12px;" align="right"></label>
                                <label class="col-md-1 control-label" style="font-size:12px;"></label>
                                <div class="col-sm-1"></div>
                                <label class="col-md-2 control-label" style="font-size:12px; text-align:right"><strong><span  class="total_fact">TOTAL: ' . $moneda_secundaria . '</span></strong></label>
                                <div class="col-sm-2" align="right"><span  class="total_fact"><strong>' . number_format(round(($total_usd), 2), 2, '.', ',') . '</strong></span></div>
                            </div>                
                    </div>
                </div>
                ';
        $sHtml .= '</table>';

        $sHtml99 .= '<fieldset style="border:#FFFFFF 1px solid; padding:2px; text-align:center; width:98%;">';
        $sHtml99 .= '<table align="right" border="0"  class="table table-striped table-condensed" style="width: 80%; margin-bottom: 0px;">
                            <tr>
                                            <td  class="iniciativa"  height="25">MONTO CON ' . $array_imp['IVA'] . ':</td>
                                            <td  class="fecha_grande" align="right">$</td>
                                            <td  class="fecha_grande" align="right">' . $fu->ObjetoHtml('con_iva') . '</td>
                                            <td  class="fecha_grande" width="1%" ></td>
                                            <td  class="iniciativa"   height="25">DESCUENTO GRAL:</td>
                                            <td  class="fecha_grande" align="right"></td>
                                            <td  class="fecha_grande" align="right">' . $fu->ObjetoHtml('descuento_general') . '</td>
                                            <td  class="fecha_grande" align="right">%</td>
                                            <td  class="fecha_grande" width="5%" ></td>
                                            <td  class="iniciativa" height="25">SUMAN:</td>
                                            <td  class="fecha_grande" align="right">$</td>
                                            <td  class="fecha_grande" align="right">' . $fu->ObjetoHtml('total_fac') . '</td>
                                            <td  class="fecha_grande" align="right"></td>
                            </tr>
                            <tr>
                                            <td  class="iniciativa"  height="25">MONTO SIN ' . $array_imp['IVA'] . ':</td>
                                            <td  class="fecha_grande" align="right">$</td>
                                            <td  class="fecha_grande" align="right">' . $fu->ObjetoHtml('sin_iva') . '</td>
                                            <td  class="fecha_grande" width="1%" ></td>

                                            <td  class="iniciativa"  height="25">DSCTO GRAL (VALOR):</td>
                                            <td  class="fecha_grande" align="right">$</td>
                                            <td  class="fecha_grande" align="right">' . $fu->ObjetoHtml('descuento_valor') . '</td>
                                            <td  class="fecha_grande" align="right"></td>

                                            <td  class="fecha_grande" width="5%"></td>

                                            <td  class="iniciativa" height="25">DSCTO GRAL (VALOR):</td>
                                            <td  class="fecha_grande" align="right">$</td>
                                            <td  class="fecha_grande" align="right">' . round(($subtotal * $descuento_general_tmp / 100), 2) . '</td>
                                            <td  class="fecha_grande" align="right"></td>
                            </tr>
                            <tr>
                                            <td  class="iniciativa"  height="25"></td>
                                            <td  class="fecha_grande" align="right"></td>
                                            <td  class="fecha_grande" align="right"></td>
                                            <td  class="fecha_grande" width="1%" ></td>

                                            <td  class="iniciativa"  colspan="3" rowspan="4" valign="top" >' . $html_txt . '</td>

                                            <td  class="fecha_grande" align="right"></td>
                                            <td  class="fecha_grande" width="5%"></td>
                                            <td  class="iniciativa"   height="25">SUBTOTAL:</td>
                                            <td  class="fecha_grande" align="right">$</td>
                                            <td  class="fecha_grande" align="right">' . number_format(round($total_fac_total, 2), 2, '.', ',') . '</td>
                                            <td  class="fecha_grande" align="right"></td>
                            </tr>
                            <tr>
                                            <td  class="iniciativa"  height="25"></td>
                                            <td  class="fecha_grande" align="right"></td>
                                            <td  class="fecha_grande" align="right"></td>
                                            <td  class="fecha_grande" width="1%" ></td>
                                            <td  class="fecha_grande"></td>
                                            <td  class="fecha_grande"></td>

                                            <td  class="iniciativa"  height="25">' . $array_imp['IVA'] . ':</td>
                                            <td  class="fecha_grande" align="right">$</td>
                                            <td  class="fecha_grande" align="right">' . $fu->ObjetoHtml('iva_total') . '</td>
                                            <td  class="fecha_grande" align="right"></td>
                            </tr>
                            <tr>
                                            <td  class="iniciativa"  height="25"></td>
                                            <td  class="fecha_grande" align="right"></td>
                                            <td  class="fecha_grande" align="right"></td>
                                            <td  class="fecha_grande" width="1%"></td>
                                            <td  class="fecha_grande"></td>
                                            <td  class="fecha_grande"></td>
                                            <td  class="iniciativa"  height="25">OTROS VALORES:</td>
                                            <td  class="fecha_grande" align="right">$</td>
                                            <td  class="fecha_grande" align="right">' . round($total_otros, 2) . '</td>
                                            <td  class="fecha_grande" align="right"></td>
                            </tr>
                            <tr>
                                            <td  class="fecha_grande"></td>
                                            <td  class="iniciativa" height="25"></td>
                                            <td  class="fecha_grande" align="right"></td>
                                            <td  class="fecha_grande" align="right" width="1%"></td>
                                            <td  class="fecha_grande" align="right"></td>
                                            <td  class="fecha_grande"></td>
                                            <td  class="total_fact"  height="25">TOTAL:</td>
                                            <td  class="total_fact" align="right">$</td>
                                            <td  class="total_fact" align="right">' . number_format(round($total_fac_total + $total_iva + $total_otros, 2), 2, '.', ',') . '</td>
                                            <td  class="total_fact" align="right"></td>
                            </tr>';
        $sHtml99 .= '</table></fieldset>';

        // RETENCION FORM
        // TIDU
        $sql = "select  defi_cod_tidu  from saedefi where
                            defi_cod_empr = $idempresa and
                            defi_cod_sucu = $sucursal and
                            defi_cod_tran = '$cod_tran' ";
        $tidu = consulta_string($sql, 'defi_cod_tidu', $oIfx, '');

        $anio = substr($aForm['fecha_pedido'], 0, 4);
        $idprdo = (substr($aForm['fecha_pedido'], 5, 2)) * 1;
        $fecha_ejer = $anio . '-12-31';
        $sql = "select ejer_cod_ejer from saeejer where ejer_fec_finl = '$fecha_ejer' and ejer_cod_empr = $idempresa ";
        $idejer = consulta_string($sql, 'ejer_cod_ejer', $oIfx, 1);

        $sql = "select  d.defi_cod_trtc, d.defi_cod_retiva,
                            ( select  tret_porct from saetret where
                                    tret_cod_empr = $idempresa and
                                    tret_cod = d.defi_cod_trtc  ) as por_ret,
                            ( select  tret_porct from saetret where
                                    tret_cod_empr = $idempresa and
                                    tret_cod = d.defi_cod_retiva  ) as por_iva,
                            ( select  tret_cta_cre from saetret where
                                    tret_cod_empr = $idempresa and
                                    tret_cod = d.defi_cod_trtc  ) as cuen_ret,
                            ( select  tret_cta_cre  from saetret where
                                    tret_cod_empr = $idempresa and
                                    tret_cod = d.defi_cod_retiva  ) as cuen_iva,
                            ( select   secu_ret_fuen  from saesecu where
                                    secu_cod_ejer = $idejer and
                                    secu_num_prdo = $idprdo and
                                    secu_cod_modu = 10 and
                                    secu_cod_tidu = '$tidu' and
                                    secu_cod_empr = $idempresa and
                                    secu_cod_sucu = $sucursal limit 1) as secu_ret
                            from saetran t, saedefi d  where
                            t.tran_cod_tran = d.defi_cod_tran and
                            t.tran_cod_empr = $idempresa and
                            t.tran_cod_sucu = $sucursal and
                            t.tran_cod_modu = 10 and
                            d.defi_cod_empr = $idempresa  and
                            d.defi_tip_defi = '0' and
                            t.tran_cod_tran = '$cod_tran' and
                            d.defi_cod_modu = 10 order by 2 ";
        //$oReturn->alert($sql);
        if ($oIfx->Query($sql)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    $cod_ret       = $oIfx->f('defi_cod_trtc');
                    $cod_ret_porc = $oIfx->f('por_ret');
                    $cuen_ret = $oIfx->f('cuen_ret');
                    $cod_ret_iva = $oIfx->f('defi_cod_retiva');
                    $cod_ret_iva_porc = $oIfx->f('por_iva');
                    $cuen_iva = $oIfx->f('cuen_iva');
                    $secu_ret = $oIfx->f('secu_ret');
                } while ($oIfx->SiguienteRegistro());
            }
        }


        $sql = "select retp_sec_retp from saeretp where
					retp_cod_empr =  $idempresa and
					retp_cod_sucu =  $sucursal and
					retp_act_retp = '1' 
					-- and retp_elec_sn  = 'S' ";
        // $secu_ret = consulta_string($sql, 'retp_sec_retp', $oIfx, 0);
        // $secu_ret = secuencial(2, '', $secu_ret, 9);

        $secu_ret     = $aForm['ret_num'];


        $aDataGrid_ret = $_SESSION['aDataGirdRete'];
        $aLabelGrid_ret = array('Id', 'Codigo Sri', 'Porcentaje', 'Base Imponible', 'Retencion', 'No Retencion', 'Cta Contable', 'Eliminar');
        unset($array_rete);
        if ($contri == 'N') {
            //$oReturn->alert('1');
            $cont = 0;
            if (!empty($cod_ret)) {
                $contri = 'NO';
                $ret_val = round(((($flete_tmp + round($con_iva, 2) + round($sin_iva, 2) + $otro_tmp) * $cod_ret_porc) / 100), 2);
                $fu->AgregarCampoNumerico($cont . '_retencion', 'Retencion|left', true, $secu_ret, 70, 10);
                $aDataGrid_ret[$cont][$aLabelGrid_ret[0]] = floatval($cont);
                $aDataGrid_ret[$cont][$aLabelGrid_ret[1]] = $cod_ret;
                $aDataGrid_ret[$cont][$aLabelGrid_ret[2]] = $cod_ret_porc;
                $aDataGrid_ret[$cont][$aLabelGrid_ret[3]] = ($flete_tmp + round($con_iva, 2) + round($sin_iva, 2) + $otro_tmp);
                //$aDataGrid_ret[$cont][$aLabelGrid_ret[4]] = round((($flete_tmp + round($con_iva, 2) + round($sin_iva, 2) + $otro_tmp - round(($subtotal * $descuento_general_tmp / 100), 2)) * ($cod_ret_porc / 100)), 2);  // valor retencion;
                $aDataGrid_ret[$cont][$aLabelGrid_ret[4]] = $ret_val; // valor retencion;
                $aDataGrid_ret[$cont][$aLabelGrid_ret[5]] = $fu->ObjetoHtml($cont . '_retencion'); //retencion;
                $aDataGrid_ret[$cont][$aLabelGrid_ret[6]] = $cuen_ret;            // cuenta;
                $aDataGrid_ret[$cont][$aLabelGrid_ret[7]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/delete_1.png"
																								onMouseOver="drc(\'Presione aqui para Eliminar\', \'Eliminar\'); return true;"
																								onMouseOut="javascript:nd(); return true;"
																								title = "Presione aqui para Eliminar"
																								style="cursor: hand !important; cursor: pointer !important;"
																								onclick="javascript:xajax_elimina_detalle_ret(' . $cont . ');"
																								alt="Eliminar"
																								align="bottom" />';
            }


            if (!empty($cod_ret_iva)) {
                $fu->AgregarCampoNumerico(($cont + 1) . '_retencion', 'Retencion|left', true, $secu_ret, 70, 10);
                $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[0]] = floatval($cont);
                $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[1]] = $cod_ret_iva;
                $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[2]] = $cod_ret_iva_porc;
                $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[3]] = round($total_iva, 2);
                $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[4]] = round((round($total_iva, 2) * ($cod_ret_iva_porc / 100)), 2);  // valor retencion;
                $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[5]] = $fu->ObjetoHtml(($cont + 1) . '_retencion'); //retencion;
                $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[6]] = $cuen_iva;            // cuenta;
                $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[7]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/delete_1.png"
																								onMouseOver="drc(\'Presione aqui para Eliminar\', \'Eliminar\'); return true;"
																								onMouseOut="javascript:nd(); return true;"
																								title = "Presione aqui para Eliminar"
																								style="cursor: hand !important; cursor: pointer !important;"
																								onclick="javascript:xajax_elimina_detalle_ret(' . ($cont + 1) . ');"
																								alt="Eliminar"
																								align="bottom" />';
            }
        } elseif ($contri == 'S') {
            //	$oReturn->alert('2');
            $cont = 0;

            if (!empty($cod_ret)) {
                $fu->AgregarCampoNumerico($cont . '_retencion', 'Retencion|left', true, $secu_ret, 70, 10);
                $aDataGrid_ret[$cont][$aLabelGrid_ret[0]] = floatval($cont);
                $aDataGrid_ret[$cont][$aLabelGrid_ret[1]] = $cod_ret;
                $aDataGrid_ret[$cont][$aLabelGrid_ret[2]] = $cod_ret_porc;
                $aDataGrid_ret[$cont][$aLabelGrid_ret[3]] = ($flete_tmp + round($con_iva, 2) + round($sin_iva, 2) + $otro_tmp);
                $aDataGrid_ret[$cont][$aLabelGrid_ret[4]] = round((($flete_tmp + round($con_iva, 2) + round($sin_iva, 2) + $otro_tmp - round(($subtotal * $descuento_general_tmp / 100), 2)) * ($cod_ret_porc / 100)), 2);  // valor retencion;
                $aDataGrid_ret[$cont][$aLabelGrid_ret[5]] = $fu->ObjetoHtml($cont . '_retencion'); //retencion;
                $aDataGrid_ret[$cont][$aLabelGrid_ret[6]] = $cuen_ret;            // cuenta;
                $aDataGrid_ret[$cont][$aLabelGrid_ret[7]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/delete_1.png"
																		onMouseOver="drc(\'Presione aqui para Eliminar\', \'Eliminar\'); return true;"
																		onMouseOut="javascript:nd(); return true;"
																		title = "Presione aqui para Eliminar"
																		style="cursor: hand !important; cursor: pointer !important;"
																		onclick="javascript:xajax_elimina_detalle_ret(' . $cont . ');"
																		alt="Eliminar"
																		align="bottom" />';
            }

            // EMPRESA ES CONTRI ESPECIAL
            $sql = "select  empr_tip_empr  from saeempr where empr_cod_empr = $idempresa ";
            $empr_contri = consulta_string($sql, 'empr_tip_empr', $oIfx, 'N');

            if ($empr_contri == 'S') {
                $sql = "select tret_porct,  tret_cta_cre, tret_cod 
								from saetret where 
								tret_cod_empr = $idempresa and 
								tret_contr_esp  = 'S'  ";
                if ($oIfx->Query($sql)) {
                    if ($oIfx->NumFilas() > 0) {
                        do {
                            $cod_ret_iva = $oIfx->f('tret_cod');
                            $cod_ret_iva_porc = $oIfx->f('tret_porct');
                            $cuen_iva = $oIfx->f('tret_cta_cre');
                        } while ($oIfx->SiguienteRegistro());
                    }
                }

                if (!empty($cod_ret_iva)) {
                    $fu->AgregarCampoNumerico(($cont + 1) . '_retencion', 'Retencion|left', true, $secu_ret, 70, 10);
                    $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[0]] = floatval($cont);
                    $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[1]] = $cod_ret_iva;
                    $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[2]] = $cod_ret_iva_porc;
                    $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[3]] = round($total_iva, 2);
                    $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[4]] = round((round($total_iva, 2) * ($cod_ret_iva_porc / 100)), 2);  // valor retencion;
                    $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[5]] = $fu->ObjetoHtml(($cont + 1) . '_retencion'); //retencion;
                    $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[6]] = $cuen_iva;            // cuenta;
                    $aDataGrid_ret[$cont + 1][$aLabelGrid_ret[7]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/delete_1.png"
																				onMouseOver="drc(\'Presione aqui para Eliminar\', \'Eliminar\'); return true;"
																				onMouseOut="javascript:nd(); return true;"
																				title = "Presione aqui para Eliminar"
																				style="cursor: hand !important; cursor: pointer !important;"
																				onclick="javascript:xajax_elimina_detalle_ret(' . ($cont + 1) . ');"
																				alt="Eliminar"
																				align="bottom" />';
                } // fin if
            } // fin s

        }
    } else {
        $sHtml = "";
    }

    $oReturn->assign("divTotal", "innerHTML", $sHtml);
    $oReturn->assign("total_fact_fp", "value", round(($total_fac + $total_iva + $total_otros + $ice_total), 2));
    $oReturn->assign("valor", "value", round(($total_fac + $total_iva + $total_otros + $ice_total), 2));
    return $oReturn;
}





// FORMA DE PAGO
// detalle de la forma de pago
function formulario_detalle_fp($sAccion = 'nuevo', $sucursal = '', $aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse();

    $usuario_informix = $_SESSION['U_USER_INFORMIX'];
    $idempresa = $_SESSION['U_EMPRESA'];
    $tipo = $aForm['tipo_fp_tmp'];
    $id_fp = $aForm['forma_pago_prove'];

    //tipo forma de pago
    $sql = "select count(*) as contador from saefpag where
                    fpag_cod_empr = $idempresa and
                    fpag_cod_sucu = $sucursal and
                    fpag_cod_fpag = '$id_fp' and
                    fpag_det_fpag = 'SI' ";
    $contador = consulta_string($sql, 'contador', $oIfx, 0);
    //        $oReturn->alert($contador);
    if ($contador > 0) {    // cheque al dia o remesa
        switch ($sAccion) {
            case 'nuevo':
                // Cabecera de Forma de Pago
                if ($tipo == 'CHE' || $tipo == 'S') {
                    $fu->AgregarCampoTexto('cuenta', 'No- Cuenta|left', true, '', 140, 180);
                    $fu->AgregarCampoTexto('numero_cheque', 'Numero Cheque|left', true, '', 140, 30);
                    $ifu->AgregarCampoTexto('banco', 'Banco|left', true, '', 140, 180);
                    $fu->AgregarCampoTexto('girador', 'Girador|left', true, '', 140, 180);
                } elseif ($tipo == 'TAR') {
                    $fu->AgregarCampoTexto('cuenta', 'No- Tarjeta|left', true, '', 140, 180);
                    $fu->AgregarCampoTexto('numero_cheque', 'Numero Voucher|left', true, '', 140, 30);
                    $ifu->AgregarCampoTexto('banco', 'Tarjeta Cliente|left', true, '', 140, 180);
                    $fu->AgregarCampoTexto('girador', 'Autorizacion|left', true, '', 140, 180);
                }
                break;
        }


        $sHtml .= '<fieldset style="border:#999999 1px solid; padding:2px; text-align:center; width:95%;">';
        $sHtml .= '<table align="center" cellpadding="0" cellspacing="2" width="100%" border="0">
                           <tr><th colspan="4" align="center" class="diagrama">DETALLE FORMAS DE PAGO ONLINE</th></tr>';
        $sHtml .= '<tr>
                                    <td class="labelFrm">' . $fu->ObjetoHtmlLBL('cuenta') . '</td>
                                    <td>' . $fu->ObjetoHtml('cuenta') . '</td>
                                     <td class="labelFrm">' . $fu->ObjetoHtmlLBL('numero_cheque') . '</td>
                                    <td>' . $fu->ObjetoHtml('numero_cheque') . '</td>
                           </tr>';
        $sHtml .= '<tr>
                                    <td class="labelFrm">' . $ifu->ObjetoHtmlLBL('banco') . '</td>
                                    <td>' . $ifu->ObjetoHtml('banco') . '</td>
                                    <td class="labelFrm">' . $fu->ObjetoHtmlLBL('girador') . '</td>
                                    <td>' . $fu->ObjetoHtml('girador') . '</td>
                           </tr>';
        $sHtml .= '<tr>
                                    <td colspan="4" align="center">
                                            <input type="button" value="A&ntilde;adir Detalle"
                                                    onClick="javascript:anadir_forma_pago()"
                                                    id="forma_pago" class="BotonFormulario"
                                                    onMouseOver="javascript:this.className=\'' . BotonFormularioActivo . '\';"
                                                    onMouseOut="javascript:this.className=\'' . BotonFormulario . '\';"
                                                    style="width:100px;  height:25px"/>
                                    </td>
                            </tr';


        $sHtml .= '</table></fieldset>';
        $oReturn->assign("divFormularioDetalleFP_DET", "innerHTML", $sHtml);
    } else {
        $oReturn->script('anadir_forma_pago(); ');
    }
    return $oReturn;
}

// ocultar detalle forma pago
function ocultar_detalle_fp()
{
    //Definicione
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oReturn = new xajaxResponse();
    $html = "";
    $oReturn->assign("divFormularioDetalleFP_DET", "innerHTML", $html);

    return $oReturn;
}

// grid de la forma de pago
function agrega_modifica_grid_fp($nTipo = 0, $secuencial = '', $aForm = '', $id = '')
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN, $DSN_Ifx;

    $oCnx = new Dbo();
    $oCnx->DSN = $DSN;
    $oCnx->Conectar();

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $aDataGrid_FP = $_SESSION['aDataGird_Pago'];

    $aLabelGrid = array('Id', 'Fecha', 'No. Dias', 'Fecha Final', 'Forma Pago', 'Porcentaje', 'Valor', 'Cuenta', 'Cheque', 'Banco', 'Girador', 'Eliminar');
    $oReturn = new xajaxResponse();

    $idempresa = $_SESSION['U_EMPRESA'];
    $sucursal = $aForm['sucursal'];

    // total de la pedido
    $total = $aForm['total_fact_fp'];
    $porcentaje = $aForm['porcentaje'];
    if (empty($porcentaje) || $porcentaje == 0) {
        $valor = $aForm['valor'];
        $porcentaje = round((($valor * 100) / $total), 2);
    } else {
        $valor = round((($total * $porcentaje) / 100), 2);
    }

    if ($nTipo == 0) {

        //GUARDA LOS DATOS DEL DETALLE
        $cont = count($aDataGrid_FP);

        $aDataGrid_FP[$cont][$aLabelGrid[0]] = floatval($cont);
        $aDataGrid_FP[$cont][$aLabelGrid[1]] = $aForm['fecha_inicio'];
        $aDataGrid_FP[$cont][$aLabelGrid[2]] = $aForm['dias_fp'];
        $aDataGrid_FP[$cont][$aLabelGrid[3]] = $aForm['fecha_final'];
        $aDataGrid_FP[$cont][$aLabelGrid[4]] = $aForm['forma_pago_prove'];
        $aDataGrid_FP[$cont][$aLabelGrid[5]] = $porcentaje;
        $aDataGrid_FP[$cont][$aLabelGrid[6]] = $valor;
        $aDataGrid_FP[$cont][$aLabelGrid[7]] = $aForm['cuenta'];
        $aDataGrid_FP[$cont][$aLabelGrid[8]] = $aForm['numero_cheque'];
        $aDataGrid_FP[$cont][$aLabelGrid[9]] = $aForm['banco'];
        $aDataGrid_FP[$cont][$aLabelGrid[10]] = $aForm['girador'];
        $aDataGrid_FP[$cont][$aLabelGrid[11]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/delete_1.png"
                                                            onMouseOver="drc(\'Presione aqui para Eliminar\', \'Eliminar\'); return true;"
                                                            onMouseOut="javascript:nd(); return true;"
                                                            title = "Presione aqui para Eliminar"
                                                            style="cursor: hand !important; cursor: pointer !important;"
                                                            onclick="javascript:xajax_elimina_detalle_fp(' . $cont . ');"
                                                            alt="Eliminar"
                                                            align="bottom" />';
    }
    $_SESSION['aDataGird_Pago'] = $aDataGrid_FP;
    $sHtml = mostrar_grid_fp();
    $oReturn->assign("divFormularioDetalle_FP", "innerHTML", $sHtml);

    $oReturn->script('totales_fp();');
    $oReturn->script('limpiar_fp();');
    return $oReturn;
}

function mostrar_grid_fp()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN, $DSN_Ifx;

    $oCnx = new Dbo();
    $oCnx->DSN = $DSN;
    $oCnx->Conectar();

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $idempresa = $_SESSION['U_EMPRESA'];
    $aDataGrid = $_SESSION['aDataGird_Pago'];
    $aLabelGrid = array('Id', 'Fecha', 'No. Dias', 'Fecha Final', 'Forma Pago', 'Porcentaje', 'Valor', 'Cuenta', 'Cheque', 'Banco', 'Girador', 'Eliminar');

    $cont = 0;
    foreach ($aDataGrid as $aValues) {
        $aux = 0;
        foreach ($aValues as $aVal) {
            if ($aux == 0)
                $aDatos[$cont][$aLabelGrid[$aux]] = $cont + 1;
            elseif ($aux == 2) {
                // dias
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
            } elseif ($aux == 4) {
                //forma de pago
                $sql = "select fpag_des_fpag from saefpag where
                                            fpag_cod_empr =  $idempresa and
                                            fpag_cod_fpag = '$aVal' ";
                $forma_pago = consulta_string($sql, 'fpag_des_fpag', $oIfx, '');
                $aDatos[$cont][$aLabelGrid[$aux]] = $forma_pago;
            } elseif ($aux == 5) {
                //porcentaje
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . ' %</div>';
            } elseif ($aux == 6) {
                // valor
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
            } elseif ($aux == 11)
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="center">
                                                                        <img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/delete_1.png"
                                                                        title = "Presione aqui para Eliminar"
                                                                        style="cursor: hand !important; cursor: pointer !important;"
                                                                        onclick="javascript:xajax_elimina_detalle_fp(' . $cont . ');"
                                                                        alt="Eliminar"
                                                                        align="bottom" />
                                                                    </div>';
            else
                $aDatos[$cont][$aLabelGrid[$aux]] = $aVal;
            $aux++;
        }
        $cont++;
    }
    return genera_grid_detalle_fp($aDatos, $aLabelGrid, '', 50);
}

function elimina_detalle_fp($id = null)
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oReturn = new xajaxResponse();

    $aLabelGrid = array('Id', 'Fecha', 'No. Dias', 'Fecha Final', 'Forma Pago', 'Porcentaje', 'Valor', 'Cuenta', 'Cheque', 'Banco', 'Girador', 'Eliminar');
    $aDataGrid = $_SESSION['aDataGird_Pago'];

    $contador = count($aDataGrid);

    if ($contador > 1) {

        unset($aDataGrid[$id]);
        $aDataGrid = array_values($aDataGrid);
        $cont = 0;
        foreach ($aDataGrid as $aValues) {
            $aux = 0;
            foreach ($aValues as $aVal) {
                if ($aux == 0)
                    $aDatos[$cont][$aLabelGrid[$aux]] = $cont;
                elseif ($aux == 11)
                    $aDatos[$cont][$aLabelGrid[$aux]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/pencil3.png"
                                                                                                title = "Presione aqui para Modificar"
                                                                                                style="cursor: hand !important; cursor: pointer !important;"
                                                                                                alt="Modificar"
                                                                                                align="bottom" />';
                else
                    $aDatos[$cont][$aLabelGrid[$aux]] = $aVal;
                $aux++;
            }
            $cont++;
        }
        $_SESSION['aDataGird_Pago'] = $aDatos;

        $sHtml = mostrar_grid_fp();
    } else {
        unset($aDataGrid[0]);
        $_SESSION['aDataGird_Pago'] = $aDatos;
        $sHtml = "";
        $sHtml = $mostrar_prueba_grid;
    }


    $oReturn->assign("divFormularioDetalle_FP", "innerHTML", $sHtml);
    $oReturn->script('totales_fp();');
    return $oReturn;
}

// guardar formas de pago
function guardar_forma_pago($sucursal = '', $cliente = '', $secuencial = '', $aForm = '')
{
    //Definiciones
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo;
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $oReturn = new xajaxResponse();

    /*     * ****************************************** */
    /* E M P R E S A     I N F O R M I X         */
    /*     * ****************************************** */
    $empresa = $_SESSION['U_EMPRESA'];
    //Cambia de minusculas a mayusculas
    $nombre_empresa = strtr(strtoupper($empresa), "���������������������������", "���������������������������");
    //Obtener el id de la empresa desde Informix
    $sql = 'SELECT * FROM SAEEMPR WHERE EMPR_COD_EMPR = ?';
    $data = array($empresa);
    if ($oIfx->Query($sql, $data)) {
        do {
            $idempresa = $oIfx->f('empr_cod_empr');
        } while ($oIfx->SiguienteRegistro());
    }
    $oIfx->Free();

    $aDataGrid = $_SESSION['aDataGird_Pago'];
    $contdata = count($aDataGrid);
    // total de la pedido
    $sql = "select sum(COALESCE(pedf_tot_fact,0) + COALESCE(pedf_iva,0) + COALESCE( pedf_fle_fact,0) +
                                    COALESCE( pedf_otr_fact,0) -  COALESCE( pedf_dsg_valo,0) + COALESCE(pedf_ice,0) ) as total from saepedf  where
                                    pedf_num_preimp = '$secuencial' and
                                    pedf_cod_empr = $idempresa and
                                    pedf_cod_sucu = $sucursal ";
    $total = consulta_string($sql, 'total', $oIfx, 0);
    $fp_total = $aForm['total_fp'];

    if ($contdata > 0) {
        if ($total == $fp_total) {

            // TRANSACCIONALIDAD
            try {
                // commit
                $oIfx->QueryT('BEGIN WORK;');

                // transaccion de informix
                // fecha ifx
                $fecha_servidor = date("m-d-Y");
                // A N I O
                $anio = date("Y");
                $fecha_ejer = $anio . '12-31';
                // E J E R C I C I O     D E     I N F O R M I X
                $sql = "select ejer_cod_ejer from saeejer where ejer_fec_finl = '$fecha_ejer' and ejer_cod_empr = $idempresa ";
                $idejer = consulta($sql, 'ejer_cod_ejer', $oIfx);

                // M E S
                $idprdo = date("m");

                // U S U A R I O       I N F O  R M I X
                $usuario_informix = $_SESSION['U_USER_INFORMIX'];

                // O B T E N E R     M O N E D A      D E S D E      I N F O R M I X
                $sql_moneda = "select pcon_mon_base from saepcon where pcon_cod_empr = $idempresa ";
                $moneda = consulta($sql_moneda, 'pcon_mon_base', $oIfx);

                // O B T E N E R     T C A M B I O      D E S D E      I N F O R M I X
                $sql_tcambio = "select tcam_fec_tcam, tcam_cod_tcam, tcam_val_tcam from saetcam where
                                                    tcam_cod_mone = $moneda and
                                                    mone_cod_empr = $idempresa and
                                                    tcam_fec_tcam = (select max(tcam_fec_tcam) from saetcam where
                                                                            tcam_cod_mone = $moneda and
                                                                            tcam_fec_tcam <= '$fecha_servidor' and
                                                                            mone_cod_empr = $idempresa) ";
                if ($oIfx->Query($sql_tcambio)) {
                    if ($oIfx->NumFilas() > 0) {
                        $tcambio = $oIfx->f('tcam_cod_tcam');
                        $val_tcambio = $oIfx->f('tcam_val_tcam');
                    } else {
                        $tcambio = 0;
                        $val_tcambio = 0;
                    }
                }
                $oIfx->Free();

                // codigo d pedido
                $sql = "select pedf_cod_pedf from saepedf where
                                        pedf_num_preimp = '$secuencial' and
                                        pedf_cod_empr = $idempresa and
                                        pedf_cod_sucu = $sucursal";
                $pedf_cod_pedf = consulta_string($sql, 'pedf_cod_pedf', $oIfx, 0);

                // transaccion
                $sql = "SELECT saepara.para_mail_cupo,saepara.para_act_impr,saepara.para_nom_impr,
                                        saepara.para_for_desp,saepara.para_fact_preimp,  para_fac_cxc FROM saepara
                                        WHERE ( saepara.para_cod_empr = $idempresa ) AND
                                        ( saepara.para_cod_sucu = $sucursal ) ";
                $tran = consulta($sql, 'para_fac_cxc', $oIfx);

                // ESTAB
                $sql_estab = "SELECT aufa_nse_fact,aufa_nau_fact,aufa_ffi_fact FROM saeaufa
                                            WHERE aufa_cod_empr = $idempresa and
                                            aufa_cod_sucu = $sucursal and
                                            aufa_est_fact = 'A' and
                                            aufa_ffi_fact >= '$fecha_servidor' and
                                            aufa_fin_fact <= '$fecha_servidor' ";
                $estab = consulta($sql_estab, 'aufa_nse_fact', $oIfx);

                // ingreso a la saefxpx

                foreach ($aDataGrid as $aValues) {
                    $aux = 0;
                    foreach ($aValues as $aVal) {
                        if ($aux == 0) {
                            $id = $aVal;
                        } elseif ($aux == 1) {
                            $fecha_inicio = $aVal;      //fecha inicio
                        } elseif ($aux == 2) {
                            $dias = $aVal;                              //no.- dias
                        } elseif ($aux == 3) {
                            $fecha_fin = $aVal;         // fecha final
                        } elseif ($aux == 4) {
                            $forma_pago = $aVal;                        // forma d pago
                        } elseif ($aux == 5) {
                            $porcentaje = $aVal;                        // porcentaje
                        } elseif ($aux == 6) {
                            $valor = $aVal;                             // valor
                        } elseif ($aux == 7) {
                            $cuenta = $aVal;                            // cuenta banco
                        } elseif ($aux == 8) {
                            $cheque = $aVal;                            // numero de cheue
                        } elseif ($aux == 9) {
                            $banco = $aVal;                             // banco
                        } elseif ($aux == 10) {
                            $girador = $aVal;                           // girador
                            // cuenta de la fp
                            $sql = "select fpag_cod_cuen, fpag_cod_clpv, fpag_cot_fpag from saefpag where
                                                                fpag_cod_empr = $idempresa and
                                                                fpag_cod_sucu = $sucursal and
                                                                fpag_cod_fpag = $forma_pago ";
                            $cuen_cod_cuen = consulta_string($sql, 'fpag_cod_cuen', $oIfx, '');
                            $clpv_tar = consulta_string($sql, 'fpag_cod_clpv', $oIfx, '');

                            // fpag cot
                            $fpag_cot = consulta_string($sql, 'fpag_cot_fpag', $oIfx, '');

                            // saefxpf
                            $sql_fxfp = "insert into saepxfp(pxfp_cod_pxfp,    pxfp_cod_sucu,     pxfp_cod_empr,
                                                                                     pxfp_cod_ejer,    pxfp_num_prdo,     pxfp_cod_fact,
                                                                                     pxfp_cod_fpag,    pxfp_cod_cuen,     pxfp_num_dias,
                                                                                     pxfp_poc_pxfp,    pxfp_val_pxfp,     pxfp_fec_pxfp,
                                                                                     pxfp_fec_fin,     pxfp_cot_fpag,     pxfp_num_cuen ,
                                                                                     pxfp_num_cheq ,   pxfp_nom_banc ,    pxfp_nom_gira )
                                                                             values( ($id+1),          $sucursal,         $empresa,
                                                                                      $idejer,         $idprdo,           $pedf_cod_pedf,
                                                                                      $forma_pago,     '$cuen_cod_cuen',  $dias,
                                                                                      $porcentaje,     $valor,            '$fecha_inicio',
                                                                                      '$fecha_fin',    '$fpag_cot',       '$cuenta',
                                                                                      '$cheque',       '$banco',          '$girador'  ) ";

                            $oIfx->QueryT($sql_fxfp);
                            //                                                    $oReturn->alert($sql_fxfp);
                        }
                        $aux++;
                    }
                }




                $oIfx->QueryT('COMMIT WORK;');
                $oReturn->alert('Forma de Pago Ingresado Correctamente');
                $oReturn->script('parent.cerrar_ventana();');
                $oReturn->script('parent.vista_previa();');
            } catch (Exception $e) {
                // rollback
                $oIfx->QueryT('ROLLBACK WORK;');
                $oReturn->alert($e->getMessage());
            }
        } else {
            $oReturn->alert('El valor de la Forma de Pago no coincide con el Total de la Factura....');
        }
    } else {
        $oReturn->alert('!!!!....Por favor ingrese la Forma de Pago....!!!!!');
    }

    return $oReturn;
}

function total_grid_fp()
{
    //Definiciones
    global $DSN_Ifx, $DSN;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $oReturn = new xajaxResponse();

    $aDataGrid = $_SESSION['aDataGird_Pago'];
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];

    $contdata = count($aDataGrid);

    if ($contdata > 0) {

        $total_iva = 0;
        $total_sin_iva = 0;
        $pedf_iva = 0;
        $total = 0;
        $con_iva = 0;
        $sin_iva = 0;
        foreach ($aDataGrid as $aValues) {
            $aux = 0;
            foreach ($aValues as $aVal) {
                if ($aux == 6) {      //VALOR
                    $total_fp += $aVal;
                }
                $aux++;
            }
        }



        $sql_cod_mone = "SELECT mone_cod_mone from 
                                saemone, saepara
                                where para_mon_def = mone_cod_mone
                                and mone_cod_empr = $idempresa
                                and para_cod_empr = $idempresa
                                and para_cod_sucu = $idsucursal
                                ";
        $cod_mone_princ = consulta_string_func($sql_cod_mone, 'mone_cod_mone', $oIfx, '');



        $sql_mone_principal = "SELECT * from saemone
                                    where mone_cod_empr = $idempresa
                                    and mone_cod_mone = $cod_mone_princ";

        if ($oIfx->Query($sql_mone_principal)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    //$moneda_principal = $oIfx->f('mone_sgl_mone') . '' . $oIfx->f('mone_smb_mene');
                    //$moneda_principal_ad = $oIfx->f('mone_sgl_mone') . '' . $oIfx->f('mone_smb_mene');
                    $moneda_principal = $oIfx->f('mone_sgl_mone');
                    $moneda_principal_ad = $oIfx->f('mone_sgl_mone');
                } while ($oIfx->SiguienteRegistro());
            }
        }





        // form total
        $fu->AgregarCampoTextoRojo('total_fp', 'Total|left', false, 0, 70, 2);
        $fu->AgregarComandoAlPonerEnfoque('total_fp', 'this.blur()');
        $fu->cCampos["total_fp"]->xValor = $total_fp;

        $sHtml .= '<fieldset style="border:#FFFFFF 1px solid; padding:2px; text-align:center; width:65%;">';
        $sHtml .= '<table align="center" cellpadding="0" cellspacing="2" width="50%" border="0">
                            <tr>
                                            <td  class="total_fact"  bgcolor="#EBF0FA" height="25">TOTAL FP: ' . $moneda_principal_ad . '</td>
                                            <td  bgcolor="#EBEBEB" class="total_fact" align="right">' . $fu->ObjetoHtml('total_fp') . '</td>
                            </tr>';
        $sHtml .= '</table></fieldset>';
    } else {
        $sHtml = "";
    }

    $oReturn->assign("divTotalFP", "innerHTML", $sHtml);
    return $oReturn;
}

function num_digito($aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    //Definiciones
    $oReturn = new xajaxResponse();
    $idempresa = $_SESSION['U_EMPRESA'];

    // VARIABLES
    $form = $aForm['factura'];
    $serie = $aForm['serie'];
    $tran = $aForm['tran'];
    $cliente = $aForm['cliente'];
    $tipo_factura = $aForm['tipo_factura'];

    if ($tipo_factura == 1) {
        // electronica
        $pais_cero  = $_SESSION['U_PAIS_CERO_ELE'];
        $num_digito = $_SESSION['U_PAIS_DIG_FACE'];
    } else {
        // preimpreso
        $pais_cero  = $_SESSION['U_PAIS_CERO_PRE'];
        $num_digito = $_SESSION['U_PAIS_DIG_FACP'];
    }

    if ($pais_cero == 'S') {
        $len = strlen($form);
        $ceros = cero_mas('0', abs($num_digito - $len));
        $valor = $ceros . $form;
    } else {
        $valor = $form;
    }
    // CONTROL SI EXISTE ESA FACTURA
    $sql = "select count(*) as cont from saeminv where
                    minv_cod_tran   = '$tran' and
                    minv_cod_clpv   = $cliente  and
                    minv_cod_empr   = $idempresa and
                    minv_fac_prov   = '$valor'  and
                    minv_ser_docu   = '$serie' and
                    minv_est_minv  <> '0' ";
    $cont = consulta_string($sql, 'cont', $oIfx, 0);
    if ($cont > 0) {
        $oReturn->alert('Ya ingreso esta factura en este Movimiento..');
        $valor = '';
    }
    $oReturn->assign('factura', "value", $valor);
    return $oReturn;
}



// U T I L I D A D E S
// tipo forma d pago
function tipo_fp($aForm = '')
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN_Ifx;

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();
    $idempresa = $_SESSION['U_EMPRESA'];
    $sucursal = $aForm['sucursal'];
    $forma_pago = $aForm['forma_pago_prove'];
    $aDataGrid = $_SESSION['aDataGird_Pago'];

    $total = $aForm['total_fact_fp'];
    if (count($aDataGrid) > 0) {
        $val_pago = 0;
        foreach ($aDataGrid as $aValues) {
            $aux = 0;
            foreach ($aValues as $aVal) {
                if ($aux == 6) {
                    $val_pago += $aVal;
                }
                $aux++;
            }
        }
        $valor = $total - $val_pago;
    } else {
        $valor = $total;
    }

    // tipo forma de pago
    $sql = "SELECT saefpag.fpag_cod_fpag, saefpag.fpag_des_fpag, FPAG_COT_FPAG, fpag_sig_fpag FROM saefpag  WHERE
                    saefpag.fpag_cod_empr = $idempresa and
                    saefpag.fpag_cod_fpag = $forma_pago ";
    $tipo = consulta_string($sql, 'fpag_sig_fpag', $oIfx, '');
    if ($tipo == 'CRE') {
        $dias  = $aForm['plazo'];
        $fecha = $aForm['fecha_pedido'];

        $fecha_ven = date("Y-m-d", strtotime($fecha . "+ " . $dias . " days"));

        $oReturn->assign("dias_fp", "value", $dias);
        $oReturn->assign("fecha_final", "value", $fecha_ven);
        $oReturn->assign("fecha_inicio", "value", $fecha);
        $oReturn->assign("fecha_entrega", "value", $fecha_ven);
    } else {
        $oReturn->assign("fecha_final", "value", '2014/02/20');
        $oReturn->assign("dias_fp", "value", 0);
    }
    $oReturn->assign("tipo_fp_tmp", "value", $tipo);
    $oReturn->assign("valor", "value", $valor);
    return $oReturn;
}

function calculo_fecha_fp($aForm = '')
{

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $oReturn    = new xajaxResponse();
    $fecha      = $aForm['fecha_inicio'];
    $dias       = $aForm['dias_fp'];
    $fecha_ven  = sumar_dias_func($fecha, $dias);
    list($a, $b, $c) = explode('/', $fecha_ven);
    $fecha_ven = $a . '-' . $b . '-' . $c;

    $oReturn->script(('fecha_final_rs(\'' . $fecha_ven . '\' )'));
    return $oReturn;
}

function fecha_informix($fecha)
{
    $m = substr($fecha, 5, 2);
    $y = substr($fecha, 0, 4);
    $d = substr($fecha, 8, 2);

    return ($m . '/' . $d . '/' . $y);
}

function fecha_mysql($fecha)
{
    $fecha_array = explode('/', $fecha);
    $m = $fecha_array[0];
    $y = $fecha_array[2];
    $d = $fecha_array[1];

    return ($d . '/' . $m . '/' . $y);
}

function getDiasMes($mes, $anio)
{
    if (is_callable("cal_days_in_month")) {
        return cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
    } else {
        //Lo hacemos a mi manera. 
        return date("d", mktime(0, 0, 0, $mes + 1, 0, $anio));
    }
}

function fecha_sri($fecha)
{
    $fecha_array = explode('/', $fecha);
    $m = $fecha_array[0];
    $y = $fecha_array[2];
    $d = $fecha_array[1];

    return ($d . '' . $m . '' . $y);
}

function restaFechas($dFecIni, $dFecFin)
{
    $dFecIni = str_replace("-", "", $dFecIni);
    $dFecIni = str_replace("/", "", $dFecIni);
    $dFecFin = str_replace("-", "", $dFecFin);
    $dFecFin = str_replace("/", "", $dFecFin);

    ereg("([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecIni, $aFecIni);

    ereg("([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecFin, $aFecFin);

    $date1 = mktime(0, 0, 0, $aFecIni[2], $aFecIni[1], $aFecIni[3]);
    $date2 = mktime(0, 0, 0, $aFecFin[2], $aFecFin[1], $aFecFin[3]);

    return round(($date2 - $date1) / (60 * 60 * 24));
}

function consulta($sql, $campo, $Conexion)
{

    $total_mes_stock = 0;
    if ($Conexion->Query($sql)) {
        if ($Conexion->NumFilas() > 0) {
            $total_mes_stock = $Conexion->f($campo);
            if (empty($total_mes_stock)) {
                $total_mes_stock = 0;
            }
        } else {
            $total_mes_stock = 0;
        }
    }
    $Conexion->Free();
    //$Conexion->Desconectar();

    return $total_mes_stock;
}

function cargar_lista_correo($aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];

    $oReturn = new xajaxResponse();

    $cliente = $aForm['cliente'];
    $sql = "select emai_cod_emai, emai_ema_emai from saeemai where 
                                            emai_cod_empr=$idempresa and emai_cod_clpv = '$cliente'";

    // $oReturn->alert($sql);
    $i = 1;
    if ($oIfx->Query($sql)) {
        $oReturn->script('eliminar_lista_correo();');
        if ($oIfx->NumFilas() > 0) {
            $correo_id = $oIfx->f('emai_cod_emai');
            do {
                $oReturn->script(('anadir_elemento_correo(' . $i++ . ',\'' . $oIfx->f('emai_cod_emai') . '\', \'' . $oIfx->f('emai_ema_emai') . '\' )'));
            } while ($oIfx->SiguienteRegistro());
            $oReturn->assign("correo_prove", "value", $correo_id);
        }
    }

    // 



    return $oReturn;
}

function digitoVerificador($cadena)
{
    //$cadena = "040820140117914132530011001001000000063272775261";
    $pivote = 7;

    $longitudCadena = strlen($cadena);
    for ($i = 0; $i < $longitudCadena; $i++) {
        if ($pivote == 1)
            $pivote = 7;
        $caracter = substr($cadena, $i, 1);
        $temporal = $caracter * $pivote;
        $pivote--;
        $cantidadTotal += $temporal;
    }

    $div = $cantidadTotal % 11;
    $digitoVerificador = 11 - $div;

    if ($digitoVerificador == 10)
        $digitoVerificador = 1;
    if ($digitoVerificador == 11)
        $digitoVerificador = 0;

    return $digitoVerificador;
}


//TIPO DE FACTURA
function tipo_factura($aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo;
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse();

    $idempresa  = $aForm['empresa'];
    $idsucursal = $aForm['sucursal'];

    $tipo_factura = $aForm['tipo_factura'];
    $fecha_emision = $aForm['fecha_emision'];
    $cliente = $aForm['cliente'];
    $sec_automatico = $aForm['sec_automatico'];

    if ($sec_automatico == 'S') {
        $sql_sec_auto = "SELECT secuencial_factura from comercial.parametro_inv where empr_cod_empr = $idempresa";
        $secuencial_fcompra = consulta_string_func($sql_sec_auto, 'secuencial_factura', $oIfx, '') + 1;
        $secuencial_fcompra = str_pad($secuencial_fcompra, 9, '0', STR_PAD_LEFT);
        $digitos_serie = $_SESSION['U_PAIS_DIG_SERP'];
        $serie_prove = str_pad(9, $digitos_serie, '9', STR_PAD_LEFT);
        $serie_prove_ad = $serie_prove;
    } else {
        $secuencial_fcompra = '';
    }

    if ($cliente != '') {

        if ($tipo_factura == 1) {
            //FACTURA ELECTRONICA
            $anio = date("Y");

            $ifu->AgregarCampoNumerico('serie_prove', 'Serie|left', true, $serie_prove, 50, 100);
            $ifu->AgregarComandoAlEscribir('serie_prove', "validaSerie('escribir');");
            $ifu->AgregarComandoAlQuitarEnfoque('serie_prove', "validaSerie('enfoque');");

            $ifu->AgregarCampoNumerico('auto_prove', 'Auto.|left', true, '', 240, 100);
            $ifu->AgregarComandoAlEscribir('auto_prove', "validaAutorizacion('electronica','escribir');");
            $ifu->AgregarComandoAlQuitarEnfoque('auto_prove', "validaAutorizacion('electronica','enfoque');");

            $ifu->AgregarCampoNumerico('factura', 'Factura|left', true, '', 80, 100);
            $ifu->AgregarComandoAlEscribir('factura', "validaFactura('electronica','escribir');");
            $ifu->AgregarComandoAlQuitarEnfoque('factura', "validaFactura('electronica','enfoque');");

            $ifu->AgregarCampoNumerico('clave_acceso', 'Clave A.|left', true, '', 300, 49);

            //selecciona ambiente de sucursal
            $sql = "select sucu_tip_ambi from saesucu where sucu_cod_empr = $idempresa and sucu_cod_sucu = $idsucursal";
            $sucu_tip_ambi = consulta_string($sql, 'sucu_tip_ambi', $oIfx, 2);

            if ($sucu_tip_ambi == 1) {
                $op = 'N';
            } else {
                $op = 'S';
            }

            $ifu->AgregarCampoSi_No('ambiente_sri', 'Produccion|left', 'S');

            $evento  = "validaFactura('electronica','escribir'); ";
            $evento0 = "validaFactura('electronica','enfoque'); ";


            $pais_cero_ele = $_SESSION['U_PAIS_CERO_ELE'];
            if ($pais_cero_ele == 'S') {
                $evento2 = "validaAutorizacion('electronica','escribir');";
                $evento22 = "validaAutorizacion('electronica','enfoque');";
                $evento1 = "validaSerie('escribir',1); ";
                $evento11 = "validaSerie('enfoque', 1); ";
            }

            $sHtml .=    '<tr>
							<td>Produccion' . $ifu->ObjetoHtml('ambiente_sri') . '</td>
							<td>' . $ifu->ObjetoHtmlLBL('clave_acceso') . '</td>
							<td>' . $ifu->ObjetoHtml('clave_acceso') . '
								<input type="button" value="Generar"
								onClick="javascript:clave_acceso_sri()"
								class="myButton_BT"
								style="width:80px; height: 20px;"/>
								&nbsp;&nbsp;
								<a href="#" onClick="javascript:redireccionar()" style="color: blue;">Entidad Fiscal</a>
							</td>
							<td>' . $ifu->ObjetoHtmlLBL('serie_prove') . '</td>
							<td>' . $ifu->ObjetoHtml('serie_prove') . '</td>
							<td>' . $ifu->ObjetoHtmlLBL('factura') . '</td>
							<td>' . $ifu->ObjetoHtml('factura') . '</td>
							<td>' . $ifu->ObjetoHtmlLBL('auto_prove') . '</td>
							<td>' . $ifu->ObjetoHtml('auto_prove') . '</td>
						</tr>';

            $sHtml =    '<div class="col-md-12">
                            <div class="form-row">
                                <div class="col-md-2" style="display:none">
                                    <label for="ambiente_sri">* Produccion:</label>
                                    <div>' . $ifu->ObjetoHtml('ambiente_sri') . '</div>
                                </div>
                                <div class="col-md-2" style="display:none">
                                    <label for="clave_acceso">* Clave Acceso:</label>							    
                                    <input type="text" class="form-control input-sm" id="clave_acceso" name="clave_acceso" style="text-align:right"/>
                                </div>
                                <div class="col-md-2" style="display:none">
                                    <div id ="imagen1" class="btn btn-primary btn-sm" onclick="clave_acceso_sri();">
                                            <span class="glyphicon glyphicon-cog"></span>
                                            Generar
                                    </div>				    
                                    <div><a href="#" onClick="javascript:redireccionar()" style="color: blue;">Entidad Fiscal</a></div>
                                </div>
                                <div class="col-md-4">
                                    <label for="serie_prove">* Serie:</label>							    
                                    <input type="text" class="form-control input-sm" id="serie_prove" name="serie_prove" style="text-align:right;" 
                                    value="' . $serie_prove . '"
                                    onkeyup="' . $evento1 . '" onchange="' . $evento11 . '" />
                                </div>
                                <div class="col-md-4">
                                    <label for="factura">* Factura:</label>		
                                    <input type="text" class="form-control input-sm" id="factura" name="factura" style="text-align:right;" 
                                    value="' . $secuencial_fcompra . '"
                                    onkeyup="' . $evento . '" onchange="' . $evento0 . '" />
                                </div>
                                <div class="col-md-4">
                                    <label for="auto_prove">* Autorizacion:</label>							    
                                    <input type="text" class="form-control input-sm" id="auto_prove" name="auto_prove" value="' . $auto_prove . '" 
                                    style="text-align:right;" onkeyup="' . $evento2 . '" onchange="' . $evento22 . '" />
                                </div>
                            </div>
                         </div>';


            $oReturn->assign('divFactura', 'innerHTML', $sHtml);
            $oReturn->assign('clave_acceso', 'focus()', '');
        } elseif ($tipo_factura == 2) {

            $tran_cod = $aForm['tran'];

            $sql = "select defi_coa_sn  from saedefi where
					defi_cod_empr = $idempresa and
					defi_cod_sucu = $idsucursal and
					defi_cod_tran = '$tran_cod' ";
            $defi_coa_sn = consulta_string_func($sql, 'defi_coa_sn', $oIfx, 'N');

            if ($defi_coa_sn == 'S') {
                $sql = "select  max(coa_fec_vali) as coa_fec_vali, coa_aut_usua, coa_seri_docu, coa_fact_ini, coa_fact_fin, coa_aut_impr
											from saecoa where
											clpv_cod_empr = $idempresa and
											clpv_cod_clpv = $cliente group by coa_fec_vali,2,3,4,5 ,6";
                if ($oIfx->Query($sql)) {
                    if ($oIfx->NumFilas() > 0) {
                        do {
                            $fec_cadu_prove = fecha_mysql_func2($oIfx->f('coa_fec_vali'));
                            $auto_prove     = $oIfx->f('coa_aut_usua');
                            $serie_prove    = $oIfx->f('coa_seri_docu');
                            $ini_prove      = ($oIfx->f('coa_fact_ini'));
                            $fin_prove      = ($oIfx->f('coa_fact_fin'));
                            $a_prove        = $oIfx->f('coa_aut_impr');
                        } while ($oIfx->SiguienteRegistro());
                    }
                }
            } else {
                $auto_prove   = '';
                $serie_prove  = '000000';
                $a_prove      = '9999999999';
                $fec_cadu_prove = '2029-12-31';
                $ini_prove  = '1';
                $fin_prove  = '99999999999';
            }

            if ($sec_automatico == 'S') {
                if ($serie_prove == '000000') {
                    $serie_prove = $serie_prove_ad;
                }
            }


            $sql = "select empr_iva_empr, empr_cod_pais  from saeempr where empr_cod_empr = $idempresa ";
            $empr_cod_pais = round(consulta_string_func($sql, 'empr_cod_pais', $oIfx, 0));

            $sql = "select pais_fact_csn   from saepais where pais_cod_pais = $empr_cod_pais ";
            $pais_fact_csn = consulta_string_func($sql, 'pais_fact_csn', $oIfx, 'N');


            $ifu->AgregarCampoTexto('auto_prove', 'No Autorizacion|left', true, $auto_prove, 300, 100);
            $ifu->AgregarComandoAlEscribir('auto_prove', "validaAutorizacion('impresa','escribir');");
            $ifu->AgregarComandoAlQuitarEnfoque('auto_prove', "validaAutorizacion('impresa','enfoque');");


            $ifu->AgregarCampoTexto('serie_prove', 'Serie|left', true, $serie_prove, 50, 100);
            $ifu->AgregarComandoAlEscribir('serie_prove', "validaSerie('escribir');");
            $ifu->AgregarComandoAlQuitarEnfoque('serie_prove', "validaSerie('enfoque');");

            $ifu->AgregarCampoFecha('fecha_validez', 'Fecha|left', true, $fec_cadu_prove);
            //$ifu->AgregarCampoTexto('fecha_validez', 'Fecha Validez|left', true, $fec_cadu_prove, 70, 100);
            $ifu->AgregarCampoTexto('factura_inicio', 'Factura Inicio|left', true, $ini_prove, 70, 100);
            $ifu->AgregarCampoTexto('factura_fin', 'Factura Fin|left', true, $fin_prove, 70, 100);

            $ifu->AgregarCampoTexto('factura', 'Factura|left', true, '', 180, 100);
            $ifu->AgregarComandoAlEscribir('factura', "validaFactura('impresa','escribir');");
            $ifu->AgregarComandoAlQuitarEnfoque('factura', "validaFactura('impresa','enfoque');");

            $ifu->AgregarCampoTexto('dgui', 'N.- NCF Documento|left', false, 'B01', 200, 30);

            $evento  = "validaFactura('impresa','escribir'); ";
            $evento0 = "validaFactura('impresa','enfoque'); ";

            $pais_cero_pre = $_SESSION['U_PAIS_CERO_PRE'];
            if ($pais_cero_pre == 'S') {
                $evento2 = "validaAutorizacion('impresa','escribir');";
                $evento22 = "validaAutorizacion('impresa','enfoque');";
                $evento1 = "validaSerie('escribir',2); ";
                $evento11 = "validaSerie('enfoque',2); ";
            }


            $sHtml .= '<tr>
                            <td>' . $ifu->ObjetoHtmlLBL('serie_prove') . '</td>
                            <td>
                                <input type="text" class="form-control input-sm" id="serie_prove" name="serie_prove" value="' . $serie_prove . '" style="width:70px; text-align:right ; height:25px"/>
                            </td>
                            <td>' . $ifu->ObjetoHtmlLBL('auto_prove') . '</td>
                            <td>
                                <input type="text" class="form-control input-sm" id="auto_prove" name="auto_prove" value="' . $auto_prove . '" style="width:120px; text-align:right; height:25px"/>
                            </td>                            
                            <td>' . $ifu->ObjetoHtmlLBL('fecha_validez') . '</td>
                            <td> <input type="date" name="fecha_validez" step="1" value="' . $fec_cadu_prove . '">   </td>
                            <td>' . $ifu->ObjetoHtmlLBL('factura_inicio') . '</td>
                            <td>
                                <input type="text" class="form-control input-sm" id="factura_inicio" name="factura_inicio" value="' . $ini_prove . '" style="width:60px; text-align:right; height:25px"/>
                            </td>
                            <td>' . $ifu->ObjetoHtmlLBL('factura_fin') . '</td>
                            <td>
                                <input type="text" class="form-control input-sm" id="factura_fin" name="factura_fin" value="' . $fin_prove . '" style="width:70px; text-align:right; height:25px"/>
                            </td>
                            <td>' . $ifu->ObjetoHtmlLBL('factura') . '</td>
                            <td>
                                <input type="text" class="form-control input-sm" id="factura" name="factura" 
                                        style="width:100px; text-align:right ; height:25px; "  onkeyup="' . $evento . '"/>
                            </td>';

            $sHtml =    '<div class="col-md-12">
                            <div class="form-row">
                                <div class="col-md-2">
                                    <label for="serie_prove">* Serie:</label>
                                    <input type="text" class="form-control input-sm" id="serie_prove" name="serie_prove" value="' . $serie_prove . '" 
                                        style="text-align:right;" onkeyup="' . $evento1 . '" onchange="' . $evento11 . '" />
                                </div>
                                
                                <div style="display:none">
                                    <label for="auto_prove">* Fecha:</label>							    
                                    <input type="date" name="fecha_validez" step="1" value="' . $fec_cadu_prove . '">
                                </div>
                                <div style="display:none">
                                    <label for="factura_inicio">F.Ini:</label>							    
                                    <input type="text" class="form-control input-sm" id="factura_inicio" name="factura_inicio" value="' . $ini_prove . '" style="text-align:right;"/>
                                </div>
                                <div style="display:none">
                                    <label for="factura_fin">F.Fin:</label>							    
                                    <input type="text" class="form-control input-sm" id="factura_fin" name="factura_fin" value="' . $fin_prove . '" style="text-align:right;"/>
                                </div>
                                <div class="col-md-2">
                                    <label for="factura">* Factura:</label>							    
                                    <input type="text" class="form-control input-sm" id="factura" name="factura" style="text-align:right ;"  
                                    value="' . $secuencial_fcompra . '"
                                    onkeyup="' . $evento . '" onchange="' . $evento0 . '" />
                                </div>
								<div class="col-md-3">
                                    <label for="auto_prove">* Autorizacion:</label>							    
                                    <input type="text" class="form-control input-sm" id="auto_prove" name="auto_prove" value="' . $auto_prove . '" 
                                    style="text-align:right;"  onkeyup="' . $evento2 . '" onchange="' . $evento22 . '" />
                                </div>
                            ';

            if ($pais_fact_csn == 'S') {
                $sHtml .= '     <div class="col-md-4">
                                    <label for="dgui">' . $ifu->ObjetoHtmlLBL('dgui') . '</label>		
                                    <input type="text" class="form-control input-sm" id="dgui" name="dgui" style="text-align:right ;" value="B01" />
                                </div>';
            }

            $sHtml .= '     
                                <div class="col-md-4">
                                    <label for="factura">Fecha Comprobante:</label>							    
                                    <input type="date" class="form-control input-sm" id="minv_fec_ncf" name="minv_fec_ncf" value="' . date("Y-m-d") . '"/>
                                </div>
                            </div>
                        </div>';

            $oReturn->assign('divFactura', 'innerHTML', $sHtml);
        } elseif ($tipo_factura == '') {
            $oReturn->assign('divFactura', 'innerHTML', '');
        }
    }


    return $oReturn;
}

function validar_factura($aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo;
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse();

    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];

    $tipo_factura   = $aForm['tipo_factura'];
    $cliente        = $aForm['cliente'];
    $factura_inicio = $aForm['factura_inicio'];
    $factura_fin    = $aForm['factura_fin'];
    $factura        = $aForm['factura'];

    if ($factura >= $factura_inicio && $factura <= $factura_fin) {
    } else {
        $oReturn->alert('::.ERROR.:: la factura numero ' . $factura . ' debe estar dentro del intervalo ' . $factura_inicio . ' - ' . $factura_fin . ' ');
        $oReturn->assign('factura', 'value', '0');
    }


    return $oReturn;
}

function fecha_mysql_func2($fecha)
{
    $fecha_array = explode('/', $fecha);
    $m = $fecha_array[0];
    $y = $fecha_array[2];
    $d = $fecha_array[1];

    return ($y . '-' . $m . '-' . $d);
}

function reporte($aForm = '')
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN_Ifx;

    $oReturn = new xajaxResponse();

    $array = $_SESSION['Print'];

    $cont = count($array);

    $idsucursal = $aForm['sucursal'];
    $cliente         = $aForm['cliente'];


    if ($cont > 0) {
        foreach ($array as $val) {
            $clave_acceso = $val[0];
            $cod_prove = $cliente;
            $factura = $val[2];
            $idejer_fact = $val[3];
            $asto = $val[4];
            $fecha_emis = $val[5];
        }
    }

    // $id = '', $nombre_archivo = '', &$rutaPdf = '', $clpv = 0, $num_fact = '', $ejer = 0, $asto = '', $fec_emis = '', $idSucursal = 0
    $_SESSION['pdf'] = reporte_retencionInve($id, $clave_acceso, $rutapdf, $cod_prove, $factura, $idejer_fact, $asto, $fecha_emis, $idsucursal);
    $oReturn->script('generar_pdf()');

    return $oReturn;
}


// ORDEN DE COMPRA
function orden_compra($idempresa, $idsucursal, $id_cliente = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse();

    $usuario_informix = $_SESSION['U_USER_INFORMIX'];
    unset($_SESSION['U_PROF_RECO']);

    $Html_reporte .= '<fieldset style="border:#999999 1px solid; padding:2px; text-align:center; width:98%;">';
    $Html_reporte .= '<legend class="Titulo">Orden Compra</legend>';
    $Html_reporte .= '<table align="center" border="0" cellpadding="2" cellspacing="1" width="98%">';
    $Html_reporte .= '<tr>
                                <th class="diagrama">N.-</th>
                                <th class="diagrama">Orden Compra</th>
                                <th class="diagrama">Fecha</th>
                                <th class="diagrama">Total</th>
								<th class="diagrama">Detalle</th>
                                <th class="diagrama">Seleccionar</th>
                         </tr>';
    $sql = " SELECT distinct( minv_num_comp),   minv_fmov,      clpv_nom_clpv,   minv_num_sec, minv_cod_clpv,  minv_dege_minv,
                        (COALESCE(minv_tot_minv,0) - COALESCE(minv_dge_valo,0) + COALESCE(minv_iva_valo,0) + COALESCE(minv_otr_valo,0) - COALESCE(minv_fle_minv,0) + COALESCE(minv_val_ice,0) ) total
                        FROM saeminv,    saeclpv,    saedmov   WHERE 
                        minv_cod_clpv =  clpv_cod_clpv  and  
                        minv_num_comp = dmov_num_comp and  
                        minv_est_minv = '1'  and 
                        minv_cod_clpv = $id_cliente  and  
                        minv_cod_tran in  ( select defi_cod_tran from saedefi Where 
                                                defi_tip_defi  = '4' and 
                                                defi_cod_empr  = $idempresa and 
                                                defi_cod_modu  = 10 and
                                                defi_cod_tran not in ( select parm_tran_ord from saeparm where parm_cod_empr = $idempresa )  )  AND  
                        minv_cod_empr = $idempresa  AND  
                        minv_cod_sucu = $idsucursal AND  
                        clpv_cod_empr = $idempresa and
                        dmov_can_dmov <> dmov_can_entr  and
                        (( minv_cer_sn is null) or ( minv_cer_sn = 'N' ) )";
    $i = 1;
    unset($array);
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            do {
                $fec_pedi     = fecha_mysql_func($oIfx->f('minv_fmov'));
                $preimp     = $oIfx->f('minv_num_sec');
                $clpv_cod     = $oIfx->f('minv_cod_clpv');
                $clpv_nom     = $oIfx->f('clpv_nom_clpv');
                $serial     = $oIfx->f('minv_num_comp');
                $total         = round($oIfx->f('total'), 2);
                $descuento     = $oIfx->f('minv_dege_minv');

                $ifu->AgregarCampoCheck($serial, '', false, 1);
                if ($sClass == 'off')
                    $sClass = 'on';
                else
                    $sClass = 'off';
                $Html_reporte .= '<tr height="20" class="' . $sClass . '"
                                        onMouseOver="javascript:this.className=\'link\';"
                                        onMouseOut="javascript:this.className=\'' . $sClass . '\';">';
                $Html_reporte .= '<td align="right">' . $i . '</td>';
                $Html_reporte .= '<td align="right">' . $preimp . '</td>';
                $Html_reporte .= '<td align="right">' . $fec_pedi . '</td>';
                $Html_reporte .= '<td align="right">' . $total . '</td>';
                $Html_reporte .= '<td align="right">
									<input type="button" value="Detalle"
										onClick="javascript:cargar_oc_det(\'' . $serial . '\', \'' . $idempresa . '\', \'' . $idsucursal . '\')"
										style="width:60px; height:25px;"
										id="BuscaBtn" class="myButton_BT" " />
								 </td>';
                $Html_reporte .= '<td align="right">' . $ifu->ObjetoHtml($serial) . '</td>';
                $Html_reporte .= '</tr>';
                $array[] = array($serial, $preimp, $clpv_cod, $descuento);
                $i++;
            } while ($oIfx->SiguienteRegistro());
            $Html_reporte .= '<tr>
                                            <td align="center" colspan="6">
                                                <input type="button" value="Cargar"
                                                    onClick="javascript:cargar_pedido()"
                                                    style="width:100px; height:25px;"
                                                    id="BuscaBtn" class="myButton_BT" />
                                            </td>
                                     </tr>';
        } else {
            $Html_reporte = 'Sin Pedidos';
        }
    }
    $oIfx->Free();
    $_SESSION['U_PROF_RECO'] = $array;

    $oReturn->assign("divFormularioDetalle", "innerHTML", $Html_reporte);
    return $oReturn;
}

function cargar_orden_compra($id_empresa, $id_sucursal, $cliente, $aForm = '')
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN, $DSN_Ifx;

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    unset($_SESSION['aDataGird_INV_MRECO']);
    $aDataGrid = $_SESSION['aDataGird_INV_MRECO'];

    $aLabelGrid = array(
        'Id',
        'Bodega',
        'Codigo Item',
        'Descripcion',
        'Unidad',
        'Cantidad',
        'Costo',
        'Impuesto',
        'Dscto 1',
        'Dscto 2',
        'Dscto Gral',
        'Total',
        'Total Con Impuesto',
        'Modificar',
        'Eliminar',
        'Cuenta',
        'Cuenta Impuesto',
        'Serie',
        'Fecha Ela',
        'Fecha Cad',
        'Detalle',
        'Precio',
        'Orden Compra',
        'Serial'
    );

    $oReturn = new xajaxResponse();

    $array = $_SESSION['U_PROF_RECO'];
    unset($_SESSION['U_PROF_APROB_RECO']);

    if (count($array) > 0) {
        //GUARDA LOS DATOS DEL DETALLE
        $id_prof = '';
        $precio = 0;
        $desc = 0;
        $flete = 0;
        $otro = 0;
        unset($array_aprob);
        foreach ($array as $val) {
            $serial     = $val[0];
            $preimp     = $val[1];
            $clpv_cod     = $val[2];
            $desc_gen     = $val[3];

            $check = $aForm[$serial];
            if (!empty($check)) {
                $array_aprob[] = array($clpv_cod, $preimp, $serial);
                $sql = "select  d.dmov_cod_prod, d.dmov_cod_bode, d.dmov_cod_unid,  
                               (d.dmov_can_dmov - d.dmov_can_entr) as cantidad, 
                                p.prbo_cta_inv, p.prbo_cta_ideb, p.prbo_iva_porc, d.dmov_cun_dmov, dmov_det1_dmov
                                from saedmov d , saeprbo p where
                                p.prbo_cod_prod = d.dmov_cod_prod and
                                d.dmov_cod_bode = p.prbo_cod_bode and
                                p.prbo_cod_empr = $id_empresa and
                                p.prbo_cod_sucu = $id_sucursal and
                                d.dmov_num_comp = $serial and
                                d.dmov_cod_empr = $id_empresa and
                                d.dmov_cod_sucu = $id_sucursal order by d.dmov_cod_dmov ";
                if ($oIfx->Query($sql)) {
                    if ($oIfx->NumFilas() > 0) {
                        do {
                            $cont = count($aDataGrid);
                            $prod_cod = $oIfx->f('dmov_cod_prod');
                            $bode_cod = $oIfx->f('dmov_cod_bode');
                            $unid_cod = $oIfx->f('dmov_cod_unid');
                            $cantidad = $oIfx->f('cantidad');
                            $costo = $oIfx->f('dmov_cun_dmov');
                            $cta_inv = $oIfx->f('prbo_cta_inv');
                            $cta_iva = $oIfx->f('prbo_cta_ideb');
                            $iva = $oIfx->f('prbo_iva_porc');
                            $detalle = $oIfx->f('dmov_det1_dmov');

                            // TOTAL
                            $total_fac = 0;
                            $descuento = 0;
                            $descuento_2 = 0;
                            $descuento_general = 0;
                            $dsc1 = ($costo * $cantidad * $descuento) / 100;
                            $dsc2 = ((($costo * $cantidad) - $dsc1) * $descuento_2) / 100;
                            if ($descuento_general > 0) {
                                // descto general
                                $dsc3 = ((($costo * $cantidad) - $dsc1 - $dsc2) * $descuento_general) / 100;
                                $total_fact_tmp = ((($costo * $cantidad) - ($dsc1 + $dsc2 + $dsc3)));
                                $tmp = ((($costo * $cantidad) - ($dsc1 + $dsc2)));
                            } else {
                                // sin descuento general
                                $total_fact_tmp = ((($costo * $cantidad) - ($dsc1 + $dsc2)));
                                $tmp = $total_fact_tmp;
                            }

                            $total_fac = round($total_fact_tmp, 2);

                            // total con iva
                            if ($iva > 0) {
                                $total_con_iva = round((($total_fac * $iva) / 100), 2) + $total_fac;
                            } else {
                                $total_con_iva = $total_fac;
                            }

                            $cont = count($aDataGrid);
                            // cantidad
                            $fu->AgregarCampoNumerico($cont . '_cantidad', 'Cantidad|LEFT', false, $cantidad, 40, 40);
                            $fu->AgregarComandoAlCambiarValor($cont . '_cantidad', 'cargar_update_cant(\'' . $cont . '\');');

                            // costo
                            $fu->AgregarCampoNumerico($cont . '_costo', 'Costo|LEFT', false, $costo, 80, 40);
                            $fu->AgregarComandoAlCambiarValor($cont . '_costo', 'cargar_update_cant(\'' . $cont . '\');');

                            // iva
                            $fu->AgregarCampoNumerico($cont . '_iva', 'Iva|LEFT', false, $iva, 40, 40);
                            $fu->AgregarComandoAlCambiarValor($cont . '_iva', 'cargar_update_cant(\'' . $cont . '\');');

                            // descto1
                            $fu->AgregarCampoNumerico($cont . '_desc1', 'Descto1|LEFT', false, 0, 40, 40);
                            $fu->AgregarComandoAlCambiarValor($cont . '_desc1', 'cargar_update_cant(\'' . $cont . '\');');

                            // descto2
                            $fu->AgregarCampoNumerico($cont . '_desc2', 'Descto2|LEFT', false, 0, 40, 40);
                            $fu->AgregarComandoAlCambiarValor($cont . '_desc2', 'cargar_update_cant(\'' . $cont . '\');');

                            $aDataGrid[$cont][$aLabelGrid[0]] = floatval($cont);
                            $aDataGrid[$cont][$aLabelGrid[1]] = $bode_cod;
                            $aDataGrid[$cont][$aLabelGrid[2]] = $prod_cod;
                            $aDataGrid[$cont][$aLabelGrid[3]] = $prod_cod;
                            $aDataGrid[$cont][$aLabelGrid[4]] = $unid_cod;
                            $aDataGrid[$cont][$aLabelGrid[5]] = $fu->ObjetoHtml($cont . '_cantidad');  //$cantidad;
                            $aDataGrid[$cont][$aLabelGrid[6]] = $fu->ObjetoHtml($cont . '_costo'); //costo;
                            $aDataGrid[$cont][$aLabelGrid[7]] = $fu->ObjetoHtml($cont . '_iva'); //iva                
                            $aDataGrid[$cont][$aLabelGrid[8]] = $fu->ObjetoHtml($cont . '_desc1'); // desc1
                            $aDataGrid[$cont][$aLabelGrid[9]] = $fu->ObjetoHtml($cont . '_desc2'); // dec2
                            $aDataGrid[$cont][$aLabelGrid[10]] = $descuento_general;
                            $aDataGrid[$cont][$aLabelGrid[11]] = $total_fac;
                            $aDataGrid[$cont][$aLabelGrid[12]] = $total_con_iva;
                            $aDataGrid[$cont][$aLabelGrid[13]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/pencil3.png"
                                                                                title = "Presione aqui para Modificar"
                                                                                style="cursor: hand !important; cursor: pointer !important;"
                                                                                onclick="agregar_detalle();"
                                                                                alt="Modificar"
                                                                                align="bottom" />';
                            $aDataGrid[$cont][$aLabelGrid[14]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/delete_1.png"
                                                                                onMouseOver="drc(\'Presione aqui para Eliminar\', \'Eliminar\'); return true;"
                                                                                onMouseOut="javascript:nd(); return true;"
                                                                                title = "Presione aqui para Eliminar"
                                                                                style="cursor: hand !important; cursor: pointer !important;"
                                                                                onclick="javascript:xajax_elimina_detalle(' . $cont . ');"
                                                                                alt="Eliminar"
                                                                                align="bottom" />';
                            $aDataGrid[$cont][$aLabelGrid[15]] = $cta_inv;
                            $aDataGrid[$cont][$aLabelGrid[16]] = $cta_iva;
                            $aDataGrid[$cont][$aLabelGrid[17]] = '';
                            $aDataGrid[$cont][$aLabelGrid[18]] = '';
                            $aDataGrid[$cont][$aLabelGrid[19]] = '';
                            $aDataGrid[$cont][$aLabelGrid[20]] = $detalle;
                            $aDataGrid[$cont][$aLabelGrid[21]] = '';
                            $aDataGrid[$cont][$aLabelGrid[22]] = $preimp;
                            $aDataGrid[$cont][$aLabelGrid[23]] = $serial;
                            $aDataGrid[$cont][$aLabelGrid[24]] = ''; // boton imprimir evaluacion
                            $aDataGrid[$cont][$aLabelGrid[25]] = ''; // data evaluacion guardada
                            $aDataGrid[$cont][$aLabelGrid[26]] = ''; // boton recepcion codigos unicos 
                            $aDataGrid[$cont][$aLabelGrid[27]] = ''; // MAC 

                            $_SESSION['aDataGird_INV_MRECO'] = $aDataGrid;
                            $sHtml = mostrar_grid($id_empresa);
                            $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
                        } while ($oIfx->SiguienteRegistro());
                    }
                }
                $oIfx->Free();
                $oReturn->script('totales_oc(' . $desc_gen . ');');
                $oReturn->assign("descuento_general", "value", $desc_gen);

                $_SESSION['U_PROF_APROB_RECO'] = $array_aprob;
            } // fin if
        } // fin foreach        

        $oReturn->script('cerrar_ventana();');
    } else {
        $oReturn->alert('Por favor seleccione una Pedido...');
    }

    $oReturn->script('cargar_descuento_oc(' . $desc_gen . ');');

    return $oReturn;
}

function genera_formulario_portafolio($aForm = '', $cliente = '', $op = '', $empresa = '', $sucursal = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

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

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse();
    //$idempresa = $_SESSION['U_EMPRESA'];
    //$sucursal = $_SESSION['U_SUCURSAL'];
    $usuario_informix = $_SESSION['U_USER_INFORMIX'];

    unset($_SESSION['ARRAY_DESPACHO']);

    if ($op == 1) {
        $order = "order by prbo_cod_prod";
    } else if ($op == 2) {
        $order = "order by prod_nom_prod";
    }

    $sql = "select 	pp.ppvpr_cod_prod, 		pp.ppvpr_nom_prod, 		pp.ppvpr_cod_bode,
					pp.ppvpr_pre_ult, 		pp.ppvpr_pre_pac, 		pp.ppvpr_dia_entr, 
					pp.ppvpr_cod_alte,		pr.prbo_cco_prbo,		p.prod_cod_prod,
					p.prod_nom_prod,		pr.prbo_cod_unid,
					COALESCE(p.prod_uni_caja,0) as prod_uni_caja,
					COALESCE(pr.prbo_iva_porc,0) as prbo_iva_porc,		
					COALESCE(pr.prbo_ice_porc,0) as prbo_ice_porc,
					COALESCE( pr.prbo_dis_prod,0) as prbo_dis_prod
			from saeprod p, saeprbo pr, saeppvpr pp
			where pp.ppvpr_cod_prod =  p.prod_cod_prod and
			p.prod_cod_prod = pr.prbo_cod_prod and
			p.prod_cod_empr = pp.ppvpr_cod_empr and
			p.prod_cod_sucu = pp.ppvpr_cod_sucu and
			pr.prbo_cod_bode = pp.ppvpr_cod_bode and
			pp.ppvpr_cod_clpv = $cliente and
			p.prod_cod_empr = $empresa and
			p.prod_cod_sucu = $sucursal";

    $sHtml .= '<table align="center" border="0" cellpadding="2" cellspacing="1" width="98%" style="border:#999999 1px solid">
                        <tr><th colspan="7" align="center" class="titulopedido">LISTADO DE PRODUCTOS</th></tr>
                        <tr>
                            <td colspan="7">
                                <table id="productos" table align="center" border="0" cellpadding="2" cellspacing="1" width="100%">
                                    <tr>
                                        <th align="center" bgcolor="#EBF0FA" class="diagrama">NO.</th>
										<th align="center" bgcolor="#EBF0FA" class="diagrama">BODEGA</th>
                                        <th align="center" bgcolor="#EBF0FA" class="diagrama">CODIGO ITEM
                                            <img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/1downarrow.png"
                                            title = "Ordenar por Codigo Producto";
                                            style="cursor: hand !important; cursor: pointer !important;"
                                            onclick="javascript:ordenar(1);"
                                            alt="Order"/>
                                        </th>
                                        <th align="center" bgcolor="#EBF0FA" class="diagrama">PRODUCTO
                                        <img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/1downarrow.png"
                                            title = "Ordenar por Nombre Producto";
                                            style="cursor: hand !important; cursor: pointer !important;"
                                            onclick="javascript:ordenar(2);"
                                            alt="Order"/>
                                        </th>
										<th align="center" bgcolor="#EBF0FA" class="diagrama">UNIDAD</th>
										<th align="center" bgcolor="#EBF0FA" class="diagrama">PRECIO</th>
										<th align="center" bgcolor="#EBF0FA" class="diagrama">EMPAQUE</th>
										<th align="center" bgcolor="#EBF0FA" class="diagrama">CANTIDAD</th>
										<th align="center" bgcolor="#EBF0FA" class="diagrama">TOTAL</th>
										<th align="center" bgcolor="#EBF0FA" class="diagrama">PRECIO FINAL</th>
										<th align="center" bgcolor="#EBF0FA" class="diagrama">COSTO UNIT.</th>
										';
    $i = 1;
    $total = 0;
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            unset($array_despacho);
            do {
                $ppvpr_cod_prod = $oIfx->f('ppvpr_cod_prod');
                $ppvpr_nom_prod = $oIfx->f('ppvpr_nom_prod');
                $ppvpr_cod_bode = $oIfx->f('ppvpr_cod_bode');
                $ppvpr_pre_ult     = $oIfx->f('ppvpr_pre_ult');
                $ppvpr_pre_pac    = $oIfx->f('ppvpr_pre_pac');
                $ppvpr_dia_entr    = $oIfx->f('ppvpr_dia_entr');
                $ppvpr_cod_alte    = $oIfx->f('ppvpr_cod_alte');
                $prbo_cco_prbo    = $oIfx->f('prbo_cco_prbo');
                $prod_uni_caja    = $oIfx->f('prod_uni_caja');
                $prbo_cod_unid    = $oIfx->f('prbo_cod_unid');
                $prbo_iva_porc    = $oIfx->f('prbo_iva_porc');
                $prbo_ice_porc    = $oIfx->f('prbo_iva_porc');
                $prbo_dis_prod    = $oIfx->f('prbo_iva_porc');
                $prod_cod_prod    = $oIfx->f('prod_cod_prod');
                $prod_nom_prod    = $oIfx->f('prod_nom_prod');

                //selecciona bodega 
                $sql_bode = "select bode_nom_bode from saebode where bode_cod_empr = $empresa and bode_cod_bode = $ppvpr_cod_bode";
                $bode_nom_bode = consulta_string($sql_bode, 'bode_nom_bode', $oIfxA, '');

                //selecciona unidad
                $sql_unid = "select unid_nom_unid from saeunid where unid_cod_empr = $empresa and unid_cod_unid = $prbo_cod_unid";
                $unid_nom_unid = consulta_string($sql_unid, 'unid_nom_unid', $oIfxA, '');

                $ifu->AgregarCampoNumerico($i . '_ppvpr_pre_pac_' . $prod_cod_prod, 'Precio|left', true, $ppvpr_pre_pac, 50, 20);
                $ifu->AgregarComandoAlEscribir($i . '_ppvpr_pre_pac_' . $prod_cod_prod, 'genera_totales(\'' . $i . '\', \'' . $prod_cod_prod . '\', \'' . $ppvpr_pre_pac . '\', \'' . $prod_uni_caja . '\')');

                //cantidad
                $ifu->AgregarCampoNumerico($i . '_cant_' . $prod_cod_prod, 'Cantidad|left', true, '', 50, 20);
                $ifu->AgregarComandoAlEscribir($i . '_cant_' . $prod_cod_prod, 'genera_totales(\'' . $i . '\', \'' . $prod_cod_prod . '\', \'' . $ppvpr_pre_pac . '\', \'' . $prod_uni_caja . '\')');

                $ifu->AgregarCampoNumerico($i . '_total_' . $prod_cod_prod, 'Total|left', true, '', 50, 20);
                $ifu->AgregarComandoAlPonerEnfoque($i . '_total_' . $prod_cod_prod, 'this.blur()');

                $ifu->AgregarCampoNumerico($i . '_precio_' . $prod_cod_prod, 'Precio|left', true, '', 50, 20);
                $ifu->AgregarComandoAlPonerEnfoque($i . '_precio_' . $prod_cod_prod, 'this.blur()');

                $ifu->AgregarCampoNumerico($i . '_costo_' . $prod_cod_prod, 'Precio|left', true, '', 50, 20);
                $ifu->AgregarComandoAlPonerEnfoque($i . '_costo_' . $prod_cod_prod, 'this.blur()');

                $array_despacho[] = array($i, $prod_cod_prod, $ppvpr_cod_bode, $prbo_iva_porc);

                if ($sClass == 'off')
                    $sClass = 'on';
                else
                    $sClass = 'off';


                $sHtml .= ' <tr height="20" class="' . $sClass . '"
                            onMouseOver="javascript:this.className=\'link\';"
                            onMouseOut="javascript:this.className=\'' . $sClass . '\';">';
                $sHtml .= '<td class="font_face_2" align="center">' . $i . '</td>';
                $sHtml .= '<td class="font_face_2" align="left">' . $bode_nom_bode . '</td>';
                $sHtml .= '<td class="font_face_2" align="left">' . $prod_cod_prod . '</td>';
                $sHtml .= '<td class="font_face_2" align="left">' . $prod_nom_prod . '</td>';
                $sHtml .= '<td class="font_face_2" align="left">' . $unid_nom_unid . '</td>';
                $sHtml .= '<td class="font_face_2" align="right">' . $ifu->ObjetoHtml($i . '_ppvpr_pre_pac_' . $prod_cod_prod) . '</td>';
                $sHtml .= '<td class="font_face_2" align="right">' . $prod_uni_caja . '</td>';
                $sHtml .= '<td class="font_face_2" align="right">' . $ifu->ObjetoHtml($i . '_cant_' . $prod_cod_prod) . '</td>';
                $sHtml .= '<td class="font_face_2" align="right">' . $ifu->ObjetoHtml($i . '_total_' . $prod_cod_prod) . '</td>';
                $sHtml .= '<td class="font_face_2" align="right">' . $ifu->ObjetoHtml($i . '_precio_' . $prod_cod_prod) . '</td>';
                $sHtml .= '<td class="font_face_2" align="right">' . $ifu->ObjetoHtml($i . '_costo_' . $prod_cod_prod) . '</td>';
                $sHtml .= '</tr>';
                $total = 0;
                $i++;
            } while ($oIfx->SiguienteRegistro());
            $sHtml .= '<tr>
                            <td colspan="10" align="right">
                                <input type="button" value="Agregar"
                                onClick="javascript:cargar_productos_portafolio(' . $bodega . ')"
                                class="myButton_GR"
                                style="width:80px; height:20px;"/>
                            </td>
                        </tr>';
        } else {
            $sHtml .= '<tr><td align="left"><span class="fecha_letra">Sin Datos....</span></td></tr>';
        }
    }
    $oIfx->Free();
    $sHtml .= '<tr class="info"><td colspan="10">Su consulta genero ' . ($i - 1) . ' registros de resultado</td>
                            </tr>
                        </table>
                    </td>
                    </tr>';
    $sHtml .= '</table>';

    $oReturn->assign("divFormularioDetallePortafolio", "innerHTML", $sHtml);

    $_SESSION['ARRAY_DESPACHO'] = $array_despacho;

    return $oReturn;
}

function cargar_productos($aForm = '', $idbodega = '', $empresa = '', $sucursal = '')
{
    //Definiciones
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo;
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $oReturn = new xajaxResponse();

    $aDataGrid = $_SESSION['aDataGird_INV_MRECO'];
    $aLabelGrid = array('Id', 'Bodega', 'Codigo Item', 'Descripcion', 'Unidad', 'Cantidad', 'Costo', 'Iva', 'Dscto 1', 'Dscto 2', 'Dscto Gral', 'Total', 'Total Con Iva', 'Modificar', 'Eliminar', 'Cuenta', 'Cuenta Iva', 'Orden Compra', 'Serial');

    //$aLabelGrid = $_SESSION['aLabelGirdProd'];
    //$idEmpresa = $_SESSION['U_EMPRESA'];
    //$idSucursal = $_SESSION['U_SUCURSAL'];
    $cliente = $aForm['cliente'];

    $array_depacho = $_SESSION['ARRAY_DESPACHO'];
    $count = count($array_depacho);

    if ($count > 0) {
        foreach ($array_depacho as $val) {
            $i             = $val[0];
            $prod         = $val[1];
            $bode         = $val[2];
            $iva        = $val[3];
            //$cantidad	= $aForm[$i . '_cant_' . $prod];
            $cantidad    = $aForm[$i . '_total_' . $prod];
            $precio        = $aForm[$i . '_precio_' . $prod];
            $costo        = $aForm[$i . '_costo_' . $prod];

            if ($cantidad > 0) {
                $cont = count($aDataGrid);
                // saeprod
                $sql = "select  p.prod_cod_prod,   pr.prbo_cod_unid,  COALESCE(pr.prbo_iva_porc,0) as prbo_iva_porc   ,
							COALESCE(pr.prbo_ice_porc,0) as prbo_ice_porc,
							COALESCE( pr.prbo_dis_prod,0 ) as stock, prod_cod_tpro,
							pr.prbo_cta_inv, pr.prbo_cta_ideb
							from saeprod p, saeprbo pr where
							p.prod_cod_prod = pr.prbo_cod_prod and
							p.prod_cod_empr = $empresa and
							p.prod_cod_sucu = $sucursal and
							pr.prbo_cod_empr = $empresa and
							pr.prbo_cod_bode = $bode and
							p.prod_cod_prod = '$prod'";
                if ($oIfx->Query($sql)) {
                    if ($oIfx->NumFilas() > 0) {
                        $idproducto = $oIfx->f('prod_cod_prod');
                        $idunidad     = $oIfx->f('prbo_cod_unid');
                        $cuenta_inv = $oIfx->f('prbo_cta_inv');
                        $cuenta_iva = $oIfx->f('prbo_cta_ideb');
                    } else {
                        $idproducto    = '';
                        $idunidad    = '';
                        $cuenta_inv = '';
                        $cuenta_iva = '';
                    }
                }
                $oIfx->Free();

                $descuento = 0;
                $descuento_2 = 0;
                $descuento_general = 0;

                // TOTAL
                $total_fac = 0;
                $dsc1 = ($costo * $cantidad * $descuento) / 100;
                $dsc2 = ((($costo * $cantidad) - $dsc1) * $descuento_2) / 100;
                if ($descuento_general > 0) {
                    // descto general
                    $dsc3 = ((($costo * $cantidad) - $dsc1 - $dsc2) * $descuento_general) / 100;
                    $total_fact_tmp = ((($costo * $cantidad) - ($dsc1 + $dsc2 + $dsc3)));
                    $tmp = ((($costo * $cantidad) - ($dsc1 + $dsc2)));
                } else {
                    // sin descuento general
                    $total_fact_tmp = ((($costo * $cantidad) - ($dsc1 + $dsc2)));
                    $tmp = $total_fact_tmp;
                }

                $total_fac = round($total_fact_tmp, 2);

                // total con iva
                if ($iva > 0) {
                    $total_con_iva = round((($total_fac * $iva)  / 100), 2) + $total_fac;
                } else {
                    $total_con_iva = $total_fac;
                }

                // cantidad
                $fu->AgregarCampoNumerico($cont . '_cantidad', 'Cantidad|LEFT', false, $cantidad, 40, 40);
                $fu->AgregarComandoAlCambiarValor($cont . '_cantidad', 'cargar_update_cant(\'' . $cont . '\');');

                // costo
                $fu->AgregarCampoNumerico($cont . '_costo', 'Costo|LEFT', false, $costo, 80, 40);
                $fu->AgregarComandoAlCambiarValor($cont . '_costo', 'cargar_update_cant(\'' . $cont . '\');');

                // iva
                $fu->AgregarCampoNumerico($cont . '_iva', 'Iva|LEFT', false, $iva, 40, 40);
                $fu->AgregarComandoAlCambiarValor($cont . '_iva', 'cargar_update_cant(\'' . $cont . '\');');

                // descto1
                $fu->AgregarCampoNumerico($cont . '_desc1', 'Descto1|LEFT', false, $descuento, 40, 40);
                $fu->AgregarComandoAlCambiarValor($cont . '_desc1', 'cargar_update_cant(\'' . $cont . '\');');

                // descto2
                $fu->AgregarCampoNumerico($cont . '_desc2', 'Descto2|LEFT', false, $descuento_2, 40, 40);
                $fu->AgregarComandoAlCambiarValor($cont . '_desc2', 'cargar_update_cant(\'' . $cont . '\');');

                $aDataGrid[$cont][$aLabelGrid[0]] = floatval($cont);
                $aDataGrid[$cont][$aLabelGrid[1]] = $bode;
                $aDataGrid[$cont][$aLabelGrid[2]] = $prod;
                $aDataGrid[$cont][$aLabelGrid[3]] = $prod;
                $aDataGrid[$cont][$aLabelGrid[4]] = $idunidad;
                $aDataGrid[$cont][$aLabelGrid[5]] = $fu->ObjetoHtml($cont . '_cantidad');  //$cantidad;
                $aDataGrid[$cont][$aLabelGrid[6]] = $fu->ObjetoHtml($cont . '_costo'); //costo;
                $aDataGrid[$cont][$aLabelGrid[7]] = $fu->ObjetoHtml($cont . '_iva'); //iva                
                $aDataGrid[$cont][$aLabelGrid[8]] = $fu->ObjetoHtml($cont . '_desc1'); // desc1
                $aDataGrid[$cont][$aLabelGrid[9]] = $fu->ObjetoHtml($cont . '_desc2'); // dec2
                $aDataGrid[$cont][$aLabelGrid[10]] = $descuento_general;
                $aDataGrid[$cont][$aLabelGrid[11]] = $total_fac;
                $aDataGrid[$cont][$aLabelGrid[12]] = $total_con_iva;
                $aDataGrid[$cont][$aLabelGrid[13]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/pencil3.png"
                                                                                        title = "Presione aqui para Modificar"
                                                                                        style="cursor: hand !important; cursor: pointer !important;"
                                                                                        onclick="agregar_detalle();"
                                                                                        alt="Modificar"
                                                                                        align="bottom" />';
                $aDataGrid[$cont][$aLabelGrid[14]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/delete_1.png"
                                                                                        onMouseOver="drc(\'Presione aqui para Eliminar\', \'Eliminar\'); return true;"
                                                                                        onMouseOut="javascript:nd(); return true;"
                                                                                        title = "Presione aqui para Eliminar"
                                                                                        style="cursor: hand !important; cursor: pointer !important;"
                                                                                        onclick="javascript:xajax_elimina_detalle(' . $cont . ');"
                                                                                        alt="Eliminar"
                                                                                        align="bottom" />';
                $aDataGrid[$cont][$aLabelGrid[15]] = $cuenta_inv;
                $aDataGrid[$cont][$aLabelGrid[16]] = $cuenta_iva;
                $aDataGrid[$cont][$aLabelGrid[17]] = '';
                $aDataGrid[$cont][$aLabelGrid[18]] = 0;

                $_SESSION['aDataGird_INV_MRECO'] = $aDataGrid;
                $sHtml = mostrar_grid($empresa);
                $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
            } //fin if cantidad
        }
        $oReturn->script('totales();');
        $oReturn->script('cerrar_ventana();');
    } else {
        $oReturn->alert('No existen datos para generar Grid...');
    }
    return $oReturn;
}

function clave_acceso20200406($aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo;
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse();

    $idempresa     = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    $aDataGrid = $_SESSION['aDataGird_INV_MRECO'];
    $aLabelGrid = array('Id', 'Bodega', 'Codigo Item', 'Descripcion', 'Unidad', 'Cantidad', 'Costo', 'Iva', 'Dscto 1', 'Dscto 2', 'Dscto Gral', 'Total', 'Total Con Iva', 'Modificar', 'Eliminar', 'Cuenta', 'Cuenta Iva', 'Orden Compra', 'Serial');


    //variables formulario
    $clave_acceso    = $aForm['clave_acceso'];
    $pos              = $aForm['clave_acceso'];
    $ambiente_sri     = $aForm['ambiente_sri'];
    //$ruc			= $aForm['ruc'];
    $cliente        = $aForm['cliente'];

    $sql = "select empr_ruc_empr from saeempr where empr_cod_empr = $idempresa ";
    $ruc = consulta_string($sql, 'empr_ruc_empr', $oIfx, 0);


    try {
        $clientOptions = array(
            "useMTOM" => FALSE,
            'trace' => 1,
            'stream_context' => stream_context_create(array('http' => array('protocol_version' => 1.0)))
        );

        if ($ambiente_sri == 'S') {
            $wsdlAutoComp[$pos] = new  SoapClient("https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl", $clientOptions);
        } else {
            $wsdlAutoComp[$pos] = new  SoapClient("https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl", $clientOptions);
        }

        //RECUPERA LA AUTORIZACION DEL COMPROBANTE
        $aClave = array("claveAccesoComprobante" => $clave_acceso);

        $autoComp[$pos] = new stdClass();
        $autoComp[$pos] = $wsdlAutoComp[$pos]->autorizacionComprobante($aClave);

        $RespuestaAutorizacionComprobante[$pos]    = $autoComp[$pos]->RespuestaAutorizacionComprobante;
        $claveAccesoConsultada[$pos]             = $RespuestaAutorizacionComprobante[$pos]->claveAccesoConsultada;
        $autorizaciones[$pos]                     = $RespuestaAutorizacionComprobante[$pos]->autorizaciones;
        $autorizacion[$pos]                        = $autorizaciones[$pos]->autorizacion;

        if (count($autorizacion[$pos]) > 1) {
            $estado[$pos]                 = $autorizacion[$pos][0]->estado;
            $numeroAutorizacion[$pos]     = $autorizacion[$pos][0]->numeroAutorizacion;
            $fechaAutorizacion[$pos]     = $autorizacion[$pos][0]->fechaAutorizacion;
            $ambiente[$pos]             = $autorizacion[$pos][0]->ambiente;
            $comprobante[$pos]             = $autorizacion[$pos][0]->comprobante;
            $mensajes[$pos]             = $autorizacion[$pos][0]->mensajes;
            $mensaje[$pos]                 = $mensajes[$pos]->mensaje;
        } else {
            $estado[$pos]                 = $autorizacion[$pos]->estado;
            $numeroAutorizacion[$pos]     = $autorizacion[$pos]->numeroAutorizacion;
            $fechaAutorizacion[$pos]     = $autorizacion[$pos]->fechaAutorizacion;
            $ambiente[$pos]             = $autorizacion[$pos]->ambiente;
            $comprobante[$pos]             = $autorizacion[$pos]->comprobante;
            $mensajes[$pos]             = $autorizacion[$pos]->mensajes;
            $mensaje[$pos]                 = $mensajes[$pos]->mensaje;
        }

        $xml    =    '';
        /*$xml 	.=	'<?xml version="1.0" encoding="UTF-8"?>';*/
        //$xml 	.=	'<autorizacion>';
        //$xml 	.=	"<estado>$estado[$pos]</estado>";
        //$xml	.=	"<numeroAutorizacion>$numeroAutorizacion[$pos]</numeroAutorizacion>";
        //$xml 	.=	"<fechaAutorizacion>$fechaAutorizacion[$pos]</fechaAutorizacion>";
        //$xml 	.=	"<ambiente>$ambiente[$pos]</ambiente>";
        //$xml 	.=	"<comprobante><![CDATA[$comprobante[$pos]]]></comprobante>";
        $xml     .=    "$comprobante[$pos]";
        //$xml 	.=	'</autorizacion>';


        if ($estado[$pos] == 'AUTORIZADO') {

            //$oReturn->alert("COMPROBANTE: $estado[$pos] $clave_acceso");

            // GUARDAR EN XML
            // CREAR CARPETA ANEXO
            $serv = "/Jireh/";
            $ruta = $serv . "Doc Electronicos Web SRI";
            // CARPETA EMPRESA
            $ruta_empr = $ruta . "/" . $idempresa;
            if (!file_exists($ruta)) {
                mkdir($ruta);
            }

            if (!file_exists($ruta_empr)) {
                mkdir($ruta_empr);
            }

            // ruta del xml
            $nombre = $clave_acceso . ".xml";
            $archivo = fopen($nombre, "w+");
            fwrite($archivo, $xml);
            fclose($archivo);

            // ruta del xml
            $archivo_xml = fopen($ruta_empr . '/' . $nombre, "w+");
            $ruta_xml    = $ruta_empr . '/' . $nombre;
            fwrite($archivo_xml, $xml);
            fclose($archivo_xml);

            /*$dia = substr($claveAccesoConsultada[$pos], 0, 2); 
            $mes = substr($claveAccesoConsultada[$pos], 2, 2); 
            $an = substr($claveAccesoConsultada[$pos], 4, 4);*/

            $xmlParse             = simplexml_load_file($ruta_xml);
            /*$autorizacion 	= $xmlParse->autorizacion;
			$estado 			= $xmlParse->estado;
			$comprobante 		= $xmlParse->comprobante;
			$numeroAutorizacion = $xmlParse->numeroAutorizacion;*/

            //$ruc = $xmlParse->comprobante->factura->infoTributaria;

            //foreach ($xmlParse->comprobante as $comprobante) {
            //$factura 		= $comprobante->factura;
            //$infoTributaria = $factura->infoTributaria;
            //$razonSocial 	= $infoTributaria->razonSocial;

            //$oReturn->alert('b'.$razonSocial);
            //}

            $estab             = $xmlParse->infoTributaria->estab;
            $ptoEmi         = $xmlParse->infoTributaria->ptoEmi;
            $secuencial     = $xmlParse->infoTributaria->secuencial;
            $identificacionComprador = $xmlParse->infoFactura->identificacionComprador;
            $detalles         = $xmlParse->detalles->detalle;

            $descuento_2 = 0;
            $descuento_general = 0;
            $total_fac = 0;
            $total_fact_tmp = 0;
            foreach ($detalles as $detalle) {
                $codigoPrincipal    = $detalle->codigoPrincipal;
                $codigoAuxiliar     = $detalle->codigoAuxiliar;
                $descripcion         = $detalle->descripcion;
                $cantidad             = $detalle->cantidad;
                $costo                 = $detalle->precioUnitario;
                $descuento             = $detalle->descuento;
                $precioTotalSinImpuesto    = $detalle->precioTotalSinImpuesto;

                $codigoPrincipal    = $codigoPrincipal . '';
                $codigoAuxiliar     = $codigoAuxiliar . '';
                $descripcion         = $descripcion . '';
                $cantidad             = $cantidad . '';
                $costo                 = $costo . '';
                $descuento             = $descuento . '';
                $precioTotalSinImpuesto    = $precioTotalSinImpuesto . '';

                $cont = count($aDataGrid);
                // saeprod

                $sql = "select 	pp.ppvpr_cod_prod, 		pp.ppvpr_nom_prod, 		pp.ppvpr_cod_bode,
								pp.ppvpr_pre_ult, 		pp.ppvpr_pre_pac, 		pp.ppvpr_dia_entr, 
								pp.ppvpr_cod_alte,		pr.prbo_cco_prbo,		p.prod_cod_prod,
								p.prod_nom_prod,		pr.prbo_cod_unid,
								COALESCE(p.prod_uni_caja,0) as prod_uni_caja,
								COALESCE(pr.prbo_iva_porc,0) as prbo_iva_porc,		
								COALESCE(pr.prbo_ice_porc,0) as prbo_ice_porc,
								COALESCE( pr.prbo_dis_prod,0) as prbo_dis_prod,
								pr.prbo_cta_inv, pr.prbo_cta_ideb
					from saeprod p, saeprbo pr, saeppvpr pp
					where pp.ppvpr_cod_prod =  p.prod_cod_prod and
					p.prod_cod_prod = pr.prbo_cod_prod and
					p.prod_cod_empr = pp.ppvpr_cod_empr and
					p.prod_cod_sucu = pp.ppvpr_cod_sucu and
					pr.prbo_cod_bode = pp.ppvpr_cod_bode and
					pp.ppvpr_cod_clpv = $cliente and
					p.prod_cod_empr = $idempresa and
					p.prod_cod_sucu = $idsucursal and
					(pp.ppvpr_cod_prod = '$codigoPrincipal' or pp.ppvpr_cod_alte = '$codigoAuxiliar')";

                if ($oIfx->Query($sql)) {
                    if ($oIfx->NumFilas() > 0) {
                        $prod_cod_prod    = $oIfx->f('prod_cod_prod');
                        $ppvpr_cod_bode = $oIfx->f('ppvpr_cod_bode');
                        $prbo_cod_unid     = $oIfx->f('prbo_cod_unid');
                        $cuenta_inv     = $oIfx->f('prbo_cta_inv');
                        $cuenta_iva     = $oIfx->f('prbo_cta_ideb');
                        $prbo_iva_porc  = $oIfx->f('prbo_iva_porc');

                        $dsc1 = ($costo * $cantidad * $descuento) / 100;
                        $dsc2 = ((($costo * $cantidad) - $dsc1) * $descuento_2) / 100;
                        if ($descuento_general > 0) {
                            // descto general
                            $dsc3 = ((($costo * $cantidad) - $dsc1 - $dsc2) * $descuento_general) / 100;
                            $total_fact_tmp = ((($costo * $cantidad) - ($dsc1 + $dsc2 + $dsc3)));
                            $tmp = ((($costo * $cantidad) - ($dsc1 + $dsc2)));
                        } else {
                            // sin descuento general
                            $total_fact_tmp = ((($costo * $cantidad) - ($dsc1 + $dsc2)));
                            $tmp = $total_fact_tmp;
                        }

                        $total_fac = round($total_fact_tmp, 2);

                        // total con iva
                        if ($prbo_iva_porc > 0) {
                            $total_con_iva = round((($total_fac * $prbo_iva_porc)  / 100), 2) + $total_fac;
                        } else {
                            $total_con_iva = $total_fac;
                        }

                        // cantidad
                        $fu->AgregarCampoNumerico($cont . '_cantidad', 'Cantidad|LEFT', false, $cantidad, 40, 40);
                        $fu->AgregarComandoAlCambiarValor($cont . '_cantidad', 'cargar_update_cant(\'' . $cont . '\');');

                        // costo
                        $fu->AgregarCampoNumerico($cont . '_costo', 'Costo|LEFT', false, $costo, 80, 40);
                        $fu->AgregarComandoAlCambiarValor($cont . '_costo', 'cargar_update_cant(\'' . $cont . '\');');

                        // iva
                        $fu->AgregarCampoNumerico($cont . '_iva', 'Iva|LEFT', false, $prbo_iva_porc, 40, 40);
                        $fu->AgregarComandoAlCambiarValor($cont . '_iva', 'cargar_update_cant(\'' . $cont . '\');');

                        // descto1
                        $fu->AgregarCampoNumerico($cont . '_desc1', 'Descto1|LEFT', false, $descuento, 40, 40);
                        $fu->AgregarComandoAlCambiarValor($cont . '_desc1', 'cargar_update_cant(\'' . $cont . '\');');

                        // descto2
                        $fu->AgregarCampoNumerico($cont . '_desc2', 'Descto2|LEFT', false, $descuento_2, 40, 40);
                        $fu->AgregarComandoAlCambiarValor($cont . '_desc2', 'cargar_update_cant(\'' . $cont . '\');');

                        $aDataGrid[$cont][$aLabelGrid[0]] = floatval($cont);
                        $aDataGrid[$cont][$aLabelGrid[1]] = $ppvpr_cod_bode;
                        $aDataGrid[$cont][$aLabelGrid[2]] = $prod_cod_prod;
                        $aDataGrid[$cont][$aLabelGrid[3]] = $prod_cod_prod;
                        $aDataGrid[$cont][$aLabelGrid[4]] = $prbo_cod_unid;
                        $aDataGrid[$cont][$aLabelGrid[5]] = $fu->ObjetoHtml($cont . '_cantidad');  //$cantidad;
                        $aDataGrid[$cont][$aLabelGrid[6]] = $fu->ObjetoHtml($cont . '_costo'); //costo;
                        $aDataGrid[$cont][$aLabelGrid[7]] = $fu->ObjetoHtml($cont . '_iva'); //iva                
                        $aDataGrid[$cont][$aLabelGrid[8]] = $fu->ObjetoHtml($cont . '_desc1'); // desc1
                        $aDataGrid[$cont][$aLabelGrid[9]] = $fu->ObjetoHtml($cont . '_desc2'); // dec2
                        $aDataGrid[$cont][$aLabelGrid[10]] = $descuento_general;
                        $aDataGrid[$cont][$aLabelGrid[11]] = $total_fac;
                        $aDataGrid[$cont][$aLabelGrid[12]] = $total_con_iva;
                        $aDataGrid[$cont][$aLabelGrid[13]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/pencil3.png"
																								title = "Presione aqui para Modificar"
																								style="cursor: hand !important; cursor: pointer !important;"
																								onclick="agregar_detalle();"
																								alt="Modificar"
																								align="bottom" />';
                        $aDataGrid[$cont][$aLabelGrid[14]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/delete_1.png"
																								onMouseOver="drc(\'Presione aqui para Eliminar\', \'Eliminar\'); return true;"
																								onMouseOut="javascript:nd(); return true;"
																								title = "Presione aqui para Eliminar"
																								style="cursor: hand !important; cursor: pointer !important;"
																								onclick="javascript:xajax_elimina_detalle(' . $cont . ');"
																								alt="Eliminar"
																								align="bottom" />';
                        $aDataGrid[$cont][$aLabelGrid[15]] = $cuenta_inv;
                        $aDataGrid[$cont][$aLabelGrid[16]] = $cuenta_iva;
                        $aDataGrid[$cont][$aLabelGrid[17]] = '';
                        $aDataGrid[$cont][$aLabelGrid[18]] = 0;

                        $_SESSION['aDataGird_INV_MRECO'] = $aDataGrid;
                        $sHtml = mostrar_grid($idempresa);
                        $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
                    }
                }
                $oIfx->Free();
                //$oReturn->alert($descripcion.'');
            }


            $serie = $estab . $ptoEmi;
            $secuencial = $secuencial . '';

            if ($ruc == $identificacionComprador . '') {
                $oReturn->alert('Validacion ejecutada correctamente...');
                $oReturn->assign('auto_prove', 'value', $numeroAutorizacion[$pos]);
                $oReturn->assign('serie_prove', 'value', $serie);
                $oReturn->assign('factura', 'value', $secuencial);
                $oReturn->script('totales();');
            } else {
                $oReturn->alert('El numero de identificacion del Proveedor: ' . $ruc . ' no coincide con la identificacion del archivo xml: ' . $identificacionComprador);
            }

            //$oReturn->alert($ruc);

            /*foreach ($xmlParse->comprobante as $val){
				$oReturn->alert($val);
			}*/
            //$oReturn->alert($ruta_xml);
            //$oReturn->alert($ruc);


        } else {
            $informacionAdicional = (strtoupper($mensaje[$clave_acceso]->informacionAdicional));
            $informacionAdicional = preg_replace('([^A-Za-z0-9 ])', '', strtoupper($mensaje[$clave_acceso][0]->informacionAdicional));
            $informacionAdicional = htmlspecialchars_decode($informacionAdicional);
            $oReturn->alert('Error...' . $informacionAdicional);
        }
    } catch (SoapFault $e) {
        $oReturn->alert($pos . ' NO HUBO CONECCION AL SRI (AUTORIZAR)');
    }

    return $oReturn;
}


function clave_acceso($aForm = '', $tipo)
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oCon = new Dbo;
    $oCon->DSN = $DSN;
    $oCon->Conectar();


    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo;
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse();

    $idempresa     = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];

    $sucursal = $aForm['sucursal'];
    $sust_trib = $aForm['sust_trib'];
    $bodega = $aForm['bodega'];
    //variables formulario
    if ($tipo == '1') {
        $clave_acceso    = $aForm['clave_acceso_'];
        $pos              = $aForm['clave_acceso_'];
    } else {
        $clave_acceso    = $aForm['clave_acceso'];
        $pos              = $aForm['clave_acceso'];
    }


    $ambiente_sri     = $aForm['ambiente_sri'];
    unset($_SESSION['DATOS_FACT_PROV']);
    //unset($_SESSION['aDataGird_INV_MRECO']);

    //$aDataGrid  = $_SESSION['aDataGird_INV_MRECO'];
    // $aLabelGrid = $_SESSION['aLabelGirdProd_INV_MRECO'];
    unset($_SESSION['ARRAY_RD']);

    //$ruc			= $aForm['ruc'];
    if ($ambiente_sri == "") {
        $ambiente_sri = "S";
    }
    $sql = "select empr_ruc_empr from saeempr where empr_cod_empr = $idempresa ";
    $ruc = consulta_string($sql, 'empr_ruc_empr', $oIfx, 0);


    try {

        $headers = array(
            "Content-Type:application/json",
            "Token-Api:9c0ab4af-30dc-4b85-93e2-f0cd28dd7e51"
        );
        $data = array(
            "clave_acceso" => $clave_acceso
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, URL_JIREH_WS . "/api/facturacion/electronica/autorizacion/comprobante");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($ch);
        $autoComp[$pos] = (object) json_decode($respuesta, true);
        $data = $autoComp[$pos];



        //$clientOptions = array(
        //    "useMTOM" => FALSE,
        //    'trace' => 1,
        //    'stream_context' => stream_context_create(array('http' => array('protocol_version' => 1.0)))
        //);

        //if ($ambiente_sri == 'S') {
        //    $wsdlAutoComp[$pos] = new  SoapClient("https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl", $clientOptions);
        //} else {
        //    $wsdlAutoComp[$pos] = new  SoapClient("https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl", $clientOptions);
        //}



        //RECUPERA LA AUTORIZACION DEL COMPROBANTE
        // $aClave = array("claveAccesoComprobante" => $clave_acceso);

        // $autoComp[$pos] = new stdClass();
        // $autoComp[$pos] = $wsdlAutoComp[$pos]->autorizacionComprobante($aClave);

        $RespuestaAutorizacionComprobante[$pos]    = $data;
        $claveAccesoConsultada[$pos]             = $RespuestaAutorizacionComprobante[$pos]->claveAccesoConsultada;
        // $autorizaciones[$pos] 					= $RespuestaAutorizacionComprobante[$pos]->autorizaciones;
        // $autorizacion[$pos]						= $autorizaciones[$pos]->autorizacion; 
        $autorizacion[$pos]                        = 1;

        if (count($autorizacion[$pos]) > 1) {
            $estado[$pos]                 = $autorizacion[$pos][0]->estado;
            $numeroAutorizacion[$pos]     = $autorizacion[$pos][0]->numeroAutorizacion;
            $fechaAutorizacion[$pos]     = $autorizacion[$pos][0]->fechaAutorizacion;
            $ambiente[$pos]             = $autorizacion[$pos][0]->ambiente;
            $comprobante[$pos]             = $autorizacion[$pos][0]->comprobante;
            $mensajes[$pos]             = $autorizacion[$pos][0]->mensajes;
            $mensaje[$pos]                 = $mensajes[$pos]->mensaje;
        } else {
            $estado[$pos]                 = $RespuestaAutorizacionComprobante[$pos]->estado;
            $numeroAutorizacion[$pos]     = $RespuestaAutorizacionComprobante[$pos]->numeroAutorizacion;
            $fechaAutorizacion[$pos]     = $RespuestaAutorizacionComprobante[$pos]->fechaAutorizacion;
            $ambiente[$pos]             = $RespuestaAutorizacionComprobante[$pos]->ambiente;
            $comprobante[$pos]             = $RespuestaAutorizacionComprobante[$pos]->comprobante;
            $mensajes[$pos]             = $RespuestaAutorizacionComprobante[$pos]->mensajes;
            $mensaje[$pos]                 = $mensajes[$pos]->mensaje;
        }




        $xml    =    '';
        /*$xml 	.=	'<?xml version="1.0" encoding="UTF-8"?>';*/
        //$xml 	.=	'<autorizacion>';
        //$xml 	.=	"<estado>$estado[$pos]</estado>";
        //$xml	.=	"<numeroAutorizacion>$numeroAutorizacion[$pos]</numeroAutorizacion>";
        //$xml 	.=	"<fechaAutorizacion>$fechaAutorizacion[$pos]</fechaAutorizacion>";
        //$xml 	.=	"<ambiente>$ambiente[$pos]</ambiente>";
        //$xml 	.=	"<comprobante><![CDATA[$comprobante[$pos]]]></comprobante>";
        $xml     .=    "$comprobante[$pos]";
        //$xml 	.=	'</autorizacion>';

        //$oReturn->alert("COMPROBANTE: $estado[$pos] $clave_acceso");
        if ($estado[$pos] == 'AUTORIZADO') {

            //$oReturn->alert("COMPROBANTE: $estado[$pos] $clave_acceso");

            // GUARDAR EN XML
            // CREAR CARPETA ANEXO
            $serv = "/Jireh/";
            $ruta = $serv . "Doc Electronicos Web SRI";
            $ruta1 = "Doc_Electronicos";

            // CARPETA EMPRESA
            $ruta_empr = $ruta1 . "/" . $idempresa;
            if (!file_exists($ruta1)) {
                mkdir($ruta1);
            }

            if (!file_exists($ruta_empr)) {
                mkdir($ruta_empr);
            }

            // ruta del xml
            $nombre = $clave_acceso . ".xml";
            $archivo = fopen($nombre, "w+");
            fwrite($archivo, $xml);
            fclose($archivo);

            // ruta del xml
            $archivo_xml = fopen($ruta_empr . '/' . $nombre, "w+");
            $ruta_xml    = $ruta_empr . '/' . $nombre;
            fwrite($archivo_xml, $xml);
            fclose($archivo_xml);

            /*$dia = substr($claveAccesoConsultada[$pos], 0, 2); 
            $mes = substr($claveAccesoConsultada[$pos], 2, 2); 
            $an = substr($claveAccesoConsultada[$pos], 4, 4);*/

            $xmlParse             = simplexml_load_file($ruta_xml);
            /*$autorizacion 	= $xmlParse->autorizacion;
			$estado 			= $xmlParse->estado;
			$comprobante 		= $xmlParse->comprobante;
			$numeroAutorizacion = $xmlParse->numeroAutorizacion;*/

            //$ruc = $xmlParse->comprobante->factura->infoTributaria;

            //foreach ($xmlParse->comprobante as $comprobante) {
            //$factura 		= $comprobante->factura;
            //$infoTributaria = $factura->infoTributaria;
            //$razonSocial 	= $infoTributaria->razonSocial;

            //$oReturn->alert('b'.$razonSocial);
            //}

            $estab             = $xmlParse->infoTributaria->estab;
            $ptoEmi         = $xmlParse->infoTributaria->ptoEmi;
            $secuencial     = $xmlParse->infoTributaria->secuencial;
            $codDoc         = $xmlParse->infoTributaria->codDoc;
            $identificacionComprador = $xmlParse->infoFactura->identificacionComprador;
            $fechaEmision   = list($a, $b, $c) = explode('/', trim($xmlParse->infoFactura->fechaEmision));
            $fechaEmision   = $c . '-' . $b . '-' . $a;


            $identificacionProveedor = trim($xmlParse->infoTributaria->ruc);
            $totalImpuesto     = $xmlParse->infoFactura->totalConImpuestos->totalImpuesto;
            $detallesx = $xmlParse->infoFactura->detalles;
            $totalbien = 0;
            $totalserv = 0;
            foreach ($totalImpuesto as $bases) {
                $codigoPorcentaje    = $bases->codigoPorcentaje;
                $baseImponible         = $bases->baseImponible;
                $valor                 = $bases->valor;

                if ($codigoPorcentaje == 2) {
                    $valor_grab12b = $baseImponible . '';
                    $totalbien = $totalbien + $valor_grab12b;
                }
                if ($codigoPorcentaje == 4) {
                    $valor_grab12b = $baseImponible . '';
                    $totalbien = $totalbien + $valor_grab12b;
                } elseif ($codigoPorcentaje == 0) {
                    $valor_grab0s = $baseImponible . '';
                    $totalserv = $totalserv + $valor_grab0s;
                }
            }


            $serie = $estab . $ptoEmi;
            $secuencial = $secuencial . '';

            //$oReturn->alert($baseImponible.'');
            if ($ruc <> $identificacionComprador . '') 
            {
                $mensaje = "Esta Factura NO fue Emitida a esta Empresa";
                $tipo_mesaje = 'error';
                $oReturn->script('alerts("' . $mensaje . '", "' . $tipo_mesaje . '");');
            }
            else
            {
                if ($tipo == '2') {
                    if ($ruc == $identificacionComprador . '') {
                        $mensaje = "Validacion ejecutada correctamente";
                        $tipo_mesaje = 'success';
                        $oReturn->script('alerts("' . $mensaje . '", "' . $tipo_mesaje . '");');
                        $oReturn->assign('auto_prove', 'value', $numeroAutorizacion[$pos]);
                        $oReturn->assign('serie', 'value', $serie);
                        $oReturn->assign('factura', 'value', $secuencial);
                        $oReturn->assign('valor_grab12b', 'value', $valor_grab12b);
                        $oReturn->assign('valor_grab0s', 'value', $valor_grab0s);
                        $oReturn->assign('valor_grab12t', 'value', $totalbien);
                        $oReturn->assign('valor_grab0t', 'value', $totalserv);
                        $oReturn->script('totales(this)');
                        $oReturn->script('totales1(this)');
                    } else {
                        $mensaje = 'El numero de identificacion del Proveedor: ' . $ruc . ' no coincide con la identificacion del archivo xml: ' . $identificacionComprador;
                        $tipo_mesaje = 'info';
                        $oReturn->script('alerts("' . $mensaje . '", "' . $tipo_mesaje . '");');
                    }
                } else {

                    //if($ruc == $identificacionComprador.''){
                    $sql = "select clpv_cod_clpv, clpv_nom_clpv,  clpv_ruc_clpv,
                            clpv_cod_fpagop, clpv_cod_tpago, clpv_pro_pago, clpv_etu_clpv, clpv_cod_cuen,
                            clpv_cod_vend, clpv_cot_clpv, clpv_pre_ven
                                    from saeclpv where 
                                    clpv_ruc_clpv='$identificacionProveedor' and 
                                    clpv_clopv_clpv='PV' and 
                                    clpv_cod_empr='$idempresa'";
                    $cod_clpv = consulta_string($sql, 'clpv_cod_clpv', $oIfx, 0);
                    $clpv_cod_clpv     = consulta_string($sql, 'clpv_cod_clpv', $oIfx, 0);
                    $clpv_nom_clpv     = consulta_string($sql, 'clpv_nom_clpv', $oIfx, '');
                    $clpv_ruc_clpv     = consulta_string($sql, 'clpv_ruc_clpv', $oIfx, '');
                    $clpv_cod_fpagop = consulta_string($sql, 'clpv_cod_fpagop', $oIfx, '');
                    $clpv_cod_tpago = consulta_string($sql, 'clpv_cod_tpago', $oIfx, 0);
                    $clpv_pro_pago  = consulta_string($sql, 'clpv_pro_pago', $oIfx, 0);
                    $clpv_etu_clpv  = consulta_string($sql, 'clpv_etu_clpv', $oIfx, 0);
                    $clpv_cod_vend  = consulta_string($sql, 'clpv_cod_vend', $oIfx, 0);
                    $clpv_cot_clpv  = consulta_string($sql, 'clpv_cot_clpv', $oIfx, 0);
                    $clpv_pre_ven   = consulta_string($sql, 'clpv_pre_ven', $oIfx, 0);

                    //	echo $sql;exit;
                    if ($cod_clpv > 0) {
                        $sql = "SELECT tran_cod_tran, trans_tip_comp, tran_des_tran
                                    FROM saetran WHERE
                                    (trans_tip_comp is not null ) AND
                                    tran_cod_empr = $idempresa AND
                                    tran_cod_sucu = $sucursal and trans_tip_comp='$codDoc'
                                    order by 2";

                        $tran_cod_tran = consulta_string($sql, 'tran_cod_tran', $oIfx, 0);
                        //$oReturn->alert($identificacionProveedor);

                        //						$oReturn->script('datos_clpv('.$cod_clpv.', \'' . $clpv_nom_clpv . '\' ,  \'' . $identificacionProveedor . '\' )');

                        $tipoRuc = $aForm['tipoRuc'];

                        $sql = "select emai_ema_emai from saeemai where emai_cod_empr = $idempresa and
                                                        emai_cod_clpv = $cod_clpv ";
                        $emai_ema_emai = consulta_string($sql, 'emai_ema_emai', $oIfx, '');

                        $oReturn->assign('ruc', 'value', $identificacionProveedor);
                        $oReturn->assign('correo_prove', 'value', $emai_ema_emai);

                        $oReturn->assign('tipo_pago', 'value', $clpv_cod_tpago);
                        $oReturn->assign('forma_pago1', 'value', $clpv_cod_fpagop);

                        $oReturn->assign('fecha_pedido', 'value', date('Y-m-d'));
                        $oReturn->assign('fecha_entrega', 'value', $fechaEmision);
                        $oReturn->assign('fecha_regc', 'value', $fechaEmision);


                        $oReturn->assign('fecha_inicio', 'value', $fechaEmision);
                        $oReturn->assign('fecha_final', 'value', $fechaEmision);

                        $oReturn->assign('plazo', 'value', 0);
                        $oReturn->assign('dias_fp', 'value', 0);



                        $oReturn->assign('valor_grab12b', 'value', $valor_grab12b);
                        $oReturn->assign('valor_grab0s', 'value', $valor_grab0s);
                        $oReturn->assign('valor_grab12t', 'value', $totalbien);
                        $oReturn->assign('valor_grab0t', 'value', $totalserv);
                        //$oReturn->assign('tipo_factura', 'value', '1');


                        //$oReturn->script('cargar_factura();');                        

                        $oReturn->assign('serie_prove', 'value', $serie);
                        $oReturn->assign('factura', 'value', $secuencial);
                        $oReturn->assign('auto_prove', 'value', $numeroAutorizacion[$pos]);

                        $sql_control = "select count(*) as contador from saeminv where minv_cod_empr = $idempresa and
                                        minv_cod_sucu = $sucursal and
                                        minv_ser_docu = '$serie' and  
                                        minv_cod_clpv = $cod_clpv and	
                                        minv_est_minv <> '0' and 
                                        minv_fac_prov = '$secuencial'";
                        $contador_ = consulta_string($sql_control, 'contador', $oIfx, '');

                        $sql_control_ = "select count(*) as cont
                                        from saefprv  where 
                                        fprv_cod_empr     = $idempresa
                                        and fprv_cod_sucu = $sucursal
                                        and fprv_cod_clpv= $cod_clpv
                                        and fprv_num_fact = '$secuencial'
                                        and fprv_num_seri = '$serie' ";
                        $contador_1 = consulta_string($sql_control_, 'cont', $oIfx, '');

                        if ($clpv_etu_clpv == 1) {
                            $clpv_etu_clpv = 'S';
                        } else {
                            $clpv_etu_clpv = 'N';
                        }

                        if (empty($clpv_pro_pago)) {
                            $clpv_pro_pago = 0;
                        }

                        // FECHA DE VENCIMIENTO
                        $fecha_venc = (sumar_dias_func(date("Y-m-d"), $prove_dia)); //  Y/m/d
                        list($a, $b, $c) = explode('/', $fecha_venc);
                        $fecha_venc = $a . '-' . $b . '-' . $c;

                        //direccion
                        $sql = "select dire_dir_dire from saedire where dire_cod_empr = $idempresa and dire_cod_clpv = $clpv_cod_clpv";
                        $dire = consulta_string_func($sql, 'dire_dir_dire', $oIfxA, '');

                        //telefono
                        $sql = "select tlcp_tlf_tlcp from saetlcp where tlcp_cod_empr = $idempresa and tlcp_cod_clpv = $clpv_cod_clpv";
                        $telefono = consulta_string_func($sql, 'tlcp_tlf_tlcp', $oIfxA, '');

                        // AUTORIZACION PROVE
                        $sql = "select  max(coa_fec_vali) as coa_fec_vali, coa_aut_usua, coa_seri_docu, coa_fact_ini, coa_fact_fin
                                from saecoa where
                                clpv_cod_empr = $idempresa and
                                clpv_cod_clpv = $clpv_cod_clpv group by coa_fec_vali,2,3,4,5 ";
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

                        //correo
                        $sql = "select emai_ema_emai from saeemai where
                                emai_cod_empr = $idempresa and
                                emai_cod_clpv = $clpv_cod_clpv ";
                        $correo = consulta_string_func($sql, 'emai_ema_emai', $oIfxA, '');

                        $oReturn->script('datos_clpv( \'' . $cod_clpv . '\', \'' . $clpv_nom_clpv . '\' , \'' . $identificacionProveedor . '\',  \'' . $dire . '\',
                                                                \'' . $telefono . '\',      \'' . $celular . '\',        \'' . $vendedor . '\',       \'' . $contacto . '\',
                                                                \'' . $precio . '\',        \'' . $clpv_cod_fpagop . '\', \'' . $clpv_cod_tpago . '\', \'' . $fec_cadu_prove . '\',
                                                                \'' . $auto_prove . '\',    \'' . $serie_prove . '\',     \'' . $fecha_venc . '\',     \'' . $clpv_pro_pago . '\',
                                                                \'' . $clpv_etu_clpv . '\', \'' . $ini_prove . '\',       \'' . $fin_prove . '\',      \'' . $clpv_cod_cuen . '\',
                                                                \'' . $correo . '\'
                                                            )');


                        if ($contador_ > 0 || $contador_1 > 0) {
                            $mensaje = "Factura numero: " . $secuencial . " ya ingresada";
                            $tipo_mesaje = 'warning';
                            $oReturn->script('alerts("' . $mensaje . '", "' . $tipo_mesaje . '");');
                            $oReturn->script('totales();');
                            unset($_SESSION['aDataGird']);
                            $sHtml = "";
                            $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
                        } else {
                            $_SESSION['DATOS_FACT_PROV'] = array($serie, $secuencial, $numeroAutorizacion[$pos]);

                            $sql = "select ppvpr_cod_alte, ppvpr_cod_prod, prbo_cta_inv, prbo_cta_ideb  ,
                                                prbo_cod_unid
                                            from saeppvpr, saeprbo where 
                                            ppvpr_cod_prod  =  prbo_cod_prod and 
                                            ppvpr_cod_bode   =  prbo_cod_bode and 
                                            ppvpr_cod_empr   =  prbo_cod_empr and 
                                            ppvpr_cod_sucu   =  prbo_cod_sucu and 
                                            ppvpr_cod_clpv   =  '$cod_clpv' and 
                                            ppvpr_cod_sucu   =  '$sucursal'
                                            and ppvpr_cod_empr = '$idempresa' and 
                                            ppvpr_cod_empr   = '$idempresa' and 
                                            prbo_cod_empr    = '$idempresa' and 
                                            prbo_cod_sucu    = '$sucursal' ";
                            //$oReturn->alert($sql);
                            unset($arra_prov_pord);

                            $existen_datos = 'N';
                            if ($oIfx->Query($sql)) {
                                if ($oIfx->NumFilas() > 0) {
                                    do {
                                        $codigo         = $oIfx->f('ppvpr_cod_alte');
                                        $cuenta         = $oIfx->f('prbo_cta_inv');
                                        $cuenta_iva        = $oIfx->f('prbo_cta_ideb');
                                        $prbo_cod_unid  = $oIfx->f('prbo_cod_unid');
                                        $arra_prov_pord[$codigo] = array($oIfx->f('ppvpr_cod_prod'), $cuenta, $cuenta_iva, $prbo_cod_unid);
                                    } while ($oIfx->SiguienteRegistro());
                                    $existen_datos = 'S';
                                }
                            }

                            //  var_dump($arra_prov_pord);exit;
                            // unset($_SESSION['aDataGird_INV_MRECO']);
                            /*$aLabelGrid = array('Id', 'Bodega', 'Codigo Item', 'Descripcion', 'Unidad', 'Cantidad', 'Costo', 
                                                    'Impuesto', 'Dscto 1', 'Dscto 2', 'Dscto Gral', 'Total', 'Total Con Impuesto', 
                                                    'Modificar', 'Eliminar', 'Cuenta', 'Cuenta Impuesto', 'Lote', 'Fecha Ela', 'Fecha Cad', 
                                                    'Detalle', 'Precio');*/

                            $bandera    = true;
                            $productos_no = '';




                            /*
                            $sql_prod = "select prod_cod_prod, prbo_cta_inv, prbo_cta_ideb,
                            prbo_cod_unid
                            from saeprod, saeprbo where 
                            prod_cod_prod  =  prbo_cod_prod and 
                            prod_cod_empr   =  prbo_cod_empr and 
                            prod_cod_sucu   =  prbo_cod_sucu and 
                            prod_cod_sucu   =  '$sucursal' and
                            prod_cod_empr   = '$idempresa' and 
                            prbo_cod_empr    = '$idempresa' and 
                            prbo_cod_sucu    = '$sucursal' ";
                            // $oReturn->alert($sql);

                        

                            if($existen_datos == 'N'){
                                unset($arra_prov_pord);
                                if ($oIfx->Query($sql_prod)) {
                                    if ($oIfx->NumFilas() > 0) {
                                        do {
                                            $codigo         = $oIfx->f('prod_cod_prod');
                                            $cuenta         = $oIfx->f('prbo_cta_inv');
                                            $cuenta_iva        = $oIfx->f('prbo_cta_ideb');
                                            $prbo_cod_unid  = $oIfx->f('prbo_cod_unid');
                                            $arra_prov_pord[$codigo] = array($oIfx->f('prod_cod_prod'), $cuenta, $cuenta_iva, $prbo_cod_unid);
                                        } while ($oIfx->SiguienteRegistro());
                                    }   
                                } 
                            }
    */




                            if (empty($bodega)) {
                                $mensaje     = "Por favor Seleccionar Bodega..!!!";
                                $tipo_mesaje = 'info';
                                $oReturn->script('alerts("' . $mensaje . '", "' . $tipo_mesaje . '");');
                            } else {
                                unset($array_rd);
                                foreach ($xmlParse->detalles->detalle as $arreglo) {







                                    $sql_prod = "select prod_cod_prod, prbo_cta_inv, prbo_cta_ideb,
                                        prbo_cod_unid
                                        from saeprod, saeprbo where 
                                        prod_cod_prod  =  prbo_cod_prod and 
                                        prod_cod_empr   =  prbo_cod_empr and 
                                        prod_cod_sucu   =  prbo_cod_sucu and 
                                        prod_cod_prod   =  '$arreglo->codigoPrincipal' and
                                        prod_cod_sucu   =  '$sucursal' and
                                        prod_cod_empr   = '$idempresa' and 
                                        prbo_cod_empr    = '$idempresa' and 
                                        prbo_cod_sucu    = '$sucursal' ";
                                    // $oReturn->alert($sql);




                                    if ($existen_datos == 'N') {
                                        unset($arra_prov_pord);
                                        if ($oIfx->Query($sql_prod)) {
                                            if ($oIfx->NumFilas() > 0) {
                                                do {
                                                    $codigo         = $oIfx->f('prod_cod_prod');
                                                    $cuenta         = $oIfx->f('prbo_cta_inv');
                                                    $cuenta_iva        = $oIfx->f('prbo_cta_ideb');
                                                    $prbo_cod_unid  = $oIfx->f('prbo_cod_unid');
                                                    $arra_prov_pord[$codigo] = array($oIfx->f('prod_cod_prod'), $cuenta, $cuenta_iva, $prbo_cod_unid);
                                                } while ($oIfx->SiguienteRegistro());
                                            }
                                        }
                                        $oIfx->Free();
                                    }

                                    // print_r($arra_prov_pord);
                                    // exit;


                                    $cantidad = floatval($arreglo->cantidad);
                                    $costo    = floatval($arreglo->precioUnitario);
                                    $descuento = floatval($arreglo->descuento);
                                    $iva      = floatval($arreglo->impuestos->impuesto->tarifa);
                                    $pro      = "'" . $arreglo->codigoPrincipal . "'";
                                    $pro      = trim(str_replace("'", "", $pro));
                                    $descripcion = $arreglo->descripcion;
                                    $idproducto =  $arra_prov_pord[$pro][0];



                                    if ($idproducto != '') {
                                        $total_fac         = 0;
                                        $descuento_general = 0;
                                        $dsc1 = ($descuento * 100) / ($costo * $cantidad);
                                        $cuenta_inv        = $arra_prov_pord[$pro][1];
                                        //	echo $cuenta_inv;exit;
                                        $cuenta_iva        = $arra_prov_pord[$pro][2];
                                        $idunidad          = $arra_prov_pord[$pro][3];

                                        if ($descuento_general > 0) {
                                            // descto general
                                            $dsc3 = (($costo * $cantidad) - ($dsc1 - $dsc2) * $descuento_general) / 100;
                                            $total_fact_tmp = ((($costo * $cantidad) - ($descuento + $dsc2 + $dsc3)));
                                            $tmp = ((($costo * $cantidad) - ($descuento + $dsc2)));
                                        } else {
                                            // sin descuento general
                                            $total_fact_tmp = (($costo * $cantidad) - ($descuento + $dsc2));
                                            $tmp = $total_fact_tmp;
                                        }

                                        $total_fac = $tmp;

                                        // total con iva.

                                        if ($iva > 0) {
                                            $total_con_iva = round((($total_fac * $iva) / 100), 2) + $total_fac;
                                        } else {
                                            $total_con_iva = $total_fac;
                                        }

                                        //GUARDA LOS DATOS DEL DETALLE
                                        /*$cont = count($aDataGrid);									 

                                            $aDataGrid[$cont][$aLabelGrid[0]] = floatval($cont);
                                            $aDataGrid[$cont][$aLabelGrid[1]] = $bodega;
                                            $aDataGrid[$cont][$aLabelGrid[2]] = $idproducto;
                                            $aDataGrid[$cont][$aLabelGrid[3]] = $descripcion;
                                            $aDataGrid[$cont][$aLabelGrid[4]] = $idunidad;
                                            $aDataGrid[$cont][$aLabelGrid[5]] = $cantidad;  //$cantidad;
                                            $aDataGrid[$cont][$aLabelGrid[6]] = $costo; //costo;
                                            $aDataGrid[$cont][$aLabelGrid[7]] = $iva; //iva                
                                            $aDataGrid[$cont][$aLabelGrid[8]] = $dsc1; // desc1
                                            $aDataGrid[$cont][$aLabelGrid[9]] = 0; // dec2
                                            $aDataGrid[$cont][$aLabelGrid[10]] = $descuento_general;
                                            $aDataGrid[$cont][$aLabelGrid[11]] = $total_fac;
                                            $aDataGrid[$cont][$aLabelGrid[12]] = $total_con_iva;
                                            $aDataGrid[$cont][$aLabelGrid[13]] = '';
                                            $aDataGrid[$cont][$aLabelGrid[14]] = '';
                                            $aDataGrid[$cont][$aLabelGrid[15]] = '';
                                            $aDataGrid[$cont][$aLabelGrid[16]] = '';
                                            $aDataGrid[$cont][$aLabelGrid[17]] = '';
                                            $aDataGrid[$cont][$aLabelGrid[18]] = $cuenta_inv;
                                            $aDataGrid[$cont][$aLabelGrid[19]] = $cuenta_iva;
                                            $aDataGrid[$cont][$aLabelGrid[20]] = '';
                                            $aDataGrid[$cont][$aLabelGrid[21]] = '';                
                                            $aDataGrid[$cont][$aLabelGrid[22]] = 0; 
                                            
                                            */


                                        $descr = (string) $descripcion[0];

                                        $array_rd[] = array(
                                            $bodega,
                                            $idproducto,
                                            $descr,
                                            $idunidad,
                                            $cantidad,
                                            $costo,
                                            $iva,
                                            $dsc1,
                                            $descuento_general,
                                            $total_fac,
                                            $total_con_iva,
                                            $cuenta_inv,
                                            $cuenta_iva
                                        );
                                    } else {
                                        $bandera = false;
                                        $descripcion = str_replace('"', '', $descripcion);
                                        $productos_no .= '* ' . $descripcion;
                                    }
                                } // fin foreach

                                if ($bandera == true) {
                                    /*$sHtml = mostrar_grid();
                                        // $oReturn->script('totales();');
                                        $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
                                        */
                                    // $oReturn->script("cargar_rd();");
                                    $mensaje     = 'Validacion ejecutada correctamente';
                                    $tipo_mesaje = 'success';
                                    $oReturn->script('alerts("' . $mensaje . '", "' . $tipo_mesaje . '");');
                                    $oReturn->assign('clave_acceso', 'value', $numeroAutorizacion[$pos]);
                                    $oReturn->script('totales();');
                                } else {

                                    $mensaje = "Estos Producto(s) no existen en la bodega: " . $productos_no . " .Si los productos son de gasto ingresar en el modulo correspondiente";
                                    //$oReturn->alert($mensaje.'sdsd');
                                    $tipo_mesaje = 'info';
                                    $oReturn->script('alerts("' . $mensaje . '", "' . $tipo_mesaje . '");');
                                    unset($_SESSION['aDataGird_INV_MRECO']);
                                    $sHtml = "";
                                    $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
                                    $oReturn->script('totales();');
                                }
                            }
                        }
                    } else {
                        $mensaje = 'El proveedor no se encuentra registrado en el sistema';
                        $tipo_mesaje = 'info';
                        $oReturn->script('alerts("' . $mensaje . '", "' . $tipo_mesaje . '");');
                    }

                    //	}else{
                    //		$oReturn->alert('El numero de identificacion del Proveedor: '. $ruc . ' no coincide con la identificacion del archivo xml: ' .$identificacionComprador);
                    //	}
                }
            }
        } else {
            $informacionAdicional = (strtoupper($mensaje[$clave_acceso]->informacionAdicional));
            $informacionAdicional = preg_replace('([^A-Za-z0-9 ])', '', strtoupper($mensaje[$clave_acceso][0]->informacionAdicional));
            $informacionAdicional = htmlspecialchars_decode($informacionAdicional);
            $mensaje = 'Error...' . $informacionAdicional;
            $tipo_mesaje = 'info';
            $oReturn->alert("'.$mensaje.'");
        }
    } catch (SoapFault $e) {
        $mensaje = $pos . ' NO HUBO CONECCION AL SRI (AUTORIZAR)';
        $tipo_mesaje = 'info';
        $oReturn->script('alerts("' . $mensaje . '", "' . $tipo_mesaje . '");');
    }

    if (count($array_rd) > 0) {
        $_SESSION['ARRAY_RD'] = $array_rd;

        $oReturn->script("cargar_rd();");
    }


    return $oReturn;
}


function cargar_rd($aForm = '')
{
    // if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
    //Definiciones
    global $DSN_Ifx, $DSN;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }


    $oReturn = new xajaxResponse();


    $idempresa  = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];

    $aDataGrid  = $_SESSION['aDataGird_INV_MRECO'];
    $aLabelGrid = $_SESSION['aLabelGirdProd_INV_MRECO'];
    $array_rd   = $_SESSION['ARRAY_RD'];

    // print_r($array_rd);
    // exit;


    if (count($array_rd) > 0) {
        foreach ($array_rd as $val) {
            $bodega         = $val[0];
            $idproducto     = $val[1];
            $Descripcion    = $val[2];
            $idunidad       = $val[3];
            $cantidad       = $val[4];
            $costo          = $val[5];
            $iva            = $val[6];
            $dsc1           = $val[7];
            $descuento_general = $val[8];
            $total_fac         = $val[9];
            $total_con_iva     = $val[10];
            $cuenta_inv        = $val[11];
            $cuenta_iva        = $val[12];



            //GUARDA LOS DATOS DEL DETALLE
            $cont = count($aDataGrid);

            $aDataGrid[$cont][$aLabelGrid[0]] = floatval($cont);
            $aDataGrid[$cont][$aLabelGrid[1]] = $bodega;
            $aDataGrid[$cont][$aLabelGrid[2]] = $idproducto;
            $aDataGrid[$cont][$aLabelGrid[3]] = $Descripcion;
            $aDataGrid[$cont][$aLabelGrid[4]] = $idunidad;
            $aDataGrid[$cont][$aLabelGrid[5]] = $cantidad;  //$cantidad;
            $aDataGrid[$cont][$aLabelGrid[6]] = $costo; //costo;
            $aDataGrid[$cont][$aLabelGrid[7]] = $iva; //iva                
            $aDataGrid[$cont][$aLabelGrid[8]] = $dsc1; // desc1
            $aDataGrid[$cont][$aLabelGrid[9]] = 0; // dec2
            $aDataGrid[$cont][$aLabelGrid[10]] = $descuento_general;
            $aDataGrid[$cont][$aLabelGrid[11]] = $total_fac;
            $aDataGrid[$cont][$aLabelGrid[12]] = $total_con_iva;
            $aDataGrid[$cont][$aLabelGrid[13]] = '';
            $aDataGrid[$cont][$aLabelGrid[14]] = '';
            $aDataGrid[$cont][$aLabelGrid[15]] = '';
            $aDataGrid[$cont][$aLabelGrid[16]] = '';
            $aDataGrid[$cont][$aLabelGrid[17]] = '';
            $aDataGrid[$cont][$aLabelGrid[18]] = $cuenta_inv;
            $aDataGrid[$cont][$aLabelGrid[19]] = $cuenta_iva;
            $aDataGrid[$cont][$aLabelGrid[20]] = '';
            $aDataGrid[$cont][$aLabelGrid[21]] = '';
            $aDataGrid[$cont][$aLabelGrid[22]] = 0;
            $aDataGrid[$cont][$aLabelGrid[23]] = '';
        }

        $_SESSION['aDataGird_INV_MRECO'] = $aDataGrid;
        $sHtml = mostrar_grid();
        $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
        $oReturn->script('totales();');
    }

    return $oReturn;
}

// REPORT ORDEN DE COMPRA
function orden_compra_reporte($aForm = '')
{

    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $idempresa  = $_SESSION['U_EMPRESA'];
    $idsucursal = $aForm['sucursal'];

    $oReturn = new xajaxResponse();

    $sHtml .= ' <table class="table table-striped table-condensed" style="width: 90%; margin-bottom: 0px;" align="center">
                    <tr>
                        <td class="info">No</td>
						<td class="info" align="center">Orden Compra</td>
                        <td class="info" align="center">Proveedor</td>
                        <td class="info" align="center">Fecha</td>                        
                        <td class="info" align="center">Detalle</td>
						<td class="info" align="center">Total</td>
                    </tr>';

    $sql = "SELECT distinct( minv_num_comp),   minv_fmov,      clpv_nom_clpv,   minv_num_sec, minv_cod_clpv,  minv_dege_minv,
				(COALESCE(minv_tot_minv,0) - COALESCE(minv_dge_valo,0) + COALESCE(minv_iva_valo,0) + COALESCE(minv_otr_valo,0) - COALESCE(minv_fle_minv,0) + COALESCE(minv_val_ice,0) ) total
				FROM saeminv,    saeclpv,    saedmov   WHERE 
				minv_cod_clpv = clpv_cod_clpv  and  
				minv_num_comp = dmov_num_comp and  
				minv_est_minv = '1'  and 
				minv_cod_tran in  ( select defi_cod_tran from saedefi Where 
										defi_tip_defi  = '4' and 
										defi_cod_empr  = $idempresa and 
										defi_cod_modu  = 10)  AND  
				minv_cod_empr = $idempresa  AND  
				minv_cod_sucu = $idsucursal AND 
                --minv_tip_ord != 1 AND  
				clpv_cod_empr = $idempresa and
				dmov_can_dmov <> dmov_can_entr  and
				(( minv_cer_sn is null) or ( minv_cer_sn = 'N' ) )";
    //$oReturn->alert($sql);
    $i = 1;
    $total = 0;
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            do {
                $fec_oc     = fecha_mysql_func($oIfx->f('minv_fmov'));
                $minv_sec     = $oIfx->f('minv_num_sec');
                $clpv_cod     = $oIfx->f('minv_cod_clpv');
                $clpv_nom     = $oIfx->f('clpv_nom_clpv');
                $serial     = $oIfx->f('minv_num_comp');
                $monto         = round($oIfx->f('total'), 2);

                $sClass = ($sClass == 'off') ? $sClass = 'on' : $sClass = 'off';
                $sHtml .= '<tr height="20" class="' . $sClass . '"
										onMouseOver="javascript:this.className=\'link\';"
										style="cursor: hand !important; cursor: pointer !important;"
										onMouseOut="javascript:this.className=\'' . $sClass . '\';"
										onClick="javascript:guia_detalle(\'' . $id_cliente . '\',\'' . $guia_cod_guia . '\');">';

                $sHtml .= '<td>' . $i . '</td>';
                $sHtml .= '<td>' . $minv_sec . '</td>';
                $sHtml .= '<td>' . $clpv_nom . '</td>';
                $sHtml .= '<td align="right">' . $fec_oc . '</td>';
                $sHtml .= '<td align="right" >
								<div class="btn btn-primary btn-sm" onClick="javascript:cargar_oc_det_gen(\'' . $serial . '\', \'' . $idempresa . '\', \'' . $idsucursal . '\')" >
									<span class="glyphicon glyphicon-cog"></span>
									Detalle
								</div>
						   </td>';
                $sHtml .= '<td align="right">' . $monto . '</td>';
                $sHtml .= '</tr>';

                $i++;
                $total += $monto;
            } while ($oIfx->SiguienteRegistro());
            $sHtml .= '<tr height="25">';
            $sHtml .= '<td></td>';
            $sHtml .= '<td></td>';
            $sHtml .= '<td></td>';
            $sHtml .= '<td align="right"></td>';
            $sHtml .= '<td align="right" class="fecha_letra">TOTAL:</td>';
            $sHtml .= '<td align="right" class="fecha_letra">' . $total . '</td>';
            $sHtml .= '</tr>';
        }
    }
    $sHtml .= '</table>';

    $modal  = '<div id="mostrarmodal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">ORDEN DE COMPRA</h4>
                        </div>
                        <div class="modal-body">';
    $modal .= $sHtml;
    $modal .= '          </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
             </div>';

    $oReturn->assign("extra", "innerHTML", $modal);
    $oReturn->script("abre_modal();");

    return $oReturn;
}

function orden_compra_reporte_det($serial, $idempresa, $idsucursal, $aForm = '')
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo();
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $oReturn = new xajaxResponse();

    $sHtml .= ' <table class="table table-striped table-condensed" style="width: 90%; margin-bottom: 0px;" align="center">
                    <tr>
                        <td class="info">No</td>
						<td class="info" align="center">Codigo</td>
                        <td class="info" align="center">Producto</td>
                        <td class="info" align="center">Cantidad</td>                        
                        <td class="info" align="center">Costo</td>
						<td class="info" align="center">Total</td>
                    </tr>';

    $sql = "select dmov_cod_prod, dmov_cod_bode, dmov_cod_unid,
				dmov_can_dmov, dmov_cun_dmov, dmov_cto_dmov
				from saedmov where
				dmov_cod_empr = $idempresa and
				dmov_cod_sucu = $idsucursal and
				dmov_num_comp = $serial ";
    //$oReturn->alert($sql);
    $i = 1;
    $total = 0;
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            do {
                $codigo    = ($oIfx->f('dmov_cod_prod'));
                $sql = "select prod_nom_prod from saeprod where prod_cod_empr = $idempresa and prod_cod_prod = '$codigo' ";
                if ($oIfxA->Query($sql)) {
                    if ($oIfxA->NumFilas() > 0) {
                        $nom_prod  = htmlentities($oIfxA->f('prod_nom_prod'));
                    }
                }

                $cant      = $oIfx->f('dmov_can_dmov');
                $costo     = $oIfx->f('dmov_cun_dmov');
                $subt      = $oIfx->f('dmov_cto_dmov');

                $sClass = ($sClass == 'off') ? $sClass = 'on' : $sClass = 'off';
                $sHtml .= '<tr height="20" class="' . $sClass . '"
										onMouseOver="javascript:this.className=\'link\';"
										style="cursor: hand !important; cursor: pointer !important;"
										onMouseOut="javascript:this.className=\'' . $sClass . '\';"
										onClick="javascript:guia_detalle(\'' . $id_cliente . '\',\'' . $guia_cod_guia . '\');">';

                $sHtml .= '<td>' . $i . '</td>';
                $sHtml .= '<td>' . $codigo . '</td>';
                $sHtml .= '<td>' . $nom_prod . '</td>';
                $sHtml .= '<td align="right">' . $cant . '</td>';
                $sHtml .= '<td align="right">' . $costo . '</td>';
                $sHtml .= '<td align="right">' . $subt . '</td>';
                $sHtml .= '</tr>';

                $i++;
                $total += $subt;
            } while ($oIfx->SiguienteRegistro());
            $sHtml .= '<tr height="25">';
            $sHtml .= '<td></td>';
            $sHtml .= '<td></td>';
            $sHtml .= '<td align="right"></td>';
            $sHtml .= '<td align="right"></td>';
            $sHtml .= '<td align="right" class="fecha_letra">TOTAL:</td>';
            $sHtml .= '<td align="right" class="fecha_letra">' . $total . '</td>';
            $sHtml .= '</tr>';
        }
    }
    $sHtml .= '</table>';

    $modal  = '<div id="mostrarmodal2" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">ORDEN DE COMPRA DETALLE</h4>
                        </div>
                        <div class="modal-body">';
    $modal .= $sHtml;
    $modal .= '          </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
             </div>';

    $oReturn->assign("extra2", "innerHTML", $modal);
    $oReturn->script("abre_modal2();");

    return $oReturn;
}

function generaReporteCompras($aForm = '')
{
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse();

    //variables de sesion
    unset($_SESSION['ARRAY_ADJUNTOS_FPRV']);
    $idempresa = $_SESSION['U_EMPRESA'];

    //varibales del formulario
    $cliente = $aForm['cliente'];
    $codigo = $aForm['codigo_producto'];
    $sucursal = $aForm['sucursal'];

    try {

        $sHtml .= '<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							<h4 class="modal-title" id="myModalLabel">REPORTE DE COMPRAS PROVEEDOR</h4>
						</div>
						<div class="modal-body">';

        $sHtml .= '<table class="table table-striped table-bordered table-hover table-condensed">';
        $sHtml .= '<tr class="info">
						<td>N°</td>
						<td>PROVEEDOR</td>
						<td>FECHA</td>
						<td>FACTURA</td>
						<td>CODIGO</td>
						<td>PRODUCTO</td>
						<td>CANITDAD</td>
						<td>COSTO</td>
						<td>TOTAL</td>
					</tr>';

        $sqlTmp = '';
        if (!empty($codigo)) {
            $sqlTmp = " ";
        }
        $sql = "select m.minv_num_comp, m.minv_fmov, d.dmov_cod_prod, p.prod_nom_prod, 
				round(d.dmov_can_dmov,2) as cantidad, 
				round(d.dmov_cun_dmov,2) as costo, 
				c.clpv_ruc_clpv, c.clpv_nom_clpv, m.minv_fac_prov
				from saeminv m, saedmov d, saeclpv c, saeprod p
				where
				m.minv_cod_empr = d.dmov_cod_empr and
				m.minv_cod_sucu = d.dmov_cod_sucu and
				m.minv_num_comp = d.dmov_num_comp and
				c.clpv_cod_empr = m.minv_cod_empr and
				c.clpv_cod_clpv = m.minv_cod_clpv and
				d.dmov_cod_prod = p.prod_cod_prod and
				d.dmov_cod_empr = p.prod_cod_empr and
				d.dmov_cod_sucu = p.prod_cod_sucu and
				m.minv_cod_empr = $idempresa and
				m.minv_cod_sucu = $sucursal and
				c.clpv_clopv_clpv = 'PV' and
				m.minv_cod_tran = '002' and
				c.clpv_cod_clpv = $cliente and
				m.minv_est_minv <> '0' and
				d.dmov_cod_prod = '$codigo'
				order by 2 desc";
        //$oReturn->alert($sql);		   
        if ($oIfx->Query($sql)) {
            if ($oIfx->NumFilas() > 0) {
                $i = 1;
                $totalCant = 0;
                $totalCosto = 0;
                $granTotal = 0;
                unset($arrayCtrl);
                do {
                    $minv_num_comp = $oIfx->f('minv_num_comp');
                    $minv_fmov = cambioFecha($oIfx->f('minv_fmov'), 'mm/dd/aaaa', 'dd/mm/aaaa');
                    $dmov_cod_prod = $oIfx->f('dmov_cod_prod');
                    $prod_nom_prod = $oIfx->f('prod_nom_prod');
                    $cantidad = $oIfx->f('cantidad');
                    $costo = $oIfx->f('costo');
                    $clpv_ruc_clpv = $oIfx->f('clpv_ruc_clpv');
                    $clpv_nom_clpv = $oIfx->f('clpv_nom_clpv');
                    $minv_fac_prov = $oIfx->f('minv_fac_prov');

                    $total = $cantidad * $costo;

                    $arrayCtrl[$i] = $minv_fac_prov;

                    if ($i > 0) {
                        if ($arrayCtrl[$i] == $arrayCtrl[$i - 1]) {
                            $minv_fac_prov = '';
                            $clpv_nom_clpv = '';
                        }
                    }

                    $sHtml .= '<tr>
								   <td align="center">' . $i . '</td>
								   <td align="left">' . $clpv_nom_clpv . '</td>
								   <td align="left">' . $minv_fmov . '</td>
								   <td align="left">' . $minv_fac_prov . '</td>
								   <td align="left">' . $dmov_cod_prod . '</td>
								   <td align="left">' . $prod_nom_prod . '</td>
								   <td align="right">' . $cantidad . '</td>
								   <td align="right">' . $costo . '</td>
								   <td align="right">' . $total . '</td>
							   </tr>';
                    $i++;
                    $totalCant += $cantidad;
                    $totalCosto += $costo;
                    $granTotal += $total;
                } while ($oIfx->SiguienteRegistro());
                $sHtml .= '<tr class="danger">
							   <td align="right" colspan="6">TOTAL:</td>
							   <td align="right">' . $totalCant . '</td>
							   <td align="right">' . $totalCosto . '</td>
							   <td align="right">' . $granTotal . '</td>
						   </tr>';
            }
        }
        $oIfx->Free();

        $sHtml .= '</table>';

        $sHtml .= '</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
							<button type="button" class="btn btn-primary" data-dismiss="modal">Procesar</button>
						</div>
					</div>
				</div>';

        $oReturn->assign("miModal", "innerHTML", $sHtml);
    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

    return $oReturn;
}


// LOTES
function form_lote($id, $aForm = '')
{

    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $oReturn = new xajaxResponse();

    $idempresa     = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    $aDataGrid  = $_SESSION['aDataGird_INV_MRECO'];

    $prod_cod     = $aDataGrid[$id]['Codigo Item'];
    $prod_nom     = $aDataGrid[$id]['Descripcion'];
    $cantidad     = $aDataGrid[$id]['Cantidad'];
    $costo        = $aDataGrid[$id]['Costo'];
    $impuesto     = $aDataGrid[$id]['Impuesto'];
    $desc1      = $aDataGrid[$id]['Dscto 1'];
    $desc2      = $aDataGrid[$id]['Dscto 2'];
    $lote       = $aDataGrid[$id]['Serie'];

    $sql         = "select prod_nom_prod, COALESCE(prod_lot_sino,'N') as prod_lot_sino, COALESCE(prod_ser_prod, 'N') as prod_ser_prod
						from saeprod where 
						prod_cod_empr = $idempresa and 
						prod_cod_sucu = $idsucursal and 
						prod_cod_prod = '$prod_cod' ";
    $prod_lot_sino     = consulta_string_func($sql, 'prod_lot_sino', $oIfx, 'N');
    $prod_ser_prod     = consulta_string_func($sql, 'prod_ser_prod', $oIfx, 'N');


    if ($prod_lot_sino == 1 || $prod_lot_sino == 'S') {
        $prod_lot_sino = 'S';
    } else {
        $prod_lot_sino = 'N';
    }

    $cambiar_valor_serie = '';
    if ($prod_ser_prod == 1 || $prod_ser_prod == 'S') {
        $prod_ser_prod = 'S';
        $cambiar_valor_serie = 'readonly';
    } else {
        $prod_ser_prod = 'N';
    }

    $fu->AgregarCampoTexto('lote_tmp', 'Lote - Serie|left', false, '', 180, 100);
    $fu->AgregarCampoFecha('fecha_ela_tmp', 'Fecha Elaboracion|left', true, date('Y') . '/' . date('m') . '/' . date('d'));
    $fu->AgregarCampoFecha('fecha_cad_tmp', 'Fecha Caducidad|left', true, date('Y') . '/' . date('m') . '/' . date('d'));

    $sHtml .= '<table class="table table-striped table-condensed" style="width: 98%; margin-bottom: 0px;" align="center">';

    $sHtml .= '<tr height="35px">';
    $sHtml .= '<td>Codigo:</td>';
    $sHtml .= '<td class="fecha_letra">' . $prod_cod . '</td>';
    $sHtml .= '</tr>';

    $sHtml .= '<tr height="35px">';
    $sHtml .= '<td>Producto:</td>';
    $sHtml .= '<td class="fecha_letra">' . $prod_nom . '</td>';
    $sHtml .= '</tr>';

    $sHtml .= '<tr height="25">
                    <td>Cantidad:</td>
                    <td class="fecha_letra">
                        <input type="text" class="form-control input-sm" id="cantidad_mod" name="cantidad_mod" style="text-align:right" value="' . $cantidad . '"  onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = 0; " ' . $cambiar_valor_serie . ' />
                    </td>
                </tr>
                <tr height="25">
                    <td>Costo:</td>
                    <td class="fecha_letra">
                        <input type="text" class="form-control input-sm" id="costo_mod" name="costo_mod" style="text-align:right" value="' . $costo . '" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = 0; " />
                    </td>
                </tr>
                <tr height="25">
                    <td>Impuesto:</td>
                    <td class="fecha_letra">
                        <input type="text" class="form-control input-sm" id="imp_mod" name="imp_mod" style="text-align:right" value="' . $impuesto . '" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = 0; " />
                    </td>
                </tr>
                <tr height="25">
                    <td>Descuento 1:</td>
                    <td class="fecha_letra">
                        <input type="text" class="form-control input-sm" id="desc1_mod" name="desc1_mod" style="text-align:right" value="' . $desc1 . '" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = 0; " />
                    </td>
                </tr>
                <tr height="25">
                    <td>Descuento 2:</td>
                    <td class="fecha_letra">
                        <input type="text" class="form-control input-sm" id="desc2_mod" name="desc2_mod" style="text-align:right" value="' . $desc2 . '" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = 0; " />
                    </td>
                </tr>';


    if ($prod_lot_sino == 'S' || $prod_ser_prod == 'S') {
        $sHtml .= '<tr height="25">';
        $sHtml .= '<td>' . $fu->ObjetoHtmlLBL('lote_tmp') . '</td>';
        $sHtml .= '<td><input type="text" class="form-control input-sm" id="lote_tmp" name="lote_tmp" style="text-align:right" value="' . $lote . '"  ' . $cambiar_valor_serie . ' /></td>';
        $sHtml .= '</tr>';
    }

    if ($prod_lot_sino == 'S') {
        $sHtml .= '<tr height="25">';
        $sHtml .= '<td>' . $fu->ObjetoHtmlLBL('fecha_ela_tmp') . '</td>';
        $sHtml .= '<td><input type="date" name = "fecha_ela_tmp" id="fecha_ela_tmp" value="' . $fecha . '"></td>';
        $sHtml .= '</tr>';

        $sHtml .= '<tr height="25">';
        $sHtml .= '<td>' . $fu->ObjetoHtmlLBL('fecha_cad_tmp') . '</td>';
        $sHtml .= '<td><input type="date" name = "fecha_cad_tmp" id="fecha_cad_tmp" value="' . $fecha . '"></td>';
        $sHtml .= '</tr>';
    }

    $sHtml .= '</table>';

    $modal  = '<div id="mostrarmodal3" class="modal fade" role="dialog">
                <div class="modal-dialog modal-ms">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">PRODUCTO</h4>
                        </div>
                        <div class="modal-body">';
    $modal .= $sHtml;
    $modal .= '          </div>
                        <div class="modal-footer">
							<div class="btn btn-primary btn-sm" onclick="procesar_lote(' . $id . ');">
								<span class="glyphicon glyphicon-list"></span>
								Procesar
							</div>
									
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
             </div>';

    $oReturn->assign("extra3", "innerHTML", $modal);
    $oReturn->script("abre_modal3();");

    return $oReturn;
}

function procesar_lote($id = '', $aForm = '')
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN, $DSN_Ifx;

    $oCnx = new Dbo();
    $oCnx->DSN = $DSN;
    $oCnx->Conectar();

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $aDataGrid  = $_SESSION['aDataGird_INV_MRECO'];
    $aLabelGrid = $_SESSION['aLabelGirdProd_INV_MRECO'];

    $oReturn     = new xajaxResponse();

    $idempresa     = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    $decimal     = 6;
    $lote               = $aForm['lote_tmp'];
    $fecha_ela          = $aForm['fecha_ela_tmp'];
    $fecha_cad          = $aForm['fecha_cad_tmp'];
    $cantidad           = $aForm['cantidad_mod'];
    $costo              = $aForm['costo_mod'];
    $iva                = $aForm['imp_mod'];
    $descuento          = $aForm['desc1_mod'];
    $descuento_2        = $aForm['desc2_mod'];
    $descuento_general  = $aForm['descuento_general'];

    // TOTAL
    $total_fac  = 0;
    $dsc1       = ($costo * $cantidad * $descuento) / 100;
    $dsc2       = ((($costo * $cantidad) - $dsc1) * $descuento_2) / 100;
    if ($descuento_general > 0) {
        // descto general
        $dsc3           = ((($costo * $cantidad) - $dsc1 - $dsc2) * $descuento_general) / 100;
        $total_fact_tmp = ((($costo * $cantidad) - ($dsc1 + $dsc2 + $dsc3)));
        $tmp            = ((($costo * $cantidad) - ($dsc1 + $dsc2)));
    } else {
        // sin descuento general
        $total_fact_tmp = ((($costo * $cantidad) - ($dsc1 + $dsc2)));
        $tmp            = $total_fact_tmp;
    }

    $total_fac = round($total_fact_tmp, 2);

    // total con iva
    if ($iva > 0) {
        $total_con_iva = round((($total_fac * $iva) / 100), 2) + $total_fac;
    } else {
        $total_con_iva = $total_fac;
    }

    //MODIFICA Y EXTRAE LOS DATOS DEL DATAGRID A LA VENTANA  DETALLE
    $aDataGrid[$id][$aLabelGrid[0]]  = floatval($id);
    $aDataGrid[$id][$aLabelGrid[1]]  = $aDataGrid[$id]['Bodega'];
    $aDataGrid[$id][$aLabelGrid[2]]  = $aDataGrid[$id]['Codigo Item'];
    $aDataGrid[$id][$aLabelGrid[3]]  = $aDataGrid[$id]['Descripcion'];
    $aDataGrid[$id][$aLabelGrid[4]]  = $aDataGrid[$id]['Unidad'];
    $aDataGrid[$id][$aLabelGrid[5]]  = $cantidad;  //$cantidad;
    $aDataGrid[$id][$aLabelGrid[6]]  = $costo; //costo;
    $aDataGrid[$id][$aLabelGrid[7]]  = $iva; //iva
    $aDataGrid[$id][$aLabelGrid[8]]  = $descuento; // desc1
    $aDataGrid[$id][$aLabelGrid[9]]  = $descuento_2; // dec2
    $aDataGrid[$id][$aLabelGrid[10]] = $descuento_general;
    $aDataGrid[$id][$aLabelGrid[11]] = $total_fac;
    $aDataGrid[$id][$aLabelGrid[12]] = $total_con_iva;
    $aDataGrid[$id][$aLabelGrid[13]] = $lote;
    $aDataGrid[$id][$aLabelGrid[14]] = $fecha_ela;
    $aDataGrid[$id][$aLabelGrid[15]] = $fecha_cad;
    $aDataGrid[$id][$aLabelGrid[16]] = '';
    $aDataGrid[$id][$aLabelGrid[17]] = '';
    $aDataGrid[$id][$aLabelGrid[18]] = $aDataGrid[$id]['Cuenta'];
    $aDataGrid[$id][$aLabelGrid[19]] = $aDataGrid[$id]['Cuenta Impuesto'];
    $aDataGrid[$id][$aLabelGrid[20]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/pencil3.png"
                                            title = "Presione aqui para Modificar"
                                            style="cursor: hand !important; cursor: pointer !important;"
                                            onclick="agregar_detalle(1);"
                                            alt="Modificar"
                                            align="bottom" />';
    $aDataGrid[$id][$aLabelGrid[21]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/delete_1.png"
                                            title = "Presione aqui para Eliminar"
                                            style="cursor: hand !important; cursor: pointer !important;"
                                            onclick="javascript:xajax_elimina_detalle(' . $id . ');"
                                            alt="Eliminar"
                                            align="bottom" />';

    $_SESSION['aDataGird_INV_MRECO'] = $aDataGrid;
    $sHtml = mostrar_grid();
    $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
    $oReturn->script('totales();');
    $oReturn->script('cerrar_ventana();');

    return $oReturn;
}


// Precios
function form_precio_inv($id, $aForm = '')
{

    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $oReturn = new xajaxResponse();

    $idempresa     = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    $aDataGrid  = $_SESSION['aDataGird_INV_MRECO'];
    unset($_SESSION['U_SAEPPR_INV']);

    $prod_cod     = $aDataGrid[$id]['Codigo Item'];
    $bode_cod     = $aDataGrid[$id]['Bodega'];
    $cant         = $aForm[$id . '_cantidad'];




    $sql         = "select prod_nom_prod, COALESCE(prod_lot_sino,'N') as prod_lot_sino, COALESCE(prod_ser_prod, 'N') as prod_ser_prod
						from saeprod where 
						prod_cod_empr = $idempresa and 
						prod_cod_sucu = $idsucursal and 
						prod_cod_prod = '$prod_cod' ";
    $prod_nom         = consulta_string_func($sql, 'prod_nom_prod', $oIfx, 0);

    $sql = "select nomp_cod_nomp, nomp_nomb_nomp from saenomp where nomp_cod_empr = $idempresa ";
    unset($array_nomp);
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            do {
                $nomp_cod_nomp  = $oIfx->f('nomp_cod_nomp');
                $nomp_nomb_nomp = $oIfx->f('nomp_nomb_nomp');

                $array_nomp[] = array($nomp_cod_nomp, $nomp_nomb_nomp);
            } while ($oIfx->SiguienteRegistro());
        }
    }
    $oIfx->Free();

    $sHtml .= ' <table class="table table-striped table-condensed" style="width: 98%; margin-bottom: 0px;" align="center">';
    $sHtml .= '<tr height="25">';
    $sHtml .= '<td>Codigo:</td>';
    $sHtml .= '<td class="fecha_letra">' . $prod_cod . '</td>';
    $sHtml .= '</tr>';

    $sHtml .= '<tr height="25">';
    $sHtml .= '<td>Producto:</td>';
    $sHtml .= '<td class="fecha_letra">' . $prod_nom . '</td>';
    $sHtml .= '</tr>';


    if (count($array_nomp) > 0) {
        $sHtml .= '<tr height="25">';
        $sHtml .= '<td colspan="2">';
        $sHtml .= ' <table class="table table-striped table-condensed" style="width: 99%; margin-bottom: 0px;" align="center">';
        $sHtml .= '<tr height="25">';
        $sHtml .= '<td class="bg-success" align="center">N.-</td>';
        $sHtml .= '<td class="bg-success" align="center">Tipo Precio</td>';
        $sHtml .= '<td class="bg-success" align="center">Precio Actual</td>';
        $sHtml .= '<td class="bg-success" align="center">Precio</td>';
        $sHtml .= '</tr>';

        $i = 1;
        unset($array_precio);
        foreach ($array_nomp as $val) {
            $nomp_cod_nomp  = $val[0];
            $nomp_nomb_nomp = $val[1];

            $sql = "select ppr_cod_ppr, ppr_cod_prod, ppr_pre_raun, ppr_cod_nomp, ppr_imp_ppr
						from saeppr where
						ppr_cod_empr = $idempresa and
						ppr_cod_sucu = $idsucursal and
						ppr_cod_bode = $bode_cod and
						ppr_cod_prod = '$prod_cod' and
						ppr_cod_nomp = $nomp_cod_nomp ";

            $ppr_cod_ppr = 0;
            $ppr_pre_raun = 0;
            if ($oIfx->Query($sql)) {
                if ($oIfx->NumFilas() > 0) {
                    $ppr_cod_ppr  = $oIfx->f('ppr_cod_ppr');
                    $ppr_pre_raun = $oIfx->f('ppr_pre_raun');
                    $array_precio[] = array($ppr_cod_ppr, $ppr_pre_raun, $prod_cod, $bode_cod, $nomp_cod_nomp);
                } else {
                    $array_precio[] = array($ppr_cod_ppr, $ppr_pre_raun, $prod_cod, $bode_cod, $nomp_cod_nomp);
                }
            }
            $oIfx->Free();

            $precio     = $aDataGrid[$id]["pvp" . $i];
            if (empty($precio)) {
                $precio     = $ppr_pre_raun;
            }

            $fu->AgregarCampoNumerico($nomp_cod_nomp, 'Precio|left', false, $precio, 80, 100);

            $sHtml .= '<tr>';
            $sHtml .= '<td align="right">' . $nomp_cod_nomp . '</td>';
            $sHtml .= '<td>' . $nomp_nomb_nomp . '</td>';
            $sHtml .= '<td align="right">' . $ppr_pre_raun . '</td>';
            $sHtml .= '<td align="right">' . $fu->ObjetoHtml($nomp_cod_nomp) . '</td>';
            $sHtml .= '</tr>';

            $i++;
        } // fin foreach
        $sHtml .= '</table></td></tr>';
    } // fin if

    $_SESSION['U_SAEPPR_INV'] = $array_precio;

    $sHtml .= '</table>';

    $modal  = '<div id="mostrarmodal4" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg" style="width: 40%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">PRECIO DE PRODUCTO</h4>
                        </div>
                        <div class="modal-body">';
    $modal .= $sHtml;
    $modal .= '          </div>
                        <div class="modal-footer">
							<div class="btn btn-primary btn-sm" onclick="procesar_precio(' . $id . ');">
								<span class="glyphicon glyphicon-list"></span>
								Procesar
							</div>
									
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
             </div>';

    $oReturn->assign("precio_modal", "innerHTML", $modal);
    $oReturn->script("abre_modal4();");

    return $oReturn;
}



// Guardar precios
function guardar_precio_inv($id = '', $aForm = '')
{

    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $oReturn = new xajaxResponse();



    $idempresa     = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    $aDataGrid  = $_SESSION['aDataGird_INV_MRECO'];



    // $oIfx->QueryT('refresh materialized view listado_productos;');


    // $_SESSION['U_SAEPPR_INV'] = $array_precio;
    // $oReturn->assign("precio_modal", "innerHTML", $modal);
    $oReturn->script("guardar_pedido($id);");

    return $oReturn;
}




function procesar_precio_inv($id = '', $aForm = '')
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN, $DSN_Ifx;

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn     = new xajaxResponse();

    $idempresa     = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    $array_inv  = $_SESSION['U_SAEPPR_INV'];

    if (count($array_inv) > 0) {
        try {
            // commit
            $oIfx->QueryT('BEGIN WORK;');

            foreach ($array_inv as $val) {
                $ppr_cod_ppr     = $val[0];
                $ppr_pre_raun    = $val[1];
                $prod_cod        = $val[2];
                $bode_cod        = $val[3];
                $nomp_cod_nomp  = $val[4];
                $precio            = $aForm[$nomp_cod_nomp];

                if ($ppr_cod_ppr > 0) {
                    // UPDATE
                    $sql = "update saeppr set ppr_pre_raun = '$precio' where
								ppr_cod_empr = $idempresa and
								ppr_cod_sucu = $idsucursal and
								ppr_cod_bode = $bode_cod and
								ppr_cod_prod = '$prod_cod' and
								ppr_cod_nomp = $nomp_cod_nomp and
								ppr_cod_ppr  = $ppr_cod_ppr	";
                } elseif ($ppr_cod_ppr == 0) {
                    // INGRESO
                    $sql = "select  max(ppr_cod_ppr) ppr_cod
								from saeppr where
								ppr_cod_empr = $idempresa and
								ppr_cod_sucu = $idsucursal and
								ppr_cod_bode = $bode_cod and
								ppr_cod_prod = '$prod_cod' ";
                    $serial    = consulta_string_func($sql, 'ppr_cod', $oIfx, 0) + 1;

                    $sql = "insert into saeppr ( ppr_cod_ppr, 		ppr_cod_prod, 		ppr_cod_bode,		ppr_cod_empr,
												 ppr_cod_sucu, 		ppr_pre_raun,		ppr_cod_nomp )
										values ( $serial,			'$prod_cod',		$bode_cod,			$idempresa,
												 $idsucursal,		$precio,		    $nomp_cod_nomp
											   )";
                }

                $oIfx->QueryT($sql);
            } // fin fpreach
            $oIfx->QueryT('refresh materialized view listado_productos;');
            $oIfx->QueryT('COMMIT WORK;');
        } catch (Exception $e) {
            // rollback
            $oIfx->QueryT('ROLLBACK WORK;');
            $oReturn->alert($e->getMessage());
        }
    } // fin if

    return $oReturn;
}

//// secuencial rete
function cargar_secuencial_rete($aForm = "")
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();
    $idsucursal = $aForm['sucursal'];
    $tipo_retencion = $aForm['tipo_retencion'];
    $idempresa = $_SESSION['U_EMPRESA'];

    $oReturn = new xajaxResponse();


    $sql = "select retp_sec_retp, retp_num_seri, retp_fech_cadu from saeretp where 
							retp_cod_empr = $idempresa and
							retp_cod_sucu = $idsucursal and
							retp_act_retp = 1  and retp_elec_sn='$tipo_retencion'";
    //$oReturn->alert($sql);
    $num_rete = consulta_string($sql, 'retp_sec_retp', $oIfx, '');
    $num_rete = secuencial(2, '', $num_rete, 9);

    $oReturn->assign("ret_num", "value", $num_rete);



    return $oReturn;
}


function cargar_electronica($aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $idempresa   = $_SESSION['U_EMPRESA'];
    $oReturn      = new xajaxResponse();

    $electronica = $aForm['electronica'];
    $idsucursal  = $aForm['sucursal'];

    if ($electronica == 'S') {
        $sql = "select retp_sec_retp, retp_num_seri, retp_fech_cadu , retp_num_auto
							from saeretp where 
							retp_cod_empr = $idempresa and
							retp_cod_sucu = $idsucursal and
							retp_act_retp = 1 and 
							retp_elec_sn  = 'S' ";
        $num_rete     = consulta_string($sql, 'retp_sec_retp', $oIfx, '');
        $num_rete     = secuencial(2, '', $num_rete, 9);
        $seri_rete       = consulta_string($sql, 'retp_num_seri', $oIfx, '');
        $ret_fec_auto = fecha_mysql_func_(consulta_string($sql, 'retp_fech_cadu', $oIfx, date("Y-m-d")));
        $rete_auto    = consulta_string($sql, 'retp_num_auto', $oIfx, '');

        $oReturn->script("automatico();");
    } else {
        $sql = "select retp_sec_retp, retp_num_seri, retp_fech_cadu , retp_num_auto
							from saeretp where 
							retp_cod_empr = $idempresa and
							retp_cod_sucu = $idsucursal and
							retp_act_retp = 1 and 
							COALESCE(retp_elec_sn,'N')  = 'N' ";
        $num_rete     = consulta_string($sql, 'retp_sec_retp', $oIfx, '');
        $num_rete     = secuencial(2, '', $num_rete, 9);
        $seri_rete       = consulta_string($sql, 'retp_num_seri', $oIfx, '');
        $ret_fec_auto = fecha_mysql_func_(consulta_string($sql, 'retp_fech_cadu', $oIfx, date("Y-m-d")));
        $rete_auto    = consulta_string($sql, 'retp_num_auto', $oIfx, '');
        $oReturn->script("manual();");
    }

    $oReturn->assign("ret_num",   "value", $num_rete);
    $oReturn->assign("serie_rete", "value", $seri_rete);
    $oReturn->assign("auto_rete",  "value", $rete_auto);
    $oReturn->assign("cad_rete",   "value", $ret_fec_auto);

    return $oReturn;
}


function cargar_digito_ret($aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    //Definiciones
    $oReturn = new xajaxResponse();
    $idempresa = $_SESSION['U_EMPRESA'];

    // VARIABLES
    $form = $aForm['ret_num'];

    $sql = "select  pccp_num_digi from saepccp where
                    pccp_cod_empr = $idempresa ";
    $num_digito = consulta_string($sql, 'pccp_num_digi', $oIfx, 9);
    $len = strlen($form);
    $ceros = cero_mas('0', abs($num_digito - $len));
    $valor = $ceros . $form;

    $oReturn->assign('ret_num', "value", $valor);
    return $oReturn;
}



// ADJUNTOS
function archivosAdjuntos($aForm = '')
{
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse();

    //variables de sesion
    unset($_SESSION['ARRAY_ADJUNTOS_FPRV']);
    $idempresa = $_SESSION['U_EMPRESA'];

    //varibales del formulario
    $cliente = $aForm['cliente'];
    $sucursal = $aForm['sucursal'];

    try {

        $ifu->AgregarCampoTexto('titulo', 'Titulo|left', false, '', 200, 200);
        $ifu->AgregarComandoAlEscribir('titulo', 'form1.titulo.value=form1.titulo.value.toUpperCase();');

        $ifu->AgregarCampoArchivo('archivo_adj', 'Archivo|left', false, '', 100, 100, '');

        $grid = '';
        $grid = mostrar_gridAdj();

        $sHtml .= '<div id="mostrarmodal5" class="modal fade" role="dialog">
					<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							<h4 class="modal-title" id="myModalLabel">SUBIR ARCHIVOS ADJUNTOS</h4>
						</div>
						<div class="modal-body">';

        $sHtml .= '<table class="table table-striped table-condensed" style="width: 99%; margin-bottom: 0px;" align="center">';
        $sHtml .= '<tr>';
        $sHtml .= '<td>' . $ifu->ObjetoHtmlLBL('titulo') . '</td>';
        $sHtml .= '<td>' . $ifu->ObjetoHtml('titulo') . '</td>';
        $sHtml .= '<td>' . $ifu->ObjetoHtmlLBL('archivo_adj') . '</td>';
        $sHtml .= '<td>' . $ifu->ObjetoHtml('archivo_adj') . '</td>';
        $sHtml .= '<td align="center">
						<div class="btn btn-success btn-sm" onclick="agregarArchivo();">
							<span class="glyphicon glyphicon-plus-sign"></span>
							Agregar
						</div>
					<td>';
        $sHtml .= '</tr>';
        $sHtml .= '</table>';

        $sHtml .= '<div id="gridArchivos" style="margin-top: 20px;">' . $grid . '</div>';

        $sHtml .= '</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
							<button type="button" class="btn btn-primary" data-dismiss="modal">Procesar</button>
						</div>
					</div>
				</div>
				</div>';

        $oReturn->assign("miAdjunto", "innerHTML", $sHtml);

        $oReturn->script("abre_modal5();");
    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

    return $oReturn;
}


function agrega_modifica_gridAdj($nTipo = 0,  $aForm = '', $id = '', $total_fact = '')
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN, $DSN_Ifx;

    $oCnx = new Dbo();
    $oCnx->DSN = $DSN;
    $oCnx->Conectar();

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $oReturn = new xajaxResponse();

    $aDataGrid = $_SESSION['aDataGirdAdj'];

    $aLabelGrid = array('Id', 'Titulo', 'Archivo', 'Eliminar');

    $archivo = substr($aForm['archivo_adj'], 3);
    $titulo  = $aForm['titulo'];

    //GUARDA LOS DATOS DEL DETALLE
    $cont = count($aDataGrid);

    $aDataGrid[$cont][$aLabelGrid[0]] = floatval($cont);
    $aDataGrid[$cont][$aLabelGrid[1]] = $titulo;
    $aDataGrid[$cont][$aLabelGrid[2]] = $archivo;
    $aDataGrid[$cont][$aLabelGrid[3]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/delete_1.png"
												title = "Presione aqui para Eliminar"
												style="cursor: hand !important; cursor: pointer !important;"
												onclick="javascript:xajax_elimina_detalleAdj(' . $cont . ');"
												alt="Eliminar"
												align="bottom" />';
    $_SESSION['aDataGirdAdj'] = $aDataGrid;
    $sHtml = mostrar_gridAdj();
    $oReturn->assign("gridArchivos", "innerHTML", $sHtml);

    return $oReturn;
}

function mostrar_gridAdj()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN, $DSN_Ifx;

    $oCnx = new Dbo();
    $oCnx->DSN = $DSN;
    $oCnx->Conectar();

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $idempresa =  $_SESSION['U_EMPRESA'];
    $aDataGrid = $_SESSION['aDataGirdAdj'];
    $aLabelGrid = array('Id', 'Titulo', 'Archivo', 'Eliminar');

    $cont = 0;
    $total     = 0;
    if (count($aDataGrid) > 0) {
        foreach ($aDataGrid as $aValues) {
            $aux = 0;
            foreach ($aValues as $aVal) {
                if ($aux == 0) {
                    $aDatos[$cont][$aLabelGrid[$aux]] = $cont + 1;
                } elseif ($aux == 1) {
                    $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
                } elseif ($aux == 2) {
                    $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
                } elseif ($aux == 3) {
                    $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
                } elseif ($aux == 4) {
                    $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="center">
																<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/delete_1.png"
																title = "Presione aqui para Eliminar"
																style="cursor: hand !important; cursor: pointer !important;"
																onclick="javascript:xajax_elimina_detalleAdj(' . $cont . ');"
																alt="Eliminar"
																align="bottom" />
															</div>';
                } else
                    $aDatos[$cont][$aLabelGrid[$aux]] = $aVal;
                $aux++;
            }
            $cont++;
        }
    }

    return genera_grid($aDatos, $aLabelGrid, 'Adjuntos', 98, null, $array_tot);
}


function elimina_detalleAdj($id = null)
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oReturn = new xajaxResponse();

    $aLabelGrid = array('Id', 'Titulo', 'Archivo', 'Eliminar');
    $aDataGrid = $_SESSION['aDataGirdAdj'];
    $contador = count($aDataGrid);
    if ($contador > 1) {
        unset($aDataGrid[$id]);
        $_SESSION['aDataGirdAdj'] = $aDataGrid;
        $sHtml = mostrar_gridAdj();
        $oReturn->assign("gridArchivos", "innerHTML", $sHtml);
    } else {
        unset($aDataGrid[0]);
        $_SESSION['aDataGirdAdj'] = $aDatos;
        $sHtml = "";
        $oReturn->assign("gridArchivos", "innerHTML", $sHtml);
    }

    return $oReturn;
}


function genera_pdf_doc($idempresa, $idsucursal, $asto_cod, $ejer_cod, $prdo_cod)
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN_Ifx;

    $oIfxA = new Dbo();
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();
    unset($_SESSION['pdf']);
    $oReturn = new xajaxResponse();

    $tipo     = $aForm['documento'];


    $sql = "select asto_cod_modu, asto_tipo_mov from saeasto where asto_cod_asto='$asto_cod'";
    $tipomov = consulta_string($sql, 'asto_tipo_mov', $oIfx, '');
    $codmodu = consulta_string($sql, 'asto_cod_modu', $oIfx, '');

    if ($tipomov == 'DI') {
        $sql = "select ftrn_ubi_web from saeftrn where ftrn_tip_movi='$tipomov' and ftrn_cod_modu=$codmodu and ftrn_ubi_web is not null";
        $ubi = consulta_string($sql, 'ftrn_ubi_web', $oIfx, '');
        if (empty($ubi)) {
            $ubi = 'Include/Formatos/comercial/diario.php';
        }
        include_once('../../' . $ubi . '');
        $diario = formato_diario($idempresa, $idsucursal, $asto_cod, $ejer_cod, $prdo_cod);
    } elseif ($tipomov == 'EG') {
        $sql = "select ftrn_ubi_web from saeftrn where ftrn_tip_movi='$tipomov' and ftrn_cod_modu=$codmodu and ftrn_ubi_web is not null";
        $ubi = consulta_string($sql, 'ftrn_ubi_web', $oIfx, '');
        if (empty($ubi)) {
            $ubi = 'Include/Formatos/comercial/egreso.php';
        }
        include_once('../../' . $ubi . '');
        $diario = formato_egreso($idempresa, $idsucursal, $asto_cod, $ejer_cod, $prdo_cod);
    } elseif ($tipomov == 'IN') {
        $sql = "select ftrn_ubi_web from saeftrn where ftrn_tip_movi='$tipomov' and ftrn_cod_modu=$codmodu and ftrn_ubi_web is not null";
        $ubi = consulta_string($sql, 'ftrn_ubi_web', $oIfx, '');
        if (empty($ubi)) {
            $ubi = 'Include/Formatos/comercial/ingreso.php';
        }
        include_once('../../' . $ubi . '');
        $diario = formato_ingreso($idempresa, $idsucursal, $asto_cod, $ejer_cod, $prdo_cod);
    }
    $_SESSION['pdf'] = $diario;

    $oReturn->script('generar_pdf()');
    return $oReturn;
}


function cargar_coti($aForm = '')
{
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    //variables del formulario
    $idempresa = $_SESSION['U_EMPRESA'];
    $mone_cod  = $aForm['moneda'];

    $sql = "select tcam_val_tcam from saetcam where
                mone_cod_empr = $idempresa and
                tcam_cod_mone = $mone_cod and
                tcam_fec_tcam in (
                                    select max(tcam_fec_tcam)  from saetcam where
                                            mone_cod_empr = $idempresa and
                                            tcam_cod_mone = $mone_cod
                                )  ";
    $coti = consulta_string($sql, 'tcam_val_tcam', $oIfx, 0);

    $oReturn->assign("cotizacion", "value", $coti);
    return $oReturn;
}


function lista_boostrap($oIfx, $sql, $campo_defecto, $campo_id, $campo_nom)
{
    $optionEmpr = '';
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            do {
                $empr_cod_empr = $oIfx->f($campo_id);
                $empr_nom_empr = htmlentities($oIfx->f($campo_nom));

                $selectedEmpr = '';
                if ($empr_cod_empr == $campo_defecto) {
                    $selectedEmpr = 'selected';
                }

                $optionEmpr .= '<option value="' . $empr_cod_empr . '" ' . $selectedEmpr . '>' . $empr_nom_empr . '</option>';
            } while ($oIfx->SiguienteRegistro());
        }
    }
    $oIfx->Free();

    return $optionEmpr;
}


function vista_previa($aForm = '', $serial)
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN_Ifx;

    $oReturn = new xajaxResponse();

    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $aForm['sucursal'];
    $tran_cod  = $aForm['tran'];
    $ejer_cod  = $aForm['ejercicio'];
    $prdo_cod  = $aForm['periodo'];

    unset($_SESSION['pdf']);
    $diario =  generar_mov_inv_tran_taller_pdf($idempresa, $idsucursal, $serial, $tran_cod, $ejer_cod, $prdo_cod);

    $_SESSION['pdf'] = $diario;
    $oReturn->script('generar_pdf()');

    return $oReturn;
}

function clpv_reporte($aForm = '')
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo();
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $idempresa  = $aForm['empresa'];
    $idsucursal = $aForm['sucursal'];
    $clpv_nom   = $aForm['cliente_nombre'];
    $clpv_ruc   = $aForm['ruc'];

    $sql_tmp = '';
    if (!empty($clpv_nom)) {
        $sql_tmp = " and (clpv_nom_clpv like '%$clpv_nom%'  or clpv_cod_char='$clpv_nom')";
    }

    $sql_tmp2 = '';
    if (!empty($clpv_ruc)) {
        $sql_tmp2 = " and clpv_ruc_clpv like '%$clpv_ruc%' ";
    }

    $oReturn = new xajaxResponse();

    $sHtml  .= '<div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">SUPLIDORES</h4>
                        </div>
                        <div class="modal-body">';

    $sHtml .= ' <table id="tbclientes"  class="table table-striped table-condensed table-bordered table-hover" style="width: 98%; margin-top: 20px;" align="center">';
    $sHtml .= '<thead>';
    $sHtml .= ' <tr>
                        <td class="fecha_letra">No-</td>
						<td class="fecha_letra" align="center">Codigo</td>
                        <td class="fecha_letra" align="center">Nombre</td>
                        <td class="fecha_letra" align="center">Subcliente</td>
                        <td class="fecha_letra" align="center">Vendedor</td>
                        <td class="fecha_letra" align="center">Identicacion</td>             
                        <td class="fecha_letra" align="center">Contribuyente Especial</td>             
                        <td class="fecha_letra" align="center">Estado</td>   
                    </tr>';
    $sHtml .= '</thead>';
    $sHtml .= '<tbody>';



    // ---------------------------------------------------------------------------------------------------------
    // CONTROL CLPV POR USUARIO, SUCURSALES
    // ---------------------------------------------------------------------------------------------------------
    $id_usuario_comercial = $_SESSION['U_ID'];
    $bloqueo_sucu_sn = 'N';
    $sucursales_usuario = '';
    $sql_data_usuario_sucu = "SELECT bloqueo_sucu_sn, sucursales_usuario from comercial.usuario where usuario_id = $id_usuario_comercial";
    if ($oIfx->Query($sql_data_usuario_sucu)) {
        if ($oIfx->NumFilas() > 0) {
            do {
                $bloqueo_sucu_sn = $oIfx->f('bloqueo_sucu_sn');
                $sucursales_usuario = $oIfx->f('sucursales_usuario');
            } while ($oIfx->SiguienteRegistro());
        }
    }
    $sql_adicional_sucu = "";
    $oIfx->Free();
    if ($bloqueo_sucu_sn == 'S') {
        if (!empty($sucursales_usuario)) {
            $sql_adicional_sucu = ' and clpv_cod_sucu in (' . $sucursales_usuario . ')';
        }
    }
    // ---------------------------------------------------------------------------------------------------------
    // FIN CONTROL CLPV POR USUARIO, SUCURSALES
    // ---------------------------------------------------------------------------------------------------------


    $sql = "select clpv_cod_clpv, clpv_nom_clpv,  clpv_ruc_clpv, clpv_est_clpv,
                        clpv_cod_fpagop, clpv_cod_tpago, clpv_pro_pago, clpv_etu_clpv, clpv_cod_cuen,
                        clpv_cod_vend, clpv_cot_clpv, clpv_pre_ven from saeclpv where
                        clpv_cod_empr   = $idempresa and
                        clpv_clopv_clpv = 'PV' 
                        $sql_tmp 
                        $sql_tmp2  
                        $sql_adicional_sucu
                        order by 2 limit 50";

    $i = 1;
    $total = 0;
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            do {
                $clpv_cod_clpv     = ($oIfx->f('clpv_cod_clpv'));
                $clpv_nom_clpv     = htmlentities($oIfx->f('clpv_nom_clpv'));
                $clpv_ruc_clpv     = $oIfx->f('clpv_ruc_clpv');
                $clpv_cod_fpagop = $oIfx->f('clpv_cod_fpagop');
                $clpv_cod_tpago = $oIfx->f('clpv_cod_tpago');
                $clpv_pro_pago  = $oIfx->f('clpv_pro_pago');
                $clpv_etu_clpv  = $oIfx->f('clpv_etu_clpv');
                $clpv_cod_vend  = $oIfx->f('clpv_cod_vend');
                $clpv_cot_clpv  = $oIfx->f('clpv_cot_clpv');
                $clpv_pre_ven   = $oIfx->f('clpv_pre_ven');


                $clpv_est_clpv = $oIfx->f('clpv_est_clpv');

                if ($clpv_est_clpv == 'A') {
                    $estado = 'ACTIVO';
                } elseif ($clpv_est_clpv == 'P') {
                    $estado = 'PENDIENTE';
                } elseif ($clpv_est_clpv == 'S') {
                    $estado = 'SUSPENDIDO';
                } else {
                    $estado = '--';
                }

                if ($clpv_etu_clpv == 1) {
                    $clpv_etu_clpv = 'S';
                } else {
                    $clpv_etu_clpv = 'N';
                }

                if (empty($clpv_pro_pago)) {
                    $clpv_pro_pago = 0;
                }

                /**
                 * Consulta Subcliente
                 */
                $sql_sub = "select count(*) as total from saeccli WHERE ccli_cod_clpv = '$clpv_cod_clpv' limit 1;";
                $sub_cliente = consulta_string_func($sql_sub, 'total', $oIfxA, 0);
                $sub_cliente_sn = ($sub_cliente > 0) ? 'SI' : 'NO';


                /**
                 * Consulta Vendedor
                 */
                $sql_vent = "select vend_cod_vend, vend_nom_vend from saevend where vend_cod_empr = $idempresa and vend_cod_vend = '$clpv_cod_vend'";
                $vendedor_info = consulta_string_func($sql_vent, 'vend_nom_vend', $oIfxA, '');


                // FECHA DE VENCIMIENTO
                $fecha_venc = (sumar_dias_func(date("Y-m-d"), $prove_dia)); //  Y/m/d
                list($a, $b, $c) = explode('-', $fecha_venc);
                $fecha_venc = $a . '-' . $b . '-' . $c;

                //direccion
                $sql = "select dire_dir_dire from saedire where dire_cod_empr = $idempresa and dire_cod_clpv = $clpv_cod_clpv";
                $dire = consulta_string_func($sql, 'dire_dir_dire', $oIfxA, '');

                //telefono
                $sql = "select tlcp_tlf_tlcp from saetlcp where tlcp_cod_empr = $idempresa and tlcp_cod_clpv = $clpv_cod_clpv";
                $telefono = consulta_string_func($sql, 'tlcp_tlf_tlcp', $oIfxA, '');

                // AUTORIZACION PROVE
                $sql = "select  max(coa_fec_vali) as coa_fec_vali, coa_aut_usua, coa_seri_docu, coa_fact_ini, coa_fact_fin
                            from saecoa where
                            clpv_cod_empr = $idempresa and
                            clpv_cod_clpv = $clpv_cod_clpv group by coa_fec_vali,2,3,4,5 ";
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

                //correo
                $sql = "select emai_ema_emai from saeemai where
                            emai_cod_empr = $idempresa and
                            emai_cod_clpv = $clpv_cod_clpv ";
                $correo = consulta_string_func($sql, 'emai_ema_emai', $oIfxA, '');


                $fecha_compra = $aForm['fecha_pedido'];
                $fecha_final = date("Y-m-d", strtotime($fecha_compra . "+ " . $clpv_pro_pago . " days"));
                $oReturn->assign('fecha_entrega', 'value', $fecha_final);


                $sClass = ($sClass == 'off') ? $sClass = 'on' : $sClass = 'off';
                $sHtml .= '<tr height="20" style="cursor: pointer" 
                            onClick="javascript:datos_clpv( \'' . $clpv_cod_clpv . '\', \'' . $clpv_nom_clpv . '\' , \'' . $clpv_ruc_clpv . '\',  \'' . $dire . '\',
                                                            \'' . $telefono . '\',      \'' . $celular . '\',        \'' . $vendedor . '\',       \'' . $contacto . '\',
                                                            \'' . $precio . '\',        \'' . $clpv_cod_fpagop . '\', \'' . $clpv_cod_tpago . '\', \'' . $fec_cadu_prove . '\',
                                                            \'' . $auto_prove . '\',    \'' . $serie_prove . '\',     \'' . $fecha_venc . '\',     \'' . $clpv_pro_pago . '\',
                                                            \'' . $clpv_etu_clpv . '\', \'' . $ini_prove . '\',       \'' . $fin_prove . '\',      \'' . $clpv_cod_cuen . '\',
                                                            \'' . $correo . '\'
                                                          )"  >';


                $sHtml .= '<td>' . $i . '</td>';
                $sHtml .= '<td>' . $clpv_cod_clpv . '</td>';
                $sHtml .= '<td>' . $clpv_nom_clpv . '</td>';
                $sHtml .= '<td>' . $sub_cliente_sn . '</td>';
                $sHtml .= '<td>' . $vendedor_info . '</td>';
                $sHtml .= '<td>' . $clpv_ruc_clpv . '</td>';
                $sHtml .= '<td align="right">' . $clpv_etu_clpv . '</td>';
                $sHtml .= '<td>' . $estado . '</td>';
                $sHtml .= '</tr>';

                $i++;
                $total += $prbo_dis_prod;
            } while ($oIfx->SiguienteRegistro());
        }
    }
    $sHtml .= '</tbody>';
    $sHtml .= '</table>';

    $sHtml .= '          </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
             </div>';



    $oReturn->assign("ModalClpv", "innerHTML", $sHtml);
    $oReturn->script("init()");

    return $oReturn;
}

// REPORT PRODCUTO INVETANARIO
// function producto_inventario($aForm = '')
// {
//     global $DSN, $DSN_Ifx;
//     if (session_status() !== PHP_SESSION_ACTIVE) {
//         session_start();
//     }

//     $oIfx = new Dbo();
//     $oIfx->DSN = $DSN_Ifx;
//     $oIfx->Conectar();


//     $oIfxA = new Dbo();
//     $oIfxA->DSN = $DSN_Ifx;
//     $oIfxA->Conectar();

//     $idempresa  = $aForm['empresa'];
//     $idsucursal = $aForm['sucursal'];
//     $bode_cod   = $aForm['bodega'];
//     $prod_nom   = $aForm['producto'];


//     $sql_tmp = '';
//     if (!empty($prod_nom)) {
//         $sql_tmp = " and ( prod_nom_prod like '%$prod_nom%' or   prod_cod_prod like '%$prod_nom%' ) ";
//     }


//     $oReturn = new xajaxResponse();

//     $sHtml  .= '<div class="modal-dialog modal-lg">
//                     <div class="modal-content">
//                         <div class="modal-header">
//                             <button type="button" class="close" data-dismiss="modal">&times;</button>
//                             <h4 class="modal-title">PRODUCTOS</h4>
//                         </div>
//                         <div class="modal-body">';

//     $sHtml .= ' <table id="tbclientes_prod" class="table table-striped table-condensed table-bordered table-hover" style="width: 98%; margin-top: 20px;" align="center">';
//     $sHtml .= '<thead>';
//     $sHtml .= '<tr>
//                         <td class="fecha_letra">No-</td>
//                         <td class="fecha_letra" align="center">Bodega</td>
// 						<td class="fecha_letra" align="center">Codigo</td>
//                         <td class="fecha_letra" align="center">Producto</td>
//                         <td class="fecha_letra" align="center">Referencia</td>
//                         <td class="fecha_letra" align="center">Tipo</td>
//                         <td class="fecha_letra" align="center">Unidad Medida</td>
//                         <td class="fecha_letra" align="center">lotes</td>
//                         <td class="fecha_letra" align="center">Series</td>
//                         <td class="fecha_letra" align="center">Stock</td>                        
//                     </tr>';
//     $sHtml .= '</thead>';
//     $sHtml .= '<tbody>';


//     $sql = "select un.unid_nom_unid, tp.tpro_des_tpro, b.bode_nom_bode, pr.prbo_cod_prod, p.prod_nom_prod, pr.prbo_dis_prod, pr.prbo_cta_inv, pr.prbo_cta_ideb,
//                         pr.prbo_uco_prod, pr.prbo_iva_porc, prod_lot_sino, prod_ser_prod, prod_cod_barr3
//                         from saeprbo pr, saeprod p, saebode b, saetpro tp, saeunid un
//                         where
//                         p.prod_cod_prod     = pr.prbo_cod_prod and
//                         pr.prbo_cod_bode     = b.bode_cod_bode and
//                         tp.tpro_cod_tpro     = p.prod_cod_tpro and
//                         un.unid_cod_unid     = pr.prbo_cod_unid and
//                         p.prod_cod_empr     = $idempresa and
//                         tp.tpro_cod_empr     = $idempresa and
//                         p.prod_cod_sucu     = $idsucursal and
//                         pr.prbo_cod_empr    = $idempresa and
//                         pr.prbo_cod_bode    = '$bode_cod'
//                         $sql_tmp order by  2 limit 50";
//     echo $sql;exit;


//     // No se hace uso de la vista ya que no actualiza los cambios realizados en el producto
//     // $sql = "select *from sp_obtener_todos_productos($idempresa , $idsucursal,$bode_cod,500,'$prod_nom');";

//     $i = 1;
//     $total = 0;
//     unset($_SESSION['U_PROD_RSC']);
//     unset($array_tmp);
//     if ($oIfx->Query($sql)) {
//         if ($oIfx->NumFilas() > 0) {
//             do {

//                 $prbo_cod_prod     = ($oIfx->f('prbo_cod_prod'));

//                 $sql_stock = "select  COALESCE( pr.prbo_dis_prod,'0' ) as stock
//                     from saeprod p, saeprbo pr where
//                     p.prod_cod_prod = pr.prbo_cod_prod and
//                     p.prod_cod_empr = $idempresa and
//                     p.prod_cod_sucu = $idsucursal and
//                     pr.prbo_cod_empr = $idempresa and
//                     pr.prbo_cod_bode = $bode_cod and
//                     p.prod_cod_prod = '$prbo_cod_prod'";
//                 $stock = consulta_string_func($sql_stock, 'stock', $oIfxA, 0);


//                 $nom_bode     = ($oIfx->f('bode_nom_bode'));
//                 $tipo_prod     = ($oIfx->f('tpro_des_tpro'));
//                 $detalle_prod     = ($oIfx->f('prod_det_prod'));
//                 $prod_nom_prod     = htmlentities($oIfx->f('prod_nom_prod'));
//                 $prbo_dis_prod     = $stock;
//                 $prbo_cta_inv     = $oIfx->f('prbo_cta_inv');
//                 $prbo_cta_ideb     = $oIfx->f('prbo_cta_ideb');
//                 $prbo_uco_prod     = $oIfx->f('prbo_uco_prod');
//                 $prbo_iva_porc     = $oIfx->f('prbo_iva_porc');
//                 $unidad_prod     = $oIfx->f('unid_nom_unid');
//                 $lote             = $oIfx->f('prod_lot_sino');
//                 $serie             = $oIfx->f('prod_ser_prod');
//                 $mac             = $oIfx->f('prod_cod_barr3');

//                 $array_tmp[$i] = array(
//                     $prbo_cod_prod,
//                     $prod_nom_prod,
//                     $prbo_cta_inv,
//                     $prbo_cta_ideb,
//                     $prbo_uco_prod,
//                     $prbo_iva_porc,
//                     $lote,
//                     $serie
//                 );

//                 if ($lote == 1 || $lote == 'S') {
//                     $lote = 'S';
//                 } else {
//                     $lote = 'N';
//                 }

//                 if ($serie == 1 || $serie == 'S') {
//                     $serie = 'S';
//                 } else {
//                     $serie = 'N';
//                 }

//                 if ($mac == 1 || $mac == 'S') {
//                     $mac = 'S';
//                 } else {
//                     $mac = 'N';
//                 }


//                 $sClass = ($sClass == 'off') ? $sClass = 'on' : $sClass = 'off';
//                 /*$sHtml .= '<tr height="20" style="cursor: pointer"  
//                                 onClick="javascript:datos_prod( \'' . $prbo_cod_prod . '\',  \'' . $prod_nom_prod . '\'  , \'' . $prbo_cta_inv . '\'   ,
//                                                                 \'' . $prbo_cta_ideb . '\' , \'' . $prbo_uco_prod . '\'  , \'' . $prbo_iva_porc . '\' ,
//                                                                 \'' . $lote . '\',      \'' . $serie . '\' )">';*/

//                 $sHtml .= '<tr  height="20" style="cursor: pointer"  onClick="javascript:datos_prod( \'' . $i . '\' ,
//                 \'' . $lote . '\',      \'' . $serie . '\',      \'' . $mac . '\'  )" >';

//                 $sHtml .= '<td>' . $i . '</td>';
//                 $sHtml .= '<td>' . $nom_bode . '</td>';
//                 $sHtml .= '<td>' . $prbo_cod_prod . '</td>';
//                 $sHtml .= '<td>' . $prod_nom_prod . '</td>';
//                 $sHtml .= '<td>' . $detalle_prod . '</td>';
//                 $sHtml .= '<td>' . $tipo_prod . '</td>';
//                 $sHtml .= '<td>' . $unidad_prod . '</td>';
//                 $sHtml .= '<td>' . $lote . '</td>';
//                 $sHtml .= '<td>' . $serie . '</td>';
//                 $sHtml .= '<td align="right">' . $prbo_dis_prod . '</td>';
//                 $sHtml .= '</tr>';

//                 $i++;
//                 $total += $prbo_dis_prod;
//             } while ($oIfx->SiguienteRegistro());
//             $sHtml .= '<tr height="25">';
//             $sHtml .= '<td></td>';
//             $sHtml .= '<td align="right"></td>';
//             $sHtml .= '<td align="right"></td>';
//             $sHtml .= '<td align="right"></td>';
//             $sHtml .= '<td align="right"></td>';
//             $sHtml .= '<td align="right"></td>';
//             $sHtml .= '<td align="right"></td>';
//             $sHtml .= '<td align="right"></td>';
//             $sHtml .= '<td align="right" class="fecha_letra">TOTAL:</td>';
//             $sHtml .= '<td align="right" class="fecha_letra">' . $total . '</td>';
//             $sHtml .= '</tr>';
//         }
//     }

//     $_SESSION['U_PROD_RSC'] = $array_tmp;

//     $sHtml .= '</tbody>';
//     $sHtml .= '</table>';

//     $sHtml .= '          </div>
//                         <div class="modal-footer">
//                             <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
//                         </div>
//                     </div>
//                 </div>
//              </div>';

//     $oReturn->assign("ModalProd", "innerHTML", $sHtml);
//     //$oReturn->script("init_prod()");
//     return $oReturn;
// }
function producto_inventario($aForm = '')
{
    global $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $idempresa  = (int)$aForm['empresa'];
    $idsucursal = (int)$aForm['sucursal'];
    $bode_cod   = trim($aForm['bodega']);
    $prod_nom   = trim($aForm['producto']);

    $sql_tmp = '';
    if ($prod_nom !== '') {
        $prod_nom = addslashes($prod_nom);
        $sql_tmp = " AND ( p.prod_nom_prod LIKE '%$prod_nom%' 
                           OR p.prod_cod_prod LIKE '%$prod_nom%' ) ";
    }

    $oReturn = new xajaxResponse();

    $sHtml = '
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">PRODUCTOS</h4>
            </div>
            <div class="modal-body">';

    $sHtml .= '
    <table id="tbclientes_prod" class="table table-striped table-condensed table-bordered table-hover" style="width:98%; margin-top:20px;">
        <thead>
            <tr>
                <td>No</td>
                <td>Bodega</td>
                <td>Código</td>
                <td>Producto</td>
                <td>Referencia</td>
                <td>Tipo</td>
                <td>Unidad</td>
                <td>Lotes</td>
                <td>Series</td>
                <td>Stock</td>
            </tr>
        </thead>
        <tbody>';

    $sql = "
    SELECT
        un.unid_nom_unid,
        tp.tpro_des_tpro,
        b.bode_nom_bode,
        pr.prbo_cod_prod,
        p.prod_nom_prod,
        pr.prbo_dis_prod,
        pr.prbo_cta_inv,
        pr.prbo_cta_ideb,
        pr.prbo_uco_prod,
        pr.prbo_iva_porc,
        p.prod_lot_sino,
        p.prod_ser_prod,
        p.prod_cod_barr3
    FROM saeprbo pr
    JOIN saeprod p
        ON p.prod_cod_prod = pr.prbo_cod_prod
       AND p.prod_cod_empr = pr.prbo_cod_empr
       AND p.prod_cod_sucu = $idsucursal
    JOIN saetpro tp
        ON tp.tpro_cod_tpro = p.prod_cod_tpro
       AND tp.tpro_cod_empr = p.prod_cod_empr
    JOIN saebode b
        ON b.bode_cod_bode = pr.prbo_cod_bode
       AND b.bode_cod_empr = pr.prbo_cod_empr
    JOIN saeunid un
        ON un.unid_cod_unid = pr.prbo_cod_unid
       AND un.unid_cod_empr = pr.prbo_cod_empr
    WHERE pr.prbo_cod_empr = $idempresa
      AND pr.prbo_cod_bode = '$bode_cod'
      $sql_tmp
    ORDER BY p.prod_nom_prod
    LIMIT 50";

    if ($oIfx->Query($sql)) {
        $i = 1;
        $total = 0;
        unset($_SESSION['U_PROD_RSC']);
        $array_tmp = [];

        do {
            $prbo_cod_prod = $oIfx->f('prbo_cod_prod');
            $prod_nom_prod = htmlentities($oIfx->f('prod_nom_prod'));
            $nom_bode      = $oIfx->f('bode_nom_bode');
            $tipo_prod     = $oIfx->f('tpro_des_tpro');
            $unidad_prod   = $oIfx->f('unid_nom_unid');
            $stock         = (float)$oIfx->f('prbo_dis_prod');

            $lote  = ($oIfx->f('prod_lot_sino') == 1 || $oIfx->f('prod_lot_sino') == 'S') ? 'S' : 'N';
            $serie = ($oIfx->f('prod_ser_prod') == 1 || $oIfx->f('prod_ser_prod') == 'S') ? 'S' : 'N';
            $mac   = ($oIfx->f('prod_cod_barr3') == 1 || $oIfx->f('prod_cod_barr3') == 'S') ? 'S' : 'N';

            $array_tmp[$i] = [
                $prbo_cod_prod,
                $prod_nom_prod,
                $oIfx->f('prbo_cta_inv'),
                $oIfx->f('prbo_cta_ideb'),
                $oIfx->f('prbo_uco_prod'),
                $oIfx->f('prbo_iva_porc'),
                $lote,
                $serie
            ];

            $sHtml .= "
            <tr style='cursor:pointer' 
                onclick=\"datos_prod('$i','$lote','$serie','$mac')\">
                <td>$i</td>
                <td>$nom_bode</td>
                <td>$prbo_cod_prod</td>
                <td>$prod_nom_prod</td>
                <td></td>
                <td>$tipo_prod</td>
                <td>$unidad_prod</td>
                <td>$lote</td>
                <td>$serie</td>
                <td align='right'>$stock</td>
            </tr>";

            $total += $stock;
            $i++;

        } while ($oIfx->SiguienteRegistro());

        $sHtml .= "
        <tr>
            <td colspan='9' align='right'><strong>TOTAL</strong></td>
            <td align='right'><strong>$total</strong></td>
        </tr>";
    }

    $_SESSION['U_PROD_RSC'] = $array_tmp;

    $sHtml .= '
        </tbody>
    </table>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
    </div>
    </div>
    </div>';

    $oReturn->assign("ModalProd", "innerHTML", $sHtml);
    return $oReturn;
}

// RECEPION DE COMPRA
function recepcion_compra($aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse();

    $idempresa     = $_SESSION['U_EMPRESA'];
    $idsucursal    = $aForm['sucursal'];
    $id_cliente = $aForm['cliente'];
    $tran_cod   = $aForm['tran'];

    $usuario_informix = $_SESSION['U_USER_INFORMIX'];
    unset($_SESSION['U_PROF_RECO']);

    $Html_reporte .= '<div class="table-responsive">';
    $Html_reporte .= '<table class="table table-striped table-condensed" style="width: 96%; margin-bottom: 0px;" align="center">';
    $Html_reporte .= '<tr>
                                <td class="info" align="center">N.-</td>
                                <td class="info" align="center">Orden Compra</td>
								<td class="info" align="center">N.- Factura</td>
                                <td class="info" align="center">Fecha</td>
                                <td class="info" align="center">Total</td>
								<td class="info" align="center">Detalle</td>
                                <td class="info" align="center">Seleccionar</td>
                         </tr>';
    $sql = " SELECT distinct( minv_num_comp),   minv_fmov,      minv_num_sec, minv_cod_clpv,  minv_dege_minv, minv_fac_prov,
                        (COALESCE(minv_tot_minv,0) - COALESCE(minv_dge_valo,0) + COALESCE(minv_iva_valo,0) + COALESCE(minv_otr_valo,0) - COALESCE(minv_fle_minv,0) + COALESCE(minv_val_ice,0) ) total
                        FROM saeminv,    saedmov   WHERE  
                        minv_num_comp = dmov_num_comp   and
                        minv_cod_clpv = $id_cliente     and  
                        minv_cod_tran in  ( select defi_cod_tran from saedefi Where 
                                                defi_tip_defi  = '4' and 
                                                defi_cod_empr  = $idempresa and 
                                                defi_cod_modu  = 10 and
                                                defi_cod_tran not in ( select parm_tran_ord from saeparm where parm_cod_empr = $idempresa )  )  AND   
                        minv_cod_empr = $idempresa      AND  
                        minv_cod_sucu = $idsucursal     AND  
                        minv_est_minv = '1' and
                        --minv_tip_ord != 1 and
                        dmov_can_dmov <> dmov_can_entr  and
                        (( minv_cer_sn is null) or ( minv_cer_sn = 'N' ) ) ";
    //$oReturn->alert($sql);
    $i = 1;
    unset($array);
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            do {
                $fec_pedi         = $oIfx->f('minv_fmov');
                $preimp         = $oIfx->f('minv_num_sec');
                $clpv_cod         = $oIfx->f('minv_cod_clpv');
                $clpv_nom         = $oIfx->f('clpv_nom_clpv');
                $serial         = $oIfx->f('minv_num_comp');
                $total             = round($oIfx->f('total'), 2);
                $descuento         = $oIfx->f('minv_dege_minv');
                $minv_fac_prov     = $oIfx->f('minv_fac_prov');
                $minv_cm1_minv     = $oIfx->f('minv_cm1_minv');

                $ifu->AgregarCampoCheck($serial, '', false, 1);
                if ($sClass == 'off')
                    $sClass = 'on';
                else
                    $sClass = 'off';
                $Html_reporte .= '<tr height="20" class="' . $sClass . '"
                                        onMouseOver="javascript:this.className=\'link\';"
                                        onMouseOut="javascript:this.className=\'' . $sClass . '\';">';
                $Html_reporte .= '<td align="right">' . $i . '</td>';
                $Html_reporte .= '<td align="right">' . $preimp . '</td>';
                $Html_reporte .= '<td align="right">' . $minv_fac_prov . '</td>';
                $Html_reporte .= '<td align="right">' . $fec_pedi . '</td>';
                $Html_reporte .= '<td align="right">' . $total . '</td>';
                $Html_reporte .= '<td align="right">										
										<div class="btn btn-primary btn-sm" onClick="javascript:cargar_reco_det_gen(\'' . $serial . '\', \'' . $idempresa . '\', \'' . $idsucursal . '\')" >
											<span class="glyphicon glyphicon-cog"></span>
											Detalle
										</div>
                                        <div id ="imagen1" class="btn btn-danger btn-sm" onclick="finalizar_oc(\'' . $serial . '\')" title="">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </div>
								 </td>';
                $Html_reporte .= '<td align="right">' . $ifu->ObjetoHtml($serial) . '</td>';
                $Html_reporte .= '</tr>';
                $array[] = array($serial, $preimp, $clpv_cod, $descuento, $fec_pedi, $minv_cm1_minv);
                $i++;
            } while ($oIfx->SiguienteRegistro());
            $Html_reporte .= '<tr>
									<td align="center" colspan="7">											
										<div class="btn btn-primary btn-sm" onClick="javascript:cargar_reco( \'' . $idempresa . '\', \'' . $idsucursal . '\', 0  )" >
											<span class="glyphicon glyphicon-th"></span>
											Procesar
										</div>
									</td>
							 </tr>
							</table>
							</div>';
        } else {
            $Html_reporte = 'Sin Datos';
        }
    }
    $oIfx->Free();
    $_SESSION['U_PROF_RECO'] = $array;


    $modal  = '<div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">ORDEN DE COMPRA</h4>
                        </div>
                        <div class="modal-body">';
    $modal .= $Html_reporte;
    $modal .= '          </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>';

    $oReturn->assign("ModalRECO", "innerHTML", $modal);

    return $oReturn;
}





function cargar_ord_compra_respaldo($aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oCon = new Dbo;
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse();


    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $aForm['sucursal'];


    unset($_SESSION['U_PROD_COD_PRECIO']);

    //////////////

    try {

        // DATOS
        // BODEGA

        // ---------------------------------------------------------------------------------------------------------
        // CONTROL CLPV POR USUARIO, SUCURSALES
        // ---------------------------------------------------------------------------------------------------------
        $id_usuario_comercial = $_SESSION['U_ID'];
        $bloqueo_sucu_sn = 'N';
        $sucursales_usuario = '';
        $sql_data_usuario_sucu = "SELECT bloqueo_sucu_sn, sucursales_usuario from comercial.usuario where usuario_id = $id_usuario_comercial";
        if ($oIfx->Query($sql_data_usuario_sucu)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    $bloqueo_sucu_sn = $oIfx->f('bloqueo_sucu_sn');
                    $sucursales_usuario = $oIfx->f('sucursales_usuario');
                } while ($oIfx->SiguienteRegistro());
            }
        }
        $sql_adicional_sucu = "";
        $oIfx->Free();
        if ($bloqueo_sucu_sn == 'S') {
            if (!empty($sucursales_usuario)) {
                $sql_adicional_sucu = ' and subo_cod_sucu in (' . $sucursales_usuario . ')';
            } else {
                $sql_adicional_sucu = 'and subo_cod_sucu = ' . $idsucursal;
            }
        }
        // ---------------------------------------------------------------------------------------------------------
        // FIN CONTROL CLPV POR USUARIO, SUCURSALES
        // ---------------------------------------------------------------------------------------------------------


        $sql = "select bode_cod_bode, bode_nom_bode from saesubo, saebode where
                        bode_cod_bode = subo_cod_bode and
                        bode_cod_empr = $idempresa and
                        subo_cod_empr = $idempresa
                        $sql_adicional_sucu
                        ";
        unset($array_bode);
        unset($array_bode_cod);
        $array_bode     = array_dato($oIfx, $sql, 'bode_nom_bode', 'bode_nom_bode');
        $array_bode_cod = array_dato($oIfx, $sql, 'bode_nom_bode', 'bode_cod_bode');


        // PRODUCTO
        $sql = "select prod_cod_prod, prod_nom_prod from saeprod where
                        prod_cod_empr = $idempresa and
                        prod_cod_sucu = $idsucursal
                        group by 1,2  ";
        unset($array_prod);
        unset($array_prod_cod);
        $array_prod     = array_dato($oIfx, $sql, 'prod_cod_prod', 'prod_cod_prod');


        // CENTRO DE COSTO
        $sql = "select ccosn_cod_ccosn,  ccosn_nom_ccosn
                from saeccosn where
                ccosn_cod_empr = $idempresa and
                ccosn_mov_ccosn = 1 order by 2";

        unset($array_prec);
        unset($array_prec_cod);
        $array_prec     = array_dato($oIfx, $sql, 'ccosn_nom_ccosn', 'ccosn_nom_ccosn');
        $array_prec_cod = array_dato($oIfx, $sql, 'ccosn_cod_ccosn', 'ccosn_cod_ccosn');

        $archivo = $aForm['archivo'];

        // archivo txt
        $archivo_real = substr($archivo, 12);
        list($xxxx, $exten) = explode(".", $archivo_real);

        if ($exten == 'txt') {
            $nombre_archivo = "upload/" . $archivo_real;

            $file       = fopen($nombre_archivo, "r");
            $datos      = file($nombre_archivo);
            $NumFilas   = count($datos);

            $table_cab  = '<br><br>';
            $table_cab  = '<h4>Lista del archivo exportado</h4>';
            $table_cab .= '<table class="table table-bordered table-striped table-condensed" style="width: 98%; margin-bottom: 0px;">';
            $table_cab .= '<tr>
                                            <td  style="width: 4.5%;">N.-</td>
                                            <td  style="width: 4.5%;">BODEGA</td>
                                            <td  style="width: 4.5%;">CODIGO PRODUCTO</td>
                                            <td  style="width: 9.5%;">PRODUCTO</td>
                                            <td  style="width: 4.5%;">CANTIDAD</td>
                                            <td class="success" style="width: 4.5%;">LOTE/SERIE</td>
                                            <td class="success" style="width: 4.5%;">FECHA ELAB.</td>
                                            <td class="success" style="width: 4.5%;">FECHA CAD.</td>
                                            <td class="success" style="width: 4.5%;">MAC</td>
                                            <td  style="width: 4.5%;">CENTRO DE COSTO</td>
                                            <td  style="width: 4.5%;">PRECIO UNIT.</td>
                                            <td  style="width: 4.5%;">FOB</td>';

            $cont = 0;
            $cont_pvp = 0;

            /*BODEGA	    CODIGO	        PRODUCTO	    CANTIDAD	        CENTRO DE COSTO	        FOB
                    */
            $datos_txt = explode("	", $datos[0]);
            foreach ($datos_txt as $val1) {
                if ($cont > 6) {
                    $cont_pvp++;
                }
                $cont++;
            }

            for ($i = 1; $i <= $cont_pvp; $i++) {
                $table_cab .= '<td  style="width: 4.5%;">PVP' . $i . '</td>';
            }

            $table_cab .= '</tr>';
            $x = 1;
            // $oReturn->alert('Buscando ...');
            unset($array);
            foreach ($datos as $val) {
                /*BODEGA	    CODIGO	        PRODUCTO	    CANTIDAD	        CENTRO DE COSTO	        FOB
                        */

                list(
                    $bode_cod,
                    $prod_cod,
                    $prod_nom,
                    $cantidad,
                    $lote_serie_txt,
                    $fecha_ela_txt,
                    $fecha_cad_txt,
                    $mac_prod,
                    $ccosto,
                    $fob,
                    $fob_real,
                    $pvp1,
                    $pvp2,
                    $pvp3,
                    $pvp4,
                    $pvp5,
                    $pvp6,
                    $pvp7,
                    $pvp8,
                    $pvp9,
                    $pvp10
                ) = explode("	", $val);

                if ($x > 1 && !empty($bode_cod)) {

                    if ($sClass == 'off') $sClass = 'on';
                    else $sClass = 'off';
                    $table_cab .= '<tr>';
                    $table_cab .= '<td>' . ($x - 1) . '</td>';
                    if (!empty($array_bode[trim($bode_cod)])) {
                        $table_cab .= '<td>' . $array_bode[trim($bode_cod)] . '</td>';
                    } else {
                        $table_cab .= '<td style="background:yellow">' . $bode_cod . '</td>';
                    }

                    if (!empty($array_prod[trim($prod_cod)])) {
                        $table_cab .= '<td>' . $array_prod[$prod_cod] . '</td>';
                    } else {
                        $table_cab .= '<td style="background:yellow">' . $prod_cod . '</td>';
                    }

                    $table_cab .= '<td>' . $prod_nom . '</td>';
                    $table_cab .= '<td align="right">' . $cantidad . '</td>';




                    // ---------------------------------------------------------------------------------------------------------------------
                    // Verificamos si existe lote del producto
                    // ---------------------------------------------------------------------------------------------------------------------

                    if (!empty($lote_serie_txt)) {
                        $id_user = $_SESSION['U_ID'];
                        $fecha_ini = '2018-01-01';
                        $fecha_fin = '2050-01-01';

                        $bode_origen = $array_bode_cod[trim($bode_cod)];

                        $sql = "delete from tmp_prod_lote_web where user_cod_web = $id_user";
                        $oIfx->QueryT($sql);

                        $sql_sp = "select * from sp_lotes_productos_web( $idempresa, $idsucursal, $bode_origen, '$fecha_ini', '$fecha_fin', '$prod_cod', '$prod_cod', '2' , $id_user, '$lote_serie_txt') ";
                        $oIfx->Query($sql_sp);

                        $sql = "select  sum(cant_lote) as cant, num_lote,  MAX(fecha_ela_lote) as felab, MAX(fecha_cad_lote) as fcad
                                from tmp_prod_lote_web where
                                user_cod_web  = $id_user and
                                bode_cod_bode = $bode_origen and
                                empr_cod_empr = $idempresa and
                                sucu_cod_sucu = $idsucursal and
                                num_lote = '$lote_serie_txt' and
                                prod_cod_prod = '$prod_cod'
                                group by 2
                                having  sum(cant_lote) <> 0
                                order by fcad
                                limit 800
                                ";

                        $num_lote = '';
                        $fecha_ela_lote = '';
                        $fecha_cad_lote = '';
                        if ($oIfx->Query($sql)) {
                            if ($oIfx->NumFilas() > 0) {
                                do {
                                    $num_lote = $oIfx->f('num_lote');
                                    $fecha_ela_lote = $oIfx->f('felab');
                                    $fecha_cad_lote = $oIfx->f('fcad');
                                } while ($oIfx->SiguienteRegistro());
                            }
                        }
                        $oIfx->Free();


                        if (!empty($num_lote)) {
                            $table_cab .= '<td style="background:yellow">' . $num_lote . ' (LOTE/SERIE YA EXISTE)</td>';
                            $table_cab .= '<td style="background:yellow">' . $fecha_ela_lote . '</td>';
                            $table_cab .= '<td style="background:yellow">' . $fecha_cad_lote . '</td>';
                            $table_cab .= '<td align="right">' . $mac_prod . '</td>';
                        } else {
                            $table_cab .= '<td>' . $lote_serie_txt . '</td>';
                            $table_cab .= '<td>' . $fecha_ela_lote . '</td>';
                            $table_cab .= '<td>' . $fecha_cad_lote . '</td>';
                            $table_cab .= '<td align="right">' . $mac_prod . '</td>';
                        }
                    } else {
                        $table_cab .= '<td align="right"></td>';
                        $table_cab .= '<td align="right"></td>';
                        $table_cab .= '<td align="right"></td>';
                        $table_cab .= '<td align="right"></td>';
                    }






                    // ---------------------------------------------------------------------------------------------------------------------
                    // Verificamos si existe lote del producto
                    // ---------------------------------------------------------------------------------------------------------------------







                    if (!empty($array_prec_cod[trim($ccosto)])) {
                        $table_cab .= '<td>' . $array_prec_cod[$ccosto] . '</td>';
                    } else {
                        $table_cab .= '<td style="background:yellow">' . $ccosto . '</td>';
                    }


                    $table_cab .= '<td>' . $fob . '</td>';

                    // FOB REAL
                    $table_cab .= '<td>' . $fob_real . '</td>';


                    for ($j = 1; $j <= $cont_pvp; $j++) {
                        $table_cab .= '<td>' . ${"pvp" . $j} . '</td>';
                    }



                    $table_cab .= '</tr>';

                    $array[] = array(
                        $array_bode_cod[$bode_cod],
                        $prod_cod,
                        $prod_nom,
                        $array_prec_cod[$ccosto],
                        $cantidad,
                        $fob
                    );
                }
                $x++;
            }

            $_SESSION['U_PROD_COD_PRECIO'] = $array;

            $html_tabla .= $table_cab;
            $html_tabla .= "</table>";

            $oReturn->assign("divFormularioDetalle2", "innerHTML", $html_tabla);
        } else {
            $oReturn->script("Swal.fire({
                                            title: '<h3><strong>!!!!....Archivo Incorrecto, por favor subir Archivo con extension .txt...!!!!!</strong></h3>',
                                            width: 800,
                                            type: 'error',   
                                            timer: 3000   ,
                                            showConfirmButton: false
                                            })");
            $oReturn->assign("divFormularioDetalle2", "innerHTML", '');
        }
    } catch (Exception $ex) {
        $oReturn->alert($ex->getMessage());
    }

    $oReturn->script("jsRemoveWindowLoad();");
    return $oReturn;
}

function cargar_ord_compra($aForm = '')
{
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oCon = new Dbo;
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo;
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse();


    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $aForm['sucursal'];
    $iva_sn = $aForm['iva_sn'];


    unset($_SESSION['U_PROD_COD_PRECIO']);

    unset($_SESSION['aDataGird_INV_MRECO']);

    //////////////

    try {

        // DATOS
        // BODEGA


        // ---------------------------------------------------------------------------------------------------------
        // CONTROL CLPV POR USUARIO, SUCURSALES
        // ---------------------------------------------------------------------------------------------------------
        $id_usuario_comercial = $_SESSION['U_ID'];
        $bloqueo_sucu_sn = 'N';
        $sucursales_usuario = '';
        $sql_data_usuario_sucu = "SELECT bloqueo_sucu_sn, sucursales_usuario from comercial.usuario where usuario_id = $id_usuario_comercial";
        if ($oIfx->Query($sql_data_usuario_sucu)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    $bloqueo_sucu_sn = $oIfx->f('bloqueo_sucu_sn');
                    $sucursales_usuario = $oIfx->f('sucursales_usuario');
                } while ($oIfx->SiguienteRegistro());
            }
        }
        $sql_adicional_sucu = "";
        $oIfx->Free();
        if ($bloqueo_sucu_sn == 'S') {
            if (!empty($sucursales_usuario)) {
                $sql_adicional_sucu = ' and subo_cod_sucu in (' . $sucursales_usuario . ')';
            } else {
                $sql_adicional_sucu = 'and subo_cod_sucu = ' . $idsucursal;
            }
        }
        // ---------------------------------------------------------------------------------------------------------
        // FIN CONTROL CLPV POR USUARIO, SUCURSALES
        // ---------------------------------------------------------------------------------------------------------


        $sql = "select bode_cod_bode, bode_nom_bode from saesubo, saebode where
                        bode_cod_bode = subo_cod_bode and
                        bode_cod_empr = $idempresa and
                        subo_cod_empr = $idempresa
                        $sql_adicional_sucu
                        ";
        unset($array_bode);
        unset($array_bode_cod);
        $array_bode     = array_dato($oIfx, $sql, 'bode_nom_bode', 'bode_nom_bode');
        $array_bode_cod = array_dato($oIfx, $sql, 'bode_nom_bode', 'bode_cod_bode');


        // PRODUCTO
        $sql = "select prod_cod_prod, prod_nom_prod from saeprod where
                        prod_cod_empr = $idempresa and
                        prod_cod_sucu = $idsucursal
                        group by 1,2  ";
        unset($array_prod);
        unset($array_prod_cod);
        //$array_prod     = array_dato($oIfx, $sql, 'prod_cod_prod', 'prod_cod_prod');


        // CENTRO DE COSTO
        $sql = "select ccosn_cod_ccosn,  ccosn_nom_ccosn
                from saeccosn where
                ccosn_cod_empr = $idempresa and
                ccosn_mov_ccosn = 1 order by 2";

        unset($array_prec);
        unset($array_prec_cod);
        //$array_prec     = array_dato($oIfx, $sql, 'ccosn_nom_ccosn', 'ccosn_nom_ccosn');
        $array_prec_cod = array_dato($oIfx, $sql, 'ccosn_cod_ccosn', 'ccosn_cod_ccosn');

        $archivo = $aForm['archivo'];

        // archivo txt
        $archivo_real = substr($archivo, 12);
        list($xxxx, $exten) = explode(".", $archivo_real);

        if ($exten == 'txt') {
            $nombre_archivo = "upload/" . $archivo_real;

            $file       = fopen($nombre_archivo, "r");
            $datos      = file($nombre_archivo);
            $NumFilas   = count($datos);

            unset($aDataGrid);
            unset($aDataPrecio);
            $aDataGrid  = $_SESSION['aDataGird_INV_MRECO'];
            $aDataPrecio  = $_SESSION['aDataGird_PRECIO'];
            $aLabelGrid = $_SESSION['aLabelGirdProd_INV_MRECO'];



            $cont = 0;
            $cont_pvp = 0;
            $datos_txt = explode("	", $datos[0]);
            foreach ($datos_txt as $val1) {
                if ($cont > 6) {
                    $cont_pvp++;
                }
                $cont++;
            }



            $x = 1;
            $oReturn->alert('Buscando ...');
            unset($array);
            foreach ($datos as $val) {
                /*BODEGA	    CODIGO	        PRODUCTO	    CANTIDAD	        CENTRO DE COSTO	        FOB
                        */

                list(
                    $bode_cod,
                    $prod_cod,
                    $prod_nom,
                    $cantidad,
                    $lote_serie_txt,
                    $fecha_ela_txt,
                    $fecha_cad_txt,
                    $mac_prod,
                    $ccosto,
                    $fob,
                    $fob_real,
                    $pvp1,
                    $pvp2,
                    $pvp3,
                    $pvp4,
                    $pvp5,
                    $pvp6,
                    $pvp7,
                    $pvp8,
                    $pvp9,
                    $pvp10
                ) = explode("	", $val);
                $costo_limpio = str_replace(',', '.', $fob);


                if ($x > 1 && !empty($bode_cod)) {

                    $array[] = array(
                        $array_bode_cod[$bode_cod],
                        $prod_cod,
                        $prod_nom,
                        $array_prec_cod[$ccosto],
                        $cantidad,
                        $costo_limpio
                    );

                    // echo($array[0][0][0]);
                    // exit;

                    $sql_prbo_cuentas = "select prbo_cta_inv, prbo_cta_ideb, prbo_cod_unid, COALESCE(prbo_iva_porc,0) as prbo_iva_porc from saeprbo where prbo_cod_prod = '$prod_cod' and prbo_cod_sucu = $idsucursal";
                    if ($oIfx->Query($sql_prbo_cuentas)) {
                        if ($oIfx->NumFilas() > 0) {
                            do {
                                $prbo_cta_inv = $oIfx->f('prbo_cta_inv');
                                $prbo_cta_ideb = $oIfx->f('prbo_cta_ideb');
                                $prbo_cod_unid = $oIfx->f('prbo_cod_unid');
                                $iva = $oIfx->f('prbo_iva_porc');
                            } while ($oIfx->SiguienteRegistro());
                        }
                    }
                    $oIfx->Free();


                    if ($iva_sn != 'S') {
                        $iva = 0;
                    }

                    $cantidad             = $cantidad;
                    $codigo_barra         = '';
                    $codigo_producto     = $prod_cod;
                    $costo                = $costo_limpio;
                    $idbodega             = $array_bode_cod[$bode_cod];
                    $descuento             = 0;
                    $descuento_2         = 0;
                    $cuenta_inv         = $prbo_cta_inv;
                    $cuenta_iva         = $prbo_cta_ideb;
                    $lote_prod           = $lote_serie_txt;
                    $fecha_ela           = $fecha_ela_txt;
                    $fecha_cad           = $fecha_cad_txt;
                    $peso                  = 0;
                    $idunidad         = $prbo_cod_unid;



                    $descuento_general = 0;
                    // TOTAL
                    $total_fac     = 0;
                    $dsc1         = ($costo * $cantidad * $descuento) / 100;
                    $dsc2         = ((($costo * $cantidad) - $dsc1) * $descuento_2) / 100;
                    if ($descuento_general > 0) {
                        // descto general
                        $dsc3                 = ((($costo * $cantidad) - $dsc1 - $dsc2) * $descuento_general) / 100;
                        $total_fact_tmp     = ((($costo * $cantidad) - ($dsc1 + $dsc2 + $dsc3)));
                        $tmp                 = ((($costo * $cantidad) - ($dsc1 + $dsc2)));
                    } else {
                        // sin descuento general
                        $total_fact_tmp     = ((($costo * $cantidad) - ($dsc1 + $dsc2)));
                        $tmp                 = $total_fact_tmp;
                    }

                    $total_fac = round($total_fact_tmp, 2);

                    // total con iva
                    if ($iva > 0) {
                        $total_con_iva = round((($total_fac * $iva) / 100), 2) + $total_fac;
                    } else {
                        $total_con_iva = $total_fac;
                    }







                    //GUARDA LOS DATOS DEL DETALLE
                    $cont = count($aDataGrid);
                    // cantidad
                    $ifu->AgregarCampoNumerico($cont . '_cantidad', 'Cantidad|LEFT', false, $cantidad, 40, 40);
                    $ifu->AgregarComandoAlCambiarValor($cont . '_cantidad', 'cargar_update_cant(\'' . $cont . '\');');

                    // costo
                    $ifu->AgregarCampoNumerico($cont . '_costo', 'Costo|LEFT', false, $costo, 40, 40);
                    $ifu->AgregarComandoAlCambiarValor($cont . '_costo', 'cargar_update_cant(\'' . $cont . '\');');

                    // iva
                    $ifu->AgregarCampoNumerico($cont . '_iva', 'Iva|LEFT', false, $iva, 40, 40);
                    $ifu->AgregarComandoAlCambiarValor($cont . '_iva', 'cargar_update_cant(\'' . $cont . '\');');

                    // descto1
                    $ifu->AgregarCampoNumerico($cont . '_desc1', 'Descto1|LEFT', false, $descuento, 40, 40);
                    $ifu->AgregarComandoAlCambiarValor($cont . '_desc1', 'cargar_update_cant(\'' . $cont . '\');');

                    // descto2
                    $ifu->AgregarCampoNumerico($cont . '_desc2', 'Descto2|LEFT', false, 0, 40, 40);
                    $ifu->AgregarComandoAlCambiarValor($cont . '_desc2', 'cargar_update_cant(\'' . $cont . '\');');

                    // PESO
                    $ifu->AgregarCampoNumerico($cont . '_peso', 'Peso|LEFT', false, $peso, 40, 40);
                    $ifu->AgregarComandoAlCambiarValor($cont . '_peso', 'cargar_update_cant(\'' . $cont . '\');');


                    $aDataGrid[$cont][$aLabelGrid[0]] = floatval($cont);
                    $aDataGrid[$cont][$aLabelGrid[1]] = $idbodega;
                    $aDataGrid[$cont][$aLabelGrid[2]] = $codigo_producto;
                    $aDataGrid[$cont][$aLabelGrid[3]] = $prod_nom;
                    $aDataGrid[$cont][$aLabelGrid[4]] = $idunidad;
                    $aDataGrid[$cont][$aLabelGrid[5]] = $cantidad;  //$cantidad;
                    $aDataGrid[$cont][$aLabelGrid[6]] = $costo; //costo;
                    $aDataGrid[$cont][$aLabelGrid[7]] = $iva; //iva
                    $aDataGrid[$cont][$aLabelGrid[8]] = $descuento; // desc1
                    $aDataGrid[$cont][$aLabelGrid[9]] = 0; // dec2
                    $aDataGrid[$cont][$aLabelGrid[10]] = $descuento_general;
                    $aDataGrid[$cont][$aLabelGrid[11]] = $total_fac;
                    $aDataGrid[$cont][$aLabelGrid[12]] = $total_con_iva;
                    $aDataGrid[$cont][$aLabelGrid[13]] = $lote_prod;
                    $aDataGrid[$cont][$aLabelGrid[14]] = $fecha_ela;
                    $aDataGrid[$cont][$aLabelGrid[15]] = $fecha_cad;
                    $aDataGrid[$cont][$aLabelGrid[16]] = '';
                    $aDataGrid[$cont][$aLabelGrid[17]] = '';
                    $aDataGrid[$cont][$aLabelGrid[18]] = $cuenta_inv;
                    $aDataGrid[$cont][$aLabelGrid[19]] = $cuenta_iva;
                    $aDataGrid[$cont][$aLabelGrid[20]] = '';
                    $aDataGrid[$cont][$aLabelGrid[21]] = '';
                    $aDataGrid[$cont][$aLabelGrid[22]] = 0;
                    $aDataGrid[$cont][$aLabelGrid[23]] = $serie_prod;
                    $aDataGrid[$cont][$aLabelGrid[24]] = '';
                    $aDataGrid[$cont][$aLabelGrid[25]] = '';
                    $aDataGrid[$cont][$aLabelGrid[26]] = '';        // Recepcion codigos unicos
                    $aDataGrid[$cont][$aLabelGrid[27]] = $mac_prod;        // Mac del producto


                    $aDataGrid[$cont]['ccosn'] = $array_prec_cod[$ccosto];
                    $aDataGrid[$cont]['fob_real'] = $fob_real;

                    for ($j = 1; $j <= $cont_pvp; $j++) {
                        $aDataGrid[$cont]["pvp" . $j] = ${"pvp" . $j};
                    }


                    // $aDataGrid[$cont]['precio'] = 47;



                    // $aDataPrecio[$cont]['precio_1'] = 






                    //Final de la lectura del archivo
                }
                $x++;
            }


            $_SESSION['aDataGird_INV_MRECO'] = $aDataGrid;
            $sHtml = mostrar_grid();
            $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
            //$oReturn->script('limpiar_prod()');
            $oReturn->script('habilita(5)');
            $oReturn->script('totales();');
            $oReturn->script('cerrar_ventana();');
        } else {
            $oReturn->script("Swal.fire({
                                            title: '<h3><strong>!!!!....Archivo Incorrecto, por favor subir Archivo con extension .txt...!!!!!</strong></h3>',
                                            width: 800,
                                            type: 'error',   
                                            timer: 3000   ,
                                            showConfirmButton: false
                                            })");
            $oReturn->assign("divFormularioDetalle", "innerHTML", '');
        }
    } catch (Exception $ex) {
        $oReturn->alert($ex->getMessage());
    }

    $oReturn->script("jsRemoveWindowLoad();");
    return $oReturn;
}








function recepcion_compra_det($serial, $idempresa, $idsucursal, $aForm = '')
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo();
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $oReturn = new xajaxResponse();

    $sHtml .= ' <table class="table table-striped table-condensed" style="width: 96%; margin-bottom: 0px;" align="center">
                    <tr>
                        <td class="info">No</td>
						<td class="info" align="center">Codigo</td>
                        <td class="info" align="center">Producto</td>
                        <td class="info" align="center">Cantidad</td>                        
                        <td class="info" align="center">Costo</td>
						<td class="info" align="center">Total</td>
                    </tr>';

    $sql = "select dmov_cod_prod, dmov_cod_bode, dmov_cod_unid,
				dmov_can_dmov, dmov_cun_dmov, dmov_cto_dmov
				from saedmov where
                dmov_can_dmov <> dmov_can_entr and 
				dmov_cod_empr = $idempresa and
				dmov_cod_sucu = $idsucursal and
				dmov_num_comp = $serial ";
    //$oReturn->alert($sql);
    $i = 1;
    $total = 0;
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            do {
                $codigo    = ($oIfx->f('dmov_cod_prod'));
                $sql = "select prod_nom_prod from saeprod where prod_cod_empr = $idempresa and prod_cod_prod = '$codigo' ";
                if ($oIfxA->Query($sql)) {
                    if ($oIfxA->NumFilas() > 0) {
                        $nom_prod  = htmlentities($oIfxA->f('prod_nom_prod'));
                    }
                }

                $cant      = $oIfx->f('dmov_can_dmov');
                $costo     = $oIfx->f('dmov_cun_dmov');
                $subt      = $oIfx->f('dmov_cto_dmov');

                $sClass = ($sClass == 'off') ? $sClass = 'on' : $sClass = 'off';
                $sHtml .= '<tr height="20" class="' . $sClass . '"
										onMouseOver="javascript:this.className=\'link\';"
										style="cursor: hand !important; cursor: pointer !important;"
										onMouseOut="javascript:this.className=\'' . $sClass . '\';"	>';

                $sHtml .= '<td>' . $i . '</td>';
                $sHtml .= '<td>' . $codigo . '</td>';
                $sHtml .= '<td>' . $nom_prod . '</td>';
                $sHtml .= '<td align="right">' . $cant . '</td>';
                $sHtml .= '<td align="right">' . $costo . '</td>';
                $sHtml .= '<td align="right">' . $subt . '</td>';
                $sHtml .= '</tr>';

                $i++;
                $total += $subt;
            } while ($oIfx->SiguienteRegistro());
            $sHtml .= '<tr height="25">';
            $sHtml .= '<td></td>';
            $sHtml .= '<td></td>';
            $sHtml .= '<td align="right"></td>';
            $sHtml .= '<td align="right"></td>';
            $sHtml .= '<td align="right" class="fecha_letra">TOTAL:</td>';
            $sHtml .= '<td align="right" class="fecha_letra">' . $total . '</td>';
            $sHtml .= '</tr>';
        }
    }
    $sHtml .= '</table>';

    $modal  = '<div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">DETALLE MOVIMIENTO</h4>
                        </div>
                        <div class="modal-body">';
    $modal .= $sHtml;
    $modal .= '          </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>';

    $oReturn->assign("ModalRECOD", "innerHTML", $modal);

    return $oReturn;
}


function cargar_reco($id_empresa, $id_sucursal, $cliente, $aForm = '')
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN, $DSN_Ifx;

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo;
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    unset($_SESSION['aDataGird_INV_MRECO']);
    $aDataGrid  = $_SESSION['aDataGird_INV_MRECO'];
    $aLabelGrid = $_SESSION['aLabelGirdProd_INV_MRECO'];

    $oReturn = new xajaxResponse();

    $array = $_SESSION['U_PROF_RECO'];
    unset($_SESSION['U_PROF_APROB_RECO']);

    $tasa_efectiva_sn = $aForm['tasa_efectiva_sn'];

    if (count($array) > 0) {
        //GUARDA LOS DATOS DEL DETALLE
        $id_prof    = '';
        $precio     = 0;
        $desc       = 0;
        $flete      = 0;
        $otro       = 0;
        unset($array_aprob);
        foreach ($array as $val) {
            $serial     = $val[0];
            $preimp     = $val[1];
            $clpv_cod     = $val[2];
            $desc_gen     = $val[3];
            $minv_fec     = $val[4];
            $minv_msn     = $val[5];

            $check = $aForm[$serial];
            if (!empty($check)) {
                $array_aprob[] = array($clpv_cod, $preimp, $serial);
                $sql = "select  d.dmov_cod_prod, d.dmov_cod_bode, d.dmov_cod_unid,  d.dmov_iva_porc,d.dmov_cod_pedi,
                               (d.dmov_can_dmov - d.dmov_can_entr) as cantidad, 
                                p.prbo_cta_inv, p.prbo_cta_ideb, p.prbo_iva_porc, d.dmov_cun_dmov, dmov_det1_dmov, dmov_cod_lote, dmov_cod_dmov,dmov_cod_serie
                                from saedmov d , saeprbo p where
                                p.prbo_cod_prod = d.dmov_cod_prod and
                                d.dmov_cod_bode = p.prbo_cod_bode and
                                p.prbo_cod_empr = $id_empresa and
                                p.prbo_cod_sucu = $id_sucursal and
                                d.dmov_can_dmov <> d.dmov_can_entr and
                                d.dmov_num_comp = $serial and
                                d.dmov_cod_empr = $id_empresa and
                                d.dmov_cod_sucu = $id_sucursal order by d.dmov_cod_dmov ";
                //echo $sql;exit;
                //$oReturn->alert($sql);
                if ($oIfx->Query($sql)) {
                    if ($oIfx->NumFilas() > 0) {
                        do {
                            $cont       = count($aDataGrid);
                            $prod_cod   = $oIfx->f('dmov_cod_prod');
                            $bode_cod   = $oIfx->f('dmov_cod_bode');
                            $unid_cod   = $oIfx->f('dmov_cod_unid');
                            $cantidad   = $oIfx->f('cantidad');
                            $costo      = $oIfx->f('dmov_cun_dmov');
                            $cta_inv    = $oIfx->f('prbo_cta_inv');
                            $cta_iva    = $oIfx->f('prbo_cta_ideb');
                            // $iva        = $oIfx->f('prbo_iva_porc');
                            $iva        = $oIfx->f('dmov_iva_porc');
                            $detalle    = $oIfx->f('dmov_det1_dmov');
                            $lote_cod   = $oIfx->f('dmov_cod_lote');
                            $serie_lote  = $oIfx->f('dmov_cod_serie');
                            $dmov_cod   = $oIfx->f('dmov_cod_dmov');
                            //CODIGO SAEPEDI ORDEN DE COMPRA
                            $dmov_cod_pedi   = intval($oIfx->f('dmov_cod_pedi'));

                            $sql        = "select prod_nom_prod from saeprod where prod_cod_empr = $id_empresa and prod_cod_sucu = $id_sucursal and prod_cod_prod = '$prod_cod' ";
                            $prod_nom   = consulta_string_func($sql, 'prod_nom_prod', $oIfxA, '');




                            // -------------------------------------------------------------------------------------------------------
                            // VALIDAMOS SI TIENE EL CHECK DE TASA EFECTIVA
                            // -------------------------------------------------------------------------------------------------------
                            if ($tasa_efectiva_sn == 'S') {
                                $costo_tasa_efec = $costo;                                                  // 887.55
                                $iva_tasa_efec = $iva;                                                      // 13%
                                // Calculos para caluclar la tasa efectiva
                                $porcentaje_impuesto = (100 - $iva_tasa_efec) / 100;                        // (100 - 13)/100 => 0.87
                                $costo_diferencia = $costo_tasa_efec * $porcentaje_impuesto;                // 887.55 * 0.87 => 772.16              >Costo que baja a la tabla
                                $costo_calculado = $costo_tasa_efec - $costo_diferencia;                    // 887.55 - 772.16 => 115.38
                                $iva_diferencia = ($costo_calculado * 100) / $costo_diferencia;             // (115.38 * 100) / 772.16 => 14.9453   >Iva que baja a la tabla

                                $costo = round($costo_diferencia, 4);
                                $iva = round($iva_diferencia, 6);
                            }
                            // -------------------------------------------------------------------------------------------------------
                            // FIN VALIDAMOS SI TIENE EL CHECK DE TASA EFECTIVA
                            // -------------------------------------------------------------------------------------------------------





                            // TOTAL
                            $total_fac          = 0;
                            $descuento          = 0;
                            $descuento_2        = 0;
                            $descuento_general  = 0;
                            $dsc1               = ($costo * $cantidad * $descuento) / 100;
                            $dsc2               = ((($costo * $cantidad) - $dsc1) * $descuento_2) / 100;
                            if ($descuento_general > 0) {
                                // descto general
                                $dsc3           = ((($costo * $cantidad) - $dsc1 - $dsc2) * $descuento_general) / 100;
                                $total_fact_tmp = ((($costo * $cantidad) - ($dsc1 + $dsc2 + $dsc3)));
                                $tmp            = ((($costo * $cantidad) - ($dsc1 + $dsc2)));
                            } else {
                                // sin descuento general
                                $total_fact_tmp = ((($costo * $cantidad) - ($dsc1 + $dsc2)));
                                $tmp            = $total_fact_tmp;
                            }

                            $total_fac          = round($total_fact_tmp, 2);

                            // total con iva
                            if ($iva > 0) {
                                $total_con_iva = round((($total_fac * $iva) / 100), 2) + $total_fac;
                            } else {
                                $total_con_iva = $total_fac;
                            }

                            $cont = count($aDataGrid);
                            $aDataGrid[$cont][$aLabelGrid[0]] = floatval($cont);
                            $aDataGrid[$cont][$aLabelGrid[1]] = $bode_cod;
                            $aDataGrid[$cont][$aLabelGrid[2]] = $prod_cod;
                            $aDataGrid[$cont][$aLabelGrid[3]] = $prod_nom;
                            $aDataGrid[$cont][$aLabelGrid[4]] = $unid_cod;
                            $aDataGrid[$cont][$aLabelGrid[5]] = $cantidad;  //$cantidad;
                            $aDataGrid[$cont][$aLabelGrid[6]] = $costo; //costo;
                            $aDataGrid[$cont][$aLabelGrid[7]] = $iva; //iva                
                            $aDataGrid[$cont][$aLabelGrid[8]] = $descuento; // desc1
                            $aDataGrid[$cont][$aLabelGrid[9]] = $descuento_2; // dec2
                            $aDataGrid[$cont][$aLabelGrid[10]] = $descuento_general;
                            $aDataGrid[$cont][$aLabelGrid[11]] = $total_fac;
                            $aDataGrid[$cont][$aLabelGrid[12]] = $total_con_iva;
                            $aDataGrid[$cont][$aLabelGrid[13]] = $lote_cod;
                            $aDataGrid[$cont][$aLabelGrid[14]] = '';
                            $aDataGrid[$cont][$aLabelGrid[15]] = '';
                            $aDataGrid[$cont][$aLabelGrid[16]] = $detalle;
                            $aDataGrid[$cont][$aLabelGrid[17]] = '';
                            $aDataGrid[$cont][$aLabelGrid[18]] = $cta_inv;
                            $aDataGrid[$cont][$aLabelGrid[19]] = $cta_iva;
                            $aDataGrid[$cont][$aLabelGrid[20]] = '';
                            $aDataGrid[$cont][$aLabelGrid[21]] = '';
                            $aDataGrid[$cont][$aLabelGrid[22]] = $dmov_cod;
                            $aDataGrid[$cont][$aLabelGrid[23]] = $serie_lote;
                        } while ($oIfx->SiguienteRegistro());
                        $_SESSION['aDataGird_INV_MRECO'] = $aDataGrid;
                        $sHtml = mostrar_grid();
                        $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
                    }
                }

                $oIfx->Free();
                $oReturn->script('totales_oc(' . $desc_gen . ');');
                $oReturn->assign("descuento_general", "value", $desc_gen);
                $oReturn->assign("observaciones",     "value", $minv_msn);
                // list($a, $b, $c)     = explode('/', $minv_fec);

                //$minv_fec           = $c . '-' . $b . '-' . $a;


                $oReturn->script('cargar_fecha_reco( \'' . $minv_fec . '\' );');

                $_SESSION['U_PROF_APROB_RECO'] = $array_aprob;
                //$_SESSION['U_OTROS']=;
            } // fin if
        } // fin foreach        

        $oReturn->script('cerrar_ventana();');
    } else {
        $oReturn->alert('Por favor seleccione una Pedido...');
    }

    $oReturn->script('cargar_descuento_oc(' . $desc_gen . ');');

    return $oReturn;
}




function datos_prod($i, $lote_prod, $serie_prod, $mac_prod, $aForm = '')
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $oReturn = new xajaxResponse();

    unset($array_tmp);
    $array_tmp = $_SESSION['U_PROD_RSC'];


    if (count($array_tmp) > 0) {
        $x = 1;
        $prbo_cod_prod = '';
        $prod_nom_prod = '';
        $prbo_cta_inv = '';
        $prbo_cta_ideb = '';
        $prbo_uco_prod = '';
        $prbo_iva_porc = '';
        $lote          = '';
        $serie = '';
        foreach ($array_tmp as $val) {
            if ($x == $i) {
                $prbo_cod_prod = $val[0];
                $prod_nom_prod = $val[1];
                $prbo_cta_inv = $val[2];
                $prbo_cta_ideb = $val[3];
                $prbo_uco_prod = $val[4];
                $prbo_iva_porc = $val[5];
                $lote          = $val[6];
                $serie         = $val[7];
            }
            $x++;
        }
        $oReturn->assign("codigo_producto",     "value", $prbo_cod_prod);
        $oReturn->assign("producto",            "value", $prod_nom_prod);
        $oReturn->assign("cuenta_inv",          "value", $prbo_cta_inv);
        $oReturn->assign("cuenta_iva",          "value", $prbo_cta_ideb);
        $oReturn->assign("costo",               "value", $prbo_uco_prod);
        $oReturn->assign("iva",                 "value", $prbo_iva_porc);

        $oReturn->script('vaciar_validacion_fecha()');

        if ($lote_prod == 'S') {
            $oReturn->script('habilita(1)');
        }

        if ($serie_prod == 'S') {
            if ($mac_prod == 'S') {
                $oReturn->script('habilita(47)');
            } else {
                $oReturn->script('habilita(3)');
            }
        }

        if ($serie_prod != 'S' && $lote_prod != 'S') {
            $oReturn->script('habilita(5)');
        }

        $oReturn->script('datos_prod2(\'' . $lote . '\', \'' . $serie . '\', \'' . $mac_prod . '\' );');

        /*document.form1.codigo_producto.value    = a;
        document.form1.producto.value           = b;
        document.form1.cuenta_inv.value         = c;
        document.form1.cuenta_iva.value         = d;
        document.form1.costo.value              = e;
        document.form1.iva.value                = f;
      */
    }

    // var_dump($array_tmp);
    return $oReturn;
}








// --------------------------------------------------------------------------------------
// Evaluacion control calidad
// --------------------------------------------------------------------------------------
function abrir_evaluacion($aForm = '', $cont)
{
    global $DSN, $DSN_Ifx;
    session_start();

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oCon = new Dbo;
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oConA = new Dbo;
    $oConA->DSN = $DSN;
    $oConA->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];



    $oReturn = new xajaxResponse();
    $sHtml  .= '<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">REGISTRO DE RECEPCION DEL PRODUCTO</h4>
        </div>
        <div class="modal-body">
        <div class="btn btn-success btn-sm" onclick="javascript:guardar_nueva_evaluacion(' . $cont . ');">
                                    <span class="glyphicon glyphicon-disk"></span> Guardar
                                </div>
        ';

    $sHtml .= ' <table class="table table-striped table-condensed table-bordered table-hover" style="width: 98%; margin-top: 20px;" align="center">
    ';


    $sql = "SELECT * FROM recepcion_parametros where id_empresa = $empresa
                                and nombre_parametro <> 'NOMBRE_RECIBE' and 
                                nombre_parametro <> 'NOMBRE_ENTREGA' and
                                nombre_parametro <> 'MOTIVO_NOVEDAD' and
                                nombre_parametro <> 'DESCRIPCION_NOVEDAD' and
                                nombre_parametro <> 'SN_DEVOLUCION'";
    if ($oCon->Query($sql)) {
        if ($oCon->NumFilas() > 0) {
            do {
                $fu->AgregarCampoSi_No('snrecepcion' . $oCon->f('id'), '' . $oCon->f('nombre_parametro') . '|left', 'S');
                $fu->AgregarComandoAlCambiarValor('snrecepcion' . $oCon->f('id'), 'cargar_novedades_recepcion()');


                $fu->AgregarCampoSi_No('sndevolucion', 'Devolucion|left', 'S');


                $sHtml .= '<tr>
                                <td class="labelFrm">' . $fu->ObjetoHtmlLBL('snrecepcion' . $oCon->f('id')) . '</td>
                                <td>' . $fu->ObjetoHtml('snrecepcion' . $oCon->f('id')) . '</td>
                                <td>
                                    <label for="observaciones">* Observaciones:</label>
                                    <input type="text" class="form-control input-sm" id="rec_observaciones' . $oCon->f('id') . '" name="rec_observaciones' . $oCon->f('id') . '" style="text-align:left" />
                                </td>
                            </tr>';
            } while ($oCon->SiguienteRegistro());
        }
    }
    $oCon->Free();

    $sHtml .= '</table>';

    $sHtml .= '
            <div style="display: none" id="novedades_class" name="novedades_class" >
            <table  class="table table-striped table-condensed table-bordered table-hover" style="width: 98%; margin-top: 20px;" align="center">
            <tr>
                <td colspan="3" style="width:100%;font-size:14px; text-align: center;  font-family: Courier; "><strong>MOTIVO DE LA NOVEDAD</strong></td>
            </tr>
            <tr>
                <td colspan="3" style="width:100%;font-size:14px; text-align: center;  font-family: Courier; ">
                    <input type="text" class="form-control input-sm" id="motiv_nove" name="motiv_nove" style="text-align:left" />
                </td>
            </tr>
            <tr>
                <td colspan="3" style="width:100%;font-size:14px; text-align: center;  font-family: Courier; "><strong>DESCRIPCION DE LA NOVEDAD</strong></td>
            </tr>
            <tr>
                <td colspan="3" style="width:100%;font-size:14px; text-align: center;  font-family: Courier; ">
                    <input type="text" class="form-control input-sm" id="desc_nove" name="desc_nove" style="text-align:left" />
                </td>
            </tr>
            <tr>
                <td colspan="2" class="labelFrm">' . $fu->ObjetoHtmlLBL('sndevolucion') . '</td>
                <td>' . $fu->ObjetoHtml('sndevolucion') . '</td>
            </tr>
            
            <tr>
                <td colspan="3">
                    <label for="observaciones">* Nombre Persona Recibe:</label>
                    <input type="text" class="form-control input-sm" id="recibe_nombre" name="recibe_nombre" style="text-align:left" />
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <label for="observaciones">* Nombre Persona Entrega:</label>
                    <input type="text" class="form-control input-sm" id="entrega_nombre" name="entrega_nombre" style="text-align:left" />
                </td>
            </tr>
            </table>
            </div>
            ';






    $sHtml .= '             </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>';









    $oReturn->assign("ModalEval", "innerHTML", $sHtml);

    return $oReturn;
}



// Formulario para completar la evaluacion
function guardar_nueva_evaluacion($aForm = '', $cont)
{
    global $DSN, $DSN_Ifx;
    session_start();

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oCon = new Dbo;
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oConA = new Dbo;
    $oConA->DSN = $DSN;
    $oConA->Conectar();

    $oConB = new Dbo;
    $oConB->DSN = $DSN;
    $oConB->Conectar();

    $oReturn = new xajaxResponse();

    unset($_SESSION['aDataGird_EVALUACION']);


    $idempresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];


    $recibe_nombre = $aForm['recibe_nombre'];
    $entrega_nombre = $aForm['entrega_nombre'];

    $motiv_nove = $aForm['motiv_nove'];
    $desc_nove = $aForm['desc_nove'];
    $sndevolucion = $aForm['sndevolucion'];

    $encargado_eval = strtoupper($aForm['encargado_eval']);
    $opciones_sn = array();


    $sql = "SELECT * FROM recepcion_parametros";
    if ($oConA->Query($sql)) {
        if ($oConA->NumFilas() > 0) {
            do {
                $id_recepcion_parametros = $oConA->f('id');
                $sneval = $aForm['snrecepcion' . $oConA->f('id')];
                $observ_eval = $aForm['rec_observaciones' . $oConA->f('id')];

                if (empty($sneval)) {
                    $sneval = 'N';
                }





                if ($oConA->f('nombre_parametro') == 'NOMBRE_RECIBE') {
                    $array = array(
                        "id_evaluacion_parametros" => $id_recepcion_parametros,
                        "sneval" => '',
                        "observ_eval" => $recibe_nombre,
                    );
                } else if ($oConA->f('nombre_parametro') == 'NOMBRE_ENTREGA') {
                    $array = array(
                        "id_evaluacion_parametros" => $id_recepcion_parametros,
                        "sneval" => '',
                        "observ_eval" => $entrega_nombre,
                    );
                } else if ($oConA->f('nombre_parametro') == 'MOTIVO_NOVEDAD') {
                    $array = array(
                        "id_evaluacion_parametros" => $id_recepcion_parametros,
                        "sneval" => '',
                        "observ_eval" => $motiv_nove,
                    );
                } else if ($oConA->f('nombre_parametro') == 'DESCRIPCION_NOVEDAD') {
                    $array = array(
                        "id_evaluacion_parametros" => $id_recepcion_parametros,
                        "sneval" => '',
                        "observ_eval" => $desc_nove,
                    );
                } else if ($oConA->f('nombre_parametro') == 'SN_DEVOLUCION') {
                    $array = array(
                        "id_evaluacion_parametros" => $id_recepcion_parametros,
                        "sneval" => $sndevolucion,
                        "observ_eval" => '',
                    );
                } else {
                    $array = array(
                        "id_evaluacion_parametros" => $id_recepcion_parametros,
                        "sneval" => $sneval,
                        "observ_eval" => $observ_eval,
                    );
                }

                array_push($opciones_sn, $array);
            } while ($oConA->SiguienteRegistro());
        }
    }
    $oConA->Free();


    $_SESSION['aDataGird_RECEPCION'] = $opciones_sn;

    $oReturn->alert('Recepcion Registrada correctamente');
    return $oReturn;
}


function guardar_evaluacion($aForm = '', $cont)
{
    session_start();
    global $DSN_Ifx;

    $oReturn = new xajaxResponse();

    $idempresa = $aForm['empresa'];
    $idsucursal = $aForm['sucursal'];
    $tran_cod  = $aForm['tran'];
    $ejer_cod  = $aForm['ejercicio'];
    $prdo_cod  = $aForm['periodo'];
    $clpv_nom  = $aForm['cliente_nombre'];


    $aDataGrid  = $_SESSION['aDataGird_INV_MRECO'];
    $idproducto = $aDataGrid[$cont]['Codigo Item'];
    $informacion_evaluacion = $aDataGrid[$cont]['datos_evaluacion'];
    $serial = 1;

    unset($_SESSION['pdf']);
    // $diario =  generar_evaluacion_parametros_pdf($idempresa, $idsucursal, $serial, $tran_cod, $ejer_cod, $prdo_cod, $idproducto );
    $diario =  generar_recepcion_only_read($idempresa, $idsucursal, $idproducto, $clpv_nom, $informacion_evaluacion);

    $_SESSION['pdf'] = $diario;
    $oReturn->script('generar_pdf_recepcion()');

    return $oReturn;
}


// --------------------------------------------------------------------------------------
// FIN Evaluacion control calidad
// --------------------------------------------------------------------------------------





// ---------------------------------------------------------------------------------------------
// funciones pollos campo balanza
// --------------------------------------------------------------------------------------

function modal_balanza($aForm = '')
{

    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $idbodega_s = $_SESSION['U_BODEGA'];

    try {



        $sql = "SELECT  b.bode_cod_bode, b.bode_nom_bode from saebode b, saesubo s where
                    b.bode_cod_bode = s.subo_cod_bode and
                    b.bode_cod_empr = $empresa and
                    s.subo_cod_empr = $empresa and
                    s.subo_cod_sucu = $sucursal";
        $lista_bode = lista_boostrap($oIfx, $sql, $idbodega_s, 'bode_cod_bode',  'bode_nom_bode');


        $html_area_prod = '';

        $html_area_prod .= '<div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table align="left" class="table table-striped table-condensed" style="width: 100%; margin-bottom: 0px;">
                                            <tr><td colspan="4" align="center" class="bg-primary">DETALLE COMPRAS</td></tr>
                                        </table>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="col-md-12">
                                        <input type="number" class="form-control input-sm" id="procesar_sn_bal" name="procesar_sn_bal" style="display: none" >

                                        <div class="form-check" style="float: left !important">
                                            <input class="form-check-input" type="checkbox" value="S" id="recalcular_demiso_sn" name="recalcular_demiso_sn" checked>
                                            <label class="form-check-label" for="flexCheckChecked">
                                                Recalcular Decomiso
                                            </label>
                                        </div>
                                        
                                        <div class="btn btn-primary btn-sm" onclick="procesar_info()" style="float: right !important">
                                            <span class="glyphicon glyphicon-check"></span> Procesar Informacion 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <table id="tbclientes_prod" class="table table-striped table-condensed table-bordered table-hover" style="width: 98%; margin-top: 20px;" align="center">
                                            <thead>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Fecha</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="date" class="form-control input-sm" id="fecha_bal" name="fecha_bal" value="' . date('Y-m-d') . '" >
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Nombre Recibe</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="text" class="form-control input-sm" id="nombre_recibe_bal" name="nombre_recibe_bal" placeholder="Nombre recibe" >
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Proveedor</td>
                                                    <td class="fecha_letra" align="center">
                                                    <input type="text" class="form-control input-sm" id="codigo_proveedor_bal" name="codigo_proveedor_bal" style="display:none" >
                                                    <div class="input-group">
                                                        <input type="text" class="form-control input-sm" placeholder="ESCRIBA SUPLIDOR Y PRESIONE ENTER" id="cliente_nombre_bal" 
                                                        name="cliente_nombre_bal" onkeyup="autocompletar( ' . $idempresa . ', event ); form1.cliente_nombre_bal.value=form1.cliente_nombre_bal.value.toUpperCase();" style="text-transform: uppercase" />
                                                        <span class="input-group-addon primary" style="cursor: pointer;" onClick="autocompletar_btn_bal(' . $idempresa . ' );"><i class="fa fa-search"></i></span>
                                                    </div> 
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Bodega</td>
                                                <td class="fecha_letra" align="center">
                                                    <select id="bodega_bal" name="bodega_bal" class="form-control input-sm">
                                                        <option value="">Seleccione una opcion..</option>
                                                        ' . $lista_bode . '
                                                    </select>
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Producto a Pesar</td>
                                                <td class="fecha_letra" align="center">
                                                <input type="text" class="form-control input-sm" id="codigo_producto_bal" name="codigo_producto_bal" style="display:none" >
                                                <div class="input-group">
                                                    <input type="text" class="form-control input-sm" placeholder="ESCRIBA PRODUCTO Y PRESIONE ENTER" id="producto_bal" 
                                                    name="producto_bal" onkeyup="autocompletar_producto( ' . $idempresa . ', event, 1 ); form1.producto_bal.value=form1.producto_bal.value.toUpperCase();" style="text-transform: uppercase" />
                                                    <span class="input-group-addon primary" id="boton_producto" name="boton_producto" style="cursor: pointer;" onClick="autocompletar_producto_btn_bal( ' . $idempresa . ' );"><i class="fa fa-search"></i></span>
                                                </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">LOTE (ID)</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="text" class="form-control input-sm" id="lote_id" name="lote_id" placeholder="LOTE (ID)" >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">GUIA</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="text" class="form-control input-sm" id="guia_id" name="guia_id" placeholder="NUMERO GUIA" >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>
                                                    <div class="btn btn-primary btn-sm" onclick="abre_modal_historial_bal()" style="float: left !important">
                                                        <span class="glyphicon glyphicon-check"></span> Historial Compras Sin Retencion
                                                    </div>
                                                </td>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table id="tbclientes_prod" class="table table-striped table-condensed table-bordered table-hover" style="width: 98%; margin-top: 20px;" align="center">
                                            <thead>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">N° Pollos Procesados</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="number" class="form-control input-sm" id="pollos_procesados_bal" name="pollos_procesados_bal" placeholder="" readonly >
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">N° Pollos Pedido</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="number" class="form-control input-sm" id="pollos_pedidos_bal" name="pollos_pedidos_bal" placeholder="" readonly>
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Peso Promedio Pedido</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="number" class="form-control input-sm" id="peso_promedio_ped_bal" name="peso_promedio_ped_bal" placeholder="" readonly>
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Precio</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="number" class="form-control input-sm" id="precio_bal" name="precio_bal" placeholder="" onchange="realizar_calculos_bal()" >
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Hora LLegada</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="time" class="form-control input-sm" id="hora_bal" name="hora_bal" placeholder="" value="' . date('H:i') . '" >
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Transportista</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="text" class="form-control input-sm" id="nonbre_transportista" name="nonbre_transportista" placeholder="Nombre Transportista" style="text-transform: uppercase" >
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Peso Promedio Jaulas</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="number" class="form-control input-sm" id="peso_promedio_jaula_bal" name="peso_promedio_jaula_bal" placeholder="Peso Promedio Jaula" value="15.75"  onchange="realizar_calculos_bal()" >
                                                </td>                       
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="col-md-12" style="text-align: center">
                                        <div id="fcad_etiq">
                                            <div class="btn btn-success btn-sm" onclick="abrir_modal_pesaje()">
                                                <span class="glyphicon glyphicon-check"></span> Iniciar Pesaje
                                            </div>
                                            <!--
                                            <div class="btn btn-success btn-sm" onclick="realizar_calculos_bal()">
                                                <span class="glyphicon glyphicon-check"></span> Actualizar
                                            </div>
                                            -->
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                    <br>
                                    <div class="col-md-12" style="text-align: center">
                                        <div name="div_productos_pesados" id="div_productos_pesados"></div>
                                    </div>
                                </div>
                            </div>
                        ';

        // -----------------------------------------------------------------------------------
        // TABLA DE BALANZAS
        // -----------------------------------------------------------------------------------


        $sqlinf = "SELECT count(*) as conteo
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE  TABLE_NAME = 'saebalpc' and table_schema='public'";
        $ctralter = consulta_string($sqlinf, 'conteo', $oIfx, 0);
        if ($ctralter == 0) {
            $sqltb = 'CREATE TABLE saebalpc (
                            balpc_cod_balpc serial,
                            balpc_cod_empr int4,
                            balpc_cod_sucu int4,
                            balpc_cod_bode int4,
                        
                            balpc_guia_id varchar(200),
                            balpc_lote_id varchar(200),
                            balpc_fec_comp timestamp,
                            balpc_nom_recibe varchar(500),
                            balpc_cod_clpv int4,
                            balpc_nom_clpv varchar(255),
                            balpc_cod_prod varchar(255),
                            balpc_nom_prod varchar(255),
                            balpc_prod_procesa float,
                            balpc_prod_pedido float,
                            balpc_peso_promedi float,
                            balpc_uco_prod float,
                            balpc_hora_balpc timestamp,
                            balpc_nom_transp varchar(255),
                            balpc_peso_jaula float,
                            balpc_peso_bruto float,
                            balpc_peso_jaulas float,
                            balpc_prod_muert float,
                            balpc_peso_muert float,
                            balpc_prod_decom float,
                            balpc_peso_decom float,
                            balpc_merm_alas float,
                            balpc_peso_alas float,
                            balpc_merm_piern float,
                            balpc_peso_piern float,
                            balpc_merm_ab float,
                            balpc_peso_ab float,
                            balpc_merm_orga float,
                            balpc_peso_orga float,
                            balpc_peso_adici float,
                            balpc_peso_neto float,
                            balpc_val_efecti float,
                            balpc_cod_modu varchar(255),
                            balpc_num_comp int4,
                        
                            balpc_cod_usua int4,
                            balpc_fech_ingr timestamp,
                            balpc_usua_act int4,
                            balpc_fech_act timestamp
                        );            
                    ';
            $oIfx->QueryT($sqltb);
        }


        $sqlinf = "SELECT count(*) as conteo
                        FROM INFORMATION_SCHEMA.COLUMNS
                        WHERE  TABLE_NAME = 'saedbalpc' and table_schema='public'";
        $ctralter = consulta_string($sqlinf, 'conteo', $oIfx, 0);
        if ($ctralter == 0) {
            $sqltb = 'CREATE TABLE saedbalpc (
                                dbalpc_cod_dbalpc serial,
                                dbalpc_cod_empr int4,
                                dbalpc_cod_sucu int4,
                                dbalpc_cod_bode int4,
                            
                                dbalpc_cod_prod varchar(50),
                                dbalpc_nom_prod varchar(255),
                                dbalpc_peso_prod float,
                                dbalpc_num_jaula float,
                                dbalpc_num_pollos float,
                                dbalpc_cod_modu varchar(255),
                                dbalpc_cod_mbalpc int4,
                                dbalpc_num_comp int4,
                            
                                dbalpc_cod_usua int4,
                                dbalpc_fech_ingr timestamp,
                                dbalpc_usua_act int4,
                                dbalpc_fech_act timestamp
                            );            
                        ';
            $oIfx->QueryT($sqltb);
        }


        // -----------------------------------------------------------------------------------
        // FIN TABLA DE BALANZAS
        // -----------------------------------------------------------------------------------




        $oReturn->assign("divFormularioBAL", "innerHTML", $html_area_prod);
        $oReturn->script("productos_pesados()");
    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

    return $oReturn;
}

function productos_pesados($aForm = '')
{

    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $peso_promedio_jaula_bal = $aForm['peso_promedio_jaula_bal'];
    $id_usuario = $_SESSION['U_ID'];

    // Nombre Usuario Logueado
    $nombre_usuario = $_SESSION['U_NOMBRECOMPLETO'];
    $oReturn->assign("nombre_recibe_bal", "value", $nombre_usuario);

    try {
        $html_area_prod = '';
        $html_area_prod .= '<table id="" class="table table-bordered table-hover table-striped table-condensed" style="margin-top: 30px">
                                <thead>
                                    <tr>
                                        <th colspan="12"><h6>LISTA DE PRODUCTOS AGREGADOS</h6></th>
                                    </tr>
                                    <tr>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold">N°</td>
                                        <td class="success" style="width: 4.5%; color: #00859B; font-weight: bold">COD. PRODUCTO</td>
                                        <td class="success" style="width: 4.5%; color: #00859B; font-weight: bold">NOMBRE PRODUCTO</td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold">CANTIDAD (PESO BRUTO)</td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold">N° JAULA</td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold">N° POLLOS</td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold">
                                            ACCIONES
                                            <div class="btn btn-danger btn-sm" onclick="limpiar_todos_datos()">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>';

        $sql_saedbalpc = "SELECT * 
                            FROM saedbalpc 
                            where 
                                dbalpc_cod_modu = 'COMPRA_SIN_RETENCION'
                                and dbalpc_cod_usua = $id_usuario
                                and dbalpc_num_comp is null
                                ORDER BY dbalpc_cod_dbalpc
                            ";


        $numeral = 1;
        $total_cantidad = 0;
        $total_jaula = 0;
        $total_pollos = 0;
        $codigo_producto_general = '';
        $nombre_producto_general = '';
        $codigo_boega_general = '';

        if ($oIfx->Query($sql_saedbalpc)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    $dbalpc_cod_dbalpc = $oIfx->f('dbalpc_cod_dbalpc');
                    $dbalpc_cod_prod = $oIfx->f('dbalpc_cod_prod');
                    $dbalpc_nom_prod = $oIfx->f('dbalpc_nom_prod');
                    $dbalpc_peso_prod = $oIfx->f('dbalpc_peso_prod');
                    $dbalpc_num_jaula = $oIfx->f('dbalpc_num_jaula');
                    $dbalpc_num_pollos = $oIfx->f('dbalpc_num_pollos');
                    $dbalpc_cod_bode = $oIfx->f('dbalpc_cod_bode');

                    $html_area_prod .= '<tr height="20" style="cursor: pointer">
                                            <td style="font-size: 12px !important"><b>' . $numeral . '</b></td>
                                            <td style="font-size: 12px !important"><b>' . $dbalpc_cod_prod . '</b></td>
                                            <td style="font-size: 12px !important"><b>' . $dbalpc_nom_prod . '</b></td>
                                            <td style="font-size: 12px !important"><b>' . $dbalpc_peso_prod . '</b></td>
                                            <td style="font-size: 12px !important"><b>' . $dbalpc_num_jaula . '</b></td>
                                            <td style="font-size: 12px !important"><b>' . $dbalpc_num_pollos . '</b></td>
                                            <td>
                                                <div id ="imagen1" class="btn btn-danger btn-sm" onclick="eliminar_producto_agregado(\'' . $dbalpc_cod_dbalpc . '\')">
                                                    <span class="glyphicon glyphicon-trash"></span>
                                                </div>
                                            </td>
                                        </tr>';
                    $numeral++;

                    $total_cantidad += $dbalpc_peso_prod;
                    $total_jaula += $dbalpc_num_jaula;
                    $total_pollos += $dbalpc_num_pollos;

                    $codigo_producto_general = $dbalpc_cod_prod;
                    $nombre_producto_general = $dbalpc_nom_prod;
                    $codigo_boega_general = $dbalpc_cod_bode;
                } while ($oIfx->SiguienteRegistro());


                $html_area_prod .= '<tr style="">
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold; font-size: 15px !important"></td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold; font-size: 15px !important"></td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold; font-size: 15px !important">TOTALES</td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold; font-size: 15px !important">' . $total_cantidad . '</td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold; font-size: 15px !important">' . $total_jaula . '</td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold; font-size: 15px !important">' . $total_pollos . '</td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold; font-size: 15px !important"></td>
                                    </tr>';
            }
        }
        $oIfx->Free();


        $html_area_prod .= '</tbody>
                    </table>
                    <br>';


        $html_area_prod .= '<div class="row">
                                <div class="col-md-6">
                                
                                    <table id="tbclientes_prod" class="table table-striped table-condensed table-bordered table-hover" style="width: 98%; margin-top: 20px;" align="center">
                                        <thead>
                                            <tr>
                                                <th colspan="12"><h6>INFORMACION ADICIONAL</h6></th>
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">X</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="number" class="form-control input-sm" id="x_bal" name="x_bal" readonly >
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold"># Pollos</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="number" class="form-control input-sm" id="numero_pollos_bal" name="numero_pollos_bal" placeholder="" readonly>
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">P. Bruto</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="number" class="form-control input-sm" id="peso_bruto_bal" name="peso_bruto_bal" placeholder="" readonly>
                                                </td>                     
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">P. Jaulas</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="number" class="form-control input-sm" id="peso_jaulas_bal" name="peso_jaulas_bal" placeholder="" readonly>
                                                </td>                     
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">P. Muertos</td>
                                                <td class="fecha_letra" align="center">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control input-sm" id="numero_muertos_bal" name="numero_muertos_bal" placeholder="N# Pollos Muertos" onchange="realizar_calculos_bal()" >
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control input-sm" id="peso_muertos_bal" name="peso_muertos_bal" placeholder="Peso Pollos Muertos" onchange="realizar_calculos_bal()" >
                                                        </div>
                                                    </div>
                                                </td>                     
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">P. Decomiso</td>
                                                <td class="fecha_letra" align="center">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control input-sm" id="numero_decomiso_bal" name="numero_decomiso_bal" placeholder="N# Decoimiso" onchange="realizar_calculos_bal()" >
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control input-sm" id="peso_decomiso_bal" name="peso_decomiso_bal" placeholder="Peso Decoimiso" onchange="realizar_calculos_bal()" >
                                                        </div>
                                                </td>                     
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Merma Alas</td>
                                                <td class="fecha_letra" align="center">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control input-sm" id="numero_decomiso_alas" name="numero_decomiso_alas" placeholder="Numero Piezas" onchange="realizar_calculos_bal()" >
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control input-sm" id="peso_decomiso_alas" name="peso_decomiso_alas" placeholder="Peso Alas" onchange="realizar_calculos_bal()" >
                                                        </div>
                                                </td>                     
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Merma Piernas</td>
                                                <td class="fecha_letra" align="center">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control input-sm" id="numero_decomiso_piernas" name="numero_decomiso_piernas" placeholder="Numero Piezas" onchange="realizar_calculos_bal()" >
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control input-sm" id="peso_decomiso_piernas" name="peso_decomiso_piernas" placeholder="Peso Piernas" onchange="realizar_calculos_bal()" >
                                                        </div>
                                                </td>                     
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Merma AB</td>
                                                <td class="fecha_letra" align="center">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control input-sm" id="numero_decomiso_merma_ab" name="numero_decomiso_merma_ab" placeholder="Numero Piezas" onchange="realizar_calculos_bal()" >
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control input-sm" id="peso_decomiso_merma_ab" name="peso_decomiso_merma_ab" placeholder="Peso Merma AB" onchange="realizar_calculos_bal()" >
                                                        </div>
                                                </td>                     
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Merma Organos</td>
                                                <td class="fecha_letra" align="center">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control input-sm" id="numero_decomiso_organos" name="numero_decomiso_organos" placeholder="Numero Piezas" onchange="realizar_calculos_bal()" >
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" class="form-control input-sm" id="peso_decomiso_organos" name="peso_decomiso_organos" placeholder="Peso Organos" onchange="realizar_calculos_bal()" >
                                                        </div>
                                                </td>                     
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">P. Adicionales</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="number" class="form-control input-sm" id="peso_buches_mollejas_bal" name="peso_buches_mollejas_bal" placeholder="Ingrese peso adicional" onchange="realizar_calculos_bal()" >
                                                </td>                     
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">P. Neto</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="number" class="form-control input-sm" id="peso_neto_bal" name="peso_neto_bal" placeholder="" readonly>
                                                </td>                     
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">V. Efectivo</td>
                                                <td class="fecha_letra" align="center">
                                                    <input type="number" class="form-control input-sm" id="valor_efectivo_bal" name="valor_efectivo_bal" placeholder="" readonly>
                                                </td>                     
                                            </tr>
                                        </thead>
                                    </table>
                                
                                </div>
                            </div>
                            ';

        if (!empty($codigo_producto_general) && !empty($nombre_producto_general) && !empty($codigo_boega_general)) {
            $oReturn->script('document.getElementById("producto_bal").readOnly = true;');
            $oReturn->script('document.getElementById("bodega_bal").style.pointerEvents = "none";');
            $oReturn->script("document.getElementById('boton_producto').setAttribute('onclick','')");
            $oReturn->assign("codigo_producto_bal", "value", $codigo_producto_general);
            $oReturn->assign("producto_bal", "value", $nombre_producto_general);
            $oReturn->assign("bodega_bal", "value", $codigo_boega_general);
        } else {
            $oReturn->script('document.getElementById("producto_bal").readOnly = false;');
            $oReturn->script('document.getElementById("bodega_bal").style.pointerEvents = "";');
            $oReturn->script("document.getElementById('boton_producto').setAttribute('onclick','autocompletar_producto_btn_bal( ' . $empresa . ' );')");
            $oReturn->assign("codigo_producto_bal", "value", '');
            $oReturn->assign("producto_bal", "value", '');
            $oReturn->assign("bodega_bal", "value", '');
        }


        $oReturn->assign("div_productos_pesados", "innerHTML", $html_area_prod);
        $oReturn->script("realizar_calculos_bal()");
    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

    return $oReturn;
}

function abrir_modal_pesaje($aForm = '')
{

    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $idbodega_s = $_SESSION['U_BODEGA'];

    try {

        $html_area_prod = '';

        $html_area_prod .= '
                                <div class="row">
                                    <div class="col-md-12">
                                    
                                        <table id="tbclientes" class="table table-bordered table-hover table-striped table-condensed" style="margin-top: 30px">
                                            <thead>
                                                <tr>
                                                    <th colspan="30"  style="text-align: center"><h6></h6></th>
                                                </tr>
                                                <tr >
                                                    <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold">PESO BRUTO</td>
                                                    <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold">N° JAULA</td>
                                                    <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold">N° POLLOS</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="width: 1.5%;">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control input-sm" placeholder="Cantidad" id="cantidad_bal_peso" name="cantidad_bal_peso" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = 0; "/>                                                         
                                                            <span class="input-group-addon primary" style="cursor: pointer;" onClick="obtener_balanza_api_peso();"><i class="fa fa-balance-scale"></i></span>
                                                        </div> 
                                                    </td> 
                                                    <td style="width: 1.5%;">
                                                        <input type="number" class="form-control input-sm" id="numero_jaula_peso" name="numero_jaula_peso" placeholder="">
                                                    </td> 
                                                    <td  style="width: 1.5%;">
                                                        <input type="number" class="form-control input-sm" id="numero_pollos_peso" name="numero_pollos_peso" placeholder="">
                                                    </td>
                                                <tr>
                                            </tbody>
                                        </table>

                                    </div>
                                    <div class="col-md-12" style="text-align: center">
                                        <div id="fcad_etiq">
                                            <div class="btn btn-success btn-sm" onclick="agregar_pesaje()">
                                                <span class="glyphicon glyphicon-check"></span> Agregar
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                        ';



        $modal = '<div id="mostrarModalBalanza" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title"><b>Pesaje de Producto</b></h4>
                            </div>
                            <div class="modal-body">';
        $modal .= $html_area_prod;
        $modal .= '          </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                 </div>';


        $oReturn->assign("divFormularioModal", "innerHTML", $modal);
        $oReturn->script("abre_modal_balanza()");
    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

    return $oReturn;
}

function clpv_reporte_bal($aForm = '')
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo();
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $idempresa  = $aForm['empresa'];
    $idsucursal = $aForm['sucursal'];
    $clpv_nom   = strtoupper($aForm['cliente_nombre_bal']);

    $sql_tmp = '';
    if (!empty($clpv_nom)) {
        $sql_tmp = " and (clpv_nom_clpv like '%$clpv_nom%' OR clpv_ruc_clpv like '%$clpv_nom%')";
    }

    $oReturn = new xajaxResponse();

    $sHtml  .= '<div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">SUPLIDORES</h4>
                        </div>
                        <div class="modal-body">';

    $sHtml .= ' <table id="tbclientes"  class="table table-striped table-condensed table-bordered table-hover" style="width: 98%; margin-top: 20px;" align="center">';
    $sHtml .= '<thead>';
    $sHtml .= ' <tr>
                        <td class="fecha_letra">No-</td>
						<td class="fecha_letra" align="center">Codigo</td>
                        <td class="fecha_letra" align="center">Nombre</td>
                        <td class="fecha_letra" align="center">Subcliente</td>
                        <td class="fecha_letra" align="center">Vendedor</td>
                        <td class="fecha_letra" align="center">Identicacion</td>             
                        <td class="fecha_letra" align="center">Contribuyente Especial</td>             
                        <td class="fecha_letra" align="center">Estado</td>   
                    </tr>';
    $sHtml .= '</thead>';
    $sHtml .= '<tbody>';

    $sql = "select clpv_cod_clpv, clpv_nom_clpv,  clpv_ruc_clpv, clpv_est_clpv,
                        clpv_cod_fpagop, clpv_cod_tpago, clpv_pro_pago, clpv_etu_clpv, clpv_cod_cuen,
                        clpv_cod_vend, clpv_cot_clpv, clpv_pre_ven from saeclpv where
                        clpv_cod_empr   = $idempresa and
                        clpv_clopv_clpv = 'PV' 
                        $sql_tmp 
                        order by 2 limit 50";

    $i = 1;
    $total = 0;
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            do {
                $clpv_cod_clpv     = ($oIfx->f('clpv_cod_clpv'));
                $clpv_nom_clpv     = htmlentities($oIfx->f('clpv_nom_clpv'));
                $clpv_ruc_clpv     = $oIfx->f('clpv_ruc_clpv');
                $clpv_cod_fpagop = $oIfx->f('clpv_cod_fpagop');
                $clpv_cod_tpago = $oIfx->f('clpv_cod_tpago');
                $clpv_pro_pago  = $oIfx->f('clpv_pro_pago');
                $clpv_etu_clpv  = $oIfx->f('clpv_etu_clpv');
                $clpv_cod_vend  = $oIfx->f('clpv_cod_vend');
                $clpv_cot_clpv  = $oIfx->f('clpv_cot_clpv');
                $clpv_pre_ven   = $oIfx->f('clpv_pre_ven');


                $clpv_est_clpv = $oIfx->f('clpv_est_clpv');

                if ($clpv_est_clpv == 'A') {
                    $estado = 'ACTIVO';
                } elseif ($clpv_est_clpv == 'P') {
                    $estado = 'PENDIENTE';
                } elseif ($clpv_est_clpv == 'S') {
                    $estado = 'SUSPENDIDO';
                } else {
                    $estado = '--';
                }

                if ($clpv_etu_clpv == 1) {
                    $clpv_etu_clpv = 'S';
                } else {
                    $clpv_etu_clpv = 'N';
                }

                if (empty($clpv_pro_pago)) {
                    $clpv_pro_pago = 0;
                }

                /**
                 * Consulta Subcliente
                 */
                $sql_sub = "select count(*) as total from saeccli WHERE ccli_cod_clpv = '$clpv_cod_clpv' limit 1;";
                $sub_cliente = consulta_string_func($sql_sub, 'total', $oIfxA, 0);
                $sub_cliente_sn = ($sub_cliente > 0) ? 'SI' : 'NO';


                /**
                 * Consulta Vendedor
                 */
                $sql_vent = "select vend_cod_vend, vend_nom_vend from saevend where vend_cod_empr = $idempresa and vend_cod_vend = '$clpv_cod_vend'";
                $vendedor_info = consulta_string_func($sql_vent, 'vend_nom_vend', $oIfxA, '');


                // FECHA DE VENCIMIENTO
                $fecha_venc = (sumar_dias_func(date("Y-m-d"), $prove_dia)); //  Y/m/d
                list($a, $b, $c) = explode('-', $fecha_venc);
                $fecha_venc = $a . '-' . $b . '-' . $c;

                //direccion
                $sql = "select dire_dir_dire from saedire where dire_cod_empr = $idempresa and dire_cod_clpv = $clpv_cod_clpv";
                $dire = consulta_string_func($sql, 'dire_dir_dire', $oIfxA, '');

                //telefono
                $sql = "select tlcp_tlf_tlcp from saetlcp where tlcp_cod_empr = $idempresa and tlcp_cod_clpv = $clpv_cod_clpv";
                $telefono = consulta_string_func($sql, 'tlcp_tlf_tlcp', $oIfxA, '');

                // AUTORIZACION PROVE
                $sql = "select  max(coa_fec_vali) as coa_fec_vali, coa_aut_usua, coa_seri_docu, coa_fact_ini, coa_fact_fin
                            from saecoa where
                            clpv_cod_empr = $idempresa and
                            clpv_cod_clpv = $clpv_cod_clpv group by coa_fec_vali,2,3,4,5 ";
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

                //correo
                $sql = "select emai_ema_emai from saeemai where
                            emai_cod_empr = $idempresa and
                            emai_cod_clpv = $clpv_cod_clpv ";
                $correo = consulta_string_func($sql, 'emai_ema_emai', $oIfxA, '');


                $fecha_compra = $aForm['fecha_pedido'];
                $fecha_final = date("Y-m-d", strtotime($fecha_compra . "+ " . $clpv_pro_pago . " days"));
                $oReturn->assign('fecha_entrega', 'value', $fecha_final);


                $sClass = ($sClass == 'off') ? $sClass = 'on' : $sClass = 'off';
                $sHtml .= '<tr height="20" style="cursor: pointer" 
                            onClick="javascript:datos_clpv_bal( \'' . $clpv_cod_clpv . '\', \'' . $clpv_nom_clpv . '\' , \'' . $clpv_ruc_clpv . '\',  \'' . $dire . '\',
                                                            \'' . $telefono . '\',      \'' . $celular . '\',        \'' . $vendedor . '\',       \'' . $contacto . '\',
                                                            \'' . $precio . '\',        \'' . $clpv_cod_fpagop . '\', \'' . $clpv_cod_tpago . '\', \'' . $fec_cadu_prove . '\',
                                                            \'' . $auto_prove . '\',    \'' . $serie_prove . '\',     \'' . $fecha_venc . '\',     \'' . $clpv_pro_pago . '\',
                                                            \'' . $clpv_etu_clpv . '\', \'' . $ini_prove . '\',       \'' . $fin_prove . '\',      \'' . $clpv_cod_cuen . '\',
                                                            \'' . $correo . '\'
                                                          )"  >';


                $sHtml .= '<td>' . $i . '</td>';
                $sHtml .= '<td>' . $clpv_cod_clpv . '</td>';
                $sHtml .= '<td>' . $clpv_nom_clpv . '</td>';
                $sHtml .= '<td>' . $sub_cliente_sn . '</td>';
                $sHtml .= '<td>' . $vendedor_info . '</td>';
                $sHtml .= '<td>' . $clpv_ruc_clpv . '</td>';
                $sHtml .= '<td align="right">' . $clpv_etu_clpv . '</td>';
                $sHtml .= '<td>' . $estado . '</td>';
                $sHtml .= '</tr>';

                $i++;
                $total += $prbo_dis_prod;
            } while ($oIfx->SiguienteRegistro());
        }
    }
    $sHtml .= '</tbody>';
    $sHtml .= '</table>';

    $sHtml .= '          </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
             </div>';



    $oReturn->assign("ModalClpv", "innerHTML", $sHtml);
    $oReturn->script("init()");

    return $oReturn;
}

function producto_inventario_bal($aForm = '')
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();


    $oIfxA = new Dbo();
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $idempresa  = $aForm['empresa'];
    $idsucursal = $aForm['sucursal'];
    $bode_cod   = $aForm['bodega_bal'];
    $prod_nom   = strtoupper($aForm['producto_bal']);


    $sql_tmp = '';
    if (!empty($prod_nom)) {
        $sql_tmp = " and ( prod_nom_prod like '%$prod_nom%' or   prod_cod_prod like '%$prod_nom%' ) ";
    }


    $oReturn = new xajaxResponse();

    $sHtml  .= '<div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">PRODUCTOS</h4>
                        </div>
                        <div class="modal-body">';

    $sHtml .= ' <table id="tbclientes_prod" class="table table-striped table-condensed table-bordered table-hover" style="width: 98%; margin-top: 20px;" align="center">';
    $sHtml .= '<thead>';
    $sHtml .= '<tr>
                        <td class="fecha_letra">No-</td>
                        <td class="fecha_letra" align="center">Bodega</td>
						<td class="fecha_letra" align="center">Codigo</td>
                        <td class="fecha_letra" align="center">Producto</td>
                        <td class="fecha_letra" align="center">Referencia</td>
                        <td class="fecha_letra" align="center">Tipo</td>
                        <td class="fecha_letra" align="center">Unidad Medida</td>
                        <td class="fecha_letra" align="center">lotes</td>
                        <td class="fecha_letra" align="center">Series</td>
                        <td class="fecha_letra" align="center">Stock</td>                        
                    </tr>';
    $sHtml .= '</thead>';
    $sHtml .= '<tbody>';


    $sql = "select un.unid_nom_unid, tp.tpro_des_tpro, b.bode_nom_bode, pr.prbo_cod_prod, p.prod_nom_prod, pr.prbo_dis_prod, pr.prbo_cta_inv, pr.prbo_cta_ideb,
                        pr.prbo_uco_prod, pr.prbo_iva_porc, prod_lot_sino, prod_ser_prod, prod_cod_barr3
                        from saeprbo pr, saeprod p, saebode b, saetpro tp, saeunid un
                        where
                        p.prod_cod_prod     = pr.prbo_cod_prod and
                        pr.prbo_cod_bode     = b.bode_cod_bode and
                        tp.tpro_cod_tpro     = p.prod_cod_tpro and
                        un.unid_cod_unid     = pr.prbo_cod_unid and
                        p.prod_cod_empr     = $idempresa and
                        p.prod_cod_sucu     = $idsucursal and
                        pr.prbo_cod_empr    = $idempresa and
                        pr.prbo_cod_bode    = '$bode_cod'
                        $sql_tmp order by  2 limit 50";


    // No se hace uso de la vista ya que no actualiza los cambios realizados en el producto
    // $sql = "select *from sp_obtener_todos_productos($idempresa , $idsucursal,$bode_cod,500,'$prod_nom');";

    $i = 1;
    $total = 0;
    unset($_SESSION['U_PROD_RSC']);
    unset($array_tmp);
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            do {

                $prbo_cod_prod     = ($oIfx->f('prbo_cod_prod'));

                $sql_stock = "select  COALESCE( pr.prbo_dis_prod,'0' ) as stock
                    from saeprod p, saeprbo pr where
                    p.prod_cod_prod = pr.prbo_cod_prod and
                    p.prod_cod_empr = $idempresa and
                    p.prod_cod_sucu = $idsucursal and
                    pr.prbo_cod_empr = $idempresa and
                    pr.prbo_cod_bode = $bode_cod and
                    p.prod_cod_prod = '$prbo_cod_prod'";
                $stock = consulta_string_func($sql_stock, 'stock', $oIfxA, 0);


                $nom_bode     = ($oIfx->f('bode_nom_bode'));
                $tipo_prod     = ($oIfx->f('tpro_des_tpro'));
                $detalle_prod     = ($oIfx->f('prod_det_prod'));
                $prod_nom_prod     = htmlentities($oIfx->f('prod_nom_prod'));
                $prbo_dis_prod     = $stock;
                $prbo_cta_inv     = $oIfx->f('prbo_cta_inv');
                $prbo_cta_ideb     = $oIfx->f('prbo_cta_ideb');
                $prbo_uco_prod     = $oIfx->f('prbo_uco_prod');
                $prbo_iva_porc     = $oIfx->f('prbo_iva_porc');
                $unidad_prod     = $oIfx->f('unid_nom_unid');
                $lote             = $oIfx->f('prod_lot_sino');
                $serie             = $oIfx->f('prod_ser_prod');
                $mac             = $oIfx->f('prod_cod_barr3');

                $array_tmp[$i] = array(
                    $prbo_cod_prod,
                    $prod_nom_prod,
                    $prbo_cta_inv,
                    $prbo_cta_ideb,
                    $prbo_uco_prod,
                    $prbo_iva_porc,
                    $lote,
                    $serie
                );

                if ($lote == 1 || $lote == 'S') {
                    $lote = 'S';
                } else {
                    $lote = 'N';
                }

                if ($serie == 1 || $serie == 'S') {
                    $serie = 'S';
                } else {
                    $serie = 'N';
                }

                if ($mac == 1 || $mac == 'S') {
                    $mac = 'S';
                } else {
                    $mac = 'N';
                }

                $sClass = ($sClass == 'off') ? $sClass = 'on' : $sClass = 'off';
                /*$sHtml .= '<tr height="20" style="cursor: pointer"  
                                onClick="javascript:datos_prod( \'' . $prbo_cod_prod . '\',  \'' . $prod_nom_prod . '\'  , \'' . $prbo_cta_inv . '\'   ,
                                                                \'' . $prbo_cta_ideb . '\' , \'' . $prbo_uco_prod . '\'  , \'' . $prbo_iva_porc . '\' ,
                                                                \'' . $lote . '\',      \'' . $serie . '\' )">';*/

                $sHtml .= '<tr  height="20" style="cursor: pointer"  onClick="javascript:datos_prod_bal( \'' . $prbo_cod_prod . '\' ,
                \'' . $prod_nom_prod . '\',      \'' . $unidad_prod . '\'  )" >';

                $sHtml .= '<td>' . $i . '</td>';
                $sHtml .= '<td>' . $nom_bode . '</td>';
                $sHtml .= '<td>' . $prbo_cod_prod . '</td>';
                $sHtml .= '<td>' . $prod_nom_prod . '</td>';
                $sHtml .= '<td>' . $detalle_prod . '</td>';
                $sHtml .= '<td>' . $tipo_prod . '</td>';
                $sHtml .= '<td>' . $unidad_prod . '</td>';
                $sHtml .= '<td>' . $lote . '</td>';
                $sHtml .= '<td>' . $serie . '</td>';
                $sHtml .= '<td align="right">' . $prbo_dis_prod . '</td>';
                $sHtml .= '</tr>';

                $i++;
                $total += $prbo_dis_prod;
            } while ($oIfx->SiguienteRegistro());
            $sHtml .= '<tr height="25">';
            $sHtml .= '<td></td>';
            $sHtml .= '<td align="right"></td>';
            $sHtml .= '<td align="right"></td>';
            $sHtml .= '<td align="right"></td>';
            $sHtml .= '<td align="right"></td>';
            $sHtml .= '<td align="right"></td>';
            $sHtml .= '<td align="right"></td>';
            $sHtml .= '<td align="right"></td>';
            $sHtml .= '<td align="right" class="fecha_letra">TOTAL:</td>';
            $sHtml .= '<td align="right" class="fecha_letra">' . $total . '</td>';
            $sHtml .= '</tr>';
        }
    }

    $_SESSION['U_PROD_RSC'] = $array_tmp;

    $sHtml .= '</tbody>';
    $sHtml .= '</table>';

    $sHtml .= '          </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
             </div>';

    $oReturn->assign("ModalProd", "innerHTML", $sHtml);
    return $oReturn;
}

function agregar_pesaje($aForm = '')
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $idempresa  = $aForm['empresa'];
    $idsucursal = $aForm['sucursal'];
    $idbodega   = $aForm['bodega_bal'];
    $codigo_producto   = $aForm['codigo_producto_bal'];
    $nombre_producto   = $aForm['producto_bal'];
    $cantidad_bal_peso   = $aForm['cantidad_bal_peso'];
    $numero_jaula_peso   = $aForm['numero_jaula_peso'];
    $numero_pollos_peso   = $aForm['numero_pollos_peso'];

    try {
        $oIfx->QueryT('BEGIN WORK;');

        $id_usuario = $_SESSION['U_ID'];
        $fecha_actual = date('Y-m-d H:i:s');

        $sql_cost = "INSERT into saedbalpc(
                                    dbalpc_cod_empr,
                                    dbalpc_cod_sucu,
                                    dbalpc_cod_bode,

                                    dbalpc_cod_prod,
                                    dbalpc_nom_prod,
                                    dbalpc_peso_prod,
                                    dbalpc_num_jaula,
                                    dbalpc_num_pollos,
                                    dbalpc_cod_modu,
                                    dbalpc_cod_mbalpc,
                                    dbalpc_num_comp,

                                    dbalpc_cod_usua,
                                    dbalpc_fech_ingr,
                                    dbalpc_usua_act,
                                    dbalpc_fech_act

                                )   values  (
                                    $idempresa,
                                    $idsucursal,
                                    $idbodega,

                                    '$codigo_producto',
                                    '$nombre_producto',
                                    $cantidad_bal_peso,
                                    $numero_jaula_peso,
                                    $numero_pollos_peso,
                                    'COMPRA_SIN_RETENCION',
                                    NULL,
                                    NULL,

                                    $id_usuario,
                                    '$fecha_actual',
                                    $id_usuario,
                                    '$fecha_actual'

                                ) ";

        $oIfx->QueryT($sql_cost);

        $oIfx->QueryT('COMMIT WORK;');

        $oReturn->script("productos_pesados()");

        $oReturn->script("Swal.fire({
                            position: 'center',
                            type: 'success',
                            title: 'Ingresado Correctamente...!',
                            showConfirmButton: false,
                            confirmButtonText: 'Aceptar',
                            timer: 500
                            })");


        // $oReturn->script("cerrar_modal_balanza();");
        $oReturn->assign("cantidad_bal_peso", "value", '');
        // $oReturn->assign("numero_jaula_peso", "value", '');
        // $oReturn->assign("numero_pollos_peso", "value", '');
    } catch (Exception $e) {
        $oIfx->QueryT('ROLLBACK WORK;');
        $oReturn->alert($e->getMessage());
    }


    return $oReturn;
}

function eliminar_producto_agregado($dbalpc_cod_dbalpc, $aForm = '')
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();


    try {
        $oIfx->QueryT('BEGIN WORK;');


        $sql_cost = "DELETE FROM saedbalpc WHERE dbalpc_cod_dbalpc = $dbalpc_cod_dbalpc";
        $oIfx->QueryT($sql_cost);

        $oIfx->QueryT('COMMIT WORK;');

        $oReturn->script("productos_pesados()");

        $oReturn->script("Swal.fire({
                            position: 'center',
                            type: 'success',
                            title: 'Eliminado Correctamente...!',
                            showConfirmButton: true,
                            confirmButtonText: 'Aceptar',
                            timer: 10000
                            })");
    } catch (Exception $e) {
        $oIfx->QueryT('ROLLBACK WORK;');
        $oReturn->alert($e->getMessage());
    }


    return $oReturn;
}

function realizar_calculos_bal($aForm = '')
{

    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $recalcular_demiso_sn = $aForm['recalcular_demiso_sn'];

    $peso_promedio_jaula_bal = $aForm['peso_promedio_jaula_bal'];
    $numero_muertos_bal = $aForm['numero_muertos_bal'];
    $numero_decomiso_bal = $aForm['numero_decomiso_bal'];
    $peso_muertos_bal = $aForm['peso_muertos_bal'];
    $peso_decomiso_bal = $aForm['peso_decomiso_bal'];

    $peso_decomiso_alas = $aForm['peso_decomiso_alas'];
    $peso_decomiso_piernas = $aForm['peso_decomiso_piernas'];
    $peso_decomiso_merma_ab = $aForm['peso_decomiso_merma_ab'];
    $peso_decomiso_organos = $aForm['peso_decomiso_organos'];

    $peso_buches_mollejas_bal = $aForm['peso_buches_mollejas_bal'];
    $precio_bal = $aForm['precio_bal'];


    $id_usuario = $_SESSION['U_ID'];

    try {


        $sql_saedbalpc = "SELECT * 
                            FROM saedbalpc 
                            where 
                                dbalpc_cod_modu = 'COMPRA_SIN_RETENCION'
                                and dbalpc_cod_usua = $id_usuario
                                and dbalpc_num_comp is null
                                ORDER BY dbalpc_cod_dbalpc
                            ";


        $total_cantidad = 0;
        $total_jaula = 0;
        $total_pollos = 0;
        if ($oIfx->Query($sql_saedbalpc)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    $dbalpc_cod_dbalpc = $oIfx->f('dbalpc_cod_dbalpc');
                    $dbalpc_cod_prod = $oIfx->f('dbalpc_cod_prod');
                    $dbalpc_nom_prod = $oIfx->f('dbalpc_nom_prod');
                    $dbalpc_peso_prod = $oIfx->f('dbalpc_peso_prod');
                    $dbalpc_num_jaula = $oIfx->f('dbalpc_num_jaula');
                    $dbalpc_num_pollos = $oIfx->f('dbalpc_num_pollos');

                    $total_cantidad += $dbalpc_peso_prod;
                    $total_jaula += $dbalpc_num_jaula;
                    $total_pollos += $dbalpc_num_pollos;
                } while ($oIfx->SiguienteRegistro());
            }
        }
        $oIfx->Free();

        // ---------------------------------------------------------------------------------------------------
        // Variables del formulario y calculos segun lo pesado
        // ---------------------------------------------------------------------------------------------------



        // obtener peso de jaulas
        $peso_total_jaulas = round($total_jaula * $peso_promedio_jaula_bal, 2);

        if ($recalcular_demiso_sn == 'S') {
            // obtenemos el peso promedio de los pollos sin muertos ni decomiso
            $peso_promedio_sin_pollos_muertos_ni_heridos = round(($total_cantidad - $peso_total_jaulas) / $total_pollos, 2);

            // Peso pollos muertos
            $peso_pollos_muertos = round($numero_muertos_bal * $peso_promedio_sin_pollos_muertos_ni_heridos, 2);

            // Peso pollos decomiso
            $peso_pollos_decomiso = round($numero_decomiso_bal * $peso_promedio_sin_pollos_muertos_ni_heridos, 2);
        } else {
            // Peso pollos muertos
            $peso_pollos_muertos = $peso_muertos_bal;

            // Peso pollos decomiso
            $peso_pollos_decomiso = $peso_decomiso_bal;
        }

        // total pollos menos muertos y heridos
        $total_pollos_menos_muertos_y_heridos = round($total_pollos - $numero_muertos_bal - $numero_decomiso_bal, 2);

        // obtenemos el peso promedio de los pollos
        $peso_promedio_pollos_total = round(($total_cantidad - $peso_total_jaulas) / $total_pollos_menos_muertos_y_heridos, 2);

        // Obtenemos el peso neto del pedido
        $peso_neto_pedido = round($total_cantidad - $peso_total_jaulas - $peso_pollos_muertos - $peso_pollos_decomiso - $peso_buches_mollejas_bal - $peso_decomiso_alas - $peso_decomiso_piernas - $peso_decomiso_merma_ab - $peso_decomiso_organos, 2);

        // Obtenemos el valor en efectivo total 
        $valor_efectivo_bal = number_format($peso_neto_pedido * $precio_bal, 2, '.', '');

        // Seteamos las variables
        $oReturn->assign("pollos_procesados_bal", "value", $total_pollos_menos_muertos_y_heridos);
        $oReturn->assign("pollos_pedidos_bal", "value", $total_pollos);
        $oReturn->assign("peso_promedio_ped_bal", "value", $peso_promedio_pollos_total);
        $oReturn->assign("peso_bruto_bal", "value", $total_cantidad);
        $oReturn->assign("peso_jaulas_bal", "value", $peso_total_jaulas);
        $oReturn->assign("peso_muertos_bal", "value", $peso_pollos_muertos);
        $oReturn->assign("peso_decomiso_bal", "value", $peso_pollos_decomiso);
        $oReturn->assign("numero_pollos_bal", "value", $total_pollos_menos_muertos_y_heridos);
        $oReturn->assign("x_bal", "value", $peso_promedio_pollos_total);
        $oReturn->assign("peso_neto_bal", "value", $peso_neto_pedido);
        $oReturn->assign("valor_efectivo_bal", "value", $valor_efectivo_bal);
        // ---------------------------------------------------------------------------------------------------
        // Variables del formulario y calculos segun lo pesado
        // ---------------------------------------------------------------------------------------------------

        unset($_SESSION['aDataGird_INV_MRECO']);
        $sHtml = mostrar_grid();
        $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
        $oReturn->script('habilita(5)');
        $oReturn->script('totales();');
    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

    return $oReturn;
}

function limpiar_todos_datos($aForm = '')
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();


    try {
        $oIfx->QueryT('BEGIN WORK;');

        $id_usuario = $_SESSION['U_ID'];
        $sql_cost = "DELETE 
                        FROM saedbalpc 
                        where 
                            dbalpc_cod_modu = 'COMPRA_SIN_RETENCION'
                            and dbalpc_cod_usua = $id_usuario
                            and dbalpc_num_comp is null";
        $oIfx->QueryT($sql_cost);

        $oIfx->QueryT('COMMIT WORK;');

        $oReturn->script("realizar_calculos_bal()");
        $oReturn->script("productos_pesados()");

        $oReturn->script("Swal.fire({
                            position: 'center',
                            type: 'success',
                            title: 'Todos los productos pendientes fueron eliminados correctamente...!',
                            showConfirmButton: true,
                            confirmButtonText: 'Aceptar',
                            timer: 10000
                            })");
    } catch (Exception $e) {
        $oIfx->QueryT('ROLLBACK WORK;');
        $oReturn->alert($e->getMessage());
    }


    return $oReturn;
}

function procesar_info($aForm = '')
{

    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo;
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $oReturn = new xajaxResponse();

    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $bodega_bal = $aForm['bodega_bal'];
    $peso_neto_bal = $aForm['peso_neto_bal'];
    $producto_bal = $aForm['producto_bal'];
    $codigo_producto_bal = $aForm['codigo_producto_bal'];
    $precio_bal = $aForm['precio_bal'];
    $codigo_proveedor_bal = $aForm['codigo_proveedor_bal'];
    $cliente_nombre_bal = $aForm['cliente_nombre_bal'];
    $fecha_bal = $aForm['fecha_bal'];

    $id_usuario = $_SESSION['U_ID'];

    unset($_SESSION['aDataGird_INV_MRECO']);

    try {
        // --------------------------------------------------------------------------
        // Datos clpv
        // --------------------------------------------------------------------------

        $sql_clpv_data = "SELECT clpv_cod_clpv, clpv_nom_clpv,  clpv_ruc_clpv, clpv_est_clpv,
                                clpv_cod_fpagop, clpv_cod_tpago, clpv_pro_pago, clpv_etu_clpv, clpv_cod_cuen,
                                clpv_cod_vend, clpv_cot_clpv, clpv_pre_ven from saeclpv where
                                clpv_cod_empr   = $empresa and
                                clpv_clopv_clpv = 'PV' and
                                clpv_cod_clpv = $codigo_proveedor_bal
                                order by 2 limit 50";

        if ($oIfx->Query($sql_clpv_data)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    $clpv_cod_clpv     = ($oIfx->f('clpv_cod_clpv'));
                    $clpv_nom_clpv     = htmlentities($oIfx->f('clpv_nom_clpv'));
                    $clpv_ruc_clpv     = $oIfx->f('clpv_ruc_clpv');
                    $clpv_cod_fpagop = $oIfx->f('clpv_cod_fpagop');
                    $clpv_cod_tpago = $oIfx->f('clpv_cod_tpago');
                    $clpv_pro_pago  = $oIfx->f('clpv_pro_pago');
                    $clpv_etu_clpv  = $oIfx->f('clpv_etu_clpv');

                    if ($clpv_etu_clpv == 1) {
                        $clpv_etu_clpv = 'S';
                    } else {
                        $clpv_etu_clpv = 'N';
                    }

                    if (empty($clpv_pro_pago)) {
                        $clpv_pro_pago = 0;
                    }

                    // FECHA DE VENCIMIENTO
                    $fecha_venc = (sumar_dias_func(date("Y-m-d"), $prove_dia)); //  Y/m/d
                    list($a, $b, $c) = explode('-', $fecha_venc);
                    $fecha_venc = $a . '-' . $b . '-' . $c;

                    //direccion
                    $sql = "select dire_dir_dire from saedire where dire_cod_empr = $empresa and dire_cod_clpv = $clpv_cod_clpv";
                    $dire = consulta_string_func($sql, 'dire_dir_dire', $oIfxA, '');

                    //telefono
                    $sql = "select tlcp_tlf_tlcp from saetlcp where tlcp_cod_empr = $empresa and tlcp_cod_clpv = $clpv_cod_clpv";
                    $telefono = consulta_string_func($sql, 'tlcp_tlf_tlcp', $oIfxA, '');

                    // AUTORIZACION PROVE
                    $sql = "select  max(coa_fec_vali) as coa_fec_vali, coa_aut_usua, coa_seri_docu, coa_fact_ini, coa_fact_fin
                            from saecoa where
                            clpv_cod_empr = $empresa and
                            clpv_cod_clpv = $clpv_cod_clpv group by coa_fec_vali,2,3,4,5 ";
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

                    //correo
                    $sql = "select emai_ema_emai from saeemai where
                            emai_cod_empr = $empresa and
                            emai_cod_clpv = $clpv_cod_clpv ";
                    $correo = consulta_string_func($sql, 'emai_ema_emai', $oIfxA, '');


                    $fecha_compra = $aForm['fecha_pedido'];
                    $fecha_final = date("Y-m-d", strtotime($fecha_compra . "+ " . $clpv_pro_pago . " days"));
                    $oReturn->assign('fecha_entrega', 'value', $fecha_final);


                    $sClass = ($sClass == 'off') ? $sClass = 'on' : $sClass = 'off';
                    $oReturn->script("datos_clpv('$clpv_cod_clpv','$clpv_nom_clpv' ,'$clpv_ruc_clpv', '$dire',
                                                           '$telefono',     '$celular',       '$vendedor',     '$contacto',
                                                           '$precio',       '$clpv_cod_fpagop','$clpv_cod_tpago','$fec_cadu_prove',
                                                           '$auto_prove',   '$serie_prove',    '$fecha_venc',    '$clpv_pro_pago',
                                                           '$clpv_etu_clpv','$ini_prove',      '$fin_prove',     '$clpv_cod_cuen',
                                                           '$correo'
                                                          )");
                } while ($oIfx->SiguienteRegistro());
            }
        }
        // --------------------------------------------------------------------------
        // FIN Datos clpv
        // --------------------------------------------------------------------------

        // --------------------------------------------------------------------------
        // Datos Productos
        // --------------------------------------------------------------------------
        $sql_productos = "SELECT un.unid_nom_unid, tp.tpro_des_tpro, b.bode_nom_bode, pr.prbo_cod_prod, p.prod_nom_prod, pr.prbo_dis_prod, pr.prbo_cta_inv, pr.prbo_cta_ideb,
                        pr.prbo_uco_prod, pr.prbo_iva_porc, prod_lot_sino, prod_ser_prod
                        from saeprbo pr, saeprod p, saebode b, saetpro tp, saeunid un
                        where
                        p.prod_cod_prod     = pr.prbo_cod_prod and
                        pr.prbo_cod_bode     = b.bode_cod_bode and
                        tp.tpro_cod_tpro     = p.prod_cod_tpro and
                        un.unid_cod_unid     = pr.prbo_cod_unid and
                        p.prod_cod_empr     = $empresa and
                        p.prod_cod_sucu     = $sucursal and
                        pr.prbo_cod_empr    = $empresa and
                        pr.prbo_cod_bode    = '$bodega_bal' and 
                        p.prod_cod_prod = '$codigo_producto_bal' and
                        pr.prbo_cod_prod = '$codigo_producto_bal'
                        order by  2 limit 50";

        $prbo_cta_inv = consulta_string_func($sql_productos, 'prbo_cta_inv', $oIfx, '');
        $prbo_cta_ideb = consulta_string_func($sql_productos, 'prbo_cta_ideb', $oIfx, '');
        $prbo_uco_prod = consulta_string_func($sql_productos, 'prbo_uco_prod', $oIfx, '');
        $prbo_iva_porc = consulta_string_func($sql_productos, 'prbo_iva_porc', $oIfx, '');

        $oReturn->assign("codigo_producto", "value", $codigo_producto_bal);
        $oReturn->assign("producto", "value", $producto_bal);
        $oReturn->assign("cuenta_inv", "value", $prbo_cta_inv);
        $oReturn->assign("cuenta_iva", "value", $prbo_cta_ideb);
        $oReturn->assign("costo", "value", $prbo_uco_prod);
        $oReturn->assign("iva", "value", $prbo_iva_porc);

        // --------------------------------------------------------------------------
        // FIN Datos Productos
        // --------------------------------------------------------------------------

        // --------------------------------------------------------------------------
        // Datos Adicionales
        // --------------------------------------------------------------------------

        $oReturn->assign("bodega", "value", $bodega_bal);
        $oReturn->assign("fecha_pedido", "value", $fecha_bal);
        $oReturn->assign("cantidad", "value", $peso_neto_bal);
        $oReturn->assign("costo", "value", $precio_bal);
        $oReturn->assign("procesar_sn_bal", "value", 1);

        // --------------------------------------------------------------------------
        // FIN Datos Adicionales
        // --------------------------------------------------------------------------

        $oReturn->script("cargar_producto()");


        $oReturn->script("Swal.fire({
                            position: 'center',
                            type: 'success',
                            title: 'Procesado Correctamente, Vaya a la pestaña compra y llene la informacion...!',
                            showConfirmButton: true,
                            confirmButtonText: 'Aceptar',
                            timer: 10000
                            })");
    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

    return $oReturn;
}

function abre_modal_historial_bal($aForm = '')
{

    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $idbodega_s = $_SESSION['U_BODEGA'];

    try {

        $html_area_prod = '';

        $html_area_prod .= '
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <label for="empresa">* Fecha Desde:</label>
                                                <input type="date" class="form-control input-sm" placeholder="" id="fecha_desde_bal" name="fecha_desde_bal" value="' . date('Y-m-d') . '"  />
                                            </div>
                                            <div class="col-md-6">
                                                <label for="empresa">* Fecha Hasta:</label>
                                                <input type="date" class="form-control input-sm" placeholder="" id="fecha_hasta_bal" name="fecha_hasta_bal" value="' . date('Y-m-d') . '"  />
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12" style="text-align: center">
                                        <br>
                                        <div id="fcad_etiq">
                                            <div class="btn btn-success btn-sm" onclick="consultar_movimientos_inv()">
                                                <span class="glyphicon glyphicon-check"></span> Consultar
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" style="text-align: center">
                                        <br>
                                        <div id="div_movimientos_inv" name="div_movimientos_inv"></div>
                                    </div>
                                </div>
                            
                        ';



        $modal = '<div id="mostrarModalBalanza" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg" style="width:1200px;">>
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title"><b>Movimientos de Inventario realizados con balanza</b></h4>
                            </div>
                            <div class="modal-body">';
        $modal .= $html_area_prod;
        $modal .= '          </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                 </div>';


        $oReturn->assign("divFormularioModal", "innerHTML", $modal);
        $oReturn->script("abre_modal_balanza()");
    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

    return $oReturn;
}

function consultar_movimientos_inv($aForm = '')
{

    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo;
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $oReturn = new xajaxResponse();

    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $idbodega_s = $_SESSION['U_BODEGA'];

    $fecha_inicio = $aForm['fecha_desde_bal'];
    $fecha_fin = $aForm['fecha_hasta_bal'];


    try {

        $sHtml = '';

        $sHtml .= '<table class="table table-striped table-bordered table-hover table-condensed" style="width: 90%; margin-bottom: 0px;" align="center">
                        <tr>
                            <td colspan="11" class="info">REPORTE MOVIMIENTO INVENTARIO</td>
                        </tr>
                        <tr>
                            <td class="info">No.</td>
                            <td class="info">FECHA</td>
                            <td class="info">TRANSACCION</td>
                            <td class="info">SECUENCIAL</td>
                            <td class="info">COMPROBANTE</td>
                            <td class="info">FACTURA</td>
                            <td class="info">CLIENTE/PROVEEDOR</td>
                            <td class="info">TOTAL CANTIDAD</td>
                            <td class="info">TOTAL COSTO</td>
                            <td class="info">ESTADO</td>
                            <td class="info">IMPRIMIR</td>
                        </tr>';

        $query = "SELECT mi.minv_fmov,     mi.minv_cod_clpv,
                            mi.minv_fac_prov, mi.minv_cod_tran, 
                            ( select tran_des_tran  from saetran where 
                                    tran_cod_tran = mi.minv_cod_tran and
                                    tran_cod_empr = $empresa ) as tran,
                            mi.minv_num_sec, mi.minv_tot_minv, mi.minv_num_comp, minv_est_minv, minv_comp_cont
                            from saeminv mi where
                            mi.minv_fmov between '$fecha_inicio'and '$fecha_fin' and
                            mi.minv_cod_empr = $empresa and
                            mi.minv_cod_sucu = $sucursal and
                            mi.minv_num_comp in (
                                select distinct(balpc_num_comp) from saebalpc where balpc_cod_modu = 'COMPRA_SIN_RETENCION'
                            )
                            
                            order by mi.minv_cod_tran ";

        $oReturn->alert('Buscando...');
        $i = 1;
        $granTotal = 0;
        $granCantidadTotal = 0;

        if ($oIfx->Query($query)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    $minv_fmov         = ($oIfx->f('minv_fmov'));
                    $minv_cod_clpv  = $oIfx->f('minv_cod_clpv');
                    $minv_fac_prov  = $oIfx->f('minv_fac_prov');
                    $minv_cod_tran  = $oIfx->f('minv_cod_tran');
                    $minv_secu      = $oIfx->f('minv_num_sec');
                    $minv_tot       = $oIfx->f('minv_tot_minv');
                    $minv_cod       = $oIfx->f('minv_num_comp');
                    $tran_nom       = $oIfx->f('tran');
                    $minv_est_minv  = $oIfx->f('minv_est_minv');
                    $minv_comp_cont  = $oIfx->f('minv_comp_cont');




                    //query nombre del proveedor
                    if (empty($minv_cod_clpv)) {
                        $minv_cod_clpv = 0;
                    }

                    $sql = "select clpv_nom_clpv from saeclpv where clpv_cod_empr = $empresa and clpv_cod_clpv = $minv_cod_clpv";
                    $clpv_nom_clpv = consulta_string($sql, 'clpv_nom_clpv', $oIfxA, '');

                    $sql_mov = "SELECT sum(dmov_can_dmov) as cantidad FROM saedmov WHERE dmov_num_comp = $minv_cod; ";
                    $total_cantidad = number_format(consulta_string($sql_mov, 'cantidad', $oIfxA, 0), 0);

                    $granCantidadTotal += $total_cantidad;

                    $total = $dmov_can_dmov * $dmov_cun_dmov;

                    if ($sClass == 'off')
                        $sClass = 'on';
                    else
                        $sClass = 'off';

                    $anio = substr($minv_fmov, 0, 4);
                    $fecha_ejer     = $anio . '-12-31';
                    $sql_ejer = "select ejer_cod_ejer from saeejer where ejer_fec_finl = '$fecha_ejer' and ejer_cod_empr = $empresa";
                    $idejer = consulta_string($sql_ejer, 'ejer_cod_ejer', $oIfxA, 1);

                    $idprdo         = (substr($minv_fmov, 5, 2)) * 1;

                    $sHtml .= '<tr height="20" class="' . $sClass . '"
                    onMouseOver="javascript:this.className=\'link\';"
                    onMouseOut="javascript:this.className=\'' . $sClass . '\';">';
                    $sHtml .= '<td align="center">' . $i++ . '</td>';
                    $sHtml .= '<td align="left">' . $minv_fmov . '</td>';
                    $sHtml .= '<td align="left">' . $minv_cod_tran . ' | ' . $tran_nom . ' </td>';
                    $sHtml .= '<td align="left">' . $minv_secu . '</td>';
                    $sHtml .= '<td align="left"><a href="#" onclick="seleccionaItem(' . $empresa . ', ' . $sucursal . ', ' . $idejer . ', ' . $idprdo . ', \'' . $minv_comp_cont . '\');">' . $minv_comp_cont . '</a></td>';
                    $sHtml .= '<td align="left">' . $minv_fac_prov . '</td>';
                    $sHtml .= '<td align="left">' . $clpv_nom_clpv . '</td>';
                    $sHtml .= '<td align="right">' . $total_cantidad . '</td>';
                    $sHtml .= '<td align="right">' . number_format($minv_tot, 2) . '</td>';
                    $sHtml .= '<td align="right">' . $minv_est_minv . '</td>';
                    $sHtml .= '<td align="right">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div id="">
                                                    <div class="btn btn-success btn-sm" onclick="vista_previa_( ' . $minv_cod . ', ' . $empresa . ',  ' . $sucursal . ' );">
                                                        <span class="glyphicon glyphicon-print"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div id="">
                                                    <div class="btn btn-primary btn-sm" onclick="modal_vista_previa_balanza( ' . $minv_cod . ', ' . $empresa . ',  ' . $sucursal . ' );">
                                                        <span class="glyphicon glyphicon-eye-open"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                      
                                    </td>';
                    $sHtml .= '</tr>';

                    $granTotal  += $minv_tot;
                } while ($oIfx->SiguienteRegistro());
                $sHtml .= '<tr></tr>';
                $sHtml .= '<tr></tr>';
                $sHtml .= '  <tr>
                        <td align="right" colspan="6" class="font_face_2" style="color: red; font-size: 12px;">TOTAL :</td>
                        <td align="right" class="font_face_2" style="color: red; font-size: 12px;">' . number_format($granCantidadTotal, 0) . '</td>
                        <td align="right" class="font_face_2" style="color: red; font-size: 12px;">' . number_format($granTotal, 2) . '</td>
                   </tr>';
                $sHtml .= '</table>';
            } else {
                $sHtml = '<span>Sin Datos para mostrar...</span>';
            }
        }



        $oReturn->assign("div_movimientos_inv", "innerHTML", $sHtml);
    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

    return $oReturn;
}

function modal_vista_previa_balanza($cod_minv, $empresa, $sucursal, $aForm = '')
{

    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $idbodega_s = $_SESSION['U_BODEGA'];

    try {

        $html_area_prod = '';

        $sql_saebalpc_data = "SELECT * FROM saebalpc where balpc_cod_modu = 'COMPRA_SIN_RETENCION' and balpc_num_comp = $cod_minv";

        $balpc_lote_id = consulta_string($sql_saebalpc_data, 'balpc_lote_id', $oIfx, '');
        $balpc_guia_id = consulta_string($sql_saebalpc_data, 'balpc_guia_id', $oIfx, '');
        $balpc_fec_comp = consulta_string($sql_saebalpc_data, 'balpc_fec_comp', $oIfx, '');
        $balpc_nom_recibe = consulta_string($sql_saebalpc_data, 'balpc_nom_recibe', $oIfx, '');
        $balpc_cod_clpv = consulta_string($sql_saebalpc_data, 'balpc_cod_clpv', $oIfx, '');
        $balpc_nom_clpv = consulta_string($sql_saebalpc_data, 'balpc_nom_clpv', $oIfx, '');
        $balpc_cod_prod = consulta_string($sql_saebalpc_data, 'balpc_cod_prod', $oIfx, '');
        $balpc_nom_prod = consulta_string($sql_saebalpc_data, 'balpc_nom_prod', $oIfx, '');
        $balpc_prod_procesa = consulta_string($sql_saebalpc_data, 'balpc_prod_procesa', $oIfx, '');
        $balpc_prod_pedido = consulta_string($sql_saebalpc_data, 'balpc_prod_pedido', $oIfx, '');
        $balpc_peso_promedi = consulta_string($sql_saebalpc_data, 'balpc_peso_promedi', $oIfx, '');
        $balpc_uco_prod = consulta_string($sql_saebalpc_data, 'balpc_uco_prod', $oIfx, '');
        $balpc_hora_balpc = consulta_string($sql_saebalpc_data, 'balpc_hora_balpc', $oIfx, '');
        $balpc_nom_transp = consulta_string($sql_saebalpc_data, 'balpc_nom_transp', $oIfx, '');
        $balpc_peso_jaula = consulta_string($sql_saebalpc_data, 'balpc_peso_jaula', $oIfx, '');
        $balpc_peso_bruto = consulta_string($sql_saebalpc_data, 'balpc_peso_bruto', $oIfx, '');
        $balpc_peso_jaulas = consulta_string($sql_saebalpc_data, 'balpc_peso_jaulas', $oIfx, '');
        $balpc_prod_muert = consulta_string($sql_saebalpc_data, 'balpc_prod_muert', $oIfx, '');
        $balpc_peso_muert = consulta_string($sql_saebalpc_data, 'balpc_peso_muert', $oIfx, '');
        $balpc_prod_decom = consulta_string($sql_saebalpc_data, 'balpc_prod_decom', $oIfx, '');
        $balpc_peso_decom = consulta_string($sql_saebalpc_data, 'balpc_peso_decom', $oIfx, '');
        $balpc_peso_adici = consulta_string($sql_saebalpc_data, 'balpc_peso_adici', $oIfx, '');
        $balpc_peso_neto = consulta_string($sql_saebalpc_data, 'balpc_peso_neto', $oIfx, '');
        $balpc_val_efecti = consulta_string($sql_saebalpc_data, 'balpc_val_efecti', $oIfx, '');
        $balpc_cod_bode = consulta_string($sql_saebalpc_data, 'balpc_cod_bode', $oIfx, 0);

        $balpc_merm_alas = consulta_string($sql_saebalpc_data, 'balpc_merm_alas', $oIfx, 0);
        $balpc_peso_alas = consulta_string($sql_saebalpc_data, 'balpc_peso_alas', $oIfx, 0);
        $balpc_merm_piern = consulta_string($sql_saebalpc_data, 'balpc_merm_piern', $oIfx, 0);
        $balpc_peso_piern = consulta_string($sql_saebalpc_data, 'balpc_peso_piern', $oIfx, 0);
        $balpc_merm_ab = consulta_string($sql_saebalpc_data, 'balpc_merm_ab', $oIfx, 0);
        $balpc_peso_ab = consulta_string($sql_saebalpc_data, 'balpc_peso_ab', $oIfx, 0);
        $balpc_merm_orga = consulta_string($sql_saebalpc_data, 'balpc_merm_orga', $oIfx, 0);
        $balpc_peso_orga = consulta_string($sql_saebalpc_data, 'balpc_peso_orga', $oIfx, 0);


        $sql_bodega_nombre = "SELECT bode_nom_bode from saebode where bode_cod_bode = $balpc_cod_bode";
        $bodega_nombre = consulta_string($sql_bodega_nombre, 'bode_nom_bode', $oIfx, '');

        $sql_unidad_medida = "SELECT un.unid_nom_unid, un.unid_sigl_unid, tp.tpro_des_tpro, b.bode_nom_bode, pr.prbo_cod_prod, p.prod_nom_prod, pr.prbo_dis_prod, pr.prbo_cta_inv, pr.prbo_cta_ideb,
                                    pr.prbo_uco_prod, pr.prbo_iva_porc, prod_lot_sino, prod_ser_prod
                                    from saeprbo pr, saeprod p, saebode b, saetpro tp, saeunid un
                                    where
                                    p.prod_cod_prod     = pr.prbo_cod_prod and
                                    pr.prbo_cod_bode     = b.bode_cod_bode and
                                    tp.tpro_cod_tpro     = p.prod_cod_tpro and
                                    un.unid_cod_unid     = pr.prbo_cod_unid and
                                    p.prod_cod_empr     = $empresa and
                                    p.prod_cod_sucu     = $sucursal and
                                    pr.prbo_cod_empr    = $empresa and
                                    pr.prbo_cod_bode    = '$balpc_cod_bode'
                                    and pr.prbo_cod_prod = '$balpc_cod_prod'
                                    and p.prod_cod_prod = '$balpc_cod_prod'
                                    order by  2 limit 50";
        $unidad_medida = consulta_string($sql_unidad_medida, 'unid_sigl_unid', $oIfx, '');
        $unidad_medida = '<label style="font-style: oblique;">' . $unidad_medida . '</label>';
        $nombre_unidad_medida = consulta_string($sql_unidad_medida, 'unid_nom_unid', $oIfx, '');





        $date = strtotime($balpc_hora_balpc);
        $hora_proceso = date('H:i:s', $date);


        $html_area_prod .= '
                                <div class="row">
                                    <div class="col-md-12">
                                        <table align="left" class="table table-striped table-condensed" style="width: 100%; margin-bottom: 0px;">
                                            <tr>
                                                <td colspan="4" align="center" class="bg-primary">DETALLE COMPRAS
                                                    <div class="btn btn-success btn-sm" onclick="vista_previa_detalle_bal();">
                                                        <span class="glyphicon glyphicon-print"></span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <br>
                                    <br>

                                    <div class="col-md-6">
                                        <table id="tbclientes_prod" class="table table-striped table-condensed table-bordered table-hover" style="width: 98%; margin-top: 20px;" align="center">
                                            <thead>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Fecha</td>
                                                <td class="fecha_letra" align="">
                                                    ' . $balpc_fec_comp . '
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Nombre Recibe</td>
                                                <td class="fecha_letra" align="">
                                                    ' . $balpc_nom_recibe . '
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Proveedor</td>
                                                <td class="fecha_letra" align="">
                                                    ' . $balpc_nom_clpv . '
                                                </td>                         
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Bodega</td>
                                                <td class="fecha_letra" align="">
                                                    (' . $balpc_cod_bode . ') - ' . $bodega_nombre . '
                                                </td>                    
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Producto a Pesar</td>
                                                <td class="fecha_letra" align="">
                                                    ' . $balpc_nom_prod . '
                                                </td> 
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">GUIA</td>
                                                <td class="fecha_letra" align="">
                                                    ' . $balpc_guia_id . '
                                                </td> 
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">LOTE (ID)</td>
                                                <td class="fecha_letra" align="">
                                                    ' . $balpc_lote_id . '
                                                </td> 
                                            </tr>
                                            
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table id="tbclientes_prod" class="table table-striped table-condensed table-bordered table-hover" style="width: 98%; margin-top: 20px;" align="center">
                                            <thead>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">N° Pollos Procesados</td>
                                                <td class="fecha_letra" align="">
                                                    ' . $balpc_prod_procesa . '
                                                </td>                        
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">N° Pollos Pedido</td>
                                                <td class="fecha_letra" align="">
                                                    ' . $balpc_prod_pedido . '
                                                </td>                      
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Peso Promedio Pedido</td>
                                                <td class="fecha_letra" align="">
                                                    ' . $balpc_peso_promedi . ' ' . $unidad_medida . '
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Precio</td>
                                                <td class="fecha_letra" align="">
                                                    ' . $balpc_uco_prod . '
                                                </td>                      
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Hora LLegada</td>
                                                <td class="fecha_letra" align="">
                                                    ' . $hora_proceso . '
                                                </td>                        
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Transportista</td>
                                                <td class="fecha_letra" align="">
                                                    ' . $balpc_nom_transp . '
                                                </td>                      
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Peso Promedio Jaulas</td>
                                                <td class="fecha_letra" align="">
                                                    ' . $balpc_peso_jaula . ' ' . $unidad_medida . '
                                                </td>                        
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <br>
                                    <br>
                                    <br>
                                    <div class="col-md-6" style="text-align: center">';

        $html_area_prod .= '<table id="" class="table table-bordered table-hover table-striped table-condensed" style="margin-top: 30px">
                                <thead>
                                    <tr>
                                        <th colspan="12"><h6>LISTA DE PRODUCTOS AGREGADOS</h6></th>
                                    </tr>
                                    <tr>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold">N°</td>
                                        <td class="success" style="width: 4.5%; color: #00859B; font-weight: bold">COD. PRODUCTO</td>
                                        <td class="success" style="width: 4.5%; color: #00859B; font-weight: bold">NOMBRE PRODUCTO</td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold">CANTIDAD (PESO BRUTO)</td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold">N° JAULA</td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold">N° POLLOS</td>
                                    </tr>
                                </thead>
                                <tbody>';

        $sql_saedbalpc = "SELECT * 
                            FROM saedbalpc 
                            where 
                                dbalpc_cod_modu = 'COMPRA_SIN_RETENCION'
                                and dbalpc_num_comp = $cod_minv
                                ORDER BY dbalpc_cod_dbalpc
                            ";


        $numeral = 1;
        $total_cantidad = 0;
        $total_jaula = 0;
        $total_pollos = 0;

        if ($oIfx->Query($sql_saedbalpc)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    $dbalpc_cod_dbalpc = $oIfx->f('dbalpc_cod_dbalpc');
                    $dbalpc_cod_prod = $oIfx->f('dbalpc_cod_prod');
                    $dbalpc_nom_prod = $oIfx->f('dbalpc_nom_prod');
                    $dbalpc_peso_prod = $oIfx->f('dbalpc_peso_prod');
                    $dbalpc_num_jaula = $oIfx->f('dbalpc_num_jaula');
                    $dbalpc_num_pollos = $oIfx->f('dbalpc_num_pollos');
                    $dbalpc_cod_bode = $oIfx->f('dbalpc_cod_bode');

                    $html_area_prod .= '<tr height="20" style="cursor: pointer">
                                            <td style=""><b>' . $numeral . '</b></td>
                                            <td style=""><b>' . $dbalpc_cod_prod . '</b></td>
                                            <td style=""><b>' . $dbalpc_nom_prod . '</b></td>
                                            <td style=""><b>' . $dbalpc_peso_prod . '</b></td>
                                            <td style=""><b>' . $dbalpc_num_jaula . '</b></td>
                                            <td style=""><b>' . $dbalpc_num_pollos . '</b></td>
                                        </tr>';
                    $numeral++;

                    $total_cantidad += $dbalpc_peso_prod;
                    $total_jaula += $dbalpc_num_jaula;
                    $total_pollos += $dbalpc_num_pollos;
                } while ($oIfx->SiguienteRegistro());


                $html_area_prod .= '<tr style="">
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold;"></td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold;"></td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold;">TOTALES</td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold;">' . $total_cantidad . '</td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold;">' . $total_jaula . '</td>
                                        <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold;">' . $total_pollos . '</td>
                                    </tr>';
            }
        }
        $oIfx->Free();


        $html_area_prod .= '</tbody>
                    </table>
                </div>';


        $html_area_prod .= '
                                <div class="col-md-6">
                                
                                    <table id="tbclientes_prod" class="table table-striped table-condensed table-bordered table-hover" style="width: 98%; margin-top: 20px;" align="center">
                                        <thead>
                                            <tr>
                                                <th colspan="12"><h6>INFORMACION ADICIONAL</h6></th>
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">X</td>
                                                <td class="fecha_letra" align="" colspan="2">
                                                    ' . $balpc_peso_promedi . ' ' . $unidad_medida . '
                                                </td>                      
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold"># Pollos</td>
                                                <td class="fecha_letra" align="" colspan="2">
                                                    ' . $balpc_prod_procesa . '
                                                </td>                      
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">P. Bruto</td>
                                                <td class="fecha_letra" align="" colspan="2">
                                                    ' . $balpc_peso_bruto . ' ' . $unidad_medida . '
                                                </td>                      
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">P. Jaulas</td>
                                                <td class="fecha_letra" align="" colspan="2">
                                                    ' . $balpc_peso_jaulas . ' ' . $unidad_medida . '
                                                </td>                     
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">P. Muertos</td>
                                                <td class="fecha_letra" align="">
                                                    Cantidad: ' . $balpc_prod_muert . '
                                                </td>    
                                                <td class="fecha_letra" align="">
                                                    Peso: ' . $balpc_peso_muert . ' ' . $unidad_medida . '
                                                </td>                   
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">P. Decomiso</td>
                                                <td class="fecha_letra" align="">
                                                    Cantidad: ' . $balpc_prod_decom . '
                                                </td>   
                                                <td class="fecha_letra" align="">
                                                    Peso: ' . $balpc_peso_decom . ' ' . $unidad_medida . '
                                                </td>                   
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Merma Alas</td>
                                                <td class="fecha_letra" align="">
                                                    N# Piezas: ' . $balpc_merm_alas . '
                                                </td>   
                                                <td class="fecha_letra" align="">
                                                    Peso: ' . $balpc_peso_alas . ' ' . $unidad_medida . '
                                                </td>                   
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Merca Piernas</td>
                                                <td class="fecha_letra" align="">
                                                    N# Piezas: ' . $balpc_merm_piern . '
                                                </td>   
                                                <td class="fecha_letra" align="">
                                                    Peso: ' . $balpc_peso_piern . ' ' . $unidad_medida . '
                                                </td>                   
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Merma AB</td>
                                                <td class="fecha_letra" align="">
                                                    N# Piezas: ' . $balpc_merm_ab . '
                                                </td>   
                                                <td class="fecha_letra" align="">
                                                    Peso: ' . $balpc_peso_ab . ' ' . $unidad_medida . '
                                                </td>                   
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Merma Organos</td>
                                                <td class="fecha_letra" align="">
                                                    N# Piezas: ' . $balpc_merm_orga . '
                                                </td>   
                                                <td class="fecha_letra" align="">
                                                    Peso: ' . $balpc_peso_orga . ' ' . $unidad_medida . '
                                                </td>                   
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">Peso Adicional</td>
                                                <td class="fecha_letra" align="" colspan="2">
                                                    ' . $balpc_peso_adici . ' ' . $unidad_medida . '
                                                </td>                    
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">P. Neto</td>
                                                <td class="fecha_letra" align="" colspan="2">
                                                    ' . $balpc_peso_neto . ' ' . $unidad_medida . '
                                                </td>                       
                                            </tr>
                                            <tr>
                                                <td  style="color: #00859B; font-weight: bold">V. Efectivo</td>
                                                <td class="fecha_letra" align="" colspan="2">
                                                    ' . $balpc_val_efecti . '
                                                </td>                      
                                            </tr>
                                        </thead>
                                    </table>
                                
                                </div>
                            
                            ';


        $html_area_prod .= '</div>';



        $_SESSION['ImpresionDetalleBalanza'] = $html_area_prod;


        $modal = '<div id="mostrarModalBalanza2" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg" style="width:1200px;">>
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title"><b>Detalle Balanza</b></h4>
                            </div>
                            <div class="modal-body">';
        $modal .= $html_area_prod;
        $modal .= '          </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                 </div>';




        $oReturn->assign("divFormularioModal2", "innerHTML", $modal);
        $oReturn->script("abre_modal_balanza2()");
    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

    return $oReturn;
}

// ---------------------------------------------------------------------------------------------
// funciones pollos campo balanza
// --------------------------------------------------------------------------------------



// ---------------------------------------------------------------------------------------------
// funcion Tabla Amortizacion
// --------------------------------------------------------------------------------------
function generar_tabla_amortizacion($aForm = '')
{

    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $S_PAIS_API_SRI = $_SESSION['S_PAIS_API_SRI'];
    // Adrian47
    if ($S_PAIS_API_SRI == '51') {

        $empresa = $aForm['empresa'];
        $sucursal = $aForm['sucursal'];
        $dias_cuotas_fp = $aForm['dias_cuotas_fp'];
        $cuotas_fp = $aForm['cuotas_fp'];
        $fecha_inicio = $aForm['fecha_inicio'];
        $valor = $aForm['valor'];
        $forma_pago_prove = $aForm['forma_pago_prove'];


        $sql_fpag = "SELECT fpag_des_fpag  from saefpag where
                        fpag_cod_fpag = $forma_pago_prove";
        $fpag_des_fpag = consulta_string($sql_fpag, 'fpag_des_fpag', $oIfx, '');


        try {

            $sHtml = '';

            $sHtml .= '
                    <table id="tbclientes" class="table table-bordered table-hover table-striped table-condensed" style="margin-top: 30px">
                        <thead>
                            <tr >
                                <td class="success" style="width: 1.5%; color: #00859B; font-weight: bold">N.-</td>
                                <td class="success" style="width: 4.5%; color: #00859B; font-weight: bold">Fecha</td>
                                <td class="success" style="width: 4.5%; color: #00859B; font-weight: bold">Dias Cuota</td>
                                <td class="success" style="width: 4.5%; color: #00859B; font-weight: bold">Fecha Final</td>
                                <td class="success" style="width: 4.5%; color: #00859B; font-weight: bold">Forma Pago</td>
                                <td class="success" style="width: 4.5%; color: #00859B; font-weight: bold">Porcentaje</td>
                                <td class="success" style="width: 4.5%; color: #00859B; font-weight: bold">Valor</td>
                            </tr>
                        </thead>
                        <tbody>';

            $fecha_dias = $fecha_inicio;
            $porcentaje_cuotas = round(100 / $cuotas_fp, 2);
            $valor_cuota = round($valor / $cuotas_fp, 2);

            $suma_dias = 0;
            $suma_porcentaje = 0;
            $suma_valor_pagar = 0;

            $suma_porcentaje2 = 0;
            $suma_valor_pagar2 = 0;

            for ($x = 1; $x <= $cuotas_fp; $x++) {

                $suma_dias += $dias_cuotas_fp;
                $suma_porcentaje += $porcentaje_cuotas;
                $suma_valor_pagar += $valor_cuota;

                if ($x == $cuotas_fp) {
                    if ($suma_porcentaje > 100) {
                        $diferencia = $suma_porcentaje - 100;
                        $porcentaje_cuotas = round($porcentaje_cuotas - $diferencia, 2);
                    } else if ($suma_porcentaje < 100) {
                        $diferencia = 100 - $suma_porcentaje;
                        $porcentaje_cuotas = round($porcentaje_cuotas + $diferencia, 2);
                    }

                    if ($suma_valor_pagar > $valor) {
                        $diferencia_val = $suma_valor_pagar - $valor;
                        $valor_cuota = round($valor_cuota - $diferencia_val, 2);
                    } else if ($suma_valor_pagar < $valor) {
                        $diferencia_val = $valor - $suma_valor_pagar;
                        $valor_cuota = round($valor_cuota + $diferencia_val, 2);
                    }
                }


                $suma_porcentaje2 += $porcentaje_cuotas;
                $suma_valor_pagar2 += $valor_cuota;


                $fecha_dias = date("Y-m-d", strtotime($fecha_dias . "+ $dias_cuotas_fp days"));

                $sHtml .= '<tr>
                           <td style="width: 1.5%;">' . $x . '</td> 
                           <td style="width: 1.5%;">' . $fecha_inicio . '</td>
                           <td style="width: 1.5%;">' . $dias_cuotas_fp . '</td>
                           <td style="width: 1.5%;">' . $fecha_dias . '</td>
                           <td style="width: 1.5%;">' . $fpag_des_fpag . '</td>
                           <td style="width: 1.5%;">' . $porcentaje_cuotas . '%</td>
                           <td style="width: 1.5%;">' . $valor_cuota . '</td>
                           
                       </tr>';
            }


            $sHtml .= '<tr>
                           <td style="width: 1.5%; font-size: 15px">TOTALES</td> 
                           <td style="width: 1.5%; font-size: 15px"></td>
                           <td style="width: 1.5%; font-size: 15px">' . $suma_dias . '</td>
                           <td style="width: 1.5%; font-size: 15px"></td>
                           <td style="width: 1.5%; font-size: 15px"></td>
                           <td style="width: 1.5%; font-size: 15px">' . $suma_porcentaje2 . '%</td>
                           <td style="width: 1.5%; font-size: 15px">' . $suma_valor_pagar2 . '</td>
                           <input type="number" id="dias_cuotas_fp_input" name="dias_cuotas_fp_input" value="' . $dias_cuotas_fp . '" style="display: none" />
                           <input type="number" id="cuotas_fp_input" name="cuotas_fp_input" value="' . $cuotas_fp . '" style="display: none" />
                           <input type="date" id="fecha_inicio_input" name="fecha_inicio_input" value="' . $fecha_inicio . '" style="display: none" />
                           <input type="number" id="valor_input" name="valor_input" value="' . $valor . '" style="display: none" />
                           
                       </tr>';




            $sHtml .= '
                        </tbody>
                    </table>
                ';

            $oReturn->assign("div_tabla_amortizacion", "innerHTML", $sHtml);
        } catch (Exception $e) {
            $oReturn->alert($e->getMessage());
        }
    }

    return $oReturn;
}
// ---------------------------------------------------------------------------------------------
// FIN funcion Tabla Amortizacion
// --------------------------------------------------------------------------------------




function finalizar_oc($num_comp_oc = 0, $aForm = '')
{

    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    try {
        $oIfx->QueryT('BEGIN WORK;');

        $id_usuario = $_SESSION['U_ID'];
        $fecha_actual = date('Y-m-d');

        $sql_update_saeminv_oc = "UPDATE saeminv set  minv_cer_sn = 'S' ,
                                     minv_fech_modi      = '$fecha_actual',
                                     minv_usua_modi      = $id_usuario where
                                     minv_num_comp       = $num_comp_oc ";
        $oIfx->QueryT($sql_update_saeminv_oc);


        $oIfx->QueryT('COMMIT WORK');
        $oReturn->script("Swal.fire({
                                        position: 'center',
                                        type: 'success',
                                        title: 'Orden de Compra Cerrada Correctamente...!',
                                        showConfirmButton: true,
                                        confirmButtonText: 'Aceptar',
                                        timer: 2000
                                    })");
    } catch (Exception $e) {
        // rollback
        $oIfx->QueryT('ROLLBACK WORK;');
        $oReturn->alert($e->getMessage());
        $oReturn->assign("ctrl", "value", 1);
    }

    return $oReturn;
}



/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
/* PROCESO DE REQUEST DE LAS FUNCIONES MEDIANTE AJAX NO MODIFICAR */
$xajax->processRequest();
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
