<?php

require("_Ajax.comun.php"); // No modificar esta linea
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  // S E R V I D O R   A J A X //
  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

/**
  Herramientas de apoyo
 */
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
    $arrayaDataGridVisible[22] = 'N';
    $arrayaDataGridVisible[24] = 'N';
    $arrayaDataGridVisible[25] = 'S';
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

    $_SESSION['aLabelGirdProd_INV_MRECO'] = array(
        'Id',         'Bodega',       'Codigo Item',      'Descripcion',      'Unidad',       'Cantidad',         'Costo',        'Impuesto',
        'Dscto 1',    'Dscto 2',      'Dscto Gral',       'Total',            'Total Con Impuesto',               'lote',        'Fecha Ela',
        'Fecha Cad',  'Detalle',      'Precio',           'Cuenta',           'Cuenta Impuesto',                  'Modificar',    'Eliminar',
        'Dmov', '', 'Evaluacion', 'datos_evaluacion', 'Recep C.U.', 'MAC'
    );

    $sql = "select mone_sgl_mone from saemone where
                    mone_cod_empr = $idempresa and
                    mone_cod_mone in ( select pcon_mon_base from saepcon where  pcon_cod_empr = $idempresa  ) ";
    $mone_sgl_mone = consulta_string_func($sql, 'mone_sgl_mone', $oIfx, '');
    unset($_SESSION['U_MONE_SIGLA']);
    $_SESSION['U_MONE_SIGLA'] = $mone_sgl_mone;

    //CAMPO PARA ATAR ORDENES DE COMPRA A UNA FACTURA
    $sqlgein = "SELECT count(*) as conteo
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE COLUMN_NAME = 'minv_comp_ord' AND TABLE_NAME = 'saeminv'";
    $ctralter = consulta_string($sqlgein, 'conteo', $oIfx, 0);
    if ($ctralter == 0) {
        $sqlalter = "alter table saeminv add  minv_comp_ord int4;";
        $oIfx->QueryT($sqlalter);
    }


    switch ($sAccion) {
        case 'nuevo':
            // EMPRESA
            $sql = "select empr_cod_empr, empr_nom_empr from saeempr ";
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

            $anio = date('Y');
            $fecha_ini_actual = $anio . '-01-01';
            $sql_cod_ejer      = "select ejer_cod_ejer from saeejer where ejer_cod_empr = $idempresa and ejer_fec_inil = '$fecha_ini_actual' ";
            $ejer_cod_ejer = consulta_string_func($sql_cod_ejer, 'ejer_cod_ejer', $oIfx, '');

            $sql_ejer = "select ejer_cod_ejer, DATE_PART('year', ejer_fec_inil) as anio
			from saeejer 
			where ejer_cod_empr = $idempresa
			order by anio desc";
            $lista_ejercicio = lista_boostrap($oIfx, $sql_ejer, $ejer_cod_ejer, 'ejer_cod_ejer',  'anio');

            $mes = date('m');
            $sql_per = "select prdo_num_prdo, prdo_nom_prdo
			from saeprdo
			where prdo_cod_empr = '$idempresa'
			and prdo_cod_ejer = '$ejer_cod_ejer'			
			order by prdo_num_prdo";
            $lista_periodo = lista_boostrap($oIfx, $sql_per, $mes, 'prdo_num_prdo',  'prdo_nom_prdo');


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
            $sql = "select sucu_cod_sucu, sucu_nom_sucu from saesucu where sucu_cod_empr = $idempresa";
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
            $ifu->AgregarComandoAlEscribir('producto', 'autocompletar_producto( event, 1 )');
            $ifu->AgregarCampoTexto('codigo_producto', 'Cod. Prod|left', false, '', 120, 100);
            $ifu->AgregarComandoAlEscribir('codigo_producto', 'autocompletar_producto( event, 2)');
            $ifu->AgregarCampoTexto('codigo_barra', 'Cod. Barra|left', false, '', 120, 100);
            $ifu->AgregarComandoAlEscribir('codigo_barra', 'autocompletar_producto( event, 3)');

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
            unset($_SESSION['aDataGirdRete']);
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
            $sHtml_Fp .= '<tr>
                                            <td class="labelFrm">' . $fu->ObjetoHtmlLBL('fecha_inicio') . '</td>
                                            <td colspan="3">
                                                <table width="99%">
                                                    <tr>
                                                        <td><input type="date" name="fecha_inicio" id="fecha_inicio" step="1" value="' . $diaHoy . '">    &nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                        <td>*No Dias:</td>
                                                        <td>
                                                            <input type="number" class="form-control input-sm" id="dias_fp" name="dias_fp" style="width:150px; text-align:right"  onchange="recalcular_fpago(2);" /> 
                                                        </td>
                                                        <td>Fecha Final:</td>
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
                                            <td colspan="4" align="center">                                                            
                                                    <div class="btn btn-primary btn-sm"onclick="javascript:anadir_detalle_fp(' . $idsucursal . ')">
                                                            <span class="glyphicon glyphicon-th-list"></span>
                                                            A&ntilde;adir
                                                    </div>							
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
            $fu->AgregarCampoFecha('fecha_ela', 'Fecha Elaboracion|left', false, '');
            $fu->AgregarCampoFecha('fecha_cad', 'Fecha Caducidad|left', false, '');

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
            $ret_fec_auto = consulta_string($sql, 'retp_fech_cadu', $oIfx, date("Y-m-d"));
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

                                    <div class="btn btn-primary btn-sm"onclick="javascript:reporte_retencionInve();">
										<span class="glyphicon glyphicon-print"></span>
										Retencion
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
                                <td> <input type="date" name="fecha_pedido" id="fecha_pedido" step="1" value="' . $diaHoy . '" ></td>
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
                            id="producto" name="producto" onkeyup="autocompletar_producto( event, 1 );">
                        </td>
                        <td>' . $ifu->ObjetoHtmlLBL('codigo_producto') . '</td>
                        <td>
                            <input class="form-control input-sm" type="text" placeholder="CODIGO"  style="width: 100px; height:25px; "
                            id="codigo_producto" name="codigo_producto" onkeyup="autocompletar_producto( event, 2 );">
                        </td>
						<td>' . $ifu->ObjetoHtmlLBL('codigo_barra') . '</td>
                        <td>
                            <input class="form-control input-sm" type="text" placeholder="CODIGO BARRAS"  style="width: 100px; height:25px; "
                            id="codigo_barra" name="codigo_barra" onkeyup="autocompletar_producto( event, 3 );">
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
                        <td><div id="lote_txt"><input class="form-control input-sm" type="text" placeholder="Lote" id="lote" name="lote" style="width:150px; height:25px; text-align:right"></div></td>
                        <td><div id="fela_etiq">' . $fu->ObjetoHtmlLBL('fecha_ela') . '                                 </div></td>
                        <td><div id="fela_txt" ><input type="date" name="fecha_ela" step="1">       </div></td>
                        <td><div id="fcad_txt" >' . $fu->ObjetoHtmlLBL('fecha_cad') . '                                    </div></td>
                        <td><div id="fcad_etiq"><input type="date" name="fecha_cad" step="1">       </div></td>                        
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

                                <div class="btn btn-primary btn-sm"onclick="javascript:reporte_retencionInve();">
                                    <span class="glyphicon glyphicon-print"></span>
                                    Retencion
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
                            </div> 
                            
                        </div><br><br>';

    $sHtml_cab .= '<div class="col-md-12">
                        <div class="form-group">
                            <label for="clave_acceso_" class="col-sm-2 control-label">Clave de Acceso:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control input-sm" id="clave_acceso_" name="clave_acceso_" value="" text-align:right; />
                            </div>
                            <div class="col-sm-2">
                                <div class="btn btn-success btn-sm" onclick="valida_existe_factura();" style="width:100%">
                                    <span class="glyphicon glyphicon-retweet"></span>
                                    Generar Clave
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
                                <label for="fecha_pedido" class="control-label">* Fecha Compra:</label>
                                    <!--
                                    <input type="date" name="fecha_pedido" id="fecha_pedido" step="1" value="' . date("Y-m-d") . '" class="form-control input-sm" >
                                    -->       
                                    <input type="date" name="fecha_pedido" id="fecha_pedido" step="1" value="' . date("Y-m-d") . '" class="form-control input-sm" onchange="anio_fecha_abierto();" >
                            </div>  
                            <div class="col-md-2">
                                <label for="fecha_entrega" class="control-label">* Fecha Pago:</label>
                                <input type="date" name="fecha_entrega"  id="fecha_entrega" step="1" value="' . date("Y-m-d") . '" class="form-control input-sm" onchange="fecha_pago(1);">   
                            </div>  
                            <div class="col-md-1">
                                <label for="plazo" class="control-label">* N. Plazo:</label>
                                <input type="number" class="form-control input-sm" id="plazo" name="plazo" style="text-align:right" onchange="recalcular_fpago(1);" />  
                            </div>
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
                                    name="producto" onkeyup="autocompletar_producto( event, 1 ); form1.producto.value=form1.producto.value.toUpperCase();"/>
                                    <span class="input-group-addon primary" style="cursor: pointer;" onClick="autocompletar_producto_btn( ' . $idempresa . ' );"><i class="fa fa-search"></i></span>
                                </div>   
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="cantidad">Cantidad:</label>    
                                <input type="text" class="form-control input-sm" placeholder="Cantidad" id="cantidad" name="cantidad" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = 0; "/>                                                                
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

    // $L = new DateTime(); 
    // $L->format( 'Y-m-t' );

    //IMPUESTO POR PAIS

    $array_imp = $_SESSION['U_EMPRESA_IMPUESTO'];
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
                                <input type="text" class="form-control input-sm" placeholder="Serie" id="serie_prod" name="serie_prod" style="display:none" />                              
                            </div>
                            <div class="col-md-3">
                                <label class="control-label" id="mac_prod_txt" style="display:none">MAC</label>
                                <input type="text" class="form-control input-sm" placeholder="Serie" id="mac_ad_prod" name="mac_ad_prod" style="display:none" />                              
                            </div>

                            <div class="col-md-3">
                                <label class="control-label" id="lote_prod_txt" style="display:none">' . $ifu->ObjetoHtmlLBL('lote') . '</label>
                                <input type="text" class="form-control input-sm" placeholder="Lote" id="lote_prod" name="lote_prod" style="display:none" />     
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="fecha_ela" style="display:none"><div style="display:"  id="fela_etiq1">' . $fu->ObjetoHtmlLBL('fecha_ela') . '</div></label>    
                                <div style="display:" id="fela_txt1" ><input type="date" name="fecha_ela" id="fecha_ela" step="1" onchange="validar_fecha_elaboracion();" class="form-control input-sm" style="display:none"></div>                                                                
                            </div> 
                        </div> 
                    </div> 
                    <div class="col-md-12">
                        <div class="form-row">
                            <div class="col-md-2">
                                <label class="control-label" for="fecha_cad" style="display:none"><div style="display:"  id="fcad_txt1" >' . $fu->ObjetoHtmlLBL('fecha_cad') . '</div></label>    
                                <div style="display:" id="fcad_etiq1"><input type="date" name="fecha_cad" id="fecha_cad" step="1" onchange="validar_fecha_caducidad();" class="form-control input-sm" style="display:none"></div>
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


    // $sHtml_lista_compras .= '<div class="row">';
    $sHtml_lista_compras .= '<table class="table" style="width: 100%;  margin-top:20px;">
                                <tr><td align="center" class="bg-primary">LISTA DE COMPRAS</td></tr>
                            </table>';
    $sHtml_lista_compras .= '<div class="col-md-12">
                        <div class="form-row">
                            <div class="col-md-3">
                                <label class="control-label" for="cliente_nombre_listac">* Suplidor:</label>                                
                                <div class="input-group">
                                    <input type="text" class="form-control input-sm" id="cliente_codigo_listac" name="cliente_codigo_listac" style="display:none" readonly />
                                    <input type="text" class="form-control input-sm" placeholder="ESCRIBA SUPLIDOR Y PRESIONE ENTER" id="cliente_nombre_listac" 
                                    name="cliente_nombre_listac" onkeyup="autocompletar_lista( ' . $idempresa . ', event ); form1.cliente_nombre_listac.value=form1.cliente_nombre_listac.value.toUpperCase();"/>
                                    <span class="input-group-addon primary" style="cursor: pointer;" onClick="autocompletar_btn_lista(' . $idempresa . ' );"><i class="fa fa-search"></i></span>
                                </div>                                
                            </div> 
                            <div class="col-md-3">
                                <label  for="tran">* Ejercicio:</label>
                                <select id="ejercicio" name="ejercicio" class="form-control input-sm" onchange="f_filtro_periodo()" required>
                                    <option value="">Seleccione una opcion..</option>
                                    ' . $lista_ejercicio . '
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label  for="tran">* Periodo:</label>
                                <select id="periodo" name="periodo" class="form-control input-sm" required>
                                    <option value="">Seleccione una opcion..</option>
                                    ' . $lista_periodo . '
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label  for="tran">* Detallado:</label>
						        <input type="checkbox" id="detallado" name="detallado" value="S"> 
                            </div>

                           
                        </div>
                    </div>';
    $sHtml_lista_compras .= '<div class="col-md-12">
                        <div class="form-row">
                              
                        </div>
                    </div>';
    $sHtml_lista_compras .= '<div class="col-md-12" style="margin-top: 20px">
                        <div class="form-row">
                            <div class="col-md-12" style="text-align: center">
                                <div class="btn btn-success btn-sm" onclick="consultar_compras();" style="width:15%">
                                    <span class="glyphicon glyphicon-retweet"></span>
                                    Consultar
                                </div>
                            </div> 
                        </div>
                    </div>';
    // $sHtml_lista_compras .= '</div>';









    $oReturn->assign("divFormularioCabecera", "innerHTML", $sHtml_cab);
    //$oReturn->assign("nota_pedido", "disabled", true);
    $oReturn->assign("divReporte", "innerHTML", "");
    $oReturn->assign("divAbono", "innerHTML", "");
    $oReturn->assign("cliente_nombre", "placeholder", "ESCRIBA EL CLIENTE O RUC Y PRESIONE F4 O ENTER...");
    $oReturn->assign("producto", "placeholder", "ESCRIBA EL PROD. Y PRESIONE F4 ....");
    $oReturn->assign("divFormularioFp", "innerHTML", $sHtml_Fp);
    $oReturn->assign("cliente_nombre", "focus()", "");
    $oReturn->assign("divFormularioRET", "innerHTML", $sHtml_ret);

    // Lista de compras
    $oReturn->assign("divFormularioCebeceraC", "innerHTML", $sHtml_lista_compras);
    // $oReturn->script("consultar_compras()");


    // --------------------------------------------------------------------------------------
    // cargar compra de inventario cuando se vaya a modificar
    // --------------------------------------------------------------------------------------
    $minv_cod_edit = $_SESSION['num_comp_edit'];
    if (!empty($minv_cod_edit)) {
        $oReturn->script("cargar_invetario_compra_ad($minv_cod_edit)");
    }
    // --------------------------------------------------------------------------------------
    // FIN cargar compra de inventario cuando se vaya a modificar
    // --------------------------------------------------------------------------------------



    return $oReturn;
}

function cargar_invetario_compra_ad($num_comp_edit, $aForm)
{
    //Definiciones
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

    $oIfxB = new Dbo();
    $oIfxB->DSN = $DSN_Ifx;
    $oIfxB->Conectar();

    $oReturn = new xajaxResponse();

    // $idempresa = $_SESSION['U_EMPRESA'];
    // $empresa = $aForm['empresa'];

    // DATOS CABECERA MINV
    $sql_saeminv = "SELECT * FROM saeminv where minv_num_comp = $num_comp_edit";
    if ($oIfx->Query($sql_saeminv)) {
        if ($oIfx->NumFilas() > 0) {
            do {
                $minv_cod_tran = $oIfx->f('minv_cod_tran');
                $minv_cod_empr = $oIfx->f('minv_cod_empr');

                // DATOS PROVEEDOOR
                $minv_cod_clpv = $oIfx->f('minv_cod_clpv');
                $sql_nombre_clpv = "SELECT clpv_cod_clpv, clpv_nom_clpv, clpv_ruc_clpv from saeclpv WHERE clpv_cod_clpv = $minv_cod_clpv limit 1;";
                if ($oIfxA->Query($sql_nombre_clpv)) {
                    if ($oIfxA->NumFilas() > 0) {
                        do {
                            $clpv_cod_clpv = $oIfxA->f('clpv_cod_clpv');
                            $clpv_nom_clpv = $oIfxA->f('clpv_nom_clpv');
                            $clpv_ruc_clpv = $oIfxA->f('clpv_ruc_clpv');

                            $sql_correo_prove = "SELECT emai_ema_emai from saeemai where
                                        emai_cod_empr = $minv_cod_empr and
                                        emai_cod_clpv = $minv_cod_clpv ";
                            $correo_clpv = consulta_string_func($sql_correo_prove, 'emai_ema_emai', $oIfxB, '');


                            $oReturn->assign("cliente", "value", $minv_cod_clpv);
                            $oReturn->assign("cliente_nombre", "value", $clpv_nom_clpv);
                            $oReturn->assign("ruc", "value", $clpv_ruc_clpv);
                            $oReturn->assign("correo_prove", "value", $correo_clpv);

                            //
                        } while ($oIfxA->SiguienteRegistro());
                    }
                }

                // CAMPOS QUE SI SALEN DE LA MINV
                $oReturn->assign("tran", "value", $minv_cod_tran);



                //
            } while ($oIfx->SiguienteRegistro());
        }
    }

    return $oReturn;
}


function f_filtro_ejercicio($aForm, $data)
{
    //Definiciones
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oCon = new Dbo();
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();
    $idempresa = $_SESSION['U_EMPRESA'];
    //variables formulario
    $empresa = $aForm['empresa'];
    if (empty($empresa)) {
        $empresa = $idempresa;
    }
    // DATOS EMPRESA
    $sql = "select ejer_cod_ejer, DATE_PART('year', ejer_fec_inil) as anio
			from saeejer 
			where ejer_cod_empr = $empresa
			order by anio desc";
    //echo $sql; exit;
    $i = 1;
    if ($oIfx->Query($sql)) {
        $oReturn->script('eliminar_lista_anio();');
        if ($oIfx->NumFilas() > 0) {
            // $i = $oIfx->NumFilas();
            do {
                $oReturn->script(('anadir_elemento_anio(' . $i++ . ',\'' . $oIfx->f('ejer_cod_ejer') . '\',\'' . $oIfx->f('anio') . '\')'));
            } while ($oIfx->SiguienteRegistro());
        }
    }
    // AÑO ACTUAL
    $sql_ejer = "select ejer_cod_ejer from saeejer where ejer_cod_empr = $empresa and DATE_PART('year', ejer_fec_inil) = DATE_PART('year', CURRENT_DATE)";
    $data = consulta_string($sql_ejer, 'ejer_cod_ejer', $oIfx, 0);
    $oReturn->assign('ejercicio', 'value', $data);
    $oReturn->script("f_filtro_periodo();");
    return $oReturn;
}

function f_filtro_periodo($aForm, $data)
{
    //Definiciones
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oCon = new Dbo();
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();


    // variables de session
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];

    //variables formulario
    $ejercicio = $aForm['ejercicio'];
    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];

    if (empty($empresa)) {
        $empresa = $idempresa;
    }
    if (empty($sucursal)) {
        $sucursal = $idsucursal;
    }


    // DATOS DEL PERIODO
    $sql = "select prdo_num_prdo, prdo_nom_prdo
			from saeprdo
			where prdo_cod_empr = '$empresa'
			and prdo_cod_ejer = '$ejercicio'			
			order by prdo_num_prdo";
    //echo $sql; exit;
    $i = 1;
    if ($oIfx->Query($sql)) {
        $oReturn->script('eliminar_lista_periodo();');
        if ($oIfx->NumFilas() > 0) {
            do {
                $oReturn->script(('anadir_elemento_periodo(' . $i++ . ',\'' . $oIfx->f('prdo_num_prdo') . '\', \'' . $oIfx->f('prdo_nom_prdo') . '\' )'));
            } while ($oIfx->SiguienteRegistro());
        }
    }
    // BUSCAR MES ACTUAL
    $sql_periodo = "select prdo_num_prdo from saeprdo where prdo_cod_empr = $empresa and prdo_cod_ejer = $ejercicio and DATE_PART('month',prdo_fec_ini) = DATE_PART('month',CURRENT_DATE)";
    $data = consulta_string($sql_periodo, 'prdo_num_prdo', $oIfx, 0);
    //echo $data; exit;
    $oReturn->assign('periodo', 'value', $data);
    return $oReturn;
}




