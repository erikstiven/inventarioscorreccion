<?
include_once('../../Include/config.inc.php');
include_once(path(DIR_INCLUDE) . 'conexiones/db_conexion.php');
include_once(path(DIR_INCLUDE) . 'comun.lib.php');
require_once 'html2pdf_v4.03_/html2pdf.class.php';
require_once('codigo_de_barras/barcode.inc.php');

if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

$oCnx = new Dbo ( );
$oCnx->DSN = $DSN;
$oCnx->Conectar();

$oIfx = new Dbo;
$oIfx->DSN = $DSN_Ifx;
$oIfx->Conectar();

$oIfxA = new Dbo;
$oIfxA->DSN = $DSN_Ifx;
$oIfxA->Conectar();


//variables de session
$id = $_GET['id'];
$idempresa = $_SESSION['U_EMPRESA'];
$sucursal = $_SESSION['U_SUCURSAL'];
$array = $_SESSION['LIST_CHECK_ETIQUETAS'];

    //query medidad
	$sql = "select ancho, alto, unidad,
			maximo
			from medida_etiq
			where id = $id";
	if($oCnx->Query($sql)){
		if($oCnx->NumFilas() > 0){
			do{
				$ancho = $oCnx->f('ancho');
				$alto = $oCnx->f('alto');
				$unidad = $oCnx->f('unidad');
				$maximo = $oCnx->f('maximo');
			}while($oCnx->SiguienteRegistro());
		}
	}
	$oCnx->Free();



	if (count($array) > 0) {
    
        foreach ($array as $val) {
			$marca = $val[0];
			$color = $val[1];
			$talla = $val[2];
			$preci = $val[3];
			$can   = $val[4];
			$prod   = $val[5];
                
            $nombArch1 = $prod;         
            $rutaCodi1 = DIR_FACTELEC . 'include/archivos/' . $nombArch1 . '.gif';
            new barCodeGenrator($prod, 1, $rutaCodi1, 100, 60, true);
            
            for ($i = 1; $i <= $can; $i++) {
                $table .= '<page backtop="0mm" backbottom="00mm" backleft="0mm" backright="0mm">';
                $table.= '<div style="width: '.$ancho.' '.$unidad.'; height: '.$alto.' '.$unidad.'; margin: 0px; padding: 0px;">';
                $table.= '<table  border="0" style="width: '.$ancho.' '.$unidad.'; height: '.$alto.' '.$unidad.'; margin: 0px; padding: 0px;" >';
                $table.= '<tr>';
                $table.= '<td align="center" style="font-weight: bold; font-size: 15px;">EL GLOBO</td>';
                $table.= '</tr>';
                $table .= '<tr>';
                $table .= '<td align="center" style="font-weight: bold; font-size: 10px;" > ' . $prod . ' T: ' . $talla . ' C:' .$color  . '  </td>';
                $table .= '</tr>';
                $table .= '<tr>';
                $table .= '<td align="center" style="font-weight: bold; font-size: 10px;" > ' .$marca  . '</td>';
                $table .= '</tr>';       
                $table .= '<tr>';
                $table .= '<td align="center" border="0" valign="top" >
                                <table border="0">
                                    <tr>
                                        <td valign="top" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td valign="top" > 
                                            <img src="' . $rutaCodi1 . '"/>
                                        </td>
                                    </tr>
                                </table>
                           </td>';
                $table .= '</tr>';
                $table .= '<tr>';
                $table .= '<td align="center" style="font-weight: bold; font-size: 12px;" > $. ' . $preci . '</td>';
                $table .= '</tr>';
                $table.= '</table>';
                $table.= '</div>';
            
                $table.= '</page>'; 
                
            }//fin for   
        }//fin foreach
	}//fin if

	/*
	select minv_num_comp, minv_fac_prov, * from saeminv 
	where minv_cod_empr = 1 and
	minv_cod_sucu = 1 and
	minv_cod_tran = '002' and
	minv_est_minv <> '0'
	*/
$html2pdf = new HTML2PDF('L', 'A4', 'es', 'true', 'UTF-8');
$html2pdf->WriteHTML($table);
$html2pdf->Output('recibo_template.pdf', '');
?>