function consultar_compras($aForm = '')
{
    //Definiciones
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oCon = new Dbo();
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo();
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $oReturn = new xajaxResponse();

    //variables de sesion
    $array = ($_SESSION['ARRAY_PINTA']);
    $usuario_web = $_SESSION['U_ID'];
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];

    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $codigo_cliente = $aForm['cliente_codigo_listac'];
    $nombre_cliente = $aForm['cliente_nombre_listac'];

    // echo($codigo_cliente);exit;

    if (empty($empresa)) {
        $empresa = $idempresa;
    }
    if (!empty($sucursal)) {
        $filtro = "and saeret.asto_cod_sucu = " . $sucursal;
        $filtro_gasto = "fprv_cod_sucu = " . $sucursal . " and";
        $filtro_compras = "and minv_cod_sucu = " . $sucursal;
    }

    //variables formulario
    $ejercicio = $aForm['ejercicio'];
    $periodo   = $aForm['periodo'];
    $detallado = $aForm['detallado'];

    $sql_anio = "select DATE_PART('year',ejer_fec_inil) as anio from saeejer where ejer_cod_empr = $empresa and ejer_cod_ejer=$ejercicio";
    $anio = consulta_string($sql_anio, 'anio', $oIfx, 0);

    if ($periodo <= 9) {
        $mes = '0' . $periodo;
    }

    try {
        $oIfx->QueryT('BEGIN');
        // TIPOS DE RETENCIONES
        $sql = " select distinct ret_cta_ret,  tret_ban_retf 
					from saeret, saetret 
					where tret_cod = ret_cta_ret
					and tret_cod_empr = asto_cod_empr
					and asto_cod_empr = $empresa
					and asto_cod_ejer = $ejercicio
					$filtro
					order by 2";
        //echo $sql; exit;
        if ($oIfx->Query($sql)) {
            if ($oIfx->NumFilas() > 0) {
                unset($arrayTipoReteciones);
                do {
                    $arrayTipoReteciones[] = array($oIfx->f('ret_cta_ret'), $oIfx->f('tret_ban_retf'));
                } while ($oIfx->SiguienteRegistro());
            }
        }


        // $html='<table class="table table-bordered table-condensed table-hover">';
        $html = '
            <table id="tbclientes"  align="center" border="0" class="table table-hover table-bordered table-striped table-condensed" style="width: 98%; margin-bottom: 0px; margin-top: 20px !important;">            
            <thead>
        ';
        //var_dump ($arrayTipoReteciones); exit;
        $oIfx->Free($arrayTipoReteciones);
        if (count($arrayTipoReteciones) > 0) {
            unset($arrayA);
            $ll_row = count($arrayTipoReteciones);
            for ($k = 0; $k < ($ll_row); $k++) {
                if ($k < ($ll_row - 1)) {
                    if ($arrayTipoReteciones[$k][1] != $arrayTipoReteciones[$k + 1][1]) {
                        $arrayA[] = array($k + 1, $arrayTipoReteciones[$k][1]);
                    }
                } else {
                    $arrayA[] = array($k + 1, $arrayTipoReteciones[$k][1]);
                }
            }

            unset($_SESSION['ACT_REPORTE']);
            $html .= '<tr>';
            if ($detallado == 'S') {
                $html .= '<td class="bg-primary" align = "center" colspan="15">Compras</td>';
                foreach ($arrayA as $arrayB) {
                    if ($arrayB[1] == 'IR') {
                        $impuesto = 'RETENCION RENTA';
                    } else {
                        $impuesto = 'RETENCION IVA';
                    }
                    $html .= '<td class="bg-primary" align = "center" colspan="' . $arrayB[0] . '">' . $impuesto . '</td>';
                }
            } else {
                $html .= '<td class="bg-primary" align = "center" colspan="14">Compras</td>';
            }
            $html .= '</tr>';
        }


        /// nombre transacciones
        $sql = "SELECT tran_cod_tran, tran_des_tran, trans_tip_comp from saetran where tran_cod_empr='$empresa'";
        //var_dump($sql);exit;

        if ($oIfx->Query($sql)) {
            if ($oIfx->NumFilas() > 0) {
                unset($arrayTipoTransacion);
                do {
                    $arrayTipoTransacion[$oIfx->f('tran_cod_tran')] =  array($oIfx->f('tran_des_tran'), $oIfx->f('trans_tip_comp'));
                } while ($oIfx->SiguienteRegistro());
            }
        }
        /// nombre crtr
        $sql = "SELECT crtr_cod_crtr, crtr_des_crtr from saecrtr ";
        if ($oIfx->Query($sql)) {
            if ($oIfx->NumFilas() > 0) {
                unset($arrayTipoCrtr);
                do {
                    $arrayTipoCrtr[$oIfx->f('crtr_cod_crtr')] =  $oIfx->f('crtr_des_crtr');
                } while ($oIfx->SiguienteRegistro());
            }
        }


        $sql_cliente = '';
        $sql_cliente2 = '';
        if (!empty($nombre_cliente)) {
            $sql_cliente = "and fprv_cod_clpv = '$codigo_cliente'";
            $sql_cliente2 = "and minv_cod_clpv = '$codigo_cliente'";
        }



        // REPORTE
        $sql = "
			select fprv_fec_emis, 
						fprv_num_seri,
						fprv_num_fact,
						fprv_cod_asto,
						fprv_cod_tran,
						clpv_nom_clpv,
						fprv_det_fprv,	
						round(COALESCE(fprv_val_grab, 0) + COALESCE(fprv_val_grbs, 0), 2) as total_graba_12,
						round(COALESCE(fprv_val_gra0, 0) + COALESCE(fprv_val_gr0s, 0), 2) as total_graba_0,
						round(COALESCE(fprv_val_viva, 0), 2) as valor_iva,
						round(COALESCE(fprv_val_grab, 0) + COALESCE(fprv_val_grbs, 0) + COALESCE(fprv_val_gra0, 0) + COALESCE(fprv_val_gr0s, 0) + COALESCE(fprv_val_viva, 0), 2) as total,
						COALESCE(fprv_val_noi,0) as no_ojeto_iva,
						COALESCE(fprv_val_exe,0) as exento_iva,
						ret_num_ret,
						fprv_cre_fisc as sustento
					FROM saeasto, saefprv, saeclpv, saeret 
					WHERE ( saeret.rete_cod_asto = saeasto.asto_cod_asto ) and  
						( saeret.asto_cod_empr = saeasto.asto_cod_empr ) and  
						( saeret.asto_cod_sucu = saeasto.asto_cod_sucu ) and  
						( saeret.asto_cod_ejer = saeasto.asto_cod_ejer ) and  
						( saefprv.fprv_cod_asto = saeasto.asto_cod_asto ) and  
						( saefprv.fprv_cod_empr = saeasto.asto_cod_empr ) and  
						( saefprv.fprv_cod_sucu = saeasto.asto_cod_sucu ) and  
						( saefprv.fprv_cod_ejer = saeasto.asto_cod_ejer ) and
						( saefprv.fprv_cod_clpv = saeclpv.clpv_cod_clpv ) and
						( saefprv.fprv_cod_empr = saeclpv.clpv_cod_empr ) and 
						fprv_cod_empr = $empresa and 
						$filtro_gasto
						fprv_cod_ejer = $ejercicio and
						 DATE_PART('month',fprv_fec_emis) = $periodo and 
						saeret.asto_num_prdo = $periodo and
						asto_est_asto not in ('AN', 'PE')
                        $sql_cliente
					UNION 
					select minv_fmov,
						minv_ser_docu as serie,
						minv_fac_prov,
						minv_tran_minv,
						minv_cod_tran,
						clpv_nom_clpv,
						minv_cm1_minv as fprv_det_fprv,											
						round(( select sum(dmov_cto_dmov) as base_grava 
									from  saedmov where 
									dmov_cod_empr = $empresa and
									dmov_cod_sucu = saeminv.minv_cod_sucu and
									dmov_iva_porc = 12 and
									dmov_num_comp = saeminv.minv_num_comp	 ),2) total_graba_12 ,
                        round(( select COALESCE(sum(dmov_cto_dmov), '0') as base_nograva from saedmov where 
									dmov_cod_empr = $empresa and
									dmov_cod_sucu = saeminv.minv_cod_sucu and
									dmov_iva_porc = 0 and
									dmov_num_comp = saeminv.minv_num_comp ) ,2 ) as total_graba_0 ,

						round(COALESCE(minv_iva_valo,0),2) as valor_iva,
						round((minv_tot_minv -COALESCE(minv_dge_valo,0) + COALESCE(minv_otr_valo,0 ) + COALESCE(minv_fle_minv,0) + COALESCE(minv_iva_valo,0)),2)  as total,
						COALESCE(minv_val_noi, '0') as no_ojeto_iva,
						COALESCE(minv_val_exe, '0') as exento_iva,
						ret_num_ret,
						minv_cod_crtr as sustento
					from saeminv, saeclpv, saeret
					where rete_cod_asto = minv_tran_minv
					and asto_cod_empr = minv_cod_empr   
					and asto_cod_sucu = minv_cod_sucu   
					and asto_cod_ejer = minv_cod_ejer    
					and minv_cod_clpv = clpv_cod_clpv
					and minv_cod_empr = clpv_cod_empr
					and minv_cod_tran in ( select D.DEFI_COD_TRAN 
										   from SAEDEFI D, SAETRAN T 
										   WHERE T.TRAN_COD_TRAN = D.DEFI_COD_TRAN 
										   AND D.DEFI_COD_MODU = 10 
										   AND D.DEFI_COD_EMPR = $empresa 
										   AND D.DEFI_TIP_DEFI = '0' 
										   AND D.DEFI_TIP_COMP in ( '01'  , '03') 
										   AND T.TRAN_COD_EMPR = $empresa )

					and minv_cod_empr  = $empresa
					$filtro_compras
					and minv_cod_ejer = $ejercicio
					and DATE_PART('month',minv_fmov) = $periodo
					and saeret.asto_num_prdo = $periodo
					and minv_est_minv = 'M'
                    $sql_cliente2
					group by 1,2,3,4,5,6,7,8,9,10,11,12,13, 14,15
					order by 1,10,3 
			";


        // echo $sql; exit;





        //$oReturn->alert($sql);
        //exit;
        if ($oIfx->Query($sql)) {
            if ($oIfx->NumFilas() > 0) {
                unset($arrayCompras);
                do {
                    //$base_imp_renta = 0;
                    //$base_imp_renta = $oIfx->f('total_graba_12');
                    $array_fec = explode('-', $oIfx->f('fprv_fec_emis'));
                    $fecha = $array_fec[1] . '-' . $array_fec[0] . '/' . $array_fec[2];

                    $tran = $arrayTipoTransacion[$oIfx->f('fprv_cod_tran')][0];
                    $tran_cod = $arrayTipoTransacion[$oIfx->f('fprv_cod_tran')][1];
                    $crtr = $oIfx->f('sustento') . '-' . substr($arrayTipoCrtr[$oIfx->f('sustento')], 0, 38);
                    $arrayCompras[] = array(
                        $fecha,    $oIfx->f('fprv_num_seri'), $oIfx->f('fprv_num_fact'), $oIfx->f('fprv_cod_asto'),  $tran, $oIfx->f('clpv_nom_clpv'),      $oIfx->f('fprv_det_fprv'), $oIfx->f('total_graba_12'),    $oIfx->f('total_graba_0'), $oIfx->f('valor_iva'),         $oIfx->f('total'), $oIfx->f('no_ojeto_iva'),         $oIfx->f('exento_iva'), $oIfx->f('ret_num_ret'),      $oIfx->f('total_graba_12'), $oIfx->f('valor_iva'), $crtr, $tran_cod
                    );
                } while ($oIfx->SiguienteRegistro());
            }
        }
        //var_dump($arrayCompras);exit;
        $oIfx->Free($arrayCompras);
        unset($arrayTotales);
        for ($i = 0; $i < count($arrayCompras); $i++) {
            $asiento = $arrayCompras[$i][3];
            $numRete = $arrayCompras[$i][13];
            $sql = "select ret_cta_ret, ret_valor												
						from saeasto, saeret
						where ( saeret.rete_cod_asto = saeasto.asto_cod_asto )
						and ( saeret.asto_cod_empr = saeasto.asto_cod_empr ) 
						and ( saeret.asto_cod_sucu = saeasto.asto_cod_sucu ) 
						and ( saeret.asto_cod_ejer = saeasto.asto_cod_ejer ) 
						and saeasto.asto_cod_asto = '$asiento'
						and saeret.ret_num_ret = '$numRete'
						and saeasto.asto_cod_ejer = $ejercicio
						and saeasto.asto_cod_empr = $empresa
						group by 1,2
						order by 1";

            //$oReturn->alert($sql);
            if ($oIfx->Query($sql)) {
                if ($oIfx->NumFilas() > 0) {
                    unset($arrayRetenciones);
                    do {
                        $arrayRetenciones[$oIfx->f('ret_cta_ret')] = array($oIfx->f('ret_valor'));
                    } while ($oIfx->SiguienteRegistro());
                    $oIfx->Free($arrayRetenciones);
                    //var_dump($arrayTipoReteciones); exit;
                    if (count($arrayTipoReteciones > 0)) {
                        $k = 18;
                        for ($j = 0; $j < count($arrayTipoReteciones); $j++) {
                            //$k = count($arrayTipoReteciones);
                            $r =  $k + $j;
                            $indice = $arrayTipoReteciones[$j][0];
                            //echo $indice; exit;
                            $valor = $arrayRetenciones[$indice][0];
                            $arrayCompras[$i][$r] = $valor;
                            $arrayTotales[$r] = $arrayTotales[$r] + $valor;
                        }
                    }
                }
            }
        }
        // ORDENAMINETO DEL ARREGLO POR EL INDICE
        // ksort($arrayTotales);
        //var_dump($arrayCompras); exit;
        //$oReturn->alert($sql);

        // GENERA TABLA DE COMPRAS - RETENCIONES
        if (count($arrayCompras) > 0) {
            $html .= '<tr>						
							<td class="bg-primary" align = "center"> Fecha </td>
							<td class="bg-primary" align = "center"> Serie </td>
							<td class="bg-primary" align = "center"> Documento </td>
							<td class="bg-primary" align = "center"> Comprobante</td>
							<td class="bg-primary" align = "center"> Doc. Tibutario </td>
							<td class="bg-primary" align = "center"> Sus. Tibutario </td>
							<td class="bg-primary" align = "center"> Proveedor	</td>
							<td class="bg-primary" align = "center"> Detalle </td>
							<td class="bg-primary" align = "center"> Con impuesto </td>
							<td class="bg-primary" align = "center"> Sin impuesto</td>
							<td class="bg-primary" align = "center"> Impuesto </td>
							<td class="bg-primary" align = "center"> Total </td>
							<td class="bg-primary" align = "center"> No Objeto Impuesto </td>
							<td class="bg-primary" align = "center"> Exento de Impuesto </td>';
            if ($detallado == 'S') {
                $html .= '<td class="bg-primary" align = "center"> Reteci&oacuten </td>
							<td class="bg-primary" align = "center"> Base Imponible Renta </td>
							<td class="bg-primary" align = "center"> Base Imponible Impuesto </td>';
                foreach ($arrayTipoReteciones as $arrayCuetas) {
                    $html .= '<td class="bg-primary" align = "center">' . $arrayCuetas[0] . ' </td>';
                }
            }
            $html .= '</tr></thead>';

            // INICIALIZAR TOTALES
            $sumaIva12     = 0;
            $sumaIva0      = 0;
            $sumaIva       = 0;
            $sumaTotal     = 0;
            $sumaBaseRenta = 0;
            $sumaBaseIva   = 0;
            $numero_compras_foreach = 0;
            $html .= '<tbody>';

            foreach ($arrayCompras as $compras) {
                //var_dump($compras);exit;
                $sumaIva12     = $sumaIva12     + $compras[7];
                $sumaIva0      = $sumaIva0      + $compras[8];
                $sumaIva       = $sumaIva       + $compras[9];
                $sumaTotal        = $sumaTotal     + $compras[10];
                $sumaNoOjeto   = $sumaNoOjeto   + $compras[11];
                $sumaExento    = $sumaExento    + $compras[12];
                $sumaBaseRenta = $sumaBaseRenta + $compras[14];
                $sumaBaseIva   = $sumaBaseIva   + $compras[15];
                $sustento      = $compras[16];
                $tansaccion    = $compras[17];
                $sumaConSIn  = $compras[7]   + $compras[8];
                $class = '';
                //ECHO $tansaccion;EXIT;
                if ($tansaccion == '04') {

                    $class = "bg-success";
                }
                if ($tansaccion == '03') {

                    $class = "bg-warning";
                }
                $contador = count($compras);
                // $contador=$contador-2;
                if ($detallado == 'S') {

                    for ($i = 0; $i < $contador; $i++) {

                        /*
                        if ($numero_compras_foreach == 1){
                            var_dump($compras);
                             exit;
                        }
                        */

                        if ($i == 0) {
                            $html .= '<tr class="' . $class . '">';
                        }
                        $color = 'black';
                        if ($i > 13) {
                            if ($compras[$i] == null) {
                                $compras[$i] = '0.00';
                                $color = 'black';
                            } else {
                                if ($i == 13 || $i == 14) {
                                    $color = 'black';
                                } else {
                                    $color = 'blue';
                                }
                            }
                            if ($i == 16 || $i == 17) {
                            } elseif ($i == 14) {
                                $html .= '<td style = "color:' . $color . '" align = "right">' . $sumaConSIn . '</td>';
                            } else {
                                $html .= '<td style = "color:' . $color . '" align = "right">' . $compras[$i] . ' </td>';
                            }
                        } else {
                            if ($i == 5) {
                                $html .= '<td style = "color:' . $color . '">' . $sustento . '</td>';
                            }
                            if ($i > 6) {
                                $html .= '<td style = "color:' . $color . '" align = "right">' . $compras[$i] . ' </td>';
                            } else {
                                if ($i == 3) {
                                    $html .= '<td> <a href="#" onclick="seleccionaItem(' . $empresa . ', ' . $idsucursal . ', ' . $ejercicio . ', ' . $periodo . ', \'' . $compras[$i] . '\');">' . $compras[$i] . '</a></td>';
                                } else {
                                    $html .= '<td style = "color:' . $color . '">' . $compras[$i] . ' </td>';
                                }
                            }
                        }

                        if ($i == $contador) {
                            $html .= '</tr>';
                        }
                    }
                } else {
                    for ($i = 0; $i < $contador; $i++) {
                        if ($i == 0) {
                            $html .= '<tr class="' . $class . '">';
                        }
                        $color = 'black';
                        if ($i < 13) {
                            if ($i == 5) {
                                $html .= '<td style = "color:' . $color . '" >' . $sustento . '</td>';
                            }
                            if ($i > 6) {
                                $html .= '<td style = "color:' . $color . '" align = "right">' . number_format($compras[$i], 2, '.', ',') . ' </td>';
                            } else {
                                if ($i == 3) {
                                    $html .= '<td> <a href="#" onclick="seleccionaItem(' . $empresa . ', ' . $idsucursal . ', ' . $ejercicio . ', ' . $periodo . ', \'' . $compras[$i] . '\');">' . $compras[$i] . '</a></td>';
                                } else {
                                    $html .= '<td style = "color:' . $color . '">' . $compras[$i] . ' </td>';
                                }
                            }
                            if ($i == $contador) {
                                $html .= '</tr>';
                            }
                        }
                    }
                }

                $numero_compras_foreach++;
            }

            $html .= '</tbody>';

            // TOTALES RETENCIONES	number_format($compras[$i],2,'.',',')
            $html .= '<tbody>';
            $html .= '<tr>';

            $html .= '<td align = "right" colspan="8" style = "color:red" bgcolor = "#CCCCCC"> TOTALES: </td>';
            $html .= '<td align = "right" bgcolor = "#CCCCCC" style = "color:red">' . number_format($sumaIva12, 2, '.', ',') . ' </td>';
            $html .= '<td align = "right" bgcolor = "#CCCCCC" style = "color:red">' . number_format($sumaIva0, 2, '.', ',') . ' </td>';
            $html .= '<td align = "right" bgcolor = "#CCCCCC" style = "color:red">' . number_format($sumaIva, 2, '.', ',') . ' </td>';
            $html .= '<td align = "right" bgcolor = "#CCCCCC" style = "color:red">' . number_format($sumaTotal, 2, '.', ',') . ' </td>';
            $html .= '<td align = "right" bgcolor = "#CCCCCC" style = "color:red">' . number_format($sumaNoOjeto, 2, '.', ',') . ' </td>';
            $html .= '<td align = "right" bgcolor = "#CCCCCC" style = "color:red">' . number_format($sumaExento, 2, '.', ',') . ' </td>';
            if ($detallado == 'S') {
                $html .= '<td align = "right" bgcolor = "#CCCCCC" style = "color:red"> </td>';
                $html .= '<td align = "right" bgcolor = "#CCCCCC" style = "color:red">' . number_format($sumaBaseRenta, 2, '.', ',') . ' </td>';
                $html .= '<td align = "right" bgcolor = "#CCCCCC" style = "color:red">' . number_format($sumaBaseIva, 2, '.', ',') . ' </td>';
                for ($f = 18; $f < count($arrayTotales) + 18; $f++) {

                    $html .= '<td style = "color:red" bgcolor = "#CCCCCC">' . number_format($arrayTotales[$f], 2, '.', ',') . '</td>';
                }
            }
            $html .= '</tr>';
            $html .= '</tbody>';


            // NOTA DE CREDITO DE INVENTARIO
            $sql = "select  t.tloc_cod_crtr, c.clv_con_clpv,   c.clpv_cod_clpv, nc.ncnd_num_docu,   c.clpv_ruc_clpv,
				c.clpv_cod_tprov, c.clpv_par_rela, nc.ncnd_cod_tcmp,   nc.ncnd_fec_emis,
				( substring(  nc.ncnd_nsr_comp   from 1 for 3 ) ) AS estab,
				( substring(  nc.ncnd_nsr_comp   from 4 for 6 ) ) AS ptoemi,
				nc.ncnd_nse_comp ,  t.tloc_nau_comp,
				round((coalesce(t.tloc_bim_ta0b,0)),2)  as base_imponible,
				round((coalesce(t.tloc_bim_tar0,0)),2) as base_imponible1,
				round((coalesce(t.tloc_bas_imgr,0)),2)  as  baseimprgrav,
				round((coalesce(t.tloc_val_mice,0)),2) as montoice,
				round((coalesce(t.tloc_val_miva,0)),2) as montoiva,
				c.clpv_cod_tpago,
				nc.ncnd_cod_strs,
				( substring(  nc.ncnd_num_srcm   from 1 for 3 ) ) AS estab_modi,
				( substring(  nc.ncnd_num_srcm   from 4 for 6 ) ) AS ptoemi_modi,
				nc.ncnd_num_sccm  as secu_modi,
				nc.ncnd_num_aucm   as auto_modi,
				nc.ncnd_cod_tcmm, t.tloc_nom_raso,
				t.tloc_cod_asto
				from saencnd  nc , saeclpv c , saetloc t where
				c.clpv_ruc_clpv = nc.ncnd_num_docu and
				nc.ncnd_nse_comp = t.tloc_nse_comp and
				c.clpv_ruc_clpv = t.tloc_num_docu and

				SUBSTR(cast (nc.ncnd_fec_emis as text), 0, 5) = '$anio' and
				SUBSTR(cast (nc.ncnd_fec_emis as text), 6, 2) = '$mes' and
				nc.ncnd_ruc_info = (    select empr_ruc_empr from saeempr where
										empr_cod_empr = $empresa ) and
				c.clpv_cod_empr = $empresa and
				c.clpv_clopv_clpv = 'PV' and
				nc.ncnd_cod_tcmp in ( '04' , '05' ) 
				order by nc.ncnd_fec_emis";
            //$oReturn->alert($sql);


            if ($oIfx->Query($sql)) {
                if ($oIfx->NumFilas()) {
                    do {
                        $cod_sustento         = $oIfx->f('tloc_cod_crtr');
                        $tpIdProv             = $oIfx->f('clv_con_clpv');
                        $IdProv             = $oIfx->f('clpv_ruc_clpv');
                        $tipo_comprobante     = $oIfx->f('ncnd_cod_tcmp');
                        $fecha_registro     = $oIfx->f('ncnd_fec_emis');
                        $estab                 = $oIfx->f('estab');
                        $ptoemi             = $oIfx->f('ptoemi');
                        $secuencial         = $oIfx->f('ncnd_nse_comp');
                        $fecha_emision         = $oIfx->f('ncnd_fec_emis');
                        $autorizacion         = $oIfx->f('tloc_nau_comp');
                        $basenograiva         = number_format(0.00, 2, '.', '');
                        $baseimponible         = number_format($oIfx->f('base_imponible'), 2, '.', '');
                        $baseimponible1     = number_format($oIfx->f('base_imponible1'), 2, '.', '');
                        $baseimpgrav         = number_format($oIfx->f('baseimprgrav'), 2, '.', '');
                        $montoice             = number_format($oIfx->f('montoice'), 2, '.', '');
                        $montoiva             = number_format($oIfx->f('montoiva'), 2, '.', '');
                        $valRetBien10         = number_format(0.00, 2, '.', '');
                        $valRetServ20         = number_format(0.00, 2, '.', '');
                        $valoretbienes         = number_format(0.00, 2, '.', '');
                        $valoretservicios     = number_format(0.00, 2, '.', '');
                        $valoretserv100     = number_format(0.00, 2, '.', '');
                        $cod_tpago             = $oIfx->f('clpv_cod_tpago');
                        $estab_modi         = $oIfx->f('estab_modi');
                        $ptoemi_modi         = $oIfx->f('ptoemi_modi');
                        $secu_modi             = $oIfx->f('secu_modi');
                        $auto_modi             = $oIfx->f('auto_modi');
                        $doc_modi             = $oIfx->f('ncnd_cod_tcmm');
                        $tloc_nom_raso         = $oIfx->f('tloc_nom_raso');
                        $clpv_cod_tprov     = $oIfx->f('clpv_cod_tprov');
                        $clpv_par_rela         = $oIfx->f('clpv_par_rela');
                        $tloc_cod_asto         = $oIfx->f('tloc_cod_asto');
                        $arreglo_secu_modi = explode("-", $secu_modi);
                        if ($arreglo_secu_modi[1] != '') {
                            $estab_modi = substr($arreglo_secu_modi[0], 0, 3);
                            $ptoemi_modi = substr($arreglo_secu_modi[0], 3, 6);
                            $secu_modi = $arreglo_secu_modi[1];
                        }

                        if ($clpv_par_rela == 'S') {
                            $clpv_par_rela = 'SI';
                        } else {
                            $clpv_par_rela = 'NO';
                        }

                        if ($sClass == 'off')
                            $sClass = 'on';
                        else
                            $sClass = 'off';

                        $tmp_nombre = '';
                        if ($tipo_comprobante == '04') {
                            // $oReturn->alert($tipo_comprobante);
                            $tmp_nombre = 'NOTA CREDITO';
                            // totales
                            $basenograiva_tot -= $basenograiva;
                            $baseimponible_tot -= $baseimponible1;
                            $baseimpgrav_tot -= $baseimpgrav;
                            $montoice_tot -= $montoice;
                            $montoiva_tot -= $montoiva;

                            // totales
                            $valRetBien10_tot -= $valRetBien10;
                            $valRetServ20_tot -= $valRetServ20;
                            $valoretbienes_tot -= $valoretbienes;
                            $valoretservicios_tot -= $valoretservicios;
                            $valoretserv100_tot -= $valoretserv100;
                        } elseif ($tipo_comprobante == '05') {
                            $tmp_nombre = 'NOTA DEBITO';
                            $basenograiva_tot += $basenograiva;
                            $baseimponible_tot +=  $baseimponible1;
                            $baseimpgrav_tot +=  $baseimpgrav;
                            $montoice_tot +=  $montoice;
                            $montoiva_tot +=  $montoiva;

                            // totales
                            $valRetBien10_tot +=  $valRetBien10;
                            $valRetServ20_tot +=  $valRetServ20;
                            $valoretbienes_tot +=  $valoretbienes;
                            $valoretservicios_tot +=  $valoretservicios;
                            $valoretserv100_tot += $valoretserv100;
                        }

                        $reporte_xml .= '<tr height="20" class="' . $sClass . '"
                                                onMouseOver="javascript:this.className=\'link\';"
                                                onMouseOut="javascript:this.className=\'' . $sClass . '\';">';
                        $reporte_xml .= '<td align="right">' . $i . '</td>';
                        $reporte_xml .= '<td align="left">' . $tmp_nombre . '</td>';
                        $reporte_xml .= '<td align="left"></td>';
                        $reporte_xml .= '<td align="right">' . $cod_sustento . '</td>';
                        $reporte_xml .= '<td align="right">' . $tpIdProv . '</td>';
                        $reporte_xml .= '<td align="left">' . $IdProv . '</td>';
                        $reporte_xml .= '<td align="left">' . $tloc_nom_raso . '</td>';
                        $reporte_xml .= '<td align="right">' . $tipo_comprobante . '</td>';
                        $reporte_xml .= '<td align="right">' . $clpv_cod_tprov . '</td>';
                        $reporte_xml .= '<td align="right">' . $clpv_par_rela . '</td>';
                        $reporte_xml .= '<td align="right">' . $fecha_registro . '</td>';
                        $reporte_xml .= '<td align="right">' . $estab . '</td>';
                        $reporte_xml .= '<td>' . $ptoemi . '</td>';
                        $reporte_xml .= '<td align="right">' . $secuencial . '</td>';
                        $reporte_xml .= '<td align="right">' . $fecha_emision . '</td>';
                        $reporte_xml .= '<td align="right">' . $autorizacion . '</td>';
                        $reporte_xml .= '<td align="right">' . $tloc_cod_asto . '</td>';
                        $reporte_xml .= '<td align="right">' . $basenograiva . '</td>';
                        $reporte_xml .= '<td align="right">' . $baseimponible1 . '</td>';
                        $reporte_xml .= '<td align="right">' . $baseimpgrav . '</td>';
                        $reporte_xml .= '<td align="right">' . $montoice . '</td>';
                        $reporte_xml .= '<td align="right">' . $montoiva . '</td>';
                        $reporte_xml .= '<td align="right">' . $valRetBien10 . '</td>';
                        $reporte_xml .= '<td align="right">' . $valRetServ20 . '</td>';
                        $reporte_xml .= '<td align="right">' . $valoretbienes . '</td>';
                        $reporte_xml .= '<td align="right">' . $valoretservicios . '</td>';
                        $reporte_xml .= '<td align="right">' . $valoretserv100 . '</td>';
                        $reporte_xml .= '<td align="right">' . $cod_tpago . '</td>';
                        $reporte_xml .= '<td align="right">NA</td>';
                        $reporte_xml .= '<td align="right">NA</td>';
                        $reporte_xml .= '<td align="right">NA</td>';
                        $reporte_xml .= '<td align="right"></td>';
                        $reporte_xml .= '<td align="right"></td>';
                        $reporte_xml .= '<td align="right"></td>';
                        $reporte_xml .= '<td align="right"></td>';
                        $reporte_xml .= '<td align="right"></td>';
                        $reporte_xml .= '<td align="right"></td>';
                        $reporte_xml .= '<td align="right"></td>';
                        $reporte_xml .= '<td align="right"></td>';
                        $reporte_xml .= '<td align="right"></td>';
                        $reporte_xml .= '<td align="right">' . $doc_modi . '</td>';
                        $reporte_xml .= '<td align="right">' . $estab_modi . '</td>';
                        $reporte_xml .= '<td align="right">' . $ptoemi_modi . '</td>';
                        $reporte_xml .= '<td align="right">' . $secu_modi . '</td>';
                        $reporte_xml .= '<td align="right">' . $auto_modi . '</td>';
                        $reporte_xml .= '</tr>';
                        $i++;
                    } while ($oIfx->SiguienteRegistro());
                    $oReturn->alert('Buscando ...');
                } else {
                    $oReturn->alert('No existen datos de esta sucursal para este ejercicio ni periodo');
                }
            }

            $html .= '<tfoot>';
            $html .= '<tr>						
							<td class="bg-primary" align = "center"> Fecha </td>
							<td class="bg-primary" align = "center"> Serie </td>
							<td class="bg-primary" align = "center"> Documento </td>
							<td class="bg-primary" align = "center"> Comprobante</td>
							<td class="bg-primary" align = "center"> Doc. Tibutario </td>
							<td class="bg-primary" align = "center"> Sus. Tibutario </td>
							<td class="bg-primary" align = "center"> Proveedor	</td>
							<td class="bg-primary" align = "center"> Detalle </td>
							<td class="bg-primary" align = "center"> Con impuesto </td>
							<td class="bg-primary" align = "center"> Sin impuesto</td>
							<td class="bg-primary" align = "center"> Impuesto </td>
							<td class="bg-primary" align = "center"> Total </td>
							<td class="bg-primary" align = "center"> No Objeto Impuesto </td>
							<td class="bg-primary" align = "center"> Exento de Impuesto </td>';
            if ($detallado == 'S') {
                $html .= '<td class="bg-primary" align = "center"> Reteci&oacuten </td>
							<td class="bg-primary" align = "center"> Base Imponible Renta </td>
							<td class="bg-primary" align = "center"> Base Imponible Impuesto </td>';
                foreach ($arrayTipoReteciones as $arrayCuetas) {
                    $html .= '<td class="bg-primary" align = "center">' . $arrayCuetas[0] . ' </td>';
                }
            }

            // $html=$reporte_xml;
            // $html=$reporte_xml;
            $html .= '</tr> ';
            $html .= '</tfoot>';





            $html .= '</table>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<h2></h2>';
            $_SESSION['ACT_REPORTE'] = $html;
        }
        $oReturn->assign("divFormularioDetalleC", "innerHTML", $html);


        //$oReturn->script("recarga();");
        $oIfx->QueryT('COMMIT WORK;');
    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }




    // Lista de compras
    // $oReturn->assign("divFormularioDetalleC", "innerHTML", $sHtml_table_compras);
    $oReturn->script("init()");

    return $oReturn;
}




function verDiarioContable($aForm = '', $empr = 0, $sucu = 0, $ejer = 0, $mes = 0, $asto = '')
{

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN_Ifx, $DSN;

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oCon = new Dbo;
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oReturn = new xajaxResponse();

    //variables del formulario
    $empresa = $aForm['empresa'];
    $anio = $aForm['anio'];
    $mes_1 = $aForm['mes_1'];
    $mes_2 = $aForm['mes_2'];
    $nivel = $aForm['nivel'];
    $campo = 0;

    $class = new GeneraDetalleAsientoContable();

    $arrayAsto = $class->informacionAsientoContable($oIfx, $empr, $sucu, $ejer, $mes, $asto);

    $arrayDiario = $class->diarioAsientoContable($oIfx, $empr, $sucu, $ejer, $mes, $asto);

    $arrayDirectorio = $class->directorioAsientoContable($oIfx, $empr, $sucu, $ejer, $mes, $asto);

    $arrayRetencion = $class->retencionAsientoContable($oIfx, $empr, $sucu, $ejer, $mes, $asto);

    $arrayAdjuntos = $class->adjuntosAsientoContable($oCon, $empr, $sucu, $ejer, $mes, $asto);

    try {

        //LECTURA SUCIA1
        // 


        //sucursal
        $sql = "select sucu_nom_sucu from saesucu where sucu_cod_sucu = $sucu";
        $sucu_nom_sucu = consulta_string_func($sql, 'sucu_nom_sucu', $oIfx, '');


        $oReturn->assign("divTituloAsto", "innerHTML", $asto . ' - ' . $sucu_nom_sucu);

        if (count($arrayAsto) > 0) {

            $table .= '<table class="table table-striped table-condensed" align="center" width="98%">';
            $table .= '<tr>';
            $table .= '<td colspan="4" class="bg-primary">DIARIO CONTABLE</td>';
            $table .= '</tr>';

            foreach ($arrayAsto as $val) {
                $asto_cod_asto = $val[0];
                $asto_vat_asto = $val[1];
                $asto_ben_asto = $val[2];
                $asto_fec_asto = $val[3];
                $asto_det_asto = $val[4];
                $asto_cod_modu = $val[5];
                $asto_usu_asto = $val[6];
                $asto_user_web = $val[7];
                $asto_fec_serv = $val[8];
                $asto_cod_tidu = $val[9];

                //modulo
                $sql = "select modu_des_modu from saemodu where modu_cod_modu = $asto_cod_modu";
                $modu_des_modu = consulta_string_func($sql, 'modu_des_modu', $oIfx, '');

                //tipo documento
                $sql = "select tidu_des_tidu from saetidu where tidu_cod_tidu = '$asto_cod_tidu'";
                $tidu_des_tidu = consulta_string_func($sql, 'tidu_des_tidu', $oIfx, '');

                $table .= '<tr>';
                $table .= '<td>Diario:</td>';
                $table .= '<td>' . $asto_cod_asto . '</td>';
                $table .= '<td>Fecha:</td>';
                $table .= '<td>' . $asto_fec_asto . '</td>';
                $table .= '</tr>';

                $table .= '<tr>';
                $table .= '<td>Beneficiario:</td>';
                $table .= '<td colspan="3">' . $asto_ben_asto . '</td>';
                $table .= '</tr>';

                $table .= '<tr>';
                $table .= '<td>Modulo:</td>';
                $table .= '<td>' . $modu_des_modu . '</td>';
                $table .= '<td>Documento:</td>';
                $table .= '<td>' . $asto_cod_tidu . ' - ' . $tidu_des_tidu . '</td>';
                $table .= '</tr>';

                $table .= '<tr>';
                $table .= '<td>Detalle:</td>';
                $table .= '<td colspan="3">' . $asto_det_asto . '</td>';
                $table .= '</tr>';
                //sucursal, cod_prove, asto_cod, ejer_cod, prdo_cod
                $table .= '<tr>';
                $table .= '<td>Formato:</td>';
                $table .= '<td align="left">
							<div class="btn btn-primary btn-sm" onclick="vista_previa_diario(' . $empresa . ',' . $sucu . ', 0, \'' . $asto . '\', ' . $ejer . ', ' . $mes . ');">
								<span class="glyphicon glyphicon-print"></span>
							</div>
						</td>';
                $table .= '<td>Valor:</td>';
                $table .= '<td class="bg-danger fecha_letra" align="left">' . number_format($asto_vat_asto, 2, '.', ',') . '</td>';
                $table .= '</tr>';
            } //fin foreach

            $table .= '</table>';

            $oReturn->assign("divInfo", "innerHTML", $table);
        }

        //directorio
        if (count($arrayDiario) > 0) {

            $tableDia .= '<table class="table table-striped table-condensed table-bordered table-hover" align="center" width="98%">';
            $tableDia .= '<tr>';
            $tableDia .= '<td colspan="5" class="bg-primary">DIARIO</td>
						<td align="center">
							<div class="btn btn-primary btn-sm" onclick="vista_previa_diario(' . $empresa . ',' . $sucu . ', 0, \'' . $asto . '\', ' . $ejer . ', ' . $mes . ');">
								<span class="glyphicon glyphicon-print"></span>
							</div>
						</td>';
            $tableDia .= '</tr>';
            $tableDia .= '<tr>';
            $tableDia .= '<td>Cuenta Contable</td>';
            $tableDia .= '<td>Centro Costos</td>';
            $tableDia .= '<td>Centro Actividad</td>';
            $tableDia .= '<td>Documento</td>';
            $tableDia .= '<td>Debito</td>';
            $tableDia .= '<td>Credito</td>';
            $tableDia .= '</tr>';
            $totalDeb = 0;
            $totalCre = 0;
            foreach ($arrayDiario as $val) {
                $dasi_cod_cuen = $val[0];
                $dasi_cod_cact = $val[1];
                $ccos_cod_ccos = $val[2];
                $dasi_dml_dasi = $val[3];
                $dasi_cml_dasi = $val[4];
                $dasi_det_asi = $val[5];
                $dasi_num_depo = $val[6];

                //clpv
                $cuen_nom_cuen = '';
                if (!empty($dasi_cod_cuen)) {
                    $sql = "select cuen_nom_cuen from saecuen where cuen_cod_cuen = '$dasi_cod_cuen' and cuen_cod_empr = $empr";
                    $cuen_nom_cuen = consulta_string_func($sql, 'cuen_nom_cuen', $oIfx, '');
                }

                $ccosn_nom_ccosn = '';
                if (!empty($ccos_cod_ccos)) {
                    $sql = "select ccosn_nom_ccosn from saeccosn where ccosn_cod_ccosn = '$ccos_cod_ccos' and ccosn_cod_empr = $empr";
                    $ccosn_nom_ccosn = consulta_string_func($sql, 'ccosn_nom_ccosn', $oIfx, '');
                }

                $cact_nom_cact = '';
                if (!empty($dasi_cod_cact)) {
                    $sql = "select cact_nom_cact from saecact where cact_cod_cact = '$dasi_cod_cact' and cact_cod_empr = $empr";
                    $cact_nom_cact = consulta_string_func($sql, 'cact_nom_cact', $oIfx, '');
                }

                $tableDia .= '<tr>';
                $tableDia .= '<td>' . $dasi_cod_cuen . ' - ' . $cuen_nom_cuen . '</td>';
                $tableDia .= '<td>' . $ccos_cod_ccos . ' - ' . $ccosn_nom_ccosn . '</td>';
                $tableDia .= '<td>' . $dasi_cod_cact . ' - ' . $cact_nom_cact . '</td>';
                $tableDia .= '<td>' . $dasi_num_depo . '</td>';
                $tableDia .= '<td align="right">' . number_format($dasi_dml_dasi, 2, '.', ',') . '</td>';
                $tableDia .= '<td align="right">' . number_format($dasi_cml_dasi, 2, '.', ',') . '</td>';
                $tableDia .= '</tr>';

                $totalDeb += $dasi_dml_dasi;
                $totalCre += $dasi_cml_dasi;
            } //fin foreach
            $tableDia .= '<tr>';
            $tableDia .= '<td align="right" class="bg-danger fecha_letra" colspan="4">TOTAL:</td>';
            $tableDia .= '<td align="right" class="bg-danger fecha_letra">' . number_format($totalDeb, 2, '.', ',') . '</td>';
            $tableDia .= '<td align="right" class="bg-danger fecha_letra">' . number_format($totalCre, 2, '.', ',') . '</td>';
            $tableDia .= '</tr>';
            $tableDia .= '</table>';

            $oReturn->assign("divDiario", "innerHTML", $tableDia);
        }

        //directorio
        if (count($arrayDirectorio) > 0) {

            $tableDir .= '<table class="table table-striped table-condensed table-bordered table-hover" align="center" width="98%">';
            $tableDir .= '<tr>';
            $tableDir .= '<td colspan="6" class="bg-primary">DIRECTORIO</td>';
            $tableDir .= '</tr>';
            $tableDir .= '<tr>';
            $tableDir .= '<td>No.</td>';
            $tableDir .= '<td>Cliente/Proveedor</td>';
            $tableDir .= '<td>Transaccion</td>';
            $tableDir .= '<td>Factura</td>';
            $tableDir .= '<td>Credito</td>';
            $tableDir .= '<td>Debito</td>';
            $tableDir .= '</tr>';
            $totalDeb = 0;
            $totalCre = 0;
            foreach ($arrayDirectorio as $val) {
                $dir_cod_dir = $val[0];
                $dir_cod_cli = $val[1];
                $tran_cod_modu = $val[2];
                $dir_cod_tran = $val[3];
                $dir_num_fact = $val[4];
                $dir_detalle = $val[5];
                $dir_fec_venc = $val[6];
                $dir_deb_ml = $val[7];
                $dir_cre_ml = $val[8];

                //clpv
                $clpv_nom_clpv = '';
                if (!empty($dir_cod_cli)) {
                    $sql = "select clpv_nom_clpv from saeclpv where clpv_cod_clpv = $dir_cod_cli";
                    $clpv_nom_clpv = consulta_string_func($sql, 'clpv_nom_clpv', $oIfx, '');
                }

                $tableDir .= '<tr>';
                $tableDir .= '<td>' . $dir_cod_dir . '</td>';
                $tableDir .= '<td>' . $clpv_nom_clpv . '</td>';
                $tableDir .= '<td>' . $dir_cod_tran . '</td>';
                $tableDir .= '<td>' . $dir_num_fact . '</td>';
                $tableDir .= '<td align="right">' . number_format($dir_cre_ml, 2, '.', ',') . '</td>';
                $tableDir .= '<td align="right">' . number_format($dir_deb_ml, 2, '.', ',') . '</td>';
                $tableDir .= '</tr>';

                $totalCre += $dir_cre_ml;
                $totalDeb += $dir_deb_ml;
            } //fin foreach
            $tableDir .= '<tr>';
            $tableDir .= '<td align="right" class="bg-danger fecha_letra" colspan="4">TOTAL:</td>';
            $tableDir .= '<td align="right" class="bg-danger fecha_letra">' . number_format($totalCre, 2, '.', ',') . '</td>';
            $tableDir .= '<td align="right" class="bg-danger fecha_letra">' . number_format($totalDeb, 2, '.', ',') . '</td>';
            $tableDir .= '</tr>';
            $tableDir .= '</table>';

            $oReturn->assign("divDirectorio", "innerHTML", $tableDir);
        }

        //retencion
        if (count($arrayRetencion) > 0) {

            $tableRet .= '<table class="table table-striped table-condensed table-bordered table-hover" align="center" width="98%">';
            $tableRet .= '<tr>';
            $tableRet .= '<td colspan="8" class="bg-primary">RETENCION</td>';
            $tableRet .= '</tr>';
            $tableRet .= '<tr>';
            $tableRet .= '<td>Cliente/Proveedor</td>';
            $tableRet .= '<td>Factura</td>';
            $tableRet .= '<td>Retencion</td>';
            $tableRet .= '<td>Codigo</td>';
            $tableRet .= '<td>Porcentaje</td>';
            $tableRet .= '<td>Base Imp.</td>';
            $tableRet .= '<td>Valor</td>';
            $tableRet .= '<td>Print</td>';
            $tableRet .= '</tr>';
            foreach ($arrayRetencion as $val) {
                $ret_cta_ret = $val[0];
                $ret_porc_ret = $val[1];
                $ret_bas_imp = $val[2];
                $ret_valor = $val[3];
                $ret_num_ret = $val[4];
                $ret_detalle = $val[5];
                $ret_num_fact = $val[6];
                $ret_ser_ret = $val[7];
                $ret_cod_clpv = $val[8];
                $ret_fec_ret = $val[9];

                //clpv
                $clpv_nom_clpv = '';
                if (!empty($ret_cod_clpv)) {
                    $sql = "select clpv_nom_clpv from saeclpv where clpv_cod_clpv = $ret_cod_clpv";
                    $clpv_nom_clpv = consulta_string_func($sql, 'clpv_nom_clpv', $oIfx, '');
                }

                //fprv
                $printRet = '';
                if ($asto_cod_modu == 4 || $asto_cod_modu == 6) {

                    //fecha fprv o minv
                    if ($asto_cod_modu == 4) {
                        $sql = "select fprv_fec_emis 
								from saefprv
								where fprv_cod_clpv = $ret_cod_clpv and
								fprv_num_fact = '$ret_num_fact' and
								fprv_cod_asto = '$asto' and
								fprv_cod_ejer = $ejer and
								fprv_cod_empr = $empr and
								fprv_cod_sucu = $sucu";
                        $fechaEmis = consulta_string_func($sql, 'fprv_fec_emis', $oIfx, '');
                    } elseif ($asto_cod_modu == 6) {
                        $sql = "select minv_fmov 
								from saeminv
								where minv_cod_clpv = $ret_cod_clpv and
								minv_fac_prov = '$ret_num_fact' and
								minv_comp_cont = '$asto' and
								minv_cod_ejer = $ejer and
								minv_cod_empr = $empr and
								minv_cod_sucu = $sucu";
                        $fechaEmis = consulta_string_func($sql, 'minv_fmov', $oIfx, '');
                    }

                    $printRet = '<div class="btn btn-primary btn-sm" onclick="genera_documento(5, \'' . $campo . '\',\'' . $fprv_clav_sri . '\' ,
																				 \'' . $ret_cod_clpv . '\'  , \'' . $ret_num_fact . '\', \'' . $ejer . '\',
																				 \'' . $asto . '\',  \'' . $fechaEmis . '\', ' . $sucu . ');">
									<span class="glyphicon glyphicon-print"></span>
								</div>';
                }

                $tableRet .= '<tr>';
                $tableRet .= '<td>' . $clpv_nom_clpv . '</td>';
                $tableRet .= '<td>' . $ret_num_fact . '</td>';
                $tableRet .= '<td>' . $ret_ser_ret . ' - ' . $ret_num_ret . '</td>';
                $tableRet .= '<td>' . $ret_cta_ret . '</td>';
                $tableRet .= '<td align="right">' . $ret_porc_ret . '</td>';
                $tableRet .= '<td align="right">' . number_format($ret_bas_imp, 2, '.', ',') . '</td>';
                $tableRet .= '<td align="right">' . number_format($ret_valor, 2, '.', ',') . '</td>';
                $tableRet .= '<td align="center">' . $printRet . '</td>';
                $tableRet .= '</tr>';
            } //fin foreach

            $tableRet .= '</table>';

            $oReturn->assign("divRetencion", "innerHTML", $tableRet);
        }

        //adjuntos
        if (count($arrayAdjuntos) > 0) {

            $tableAdj .= '<table class="table table-striped table-condensed table-bordered table-hover" align="center" width="98%">';
            $tableAdj .= '<tr>';
            $tableAdj .= '<td colspan="2" class="bg-primary">ARCHIVOS ADJUNTOS</td>';
            $tableAdj .= '</tr>';
            $tableAdj .= '<tr>';
            $tableAdj .= '<td>Titulo</td>';
            $tableAdj .= '<td>Ruta</td>';
            $tableAdj .= '</tr>';
            foreach ($arrayAdjuntos as $val) {
                $titulo = $val[0];
                $ruta = $val[1];

                $tableAdj .= '<tr>';
                $tableAdj .= '<td>' . $titulo . '</td>';
                $tableAdj .= '<td><a href="#" onclick="dowloand(\'' . $ruta . '\')">' . $ruta . '</a></td>';
                $tableAdj .= '</tr>';
            } //fin foreach

            $tableAdj .= '</table>';

            $oReturn->assign("divAdjuntos", "innerHTML", $tableAdj);
        }
    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

    return $oReturn;
}




function genera_pdf_doc_compras($idempresa, $idsucursal, $asto_cod, $ejer_cod, $prdo_cod)
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
    $usuario = $_SESSION['U_NOMBRECOMPLETO'];

    $diario = generar_diarios_ingresos_pdf($idempresa, $idsucursal, $asto_cod, $ejer_cod, $prdo_cod);
    $_SESSION['pdf'] = $diario;

    $oReturn->script('generar_pdf_compras()');
    return $oReturn;
}



function genera_documento($tipo_documento = 0, $id = '', $clavAcce = 'no_autorizado', $clpv = 0,  $num_fact = '',  $ejer = 0,  $asto = '',  $fec_emis = '', $sucu = 0)
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    global $DSN_Ifx;

    $oReturn = new xajaxResponse();

    switch ($tipo_documento) {
        case 1:
            $_SESSION['pdf'] = reporte_factura($id, $clavAcce, $sucu);
            break;
        case 2:
            $_SESSION['pdf'] = reporte_notaDebito($id, $clavAcce);
            break;
        case 3:
            $_SESSION['pdf'] = reporte_notaCredito($id, $clavAcce, $sucu);
            break;
        case 4:
            $_SESSION['pdf'] = reporte_guiaRemision($id, $clavAcce, $sucu);
            break;
        case 5:
            $id = $_SESSION['sqlId'][$id];
            $_SESSION['pdf'] = reporte_retencionGasto($id, $clavAcce, $rutapdf, $clpv,  $num_fact,  $ejer,  $asto,  $fec_emis, $sucu);
            break;
        case 6:
            $id = $_SESSION['sqlId'][$id];
            $_SESSION['pdf'] = reporte_retencionInve($id, $clavAcce,  $rutapdf, $clpv,  $num_fact,  $ejer,  $asto,  $fec_emis, $sucu);
            break;
        case 7:
            $_SESSION['pdf'] = reporte_factura_export($id, $clavAcce);
            break;
        case 8:
            $_SESSION['pdf'] = reporte_factura_flor($id, $clavAcce);
            break;
        case 9:
            $_SESSION['pdf'] = reporte_factura_flor_export($id, $clavAcce);
            break;
        case 10:
            $_SESSION['pdf'] = reporte_guiaRemisionFlor($id, $clavAcce);
            break;
    }

    $oReturn->script('generar_pdf_compras()');

    return $oReturn;
}





function valida_existe_factura($aForm = '', $clave)
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


    $minv_ser_docu = substr($clave, 24, 6);
    $minv_fac_prov = substr($clave, 30, 9);

    $sql = "SELECT count(*) as cont from saeminv 
    where minv_ser_docu = '$minv_ser_docu' 
    and minv_fac_prov = '$minv_fac_prov' 
    and minv_cod_tran <> '031'
    and minv_est_minv  <> '0'
    ";

    $count_existe_factura = consulta_string_func($sql, 'cont', $oIfx, 0);
    $count_existe_factura = 0;
    if ($count_existe_factura > 0) {
        $oReturn->script('alert_existe_factura()');
    } else {
        $oReturn->script('alert_validacion_ride()');
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

    $oReturn = new xajaxResponse();
    //      VARIABLES
    $idempresa         = $_SESSION['U_EMPRESA'];
    $sucursal         = $aForm['sucursal'];
    $tipo_retencion = $aForm['tipo_retencion'];
    $aDataGrid         = $_SESSION['aDataGird_INV_MRECO'];
    $aDataGrid_FP     = $_SESSION['aDataGird_Pago'];
    $aDataGrid_Rete = $_SESSION['aDataGirdRete'];
    $contdata         = count($aDataGrid);
    $contdatafp     = count($aDataGrid_FP);
    $contdataret     = count($aDataGrid_Rete);
    $array_otros     = $_SESSION['U_OTROS'];

    $cliente         = $aForm['cliente'];
    $sql = "select clpv_ret_sn from saeclpv where
				clpv_cod_empr = $idempresa and
				clpv_cod_clpv = $cliente ";
    $clpv_ret_sn = consulta_string($sql, 'clpv_ret_sn', $oIfx, 'N');

    $inv_ctrl = 0;
    if ($clpv_ret_sn == 'S') {
        // APLICACION RETENCION
        if ($contdataret > 0) {
            $inv_ctrl = 1;
        }
    } else {
        // SIN RETENCION
        $inv_ctrl = 1;
        unset($_SESSION['aDataGirdRete']);
    }

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
            $fecha_ejer     = $anio . '-12-31';
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
									minv_fac_prov = '$factura' and
                                    minv_cod_tran <> '031'";
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

            if ($contador_ > 0 && $contador_1 > 0) {
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
                        // MONEDA LOCAL
                        $sql = "select tcam_val_tcam   from saetcam where
                                    mone_cod_empr = $idempresa and 
                                    tcam_cod_mone in ( select pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa  ) and
                                    tcam_fec_tcam in ( select  max(tcam_fec_tcam)   from saetcam where
                                                                mone_cod_empr = $idempresa and 
                                                                tcam_cod_mone in ( select pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa )
                                                    ) ";
                        $val_camb = consulta_string_func($sql, 'tcam_val_tcam', $oIfx, '0');
                        $coti_ext = $val_camb;
                    } else {
                        $coti_ext = $coti;
                    }


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


                    // SAEDASI PROVEEDOR COMPRA
                    // NOMBRE PROVEEDOR
                    $sql = "select  cuen_nom_cuen  from saecuen where
                                    cuen_cod_empr = $idempresa and
                                    cuen_cod_cuen = '$cuenta_prove' ";
                    $cuen_prove_nom = consulta_string($sql, 'cuen_nom_cuen', $oIfx, '');


                    $sql = "insert into saedasi    (  asto_cod_asto,        asto_cod_empr,      asto_cod_sucu,      dasi_num_prdo,
                                                      asto_cod_ejer,        dasi_cod_cuen,      dasi_dml_dasi,      dasi_cml_dasi,
                                                      dasi_dme_dasi,        dasi_cme_dasi,      dasi_tip_camb,      dasi_det_asi,
                                                      dasi_nom_ctac,        dasi_cod_clie,      dasi_cod_tran,      dasi_user_web )
                                            values  ( '$secu_asto',         $idempresa,         $sucursal,          $idprdo,
                                                       $idejer,            '$cuenta_prove',     0,                  $total_compra,
                                                       0,                   $total_compra_ext,  $coti,              '$detalle_asto' ,
                                                      '$cuen_prove_nom',    $cliente,           '$tran',            $usuario_web   ); ";
                    $oIfx->QueryT($sql);


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
                    $oIfx->Free();
                    $secu_minv = secuencial(2, '0', $secu_minv, 8);

                    $ret_electronica = $aForm['electronica'];

                    // FACTURA ADICIONAL
                    $dgui                = $aForm['dgui'];
                    $minv_fec_ncf        = $aForm['minv_fec_ncf'];
                    if (!empty($minv_fec_ncf)) {
                        $minv_fec_ncf  = fecha_informix_func($minv_fec_ncf);
                    }

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

                            $sql = "update saedmov set dmov_can_entr = dmov_can_dmov  where dmov_num_comp = $serial_oc and dmov_cod_empr = $idempresa and dmov_cod_sucu = $sucursal ";
                            $oIfx->QueryT($sql);
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
														minv_ruc_clpv,      minv_comp_ord)
                                                  values( ($minv_num_comp + 1),   1,             	'$secu_minv',       	$tcambio,
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
                                                          CURRENT_DATE,        	'$fpago_prove',     	'$tipo_pago',
                                                          $anio,           	$idprdo,            	$usuario_web,
                                                          '$secu_asto' ,  	'$secu_asto',        	$cliente,
                                                          '$minv_email_clpv', '$ret_electronica',   '$dgui' ,
														  $coti_ext,         '$msn_reco' ,			'$minv_nom_clpv',
														  '$minv_ruc_clpv'	, $serial_oc) ";
                    $oIfx->QueryT($sql_minv);

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
                            }
                        } // fin foreach
                    } // fin otros
                    //




                    //   DETALLE SAEDMOV
                    $x = 1;
                    $j = 0;
                    unset($arrray_dmov);


                    $inserta_prod_dmov = 'N';
                    foreach ($aDataGrid as $aValues) {

                        // -------------------------------------------------------------------------------------------------------------
                        // Recorremos la ordenes de compra que han sido seleccionadas para obtener los codigos unicos de cada sku
                        // -------------------------------------------------------------------------------------------------------------

                        $cod_prod_grid = $aValues['Codigo Item'];
                        $dmov_cod_dmov_prod_grid = $aValues['Dmov'];
                        $lote_prod_ad = $aValues['lote'];

                        if (empty($dmov_cod_dmov_prod_grid)) {
                            $dmov_cod_dmov_prod_grid = 0;
                        }



                        $sql_prod_data = "SELECT prod_nom_prod, prod_ser_prod, prod_aut_cons from saeprod where prod_cod_empr = $idempresa and prod_cod_sucu = $sucursal and prod_cod_prod = '$cod_prod_grid' ";
                        $prod_ser_prod   = '';
                        $prod_aut_cons   = '';
                        if ($oIfx->Query($sql_prod_data)) {
                            if ($oIfx->NumFilas() > 0) {
                                do {
                                    $prod_ser_prod = $oIfx->f('prod_ser_prod');
                                    $prod_aut_cons = $oIfx->f('prod_aut_cons');
                                } while ($oIfx->SiguienteRegistro());
                            }
                        }
                        $oIfx->Free();

                        $producto_config_serie_auto = 'N';
                        if ($prod_ser_prod == 'S' && $prod_aut_cons == 'S') {

                            //INGRESO ALMACEN
                            $array_oc = $_SESSION['U_PROF_APROB_RECO'];
                            $msn_reco = '';
                            if (count($array_oc) > 0) {
                                foreach ($array_oc as $val) {
                                    $clpv_cod_oc = $val[0];
                                    $serial_oc   = $val[2];

                                    $sql_data_series = "SELECT * from series_oc where minv_num_comp = $serial_oc and prod_cod_prod = '$cod_prod_grid' and dmov_cod_dmov = $dmov_cod_dmov_prod_grid";
                                    //$codigos_unicos_rec = consulta_string($sql_data_series, 'codigos_unicos_rec', $oIfx, '');
                                    $codigos_unicos_rec = '';

                                    if ($oIfx->Query($sql_data_series)) {
                                        if ($oIfx->NumFilas() > 0) {
                                            do {
                                                $codigos_unicos_rec = $oIfx->f('codigos_unicos_rec');
                                                $codigos_unicos_tot = $oIfx->f('codigos_unicos_tot');
                                            } while ($oIfx->SiguienteRegistro());
                                        }
                                    }
                                    $oIfx->Free();

                                    $codigos_unicos_rec = substr($codigos_unicos_rec, 1);
                                    $array_codigos_unicos_rec = explode(",", $codigos_unicos_rec);

                                    if (count($array_codigos_unicos_rec) < 1) {
                                        if (!empty($codigos_unicos_rec)) {
                                            array_push($array_codigos_unicos_rec, $codigos_unicos_rec);
                                        } else {
                                            $array_codigos_unicos_rec = array();
                                        }
                                    }


                                    $codigos_unicos_tot = substr($codigos_unicos_tot, 1);
                                    $array_codigos_unicos_tot = explode(",", $codigos_unicos_tot);

                                    if (count($array_codigos_unicos_tot) < 1) {
                                        if (!empty($codigos_unicos_tot)) {
                                            array_push($array_codigos_unicos_tot, $codigos_unicos_tot);
                                        } else {
                                            $array_codigos_unicos_tot = array();
                                        }
                                    }

                                    $sql_update_saeminv_oc = "UPDATE saeminv set  minv_cer_sn = 'N' ,
                                                                        minv_fech_modi      = CURRENT_DATE,
                                                                        minv_usua_modi      = $usuario_web where
                                                                        minv_cod_empr       = $idempresa and
                                                                        minv_cod_sucu       = $sucursal and
                                                                        minv_num_comp       = $serial_oc ";
                                    $oIfx->QueryT($sql_update_saeminv_oc);

                                    $cantidad_codigos_tot = count($array_codigos_unicos_tot);
                                    if ($cantidad_codigos_tot > 0) {
                                        $sql_update_saedmov = "UPDATE saedmov set dmov_can_entr = $cantidad_codigos_tot  where dmov_num_comp = $serial_oc and dmov_cod_empr = $idempresa and dmov_cod_sucu = $sucursal ";
                                        $oIfx->QueryT($sql_update_saedmov);
                                    }

                                    $cantidad_codigos_rec = count($array_codigos_unicos_rec);
                                    if ($cantidad_codigos_rec > 0) {
                                        $sql_update_series_oc_tot = "UPDATE series_oc set codigos_unicos_rec = '' where  minv_num_comp = $serial_oc and prod_cod_prod = '$cod_prod_grid' and dmov_cod_dmov = $dmov_cod_dmov_prod_grid";
                                        $oIfx->QueryT($sql_update_series_oc_tot);
                                    }




                                    /*
                                    $sql_dmov_oc = "SELECT * FROM saedmov where dmov_num_comp = $serial_oc";
                                    if ($oIfx->Query($sql_dmov_oc)) {
                                        if ($oIfx->NumFilas() > 0) {
                                            do {
                                                $dmov_cod_prod = $oIfx->f('dmov_cod_prod');
                                                $dmov_cod_dmov = $oIfx->f('dmov_cod_dmov');

                                                $sql_data_series = "SELECT * from series_oc where minv_num_comp = $serial_oc and prod_cod_prod = '$dmov_cod_prod' and dmov_cod_dmov = $dmov_cod_dmov";
                                                $codigos_unicos_rec = consulta_string($sql_data_series, 'codigos_unicos_rec', $oIfxA, '');
                                                $codigos_unicos_rec = substr($codigos_unicos_rec, 1);
                                                $array_codigos_unicos_rec = explode(",", $codigos_unicos_rec);
                                                foreach ($array_codigos_unicos_rec as $key => $codigo_unico_rec) {
                                                    var_dump($codigo_unico_rec);
                                                    exit;
                                                }
                                            } while ($oIfx->SiguienteRegistro());
                                        }
                                    }
                                    $oIfx->Free();
                                    */
                                }
                            }
                            $producto_config_serie_auto = 'S';
                        } else {
                            $array_codigos_unicos_rec = array();
                        }

                        // -------------------------------------------------------------------------------------------------------------
                        // FIN Recorremos la ordenes de compra que han sido seleccionadas para obtener los codigos unicos de cada sku
                        // -------------------------------------------------------------------------------------------------------------
                        $inserta_prod_dmov = 'N';
                        if (count($array_codigos_unicos_rec) > 0 && $producto_config_serie_auto == 'S') {
                            foreach ($array_codigos_unicos_rec as $key49 => $codigo_unico) {
                                if (!empty($codigo_unico)) {
                                    $inserta_prod_dmov = 'S';
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
                                    $aux = 0;
                                    $total = 0;
                                    $pedf_iva = 0;
                                    $sql_d .= "(";



                                    foreach ($aValues as $key => $aVal) {



                                        if ($aux == 0) {
                                            $sql_d .= " " . $x . ",";                 //dmov cod dmov
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
                                            $cant = 1;
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
                                            $lote = $codigo_unico;
                                        } elseif ($aux == 19) {
                                            $cuenta_iva = $aVal;
                                        } elseif ($aux == 14) {
                                            $fec_ela = $aVal;
                                        } elseif ($aux == 15) {
                                            $fec_cad = $aVal;
                                        } elseif ($aux == 18) {
                                            $cuenta_prod = $aVal;
                                        } elseif ($aux == 23) {
                                            $serie = $aVal;

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


                                            if (empty($iva)) {
                                                $iva = 0;
                                            }

                                            // Centro de costo Adn
                                            $ccosn_costo = $aDataGrid[$j]["ccosn"];
                                            $mac_producto = '';


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
                                            $costo_real = $costo;
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
                                            $sql_d .= " " . $iva . ",";       //dsc1
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

                                            // saecost
                                            // ID DEL SAECOST
                                            $sql_id_cost = "select max(cost_cod_cost) as maximo from saecost where
                                                        cost_cod_prod = '$prod' and
                                                        cost_cod_empr = $idempresa ";
                                            $cost_cod_cost = consulta_string($sql_id_cost, 'maximo', $oIfx, 0);
                                            // INGRESO SAECOST
                                            $sql_cost = "insert into saecost(cost_cod_cost,       cost_cod_prod,      cost_num_comp,
                                                                cost_cod_dmov,        cost_cod_bode,      cost_cod_sucu,
                                                                cost_cod_empr,        cost_num_prdo,      cost_cod_ejer,
                                                                cost_fec_cost,        cost_can_cost,      cost_val_unit,
                                                                cost_est_cost,        cost_tip_cost )
                                                        values(($cost_cod_cost+1),    '$prod',            $serial_minv,
                                                                    ($x),              $bod,                 $sucursal,
                                                                    $idempresa,          $idprdo,            $idejer,
                                                                    '$fecha_pedido',     ($cant_real),       $cost_act,
                                                                    1,                   'I' ) ";
                                            $oIfx->QueryT($sql_cost);



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
                                    $x++;
                                }
                            }
                        } else if ($producto_config_serie_auto == 'N' || !empty($lote_prod_ad)) {
                            $inserta_prod_dmov = 'S';

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
                            $aux = 0;
                            $total = 0;
                            $pedf_iva = 0;
                            $sql_d .= "(";

                            foreach ($aValues as $key => $aVal) {

                                if ($aux == 0) {
                                    $sql_d .= " " . $x . ",";                 //dmov cod dmov
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
                                } elseif ($aux == 15) {
                                    $fec_cad = $aVal;
                                } elseif ($aux == 18) {
                                    $cuenta_prod = $aVal;
                                } elseif ($aux == 23) {
                                    $serie = $aVal;

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


                                    if (empty($iva)) {
                                        $iva = 0;
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
                                    $sql_d .= " " . $iva . ",";       //dsc1
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

                                    // saecost
                                    // ID DEL SAECOST
                                    $sql_id_cost = "select max(cost_cod_cost) as maximo from saecost where
                                        cost_cod_prod = '$prod' and
                                        cost_cod_empr = $idempresa ";
                                    $cost_cod_cost = consulta_string($sql_id_cost, 'maximo', $oIfx, 0);
                                    // INGRESO SAECOST
                                    $sql_cost = "insert into saecost(cost_cod_cost,       cost_cod_prod,      cost_num_comp,
                                                            cost_cod_dmov,        cost_cod_bode,      cost_cod_sucu,
                                                            cost_cod_empr,        cost_num_prdo,      cost_cod_ejer,
                                                            cost_fec_cost,        cost_can_cost,      cost_val_unit,
                                                            cost_est_cost,        cost_tip_cost )
                                                    values(($cost_cod_cost+1),    '$prod',            $serial_minv,
                                                                ($x),              $bod,                 $sucursal,
                                                                $idempresa,          $idprdo,            $idejer,
                                                                '$fecha_pedido',     ($cant_real),       $cost_act,
                                                                1,                   'I' ) ";
                                    $oIfx->QueryT($sql_cost);



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
                            $x++;
                        }

                        if ($inserta_prod_dmov == 'N') {
                            throw new Exception('Debe ingresar los codigos unicos de los productos. No puede guardar la compra sin productos agregados');
                        }

                        $j++;
                    } // fin foreach dmov


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

                            $sql = "insert into saedasi (asto_cod_asto,        asto_cod_empr,      asto_cod_sucu,      dasi_num_prdo,
                                                              asto_cod_ejer,        dasi_cod_cuen,      dasi_dml_dasi,      dasi_cml_dasi,
                                                              dasi_dme_dasi,        dasi_cme_dasi,      dasi_tip_camb,      dasi_det_asi,
                                                              dasi_nom_ctac,        dasi_cod_clie,      dasi_cod_tran,      dasi_user_web )
                                                    values  ( '$secu_asto',         $idempresa,         $sucursal,          $idprdo,
                                                               $idejer,            '$key',              $suma,              0,
                                                               $suma_ext,           0,                  $coti,              '$detalle_asto' ,
                                                              '$cuen_prod_nom',     $cliente,           '$tran',            $usuario_web   ); ";
                            $oIfx->QueryT($sql);
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

                                $sql_ultimo_id = "select max(mxfp_cod_mxfp) as mxfp_cod_mxfp from saemxfp";
                                $ultimo_id = consulta_string($sql_ultimo_id, 'mxfp_cod_mxfp', $oIfxA, 0) + 1;

                                $sql_d .= " " . $ultimo_id . ",";                 //mxfp cod 
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


                    // R E T E N C I O N
                    if (count($aDataGrid_Rete) > 0) {
                        $ret_serie         = $aForm['serie_rete'];
                        $ret_auto          = $aForm['auto_rete'];
                        $ret_fec_auto   = $aForm['cad_rete'];

                        if ($ret_electronica == 'S') {
                            // RETENCION ELECTRONICA
                            $sql = "select retp_sec_retp 
									from saeretp  where 
									retp_cod_empr = $idempresa and
									retp_cod_sucu = $sucursal and
									retp_act_retp = '1' and
									retp_elec_sn  = 'S' ";
                            $retp_sec_retp = consulta_string($sql, 'retp_sec_retp', $oIfx, 0);
                            $retp_sec_retp = secuencial(2, '', $retp_sec_retp, 9);

                            $sql = "update saeretp set retp_sec_retp = $retp_sec_retp
										where retp_cod_empr = $idempresa and
										retp_cod_sucu       = $sucursal and
										retp_act_retp       = '1' and
										retp_elec_sn        = 'S' ";
                            $oIfx->QueryT($sql);
                        } else {
                            // RETENCION MANUAL
                            $retp_sec_retp = $aForm['ret_num'];
                            //$oReturn->alert($retp_sec_retp);

                            $sql = "update saeretp set retp_sec_retp = $retp_sec_retp
										where retp_cod_empr = $idempresa and
										retp_cod_sucu       = $sucursal and
										retp_act_retp       = '1' and
										retp_elec_sn        = 'N' ";
                            $oIfx->QueryT($sql);
                        }

                        $x = 1;
                        $detalle_ret = $des_tran . ' ' . $serie_prove . '-' . $factura . '-001';
                        $total_ret = 0;
                        foreach ($aDataGrid_Rete as $aValues) {
                            $sql_d = 'insert into saeret( ret_cod_ret,     rete_cod_asto,      asto_cod_empr,
														  asto_cod_sucu,
														  asto_cod_ejer,   asto_num_prdo,      ret_cta_ret,
														  ret_porc_ret,    ret_bas_imp,        ret_valor,
														  ret_num_ret,     ret_detalle,        ret_tip_camb,
														  ret_deb_ml,      ret_cre_ml,         ret_deb_mex,
													  	  ret_cre_mex,     rete_nom_benf,      rete_dire_benf,
														  rete_telf_benf,  rete_ruci_benf,     ret_num_fact,
														  ret_cod_clpv,    ret_ser_ret,        ret_aut_ret,
														  ret_fec_ret,     ret_email_clpv,     ret_nom_clpv,
														  ret_elec_sn	)
													values ';
                            $aux = 0;
                            $sql_d .= "(";
                            $j = 0;
                            foreach ($aValues as $aVal) {
                                if ($aux == 0) {
                                    $sql_d .= " " . $x . ",";                 //ret cod
                                    $sql_d .= " '" . $secu_asto . "',";                 // asto
                                    $sql_d .= " " . $idempresa . ",";                 //
                                    $sql_d .= " " . $sucursal . ",";                 //ret cod
                                    $sql_d .= " " . $idejer . ",";                 //ret cod
                                    $sql_d .= " " . $idprdo . ",";                 //ret cod
                                } elseif ($aux == 1) {
                                    $cod_ret = $aVal;
                                } elseif ($aux == 2) {
                                    $porc_ret = $aVal;
                                } elseif ($aux == 3) {
                                    $base_impo = $aVal;
                                } elseif ($aux == 4) {
                                    $val_ret = $aVal;
                                } elseif ($aux == 5) {
                                    //$num_ret = $aForm[$j . '_retencion'];
                                    $num_ret   = $retp_sec_retp;
                                } elseif ($aux == 6) {                                  //
                                    $cta_re = $aVal;
                                    $cero     = 0;
                                    $uno     = 1;

                                    if (empty($porc_ret)) {
                                        $porc_ret = 0;
                                    }

                                    if (empty($base_impo)) {
                                        $base_impo = 0;
                                    }



                                    $val_ret_ext = 0;
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
                                        $val_ret_ext = round(($val_ret / $val_camb), 2);
                                    } else {
                                        $val_ret_ext = round(($val_ret / $coti), 2);
                                    }


                                    $sql_d .= " '" . $cod_ret . "',";                      //
                                    $sql_d .= " '" . $porc_ret . "',";     //
                                    $sql_d .= " '" . $base_impo . "',";             //
                                    $sql_d .= " " . $val_ret . ",";
                                    $sql_d .= " '" . $num_ret . "',";        //
                                    $sql_d .= " '" . $detalle_ret . "', ";
                                    $sql_d .= " " . $coti . ", ";
                                    $sql_d .= " " . $cero . ", ";
                                    $sql_d .= " " . $val_ret . ", ";
                                    $sql_d .= " '" . $cero . "', ";
                                    $sql_d .= " '" . $val_ret_ext . "', ";  // moneda extrangera
                                    $sql_d .= " '" . $cliente_nom . "', ";
                                    $sql_d .= " '" . $dir_prove . "', ";
                                    $sql_d .= " '" . $tel_prove . "', ";
                                    $sql_d .= " '" . $ruc . "', ";
                                    $sql_d .= " '" . $factura . "', ";
                                    $sql_d .= " '" . $cliente . "', ";
                                    $sql_d .= " '" . $ret_serie . "', ";
                                    $sql_d .= " '" . $ret_auto . "', ";
                                    $sql_d .= " '" . $ret_fec_auto . "', ";
                                    $sql_d .= " '" . $minv_email_clpv . "', ";
                                    $sql_d .= " '" . $cliente_nom . "',  ";
                                    $sql_d .= " '" . $ret_electronica . "' ";

                                    if (!empty($cod_ret)) {
                                        // SAEDASI RETENCION
                                        // NOMBRE CUENTA PRODUCTOS
                                        $sql = "select  cuen_nom_cuen  from saecuen where
																			cuen_cod_empr = $idempresa and
																			cuen_cod_cuen = '$cta_re' ";
                                        $cuen_ret_nom = consulta_string($sql, 'cuen_nom_cuen', $oIfx, '');


                                        $sql = "insert into saedasi (asto_cod_asto,        asto_cod_empr,      asto_cod_sucu,      dasi_num_prdo,
																		  asto_cod_ejer,        dasi_cod_cuen,      dasi_dml_dasi,      dasi_cml_dasi,
																		  dasi_dme_dasi,        dasi_cme_dasi,      dasi_tip_camb,      dasi_det_asi,
																		  dasi_nom_ctac,        dasi_cod_ret,       dasi_cta_ret,       dasi_cod_clie,
																		  dasi_cod_tran,        dasi_user_web )
																values  ( '$secu_asto',         $idempresa,         $sucursal,          $idprdo,
																		   $idejer,            '$cta_re',           0,                  $val_ret,
																		   0,                   $val_ret_ext,           $coti,             '$detalle_asto' ,
																		  '$cuen_ret_nom',     '$num_ret',          '$cod_ret',         $cliente,
																		  '$tran',            $usuario_web   ); ";
                                        $oIfx->QueryT($sql);
                                        $total_ret += $val_ret;
                                    } // fin if
                                }
                                $aux++;
                            }
                            $sql_d .= ");";
                            $oIfx->QueryT($sql_d);

                            $fechaEmisionDocSustento = fecha_mysql($fecha_pedido);

                            //DETALLE DE LAS RETENCIONES
                            if ($cod_ret == '721' || $cod_ret == '725' || $cod_ret == '723') {
                                $codigo = '2';
                                if ($cod_ret == '721')
                                    $cod_ret = '1';
                                elseif ($cod_ret == '723')
                                    $cod_ret = '2';
                                elseif ($cod_ret == '725')
                                    $cod_ret = '3';
                            } else
                                $codigo = '1';

                            $x++;
                            $j++;
                        } // fin foreach saeret


                        // SAEASTO DASI TOTAL DE LA RET
                        $ret_asumido = $aForm['ret_asumido'];

                        $sql = "select tran_cod_tran  from saetran where
											tran_cod_empr = $idempresa and
											tran_cod_sucu = $sucursal and
											tran_cod_tran like 'RET%' ";
                        $tran_ret = consulta_string($sql, 'tran_cod_tran', $oIfx, 'RET');

                        if ($ret_asumido == 'S') {
                            // RENTENCION ASUMIDO
                            $sql = "select defi_cret_asumi ,cuen_nom_cuen  from saedefi, saecuen where
										cuen_cod_cuen = defi_cret_asumi and
										cuen_cod_empr = $idempresa and
										defi_cod_empr = $idempresa and
										defi_cod_sucu = $sucursal and
										defi_cod_tran = '$tran' ";
                            if ($oIfx->Query($sql)) {
                                if ($oIfx->NumFilas() > 0) {
                                    $defi_cret_asumi = $oIfx->f('defi_cret_asumi');
                                    $cuen_nom_cuen   = $oIfx->f('cuen_nom_cuen');
                                }
                            }
                            $oIfx->Free();

                            $total_ret_ext = 0;
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
                                $total_ret_ext = round(($total_ret / $val_camb), 2);
                            } else {
                                $total_ret_ext = round(($total_ret / $coti), 2);
                            }

                            $sql = "insert into saedasi ( asto_cod_asto,        asto_cod_empr,      asto_cod_sucu,      dasi_num_prdo,
														  asto_cod_ejer,        dasi_cod_cuen,      dasi_dml_dasi,      dasi_cml_dasi,
														  dasi_dme_dasi,        dasi_cme_dasi,      dasi_tip_camb,      dasi_det_asi,
														  dasi_nom_ctac,        dasi_cod_clie,      dasi_cod_tran,      dasi_user_web,
														  dasi_cod_ret )
												values  ( '$secu_asto',         $idempresa,         $sucursal,          $idprdo,
														   $idejer,            '$defi_cret_asumi',  $total_ret,         0,
														   $total_ret_ext,      0,                  $coti,              '$detalle_asto' ,
														  '$cuen_nom_cuen',     $cliente,           '$tran_ret',        $usuario_web ,
														  '$num_ret'  ); ";
                            $oIfx->QueryT($sql);
                        } else {
                            // RETENCION NORMAL
                            $total_ret_ext = 0;
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
                                $total_ret_ext = round(($total_ret / $val_camb), 2);
                            } else {
                                $total_ret_ext = round(($total_ret / $coti), 2);
                            }

                            $sql = "insert into saedasi (asto_cod_asto,        asto_cod_empr,      asto_cod_sucu,      dasi_num_prdo,
                                                      asto_cod_ejer,        dasi_cod_cuen,      dasi_dml_dasi,      dasi_cml_dasi,
                                                      dasi_dme_dasi,        dasi_cme_dasi,      dasi_tip_camb,      dasi_det_asi,
                                                      dasi_nom_ctac,        dasi_cod_clie,      dasi_cod_tran,      dasi_user_web,
                                                      dasi_cod_ret )
                                            values  ( '$secu_asto',         $idempresa,         $sucursal,          $idprdo,
                                                       $idejer,            '$cuenta_prove',     $total_ret,         0,
                                                       $total_ret_ext,      0,                  1,                 '$detalle_asto' ,
                                                      '$cuen_prove_nom',    $cliente,           '$tran_ret',        $usuario_web ,
                                                      '$num_ret'  ); ";
                            $oIfx->QueryT($sql);


                            // SAEDIR RETE
                            $sql = "insert into saedir (  dir_cod_dir,          dire_cod_asto,     dire_cod_empr,       dire_cod_sucu,
														  asto_cod_ejer,        asto_num_prdo,     dir_cod_cli,         tran_cod_modu,
														  dir_cod_tran,         dir_num_fact,      dir_fec_venc,
														  dir_detalle,          dire_tip_camb,     dir_deb_ml,          dir_cre_ml,
														  dir_deb_mex,          dir_cred_mex,      bandera_cr,          dire_suc_clpv,
														  dir_user_web  )
												 values(  2,                    '$secu_asto',     $idempresa,          $sucursal,
														  $idejer,              $idprdo,          $cliente,            10,
														  '$tran_ret',          '$fact_dir',      '$fecha_pedido',
														  '$detalle_asto',       $coti,            $total_ret,          0,
														   $total_ret_ext,       0,               'DB',                 $sucursal,
														   $usuario_web  ); ";
                            $oIfx->QueryT($sql);
                        }




                        // UPDATE SECUENCIA SAESECU RET
                        $sql = "update saesecu set secu_ret_fuen  = '$num_ret' where
                                                    secu_cod_empr = $idempresa and
                                                    secu_cod_sucu = $sucursal and
                                                    secu_cod_tidu = '$tidu' and
                                                    secu_cod_modu = 10 and
                                                    secu_cod_ejer = $idejer and
                                                    secu_num_prdo = $idprdo ";
                        $oIfx->QueryT($sql);
                    } // fin if


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




                    $oIfx->QueryT('COMMIT WORK;');
                    $oReturn->alert('Compra Ingresado Correctamente');
                    $oReturn->assign("nota_compra", "value",     $secu_minv);
                    $oReturn->assign("codMinv", "value",         $serial_minv);
                    $oReturn->assign("ejercicio", "value",         $idejer);
                    $oReturn->assign("periodo", "value",         $idprdo);
                    $oReturn->assign("asiento", "value",         $secu_asto);
                    //codMinv

                    $oReturn->script("vista_previa('.$serial_minv.', '.$idempresa.', '.$sucursal.');");

                    $array_print[] = array($claveAcceso, $cliente, $factura, $idejer, $secu_asto, $fechaEmision);
                } else {
                    $oReturn->alert('::.ERROR.:: la factura numero ' . $factura . ' debe estar dentro del intervalo ' . $factura_inicio . ' - ' . $factura_fin . ' ');
                    $oReturn->assign("ctrl", "value", 1);
                }
            }
        } catch (Exception $e) {
            // rollback
            $oIfx->QueryT('ROLLBACK WORK;');
            $oReturn->script("Swal.fire({
                                width: '800px',
                                position: 'left',
                                type: 'error',
                                title: '" . $e->getMessage() . "',
                                showConfirmButton: true,
                                confirmButtonText: 'Aceptar',
                                timer: 99000
                            })");
            $oReturn->assign("ctrl", "value", 1);
        }
    } else {
        if ($clpv_ret_sn == 'S') {
            // APLICACION RETENCION
            $oReturn->alert('!!!!....Por favor Seleccionar Productos - Forma de Pago - Reteciones....!!!!!');
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
    $anio = $array_fecha_compra[0];
    $mes = $array_fecha_compra[1];


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
            "ruc" => $ruc, "tipoAmbiente" => $tipoAmbiente, "tiempoEspera" => $tiempoEspera,
            "token" => $token, "pathArchivo" => $pathArchivo, "pathFirmados" => $pathFirmados,
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

            $_SESSION['pdf'] = reporte_retencionInve($rutaDia, $nombre, $rutaPdf, $cliente, $factura, $idejer, $secu_asto, $fechaEmision);
            //$_SESSION['pdf'] = reporte_retencionInve($sqlRete,'docu');
            $oReturn->script('generar_pdf()');
        }
    } catch (SoapFault $e) {
        $oReturn->alert("NO HAY CONECCION CON LA FIRMA");

        $_SESSION['pdf'] = reporte_retencionInve($rutaDia, $nombre, $rutaPdf, $cliente, $factura, $idejer, $secu_asto, $fechaEmision);
        //$_SESSION['pdf'] = reporte_retencionInve($sqlRete,'docu');
        $oReturn->script('generar_pdf()');

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

            $_SESSION['pdf'] = reporte_retencionInve($rutaDia, $nombre, $rutaPdf, $cliente, $factura, $idejer, $secu_asto, $fechaEmision);
            //$_SESSION['pdf'] = reporte_retencionInve($sqlRete,'docu');
            $oReturn->script('generar_pdf()');
        }
    } catch (SoapFault $e) {
        $oReturn->alert("NO HAY CONECCION AL SRI (VALIDAR) " . $e);

        $_SESSION['pdf'] = reporte_retencionInve($rutaDia, $nombre, $rutaPdf, $cliente, $factura, $idejer, $secu_asto, $fechaEmision);
        //$_SESSION['pdf'] = reporte_retencionInve($sqlRete,'docu');
        $oReturn->script('generar_pdf()');

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

            $_SESSION['pdf'] = reporte_retencionInve($rutaDia, $nombre, $rutaPdf, $cliente, $factura, $idejer, $secu_asto, $fechaEmision);
            $oReturn->script('generar_pdf()');

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

            $_SESSION['pdf'] = reporte_retencionInve($rutaDia, $nombre, $rutaPdf, $cliente, $factura, $idejer, $secu_asto, $fechaEmision);
            //$_SESSION['pdf'] = reporte_retencionInve($sqlUpdate,'docu');
            $oReturn->script('generar_pdf()');
        }
    } catch (SoapFault $e) {
        $oReturn->alert("'NO HAY CONECCION AL SRI (AUTORIZAR) '");

        $_SESSION['pdf'] = reporte_retencionInve($rutaDia, $nombre, $rutaPdf, $cliente, $factura, $idejer, $secu_asto, $fechaEmision);
        //$_SESSION['pdf'] = reporte_retencionInve($sqlUpdate,'docu');
        $oReturn->script('generar_pdf()');

        $sqlError = "update saeminv set minv_erro_sri = 'NO HUBO CONECCION AL SRI (AUTORIZAR)' " . $sqlUpdate;
        $oIfx->QueryT($sqlError);
    }

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
        $sql = "select bode_cod_bode, bode_nom_bode from saesubo, saebode where
                        bode_cod_bode = subo_cod_bode and
                        bode_cod_empr = $idempresa and
                        subo_cod_empr = $idempresa and
                        subo_cod_sucu = $idsucursal ";
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
                                            <td class="success" style="width: 4.5%;">N.-</td>
                                            <td class="success" style="width: 4.5%;">BODEGA</td>
                                            <td class="success" style="width: 4.5%;">CODIGO PRODUCTO</td>
                                            <td class="success" style="width: 9.5%;">PRODUCTO</td>
                                            <td class="success" style="width: 4.5%;">CANTIDAD</td>
                                            <td class="success" style="width: 4.5%;">LOTE/SERIE</td>
                                            <td class="success" style="width: 4.5%;">FECHA ELAB.</td>
                                            <td class="success" style="width: 4.5%;">FECHA CAD.</td>
                                            <td class="success" style="width: 4.5%;">MAC</td>
                                            <td class="success" style="width: 4.5%;">CENTRO DE COSTO</td>
                                            <td class="success" style="width: 4.5%;">PRECIO UNIT.</td>
                                            <td class="success" style="width: 4.5%;">FOB.</td>';

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
                $table_cab .= '<td class="success" style="width: 4.5%;">PVP' . $i . '</td>';
            }

            $table_cab .= '</tr>';
            $x = 1;
            // $oReturn->alert('Buscando ...');
            unset($array);
            foreach ($datos as $val) {
                /*BODEGA	    CODIGO	        PRODUCTO	    CANTIDAD	        CENTRO DE COSTO	        FOB
                        */

                list(
                    $bode_cod, $prod_cod, $prod_nom, $cantidad, $lote_serie_txt, $fecha_ela_txt, $fecha_cad_txt, $mac_prod, $ccosto, $fob, $fob_real, $pvp1, $pvp2, $pvp3, $pvp4, $pvp5, $pvp6, $pvp7, $pvp8, $pvp9, $pvp10
                ) = explode("	", $val);
                $costo_limpio = str_replace(',', '.', $fob);

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
                        $sql_prod_nom = "select prod_nom_prod from saeprod where prod_cod_prod = '$prod_cod'";
                        $prod_nom = consulta_string($sql_prod_nom, 'prod_nom_prod', $oIfx, '');
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
                        $array_bode_cod[$bode_cod],         $prod_cod,                          $prod_nom,
                        $array_prec_cod[$ccosto],      $cantidad,                             $fob
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
        $sql = "select bode_cod_bode, bode_nom_bode from saesubo, saebode where
                        bode_cod_bode = subo_cod_bode and
                        bode_cod_empr = $idempresa and
                        subo_cod_empr = $idempresa and
                        subo_cod_sucu = $idsucursal ";
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
                    $bode_cod,  $prod_cod,  $prod_nom,  $cantidad, $lote_serie_txt, $fecha_ela_txt, $fecha_cad_txt, $mac_prod, $ccosto, $fob, $fob_real, $pvp1, $pvp2, $pvp3, $pvp4, $pvp5, $pvp6, $pvp7, $pvp8, $pvp9, $pvp10
                ) = explode("	", $val);
                $costo_limpio = str_replace(',', '.', $fob);

                $sql_prod_nom = "select prod_nom_prod from saeprod where prod_cod_prod = '$prod_cod'";
                $prod_nom = consulta_string($sql_prod_nom, 'prod_nom_prod', $oIfx, '');

                if ($x > 1 && !empty($bode_cod)) {

                    $array[] = array(
                        $array_bode_cod[$bode_cod],         $prod_cod, $prod_nom,
                        $array_prec_cod[$ccosto],      $cantidad,    $costo_limpio
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

                    $cantidad            = $cantidad;
                    $codigo_barra        = '';
                    $codigo_producto     = $prod_cod;
                    $costo               = $costo_limpio;
                    $idbodega            = $array_bode_cod[$bode_cod];
                    $descuento           = 0;
                    $descuento_2         = 0;
                    $cuenta_inv          = $prbo_cta_inv;
                    $cuenta_iva          = $prbo_cta_ideb;
                    $lote_prod           = $lote_serie_txt;
                    $fecha_ela           = $fecha_ela_txt;
                    $fecha_cad           = $fecha_cad_txt;
                    $peso                = 0;
                    $idunidad            = $prbo_cod_unid;



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
                    $aDataGrid[$cont][$aLabelGrid[23]] = '';
                    $aDataGrid[$cont][$aLabelGrid[24]] = '';
                    $aDataGrid[$cont][$aLabelGrid[25]] = '';
                    $aDataGrid[$cont][$aLabelGrid[26]] = '';
                    $aDataGrid[$cont][$aLabelGrid[27]] = $mac_prod;

                    $aDataGrid[$cont]['ccosn'] = $array_prec_cod[$ccosto];
                    $aDataGrid[$cont]['fob_real'] = $fob_real;
                    for ($j = 1; $j <= $cont_pvp; $j++) {
                        // Lee la primera fila del encabezado del excel
                        $aDataGrid[$cont]["pvp" . $j] = ${"pvp" . $j};
                    }
                }
                $x++;
            }


            $_SESSION['aDataGird_INV_MRECO'] = $aDataGrid;
            $sHtml = mostrar_grid();
            $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
            $oReturn->script('limpiar_prod()');
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
    $cantidad = $aForm['cantidad'];
    $codigo_barra = $aForm['codigo_barra'];
    $codigo_producto = $aForm['codigo_producto'];
    $costo = $aForm['costo'];
    $iva = $aForm['iva'];
    $idbodega = $aForm['bodega'];
    $descuento = vacios($aForm['desc1'], 0);
    $descuento_2 = vacios($aForm['descuento_2'], 0);
    $cuenta_inv = $aForm['cuenta_inv'];
    $cuenta_iva = $aForm['cuenta_iva'];
    $lote_prod = $aForm['lote_prod'];
    $serie_prod = $aForm['serie_prod'];
    $mac_ad_prod = $aForm['mac_ad_prod'];
    $fecha_ela = $aForm['fecha_ela'];
    $fecha_cad = $aForm['fecha_cad'];
    $prod_nom = $aForm['producto'];
    $ccosn = $aForm['ccosn'];
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
        $tran_cod = $aForm['tran'];
        $costo = saetran_costo($oIfx, $idempresa,  $idsucursal, $codigo_producto, $idbodega, $tran_cod, $costo, $cantidad);

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
                        $serie_aDataGrid = $data_aDataGrid['lote'];
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
                if (isset($aDataGrid)) {
                    $cont = count($aDataGrid);
                } else {
                    $cont = 0;
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
            $oReturn->script('limpiar_prod()');
            $oReturn->script('habilita(5)');
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
        if (isset($array_otros)) {
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
        }


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


        $sql = "select pcon_mon_base, pcon_seg_mone from saepcon where pcon_cod_empr = $idempresa ";
        $mone_cod = consulta_string_func($sql, 'pcon_mon_base', $oIfx, '');



        $sql_mone_principal = "select * from saemone
            where mone_cod_empr = $idempresa
            and mone_cod_mone = $mone_cod";

        if ($oIfx->Query($sql_mone_principal)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    $moneda_principal = $oIfx->f('mone_sgl_mone') . '' . $oIfx->f('mone_smb_mene');
                    $moneda_principal_ad = $oIfx->f('mone_sgl_mone') . '' . $oIfx->f('mone_smb_mene');
                } while ($oIfx->SiguienteRegistro());
            }
        }


        $sql_mone_secundaria = "select * from saemone
            where mone_cod_empr = $idempresa
            and mone_cod_mone <> $mone_cod";

        if ($oIfx->Query($sql_mone_secundaria)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    $moneda_secundaria = $oIfx->f('mone_sgl_mone') . '' . $oIfx->f('mone_smb_mene');
                    $moneda_secundaria_ad = $oIfx->f('mone_sgl_mone') . '' . $oIfx->f('mone_smb_mene');
                } while ($oIfx->SiguienteRegistro());
            }
        }




        $sigl_mone = $_SESSION['U_MONE_SIGLA'] . '$';
        $moneda    = $aForm['moneda'];
        $val_camb  = $aForm['cotizacion'];
        $sql       = "select pcon_mon_base from saepcon where pcon_cod_empr = $idempresa ";
        $mone_base = consulta_string_func($sql, 'pcon_mon_base', $oIfx, '');

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
        }


        $total_usd = round(((round(($total_fac_total + $total_iva + $total_otros), 2)) / $val_camb), 2);

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
        //$fecha_ejer = '31-12-' . $anio;
        $fecha_ejer = $anio . '-12-31';
        $sql = "select ejer_cod_ejer from saeejer where ejer_fec_finl = '$fecha_ejer' and ejer_cod_empr = $idempresa ";
        $idejer = consulta_string($sql, 'ejer_cod_ejer', $oIfx, 1);

        $sql = "SELECT  d.defi_cod_trtc, d.defi_cod_retiva,
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
                                    secu_cod_sucu = $sucursal limit 1 ) as secu_ret
                            from saetran t, saedefi d  where
                            t.tran_cod_tran = d.defi_cod_tran and
                            t.tran_cod_empr = $idempresa and
                            t.tran_cod_sucu = $sucursal and
                            t.tran_cod_modu = 10 and
                            d.defi_cod_empr = $idempresa  and
                            d.defi_tip_defi = '0' and
                            t.tran_cod_tran = '$cod_tran' and
                            d.defi_cod_modu = 10 order by 2 limit 1";



        /*
        $nombre = "revision.txt";

        $fp = fopen($nombre, "r");
        $sql_anterior = fread($fp, filesize($nombre));

        $archivo = fopen($nombre, "w+");
        fwrite($archivo, $sql_anterior.'::::::::'.$sql);
        fclose($archivo);
      

        //$oReturn->alert($sql);
         */

        if ($oIfx->Query($sql)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    $cod_ret = $oIfx->f('defi_cod_trtc');
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

        unset($_SESSION['aDataGirdRete']);
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
        $html_rete = '<span class="total_fact" >EL PROVEEDOR ' . $contri . ' ES CONTRIBUYENTE ESPECIAL</span>';
        if (count($aDataGrid_ret) > 0) {
            $_SESSION['aDataGirdRete'] = $aDataGrid_ret;
            $sHtmlRete = mostrar_grid_ret();
        }

        $oReturn->assign("divFormularioDetalleRET", "innerHTML", $sHtmlRete);
    } else {
        $sHtml = "";
    }

    $oReturn->assign("divTotal", "innerHTML", $sHtml);
    $oReturn->assign("total_fact_fp", "value", round(($total_fac + $total_iva + $total_otros + $ice_total), 2));
    $oReturn->assign("valor", "value", round(($total_fac + $total_iva + $total_otros + $ice_total), 2));
    $oReturn->assign("divFormularioCabeceraRET", "innerHTML", $html_rete);
    return $oReturn;
}


// RETENCION 
function agrega_modifica_grid_ret($nTipo = 0, $aForm = '')
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

    $aDataGrid_ret = $_SESSION['aDataGirdRete'];
    $aLabelGrid_ret = array('Id', 'Codigo Sri', 'Porcentaje', 'Base Imponible', 'Retencion', 'No Retencion', 'Cta Contable', 'Eliminar');
    $oReturn = new xajaxResponse();

    $idempresa  = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    $decimal = 6;

    $cod_ret      = $aForm['cod_ret'];
    $cod_ret_porc = $aForm['ret_porc'];
    $base_ret     = $aForm['ret_base'];
    $secu_ret     = $aForm['ret_num'];


    $sql = "select  tret_cta_cre 
                from saetret, saecuen where
                tret_cta_cre = cuen_cod_cuen and
				tret_cod_empr = $idempresa and
				tret_cod      = '$cod_ret' ";
    $cuen_ret = consulta_string($sql, 'tret_cta_cre', $oIfx, 0);



    if ($nTipo == 0 && $cuen_ret != null) {

        //GUARDA LOS DATOS DEL DETALLE
        $cont = count($aDataGrid_ret);
        $fu->AgregarCampoNumerico($cont . '_retencion', 'Retencion|left', true, $secu_ret, 70, 10);
        $aDataGrid_ret[$cont][$aLabelGrid_ret[0]] = floatval($cont);
        $aDataGrid_ret[$cont][$aLabelGrid_ret[1]] = $cod_ret;
        $aDataGrid_ret[$cont][$aLabelGrid_ret[2]] = $cod_ret_porc;
        $aDataGrid_ret[$cont][$aLabelGrid_ret[3]] = $base_ret;
        $aDataGrid_ret[$cont][$aLabelGrid_ret[4]] = round(($base_ret * $cod_ret_porc / 100), 2);
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
        $_SESSION['aDataGirdRete'] = $aDataGrid_ret;
        $sHtml = mostrar_grid_ret();
        $oReturn->assign("divFormularioDetalleRET", "innerHTML", $sHtml);
    } else {
        $oReturn->alert('Verificar cuenta contable de retencion');
    }


    return $oReturn;
}


function mostrar_grid_ret()
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
    $aDataGrid = $_SESSION['aDataGirdRete'];
    $aLabelGrid = array('Id', 'Codigo Sri', 'Porcentaje', 'Base Imponible', 'Retencion', 'No Retencion', 'Cta Contable', 'Eliminar');

    $cont = 0;
    foreach ($aDataGrid as $aValues) {
        $aux = 0;
        foreach ($aValues as $aVal) {
            if ($aux == 0)
                $aDatos[$cont][$aLabelGrid[$aux]] = $cont + 1;
            elseif ($aux == 1) {
                // NOMBRE RETENCION
                $sql = "select tret_det_ret from saetret where
                                            tret_cod_empr = $idempresa and
                                            tret_cod =  '$aVal' ";
                $ret = consulta_string($sql, 'tret_det_ret', $oIfx, '');
                $aDatos[$cont][$aLabelGrid[$aux]] = $ret;
            } elseif ($aux == 2) {
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
            } elseif ($aux == 3) {
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
            } elseif ($aux == 4) {
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
            } elseif ($aux == 5) {
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
            } elseif ($aux == 6) {
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="right">' . $aVal . '</div>';
            } elseif ($aux == 7)
                $aDatos[$cont][$aLabelGrid[$aux]] = '<div align="center">
                                                                        <img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/delete_1.png"
                                                                        title = "Presione aqui para Eliminar"
                                                                        style="cursor: hand !important; cursor: pointer !important;"
                                                                        onclick="javascript:xajax_elimina_detalle_ret(' . $cont . ');"
                                                                        alt="Eliminar"
                                                                        align="bottom" />
                                                                    </div>';
            else
                $aDatos[$cont][$aLabelGrid[$aux]] = $aVal;
            $aux++;
        }
        $cont++;
    }
    return genera_grid_ret($aDatos, $aLabelGrid, 'Retencion', 68);
}

function elimina_detalle_ret($id = null)
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oReturn = new xajaxResponse();

    $aLabelGrid = array('Id', 'Codigo Sri', 'Porcentaje', 'Base Imponible', 'Retencion', 'No Retencion', 'Cta Contable', 'Eliminar');

    $aDataGrid = $_SESSION['aDataGirdRete'];
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
                elseif ($aux == 7)
                    $aDatos[$cont][$aLabelGrid[$aux]] = '<img src="' . $_COOKIE['JIREH_IMAGENES'] . 'iconos/delete_1.png"
                                                                                        title = "Presione aqui para Eliminar"
                                                                                        style="cursor: hand !important; cursor: pointer !important;"
                                                                                        onclick="javascript:xajax_elimina_detalle_ret(' . $cont . ');"
                                                                                        alt="Eliminar"
                                                                                        align="bottom" />';
                else
                    $aDatos[$cont][$aLabelGrid[$aux]] = $aVal;
                $aux++;
            }
            $cont++;
        }
        $_SESSION['aDataGirdRete'] = $aDatos;


        $sHtml = mostrar_grid_ret();
    } else {
        unset($aDataGrid[0]);
        $_SESSION['aDataGirdRete'] = $aDatos;
        $sHtml = "";
        $sHtml = $mostrar_prueba_grid;
    }


    $oReturn->assign("divFormularioDetalleRET", "innerHTML", $sHtml);
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
                $fecha_ejer = $anio . '-12-31';
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


        // form total
        $fu->AgregarCampoTextoRojo('total_fp', 'Total|left', false, 0, 70, 2);
        $fu->AgregarComandoAlPonerEnfoque('total_fp', 'this.blur()');
        $fu->cCampos["total_fp"]->xValor = $total_fp;

        $sHtml .= '<fieldset style="border:#FFFFFF 1px solid; padding:2px; text-align:center; width:65%;">';
        $sHtml .= '<table align="center" cellpadding="0" cellspacing="2" width="50%" border="0">
                            <tr>
                                            <td  class="total_fact"  bgcolor="#EBF0FA" height="25">TOTAL FP: $</td>
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

        if (!empty($dias) && !empty($fecha)) {
            $fecha_ven = date("Y-m-d", strtotime($fecha . "+ " . $dias . " days"));

            $oReturn->assign("dias_fp", "value", $dias);
            $oReturn->assign("fecha_final", "value", $fecha_ven);
            $oReturn->assign("fecha_inicio", "value", $fecha);
            $oReturn->assign("fecha_entrega", "value", $fecha_ven);
        }
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
                                    onkeyup="' . $evento1 . '" onchange="' . $evento11 . '" />
                                </div>
                                <div class="col-md-4">
                                    <label for="factura">* Factura:</label>		
                                    <input type="text" class="form-control input-sm" id="factura" name="factura" style="text-align:right;" 
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
                $auto_prove   = '000';
                $serie_prove  = '000000';
                $a_prove      = '9999999999';
                $fec_cadu_prove = '2029-12-31';
                $ini_prove  = '1';
                $fin_prove  = '99999999999';
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
        'Id',                 'Bodega',                 'Codigo Item',                 'Descripcion',                 'Unidad',
        'Cantidad',         'Costo',                 'Impuesto',                 'Dscto 1',                  'Dscto 2',
        'Dscto Gral',         'Total',                 'Total Con Impuesto',         'Modificar',                 'Eliminar',
        'Cuenta',             'Cuenta Impuesto',         'Lote',                     'Fecha Ela',                 'Fecha Cad',
        'Detalle',             'Precio',                'Orden Compra',         'Serial'
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


function clave_acceso($aForm = '', $tipo, $leerxml, $codigoPrincipal)
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
            // CARPETA EMPRESA
            $ruta1 = "Doc_Electronicos";
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
                } elseif ($codigoPorcentaje == 0) {
                    $valor_grab0s = $baseImponible . '';
                    $totalserv = $totalserv + $valor_grab0s;
                }
            }


            $serie = $estab . $ptoEmi;
            $secuencial = $secuencial . '';

            //$oReturn->alert($baseImponible.'');

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
                    $oReturn->assign('fecha_pedido', 'value', $fechaEmision);
                    $oReturn->assign('fecha_entrega', 'value', $fechaEmision);
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
									minv_fac_prov = '$secuencial' and
                                    minv_cod_tran <> '031'";
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

                    $fecha_compra = $aForm['fecha_pedido'];
                    $fecha_final = date("Y-m-d", strtotime($fecha_compra . "+ " . $clpv_pro_pago . " days"));
                    $oReturn->assign('fecha_entrega', 'value', $fecha_final);
                    $oReturn->script('datos_clpv( \'' . $cod_clpv . '\', \'' . $clpv_nom_clpv . '\' , \'' . $identificacionProveedor . '\',  \'' . $dire . '\',
                                                            \'' . $telefono . '\',      \'' . $celular . '\',        \'' . $vendedor . '\',       \'' . $contacto . '\',
                                                            \'' . $precio . '\',        \'' . $clpv_cod_fpagop . '\', \'' . $clpv_cod_tpago . '\', \'' . $fec_cadu_prove . '\',
                                                            \'' . $auto_prove . '\',    \'' . $serie_prove . '\',     \'' . $fecha_venc . '\',     \'' . $clpv_pro_pago . '\',
                                                            \'' . $clpv_etu_clpv . '\', \'' . $ini_prove . '\',       \'' . $fin_prove . '\',      \'' . $clpv_cod_cuen . '\',
                                                            \'' . $correo . '\'
                                                          )');


                    // if ($contador_ > 0 && $contador_1 > 0) {
                    if ($contador_ > 0) {
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


                        // $leerxml, $codigoPrincipal
                        if ($leerxml == 1) {
                            if (empty($bodega)) {
                                $mensaje     = "Por favor Seleccionar Bodega..!!!";
                                $tipo_mesaje = 'info';
                                $oReturn->script('alerts("' . $mensaje . '", "' . $tipo_mesaje . '");');
                            } else {
                                unset($array_rd);
                                foreach ($xmlParse->detalles->detalle as $arreglo) {



                                    // var_dump($arreglo);  adrian47
                                    // exit;




                                    if ($codigoPrincipal == 0) {
                                        $sql_prod = "select prod_cod_prod, prbo_cta_inv, prbo_cta_ideb,
                                        prbo_cod_unid
                                        from saeprod, saeprbo where 
                                        prod_cod_prod  =  prbo_cod_prod and 
                                        prod_cod_empr   =  prbo_cod_empr and 
                                        prod_cod_sucu   =  prbo_cod_sucu and 
                                        prod_cod_prod   =  '$arreglo->codigoAuxiliar' and
                                        prod_cod_sucu   =  '$sucursal' and
                                        prod_cod_empr   = '$idempresa' and 
                                        prbo_cod_empr    = '$idempresa' and 
                                        prbo_cod_sucu    = '$sucursal' ";
                                    } else {
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
                                    }


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


                                    if ($codigoPrincipal == 0) {
                                        $pro      = "'" . $arreglo->codigoAuxiliar . "'";
                                    } else {
                                        $pro      = "'" . $arreglo->codigoPrincipal . "'";
                                    }

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
                                            $bodega, $idproducto, $descr, $idunidad, $cantidad, $costo,
                                            $iva, $dsc1, $descuento_general, $total_fac, $total_con_iva,
                                            $cuenta_inv, $cuenta_iva
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
                                    $oReturn->script('totales()');
                                } else {

                                    $mensaje = "Estos Producto(s) no existen en la bodega: " . $productos_no . " .Si los productos son de gasto ingresar en el modulo correspondiente";
                                    //$oReturn->alert($mensaje.'sdsd');
                                    $tipo_mesaje = 'info';
                                    $oReturn->script('alerts("' . $mensaje . '", "' . $tipo_mesaje . '");');
                                    unset($_SESSION['aDataGird_INV_MRECO']);
                                    $sHtml = "";
                                    $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
                                    //$oReturn->script('totales();');
                                }
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
        $oReturn->script('totales();');
        $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
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
    $lote       = $aDataGrid[$id]['lote'];

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
    $fu->AgregarCampoFecha('fecha_ela_tmp', 'Fecha Elaboracion|left', true, '');
    $fu->AgregarCampoFecha('fecha_cad_tmp', 'Fecha Caducidad|left', true, '');

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
        $sHtml .= '<td><input type="date" name = "fecha_ela_tmp" id="fecha_ela_tmp"></td>';
        $sHtml .= '</tr>';

        $sHtml .= '<tr height="25">';
        $sHtml .= '<td>' . $fu->ObjetoHtmlLBL('fecha_cad_tmp') . '</td>';
        $sHtml .= '<td><input type="date" name = "fecha_cad_tmp" id="fecha_cad_tmp"></td>';
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
    $oReturn->script('limpiar_prod()');
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

    $prod_cod = $aDataGrid[$id]['Codigo Item'];
    $bode_cod = $aDataGrid[$id]['Bodega'];
    $cant = $aForm[$id . '_cantidad'];
    $costo = $aDataGrid[$id]['Costo'];




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

    $sHtml .= '<tr height="25">';
    $sHtml .= '<td>Copiar Precio en Todas Sucursales:</td>';
    $sHtml .= '<td class="fecha_letra">
                    <input class="form-check-input" type="checkbox" id="check_copiar_sucursales" name="check_copiar_sucursales" value="S" aria-label="...">
                </td>';
    $sHtml .= '</tr>';

    $sHtml .= '<tr height="25">';
    $sHtml .= '<td></td>';
    $sHtml .= '<td class="fecha_letra"><input type="number" id="costo_prod_tot" name="costo_prod_tot" value="' . $costo . '" style="display: none" /></td>';
    $sHtml .= '</tr>';


    if (count($array_nomp) > 0) {
        $sHtml .= '<tr height="25">';
        $sHtml .= '<td colspan="2">';
        $sHtml .= ' <table class="table table-striped table-condensed" style="width: 99%; margin-bottom: 0px;" align="center">';
        $sHtml .= '<tr height="25">';
        $sHtml .= '<td class="bg-success" align="center">N.-</td>';
        $sHtml .= '<td class="bg-success" align="center">Tipo Precio</td>';
        $sHtml .= '<td class="bg-success" align="center">Costo Compra</td>';
        $sHtml .= '<td class="bg-success" align="center">Precio Actual</td>';
        $sHtml .= '<td class="bg-success" align="center">Porcentaje %</td>';
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

            $precio = $aDataGrid[$id]["pvp" . $i];
            if (empty($precio)) {
                $precio = $ppr_pre_raun;
            }

            $fu->AgregarCampoNumerico($nomp_cod_nomp, 'Precio|left', false, $precio, 80, 100);

            $fu->AgregarCampoNumerico($nomp_cod_nomp . '_procentaje', 'Precio|left', false, 0, 80, 100);
            $fu->AgregarComandoAlCambiarValor($nomp_cod_nomp . '_procentaje', 'recalcular_precio_venta(' . $nomp_cod_nomp . ')');

            $sHtml .= '<tr>';
            $sHtml .= '<td align="right">' . $nomp_cod_nomp . '</td>';
            $sHtml .= '<td>' . $nomp_nomb_nomp . '</td>';
            $sHtml .= '<td align="right">' . $costo . '</td>';
            $sHtml .= '<td align="right">' . $ppr_pre_raun . '</td>';
            $sHtml .= '<td align="right">' . $fu->ObjetoHtml($nomp_cod_nomp . '_procentaje') . '</td>';
            $sHtml .= '<td align="right">' . $fu->ObjetoHtml($nomp_cod_nomp) . '</td>';
            $sHtml .= '</tr>';

            $i++;
        } // fin foreach
        $sHtml .= '</table></td></tr>';
    } // fin if

    $_SESSION['U_SAEPPR_INV'] = $array_precio;

    $sHtml .= '</table>';

    $modal  = '<div id="mostrarmodal4" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg" style="width: 60% !important;">
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


function recalcular_precio_venta($nomp_cod_nomp, $aForm = '')
{

    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $costo_prod_tot = $aForm['costo_prod_tot'];
    $procentaje = $aForm[$nomp_cod_nomp . '_procentaje'];
    $calculo_porcentaje = ($costo_prod_tot * $procentaje) / 100;
    $calculo_precio = number_format($costo_prod_tot + $calculo_porcentaje, 2, '.', '');
    $oReturn->assign($nomp_cod_nomp, "value", $calculo_precio);

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

    $oIfxA = new Dbo;
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $oReturn     = new xajaxResponse();

    $idempresa     = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    $array_inv  = $_SESSION['U_SAEPPR_INV'];
    $copiar_todas_sucursales = $aForm['check_copiar_sucursales'];

    if (count($array_inv) > 0) {
        try {
            // commit
            $oIfx->QueryT('BEGIN WORK;');

            foreach ($array_inv as $val) {
                $ppr_cod_ppr     = $val[0];
                $ppr_pre_raun    = $val[1];
                $prod_cod        = $val[2];
                $bode_cod        = $val[3];
                $nomp_cod_nomp   = $val[4];
                $precio          = $aForm[$nomp_cod_nomp];


                if ($copiar_todas_sucursales == 'S') {
                    $sql_todas_bodegas = "SELECT subo_cod_empr, subo_cod_sucu, subo_cod_bode, prbo_cod_prod
                                            FROM saesubo 
                                            inner join saeprbo
                                                on prbo_cod_bode = subo_cod_bode 
                                            where 
                                                prbo_cod_prod = '$prod_cod'
                                            and subo_cod_empr = $idempresa";
                    if ($oIfxA->Query($sql_todas_bodegas)) {
                        if ($oIfxA->NumFilas() > 0) {
                            do {
                                $sucursal = $oIfxA->f('subo_cod_sucu');
                                $bodega = $oIfxA->f('subo_cod_bode');

                                $sql_existe_ppr = "SELECT count(*) as contador from saeppr where 
                                                ppr_cod_prod = '$prod_cod'
                                                and ppr_cod_bode = $bodega
                                                and ppr_cod_sucu = $sucursal
                                                and ppr_cod_empr = $idempresa
                                                and ppr_cod_nomp = $nomp_cod_nomp
                                                ";
                                $contador = consulta_string_func($sql_existe_ppr, 'contador', $oIfx, 0);

                                if ($contador > 0) {
                                    // UPDATE
                                    $sql = "update saeppr set ppr_pre_raun = '$precio' where
                                                ppr_cod_empr = $idempresa and
                                                ppr_cod_sucu = $sucursal and
                                                ppr_cod_bode = $bodega and
                                                ppr_cod_prod = '$prod_cod' and
                                                ppr_cod_nomp = $nomp_cod_nomp and
                                                ppr_cod_ppr  = $ppr_cod_ppr	";
                                } elseif ($contador == 0) {
                                    // INGRESO
                                    $sql = "select  max(ppr_cod_ppr) ppr_cod
                                                from saeppr where
                                                ppr_cod_empr = $idempresa and
                                                ppr_cod_sucu = $sucursal and
                                                ppr_cod_bode = $bodega and
                                                ppr_cod_prod = '$prod_cod' ";
                                    $serial    = consulta_string_func($sql, 'ppr_cod', $oIfx, 0) + 1;

                                    $sql = "insert into saeppr ( ppr_cod_ppr, 		ppr_cod_prod, 		ppr_cod_bode,		ppr_cod_empr,
												 ppr_cod_sucu, 		ppr_pre_raun,		ppr_cod_nomp )
										values ( $serial,			'$prod_cod',		$bodega,			$idempresa,
												 $sucursal,		$precio,		    $nomp_cod_nomp
											   )";
                                }
                                $oIfx->QueryT($sql);
                            } while ($oIfxA->SiguienteRegistro());
                        }
                    }
                    $oIfxA->Free();
                } else {

                    $sql_existe_ppr = "SELECT count(*) as contador from saeppr where 
                                                ppr_cod_prod = '$prod_cod'
                                                and ppr_cod_bode = $bode_cod
                                                and ppr_cod_sucu = $idsucursal
                                                and ppr_cod_empr = $idempresa
                                                and ppr_cod_nomp = $nomp_cod_nomp
                                                ";
                    $contador = consulta_string_func($sql_existe_ppr, 'contador', $oIfx, 0);


                    if ($contador > 0) {
                        // UPDATE
                        $sql = "update saeppr set ppr_pre_raun = '$precio' where
                                    ppr_cod_empr = $idempresa and
                                    ppr_cod_sucu = $idsucursal and
                                    ppr_cod_bode = $bode_cod and
                                    ppr_cod_prod = '$prod_cod' and
                                    ppr_cod_nomp = $nomp_cod_nomp and
                                    ppr_cod_ppr  = $ppr_cod_ppr	";
                    } elseif ($contador == 0) {
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
                }
            } // fin fpreach


            $oIfx->QueryT('COMMIT WORK;');
            $oReturn->alert('Precios Almacenados Correctamenete');
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
        $ret_fec_auto = consulta_string($sql, 'retp_fech_cadu', $oIfx, date("Y-m-d"));
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

        $ifu->AgregarCampoArchivo('archivo', 'Archivo|left', false, '', 100, 100, '');

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
        $sHtml .= '<td>' . $ifu->ObjetoHtmlLBL('archivo') . '</td>';
        $sHtml .= '<td>' . $ifu->ObjetoHtml('archivo') . '</td>';
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

    $archivo = substr($aForm['archivo'], 3);
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


    $diario = generar_diarios_ingresos_pdf($idempresa, $idsucursal, $asto_cod, $ejer_cod, $prdo_cod);
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
        $sql_tmp = " and (clpv_nom_clpv like '%$clpv_nom%' or clpv_cod_char='$clpv_nom')";
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

    $sHtml .= ' <table id="tbcompras"  class="table table-striped table-condensed table-bordered table-hover" style="width: 98%; margin-top: 20px;" align="center">';
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
                        $sql_tmp $sql_tmp2  order by 2 limit 50";

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


function clpv_reporte_lista($aForm = '')
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
    $clpv_nom   = $aForm['cliente_nombre_listac'];
    $clpv_ruc   = $aForm['ruc'];

    $sql_tmp = '';
    if (!empty($clpv_nom)) {
        $sql_tmp = " and (clpv_nom_clpv like '%$clpv_nom%' OR clpv_ruc_clpv like '%$clpv_nom%')";
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

    $sHtml .= ' <table id=""  class="table table-striped table-condensed table-bordered table-hover" style="width: 98%; margin-top: 20px;" align="center">';
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
                        $sql_tmp $sql_tmp2  order by 2 limit 50";

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
                            onClick="javascript:datos_clpv_lista( \'' . $clpv_cod_clpv . '\', \'' . $clpv_nom_clpv . '\' , \'' . $clpv_ruc_clpv . '\',  \'' . $dire . '\',
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
    // $oReturn->script("init()");

    return $oReturn;
}

// REPORT PRODCUTO INVETANARIO
function producto_inventario($aForm = '')
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
    $bode_cod   = $aForm['bodega'];
    $prod_nom   = $aForm['producto'];


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
                        <td class="fecha_letra" align="center">Serie</td>
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


    // No se hace uso de la vista porque no actualiza los campos al moemnto de utilizarlos. 
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
                    $prbo_cod_prod,  $prod_nom_prod, $prbo_cta_inv,
                    $prbo_cta_ideb,  $prbo_uco_prod, $prbo_iva_porc,
                    $lote,           $serie
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

                $sHtml .= '<tr  height="20" style="cursor: pointer"  onClick="javascript:datos_prod( \'' . $i . '\' ,
                \'' . $lote . '\',      \'' . $serie . '\',      \'' . $mac . '\'  )" >';

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
    $oReturn->script("init_prod()");
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
                        dmov_can_dmov <> dmov_can_entr  and
                        ( minv_cer_sn is null or  minv_cer_sn = 'N' ) ";

    //$oReturn->alert($sql);
    $i = 1;
    unset($array);
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            do {
                $fec_pedi         = fecha_mysql_func($oIfx->f('minv_fmov'));
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
                        <td class="info" align="center">Cantidad Recib</td>                        
                        <td class="info" align="center">Costo</td>
						<td class="info" align="center">Total</td>
                    </tr>';

    $sql = "select dmov_cod_prod, dmov_cod_bode, dmov_cod_unid,
				dmov_can_dmov, dmov_can_entr, dmov_cun_dmov, dmov_cto_dmov
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
                $dmov_can_entr      = $oIfx->f('dmov_can_entr');

                $sClass = ($sClass == 'off') ? $sClass = 'on' : $sClass = 'off';
                $sHtml .= '<tr height="20" class="' . $sClass . '"
										onMouseOver="javascript:this.className=\'link\';"
										style="cursor: hand !important; cursor: pointer !important;"
										onMouseOut="javascript:this.className=\'' . $sClass . '\';"	>';

                $sHtml .= '<td>' . $i . '</td>';
                $sHtml .= '<td>' . $codigo . '</td>';
                $sHtml .= '<td>' . $nom_prod . '</td>';
                $sHtml .= '<td align="right">' . $cant . '</td>';
                $sHtml .= '<td align="right">' . $dmov_can_entr . '</td>';
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
                $sql = "select  d.dmov_cod_prod, d.dmov_cod_bode, d.dmov_cod_unid,  d.dmov_iva_porc,
                               (d.dmov_can_dmov - d.dmov_can_entr) as cantidad, 
                                p.prbo_cta_inv, p.prbo_cta_ideb, p.prbo_iva_porc, d.dmov_cun_dmov, dmov_det1_dmov, dmov_cod_lote, dmov_cod_dmov, dmov_cod_serie
                                from saedmov d , saeprbo p where
                                p.prbo_cod_prod = d.dmov_cod_prod and
                                d.dmov_cod_bode = p.prbo_cod_bode and
                                p.prbo_cod_empr = $id_empresa and
                                p.prbo_cod_sucu = $id_sucursal and
                                d.dmov_num_comp = $serial and
                                d.dmov_cod_empr = $id_empresa and
                                d.dmov_cod_sucu = $id_sucursal order by d.dmov_cod_dmov ";

                ///   echo $sql;exit;


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
                            $dmov_cod   = $oIfx->f('dmov_cod_dmov');
                            $serie_lote  = $oIfx->f('dmov_cod_serie');

                            $sql        = "select prod_nom_prod, prod_ser_prod, prod_aut_cons from saeprod where prod_cod_empr = $id_empresa and prod_cod_sucu = $id_sucursal and prod_cod_prod = '$prod_cod' ";
                            //$prod_nom   = consulta_string_func($sql, 'prod_nom_prod', $oIfxA, '');
                            $prod_nom   = '';
                            $prod_ser_prod   = '';
                            $prod_aut_cons   = '';
                            if ($oIfxA->Query($sql)) {
                                if ($oIfxA->NumFilas() > 0) {
                                    do {
                                        $prod_nom = $oIfxA->f('prod_nom_prod');
                                        $prod_ser_prod = $oIfxA->f('prod_ser_prod');
                                        $prod_aut_cons = $oIfxA->f('prod_aut_cons');
                                    } while ($oIfxA->SiguienteRegistro());
                                }
                            }
                            $oIfxA->Free();
                            // -------------------------------------------------------------------------------------------------------------------
                            // Verificamos si el producto tiene serie y es autoincremental
                            // -------------------------------------------------------------------------------------------------------------------

                            if ($prod_ser_prod == 'S' && $prod_aut_cons == 'S') {
                                $minv_num_comp = $serial;
                                $div_agrega_codigo_unico = '<div id ="imagen1" class="btn btn-success btn-sm" onclick="agregar_series(\'' . $minv_num_comp . '\', \'' . $prod_cod . '\', \'' . $dmov_cod . '\', \'' . $cont . '\')">
                                                    <span class="glyphicon glyphicon-plus"></span>
                                                </div>';


                                $sql_data_series = "SELECT * from series_oc where minv_num_comp = $serial and prod_cod_prod = '$prod_cod' and dmov_cod_dmov = $dmov_cod";
                                $codigos_unicos_rec = '';
                                if ($oIfx->Query($sql_data_series)) {
                                    if ($oIfx->NumFilas() > 0) {
                                        do {
                                            $codigos_unicos_rec = $oIfx->f('codigos_unicos_rec');
                                        } while ($oIfx->SiguienteRegistro());
                                    }
                                }
                                $oIfx->Free();

                                $cont_receibidos = 0;
                                $array_codigos_unicos = explode(",", $codigos_unicos_rec);
                                foreach ($array_codigos_unicos as $key => $codigo_recibido) {
                                    if ($codigo_recibido == $codigo_unico_prod) {
                                        $existe_codigo_recibido = 'S';
                                    }
                                    $cont_receibidos++;
                                }

                                $cantidad = $cont_receibidos - 1;
                            }

                            // -------------------------------------------------------------------------------------------------------------------
                            // FIN Verificamos si el producto tiene serie y es autoincremental
                            // -------------------------------------------------------------------------------------------------------------------















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

                            if ($iva == 0) {
                                $sql_prbo = "SELECT prbo_iva_porc FROM saeprbo where prbo_cod_prod='$prod_cod'";
                                $iva  = consulta_string_func($sql_prbo, 'prbo_iva_porc', $oIfxA, 0);
                            }
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
                            $aDataGrid[$cont][$aLabelGrid[24]] = '';
                            $aDataGrid[$cont][$aLabelGrid[25]] = '';
                            $aDataGrid[$cont][$aLabelGrid[26]] = $div_agrega_codigo_unico;
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
                // $minv_fec           = $c . '-' . $b . '-' . $a;
                $oReturn->script('cargar_fecha_reco( \'' . $minv_fec . '\' );');

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

// --------------------------------------------------------------------------------------
// Modal Capturar codigos unicos recibidos
// --------------------------------------------------------------------------------------
function agregar_series($num_comp_oc = 0, $cod_prod_oc = '', $dmov_cod = 0, $cont_datagrid = 0, $aForm = '')
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

    try {

        $html_area_prod = '';
        $table_producto = '

                            <div class="row">
                                <div class="col-md-6">
                                    <h2>Codigo Unico del SKU</h2>                                
                                    <label class="control-label" for="cliente_nombre">Al digitar el codigo unico, se agrega a la lista de codigo recibido</label>                                
                                    <div class="input-group">
                                        <input class="form-control input-sm" type="text" name="codigo_unico_prod" id="codigo_unico_prod" onchange="consultar_codigo_unico(\'' . $num_comp_oc . '\', \'' . $cod_prod_oc . '\', \'' . $dmov_cod . '\', \'' . $cont_datagrid . '\');" value="">
                                        <span class="input-group-addon primary" style="cursor: pointer;" onClick="consultar_codigo_unico(\'' . $num_comp_oc . '\', \'' . $cod_prod_oc . '\', \'' . $dmov_cod . '\', \'' . $cont_datagrid . '\');"><i class="fa fa-search"></i></span>
                                    </div>  
                                </div>
                            </div>

                        <table id="" class="table table-bordered table-hover table-striped table-condensed" style="margin-top: 30px; width: 100%;">
                            <thead>
                                <tr>
                                    <td class="" style="width: 0.5%; color: #; font-weight: 900" colspan="">N.-</td>
                                    <td class="" style="width: 4.5%; color: #; font-weight: 900" colspan="">Codigo Enviado</td>
                                    <td class="" style="width: 4.5%; color: #; font-weight: 900" colspan="">Codigo Recibido</td>
                                    <td class="" style="width: 1.5%; color: #; font-weight: 900" colspan="">Cantidad</td>
                                </tr>
                            </thead>
                            <tbody>';

        $sql_data_series = "SELECT * from series_oc where minv_num_comp = $num_comp_oc and prod_cod_prod = '$cod_prod_oc' and dmov_cod_dmov = $dmov_cod";
        $codigos_unicos = consulta_string($sql_data_series, 'codigos_unicos', $oIfx, '');
        $codigos_unicos = str_replace('[', '',  $codigos_unicos);
        $codigos_unicos = str_replace(']', '',  $codigos_unicos);
        $array_codigos_unicos = explode(",", $codigos_unicos);

        foreach ($array_codigos_unicos as $key => $codigo_unico) {
            $key = $key + 1;
            $table_producto .= '<tr>
                                    <td style="mso-number-format:\@;">' . $key . '</td>
                                    <td style="mso-number-format:\@; text-align: center">' . $codigo_unico . '</td>
                                    <td style="mso-number-format:\@; text-align: center; display: flex;justify-content: space-between;align-items: center"><div id="serie_' . $codigo_unico . '" name="serie_' . $codigo_unico . '"></div><div id="elimina_' . $codigo_unico . '" name="elimina_' . $codigo_unico . '"></div></td>
                                    <td style="mso-number-format:\@; text-align: right"><div id="cant_' . $codigo_unico . '" name="cant_' . $codigo_unico . '"></div></td>
                                </tr>';
        }


        unset($_SESSION['ArrayCodigosUnicos']);
        $_SESSION['ArrayCodigosUnicos'] = $array_codigos_unicos;

        $table_producto .= '</tbody>
                        </table>';


        $html_area_prod .= '<div class="row">
                                <div class="col-md-12">';

        $html_area_prod .= $table_producto;

        $html_area_prod .= '
                                </div>
                            </div>

                            ';

        $modal = '<div id="mostrarModalCodUnic" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg" style="width:1100px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title"><b>ETIQUETADO DE CODIGOS UNICOS</b></h4>
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

        $oReturn->assign("divFormularioModalCodigoUnico", "innerHTML", $modal);
        $oReturn->script("abre_modal_codigo_unico();");
        $oReturn->script('verificar_codigos_u_insertados(\'' . $num_comp_oc . '\', \'' . $cod_prod_oc . '\', \'' . $dmov_cod . '\', \'' . $cont_datagrid . '\');');
    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

    return $oReturn;
}

function consultar_codigo_unico($num_comp_oc = 0, $cod_prod_oc = '', $dmov_cod = 0,  $cont_datagrid = 0, $aForm = '')
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
    $codigo_unico_prod = $aForm['codigo_unico_prod'];

    $array_codigos_unicos = $_SESSION['ArrayCodigosUnicos'];
    try {

        $existe_serie = 'N';
        foreach ($array_codigos_unicos as $key => $codigo_unico_oc) {
            if ($codigo_unico_oc == $codigo_unico_prod) {
                $existe_serie = 'S';
            }
        }

        $existe_codigo_recibido = 'N';
        if ($existe_serie == 'S') {

            $sql_data_series = "SELECT * from series_oc where minv_num_comp = $num_comp_oc and prod_cod_prod = '$cod_prod_oc' and dmov_cod_dmov = $dmov_cod";
            //$codigos_unicos_rec = consulta_string($sql_data_series, 'codigos_unicos_rec', $oIfx, '');
            $codigos_unicos_rec = '';
            $codigos_unicos_tot = '';
            if ($oIfx->Query($sql_data_series)) {
                if ($oIfx->NumFilas() > 0) {
                    do {
                        $codigos_unicos_rec = $oIfx->f('codigos_unicos_rec');
                        $codigos_unicos_tot = $oIfx->f('codigos_unicos_tot');
                    } while ($oIfx->SiguienteRegistro());
                }
            }
            $oIfx->Free();

            $cont_receibidos = 0;
            $array_codigos_unicos = explode(",", $codigos_unicos_rec);
            foreach ($array_codigos_unicos as $key => $codigo_recibido) {
                if ($codigo_recibido == $codigo_unico_prod) {
                    $existe_codigo_recibido = 'S';
                }
                $cont_receibidos++;
            }

            $array_codigos_tot = explode(",", $codigos_unicos_tot);
            foreach ($array_codigos_tot as $key2 => $codigo_totales) {
                if ($codigo_totales == $codigo_unico_prod) {
                    $existe_codigo_recibido = 'S';
                }
            }

            if ($existe_codigo_recibido == 'N') {
                $codigos_unicos_rec = $codigos_unicos_rec . ',' . $codigo_unico_prod;
                $codigos_unicos_tot = $codigos_unicos_tot . ',' . $codigo_unico_prod;
                $cont_receibidos++;
                $html_boton_elimina = '<div id ="imagen1" title="Eliminar entrega" class="btn btn-danger btn-sm" onclick="eliminar_codigo_unico_recep(\'' . $num_comp_oc . '\', \'' . $cod_prod_oc . '\', \'' . $dmov_cod . '\', \'' . $codigo_unico_prod . '\', \'' . $cont_datagrid . '\');">
                                            <i class="fa fa-trash"></i>
                                        </div>';
                $oReturn->assign("elimina_" . $codigo_unico_prod, "innerHTML", $html_boton_elimina);
            }

            $cont_receibidos = $cont_receibidos - 1;
            $aDataGrid = $_SESSION['aDataGird_INV_MRECO'];

            $cantidad_datagrid = $aDataGrid[$cont_datagrid]['Cantidad'];
            $costo_datagrid = $aDataGrid[$cont_datagrid]['Costo'];
            $totales_cant_cost = round($cont_receibidos * $costo_datagrid, 2);

            $aDataGrid[$cont_datagrid]['Cantidad'] = $cont_receibidos;
            $aDataGrid[$cont_datagrid]['Total'] = $totales_cant_cost;
            $aDataGrid[$cont_datagrid]['Total Con Impuesto'] = $totales_cant_cost;

            $_SESSION['aDataGird_INV_MRECO'] = $aDataGrid;
            $sHtml = mostrar_grid();
            $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
            $oReturn->script('habilita(5)');
            $oReturn->script('totales();');


            $sql_update_series_oc = "UPDATE series_oc set codigos_unicos_rec = '$codigos_unicos_rec', codigos_unicos_tot = '$codigos_unicos_tot' where  minv_num_comp = $num_comp_oc and prod_cod_prod = '$cod_prod_oc' and dmov_cod_dmov = $dmov_cod";
            $oIfx->QueryT($sql_update_series_oc);


            $oReturn->assign("serie_" . $codigo_unico_prod, "innerHTML", $codigo_unico_prod);
            $oReturn->assign("cant_" . $codigo_unico_prod, "innerHTML", 1);
            $oReturn->script("document.getElementById('codigo_unico_prod').value = ''");
            $oReturn->script("document.getElementById('codigo_unico_prod').focus();");
            //$oReturn->script("document.getElementById('serie_$codigo_unico_prod').style.backgroundColor = 'green';");
        } else {
            $oReturn->script("Swal.fire({
                                    position: 'center',
                                    type: 'error',
                                    title: 'Serie: $codigo_unico_prod No existe !',
                                    showConfirmButton: true,
                                    confirmButtonText: 'Aceptar',
                                    timer: 2000
                                })");
        }
    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

    return $oReturn;
}

function verificar_codigos_u_insertados($num_comp_oc = 0, $cod_prod_oc = '', $dmov_cod = 0, $cont_datagrid, $aForm = '')
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
    $codigo_unico_prod = $aForm['codigo_unico_prod'];

    try {
        $sql_data_series = "SELECT * from series_oc where minv_num_comp = $num_comp_oc and prod_cod_prod = '$cod_prod_oc' and dmov_cod_dmov = $dmov_cod";
        $codigos_unicos_rec = array();
        $codigos_unicos_tot = array();
        if ($oIfx->Query($sql_data_series)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    $codigos_unicos_rec = $oIfx->f('codigos_unicos_rec');
                    $codigos_unicos_tot = $oIfx->f('codigos_unicos_tot');
                } while ($oIfx->SiguienteRegistro());
            }
        }
        $oIfx->Free();

        $array_codigos_unicos = explode(",", $codigos_unicos_rec);
        foreach ($array_codigos_unicos as $key => $codigo_recibido) {


            $html_boton_elimina = '<div id ="imagen1" title="Eliminar entrega" class="btn btn-danger btn-sm" onclick="eliminar_codigo_unico_recep(\'' . $num_comp_oc . '\', \'' . $cod_prod_oc . '\', \'' . $dmov_cod . '\', \'' . $codigo_recibido . '\', \'' . $cont_datagrid . '\');">
                                            <i class="fa fa-trash"></i>
                                        </div>';

            $oReturn->assign("serie_" . $codigo_recibido, "innerHTML", $codigo_recibido);
            $oReturn->assign("cant_" . $codigo_recibido, "innerHTML", 1);
            $oReturn->assign("elimina_" . $codigo_recibido, "innerHTML", $html_boton_elimina);
            $oReturn->script("document.getElementById('codigo_unico_prod').value = ''");
            $oReturn->script("document.getElementById('codigo_unico_prod').focus();");
        }

        $array_codigos_totales = explode(",", $codigos_unicos_tot);
        foreach ($array_codigos_totales as $key2 => $codigo_totales) {
            $oReturn->assign("serie_" . $codigo_totales, "innerHTML", $codigo_totales);
            $oReturn->assign("cant_" . $codigo_totales, "innerHTML", 1);
            $oReturn->script("document.getElementById('codigo_unico_prod').value = ''");
            $oReturn->script("document.getElementById('codigo_unico_prod').focus();");
        }
    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

    return $oReturn;
}

function eliminar_codigo_unico_recep($num_comp_oc = 0, $cod_prod_oc = '', $dmov_cod = 0, $codigo_unico, $cont_datagrid, $aForm = '')
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

    try {


        $sql_data_series = "SELECT * from series_oc where minv_num_comp = $num_comp_oc and prod_cod_prod = '$cod_prod_oc' and dmov_cod_dmov = $dmov_cod";
        //$codigos_unicos_rec = consulta_string($sql_data_series, 'codigos_unicos_rec', $oIfx, '');
        $codigos_unicos_rec = '';
        $codigos_unicos_tot = '';
        if ($oIfx->Query($sql_data_series)) {
            if ($oIfx->NumFilas() > 0) {
                do {
                    $codigos_unicos_rec = $oIfx->f('codigos_unicos_rec');
                    $codigos_unicos_tot = $oIfx->f('codigos_unicos_tot');
                } while ($oIfx->SiguienteRegistro());
            }
        }
        $oIfx->Free();

        $texto_remplazar = ',' . $codigo_unico;
        $codigos_unicos_rec = str_replace($texto_remplazar, "", $codigos_unicos_rec);
        $codigos_unicos_tot = str_replace($texto_remplazar, "", $codigos_unicos_tot);

        $sql_update_series_oc = "UPDATE series_oc set codigos_unicos_rec = '$codigos_unicos_rec', codigos_unicos_tot = '$codigos_unicos_tot' where  minv_num_comp = $num_comp_oc and prod_cod_prod = '$cod_prod_oc' and dmov_cod_dmov = $dmov_cod";
        $oIfx->QueryT($sql_update_series_oc);

        $oReturn->script("document.getElementById('codigo_unico_prod').value = ''");
        $oReturn->script("document.getElementById('codigo_unico_prod').focus();");



        $oReturn->assign("serie_" . $codigo_unico, "innerHTML", '');
        $oReturn->assign("cant_" . $codigo_unico, "innerHTML", '');
        $oReturn->assign("elimina_" . $codigo_unico, "innerHTML", '');

        // -----------------------------------------------------------------------------------------
        // Actualizar costo y cantidad en aDatagrid
        // -----------------------------------------------------------------------------------------

        $cont_receibidos = 0;
        $array_codigos_unicos = explode(",", $codigos_unicos_rec);
        foreach ($array_codigos_unicos as $key => $codigo_recibido) {
            if ($codigo_recibido == $codigo_unico_prod) {
                $existe_codigo_recibido = 'S';
            }
            $cont_receibidos++;
        }
        $cont_receibidos = $cont_receibidos - 1;
        $aDataGrid = $_SESSION['aDataGird_INV_MRECO'];

        $cantidad_datagrid = $aDataGrid[$cont_datagrid]['Cantidad'];
        $costo_datagrid = $aDataGrid[$cont_datagrid]['Costo'];
        $totales_cant_cost = round($cont_receibidos * $costo_datagrid, 2);

        $aDataGrid[$cont_datagrid]['Cantidad'] = $cont_receibidos;
        $aDataGrid[$cont_datagrid]['Total'] = $totales_cant_cost;
        $aDataGrid[$cont_datagrid]['Total Con Impuesto'] = $totales_cant_cost;

        $_SESSION['aDataGird_INV_MRECO'] = $aDataGrid;
        $sHtml = mostrar_grid();
        $oReturn->assign("divFormularioDetalle", "innerHTML", $sHtml);
        $oReturn->script('habilita(5)');
        $oReturn->script('totales();');

        // -----------------------------------------------------------------------------------------
        // Actualizar costo y cantidad en aDatagrid
        // -----------------------------------------------------------------------------------------


    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

    return $oReturn;
}


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

// --------------------------------------------------------------------------------------
// FIN Modal Capturar codigos unicos recibidos
// --------------------------------------------------------------------------------------


/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
/* PROCESO DE REQUEST DE LAS FUNCIONES MEDIANTE AJAX NO MODIFICAR */
$xajax->processRequest();
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
