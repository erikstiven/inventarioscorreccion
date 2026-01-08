<?
include_once('../../Include/config.inc.php');

include_once(path(DIR_INCLUDE) . 'conexiones/db_conexion.php');
include_once(path(DIR_INCLUDE) . 'comun.lib.php');
require_once 'html2pdf_v4.03_/html2pdf.class.php';

require_once(path(DIR_INCLUDE) .'codigo_de_barras/nuevo_barcode/class/BCGFontFile.php');
require_once(path(DIR_INCLUDE) .'codigo_de_barras/nuevo_barcode/class/BCGColor.php');
require_once(path(DIR_INCLUDE) .'codigo_de_barras/nuevo_barcode/class/BCGDrawing.php');
require_once(path(DIR_INCLUDE) .'codigo_de_barras/nuevo_barcode/class/BCGcode128.barcode.php');

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
$ancho = $_GET['ancho'];
$alto = $_GET['alto'];

$array = $_SESSION['LIST_CHECK_ETIQUETAS'];
    //query medidad
/*	$sql = "select ancho, alto, unidad,
			maximo
			from comercial.medida_etiq
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
	$oCnx->Free();*/


    $ancho_conv = $ancho * 60;
	$alto_conv = $alto * 60;



	if (count($array) > 0) {
    
        foreach ($array as $val) {
			$marca = $val[0];
			$color = $val[1];
			$talla = $val[2];
			$preci = $val[3];
			$can   = $val[4];
			$prod   = $val[5];

            $cod_prod=$prod;

            $idempresa   = $val[6];
            if(empty($idempresa)) $id_empresa= $_SESSION['U_EMPRESA'];
            $sucursal   = $val[7];
            if(empty($sucursal)) $sucursal= $_SESSION['U_SUCURSAL'];
            $bode_cod   = $val[8];
            
                
            //$nombArch1 = $prod;         
           // $rutaCodi1 = DIR_FACTELEC . 'include/archivos/' . $nombArch1 . '.gif';
            //new barCodeGenrator($prod, 1, $rutaCodi1, 100, 60, true);
            
            $nom_arch=$prod;
			$nom_arch=str_replace(':','',$nom_arch);
			$nom_arch=str_replace('/','',$nom_arch);
			$nom_arch=str_replace('<','',$nom_arch);
			$nom_arch=str_replace('>','',$nom_arch);
			$nom_arch=str_replace('?','',$nom_arch);
			

            $ImagenQRProd = $nom_arch.'.png';

            $nombre_fichero = '../crear_prod_ter_inv/libreria_QR/barras/' . $nom_arch . '.png';

						if (!file_exists($nombre_fichero)) {
                            generar_cod_barra($prod);
                        }
	$sql_nom_prod = "SELECT prod_nom_prod, prod_apli_prod FROM saeprod where prod_cod_prod = '$cod_prod'";
	$nom_prod = consulta_string($sql_nom_prod, 'prod_nom_prod', $oIfx, '');
	$apli_prod = consulta_string($sql_nom_prod, 'prod_apli_prod', $oIfx, '');
	$apli_prod =strip_tags($apli_prod );
	//$apli_prod=substr($apli_prod,0,130);

	//ULTIMO COSTO DEL PRODUCTO
	$sqlu = "select bode_nom_bode, prod_cod_prod,prod_nom_prod, prod_des_prod,   medi_des_medi, 
											prbo_smi_prod, prbo_sma_prod, prbo_ped_prod, prbo_dis_prod,
											prbo_fec_ucom, prbo_pco_prod, prbo_fec_uven, prbo_pve_prod, unid_sigl_unid, prbo_uco_prod
											from saeprod, saeprbo, saebode, saemedi, saeunid
											where prod_cod_prod = prbo_cod_prod
											and prod_cod_empr = prbo_cod_empr
											and prod_cod_sucu = prbo_cod_sucu
											and prbo_cod_bode = bode_cod_bode
											and prbo_cod_empr = bode_cod_empr
											and prod_cod_medi = medi_cod_medi
											and prod_cod_empr = medi_cod_empr
											and prbo_cod_unid = unid_cod_unid
											and prbo_cod_empr = unid_cod_empr
											and prod_cod_empr = $idempresa
											and prod_cod_sucu = $sucursal
											and prbo_cod_bode = $bode_cod
											and prod_cod_prod = '$cod_prod' ";
	$ultimo_costo=consulta_string($sqlu,'prbo_uco_prod', $oIfx, 0);
	$ultimo_costo=number_format($ultimo_costo,2,'.','');

	//PVP DEL PRODUCTO

	$sql="select ppr_pre_raun from saeppr where ppr_cod_prod='$cod_prod'  and ppr_cod_empr=$idempresa and ppr_cod_sucu=$sucursal 
	and ppr_cod_bode=$bode_cod and ppr_cod_nomp in (select nomp_cod_nomp from saenomp
	where nomp_nomb_nomp like '%PVP%' and nomp_cod_empr =$idempresa and nomp_cod_sucu=$sucursal)";
	$pvp=consulta_string($sql,'ppr_pre_raun', $oIfx, 0);
	$pvp=number_format($pvp,2,'.','');

	

	$sql_nom_empr = "SELECT empr_nom_empr FROM saeempr where empr_cod_empr = '$idempresa'";
	$nom_empr = consulta_string($sql_nom_empr, 'empr_nom_empr', $oIfx, '');

	$sql="SELECT sucu_cod_prec, sucu_cod_cost from saesucu where sucu_cod_sucu=$sucursal";
	$sucu_precio=consulta_string($sql, 'sucu_cod_prec', $oIfx, '');
	$sucu_costo=trim(consulta_string($sql, 'sucu_cod_cost', $oIfx, ''));


	//NOMENCALTURA COSTO 
	$nomc_costo='';
	if(strlen($sucu_costo)>0){

		$ultimo_costo=strval($ultimo_costo);
		$array_cos=str_split($sucu_costo);
		
		if(count($array_cos)>0){
			$i=1;
			$tam=count($array_cos);
			foreach($array_cos as $vcos){

				$ultimo_costo=str_replace($i,$vcos, $ultimo_costo);

				if($i==$tam){
					$ultimo_costo=str_replace('0',$vcos, $ultimo_costo);
				}
				$i++;
			}
			$nomc_costo=$ultimo_costo;
		}
	}

		//NOMENCALTURA PRECIO 
	$nomc_pvp='';
	if(strlen($sucu_precio)>0){

		$pvp=strval($pvp);
		$array_pvp=str_split($sucu_precio);
		
		if(count($array_pvp)>0){
			$i=1;
			$tam=count($array_pvp);
			foreach($array_pvp as $vcos){

				$pvp=str_replace($i,$vcos, $pvp);

				if($i==$tam){
					$pvp=str_replace('0',$vcos, $pvp);
				}
				$i++;
			}
			$nomc_pvp=$pvp;
		}
	}

	//FECHA DEL ULTIMO MOVIMIENTO DE COMPRA
	$sqlcomp="SELECT 
	minv_fmov,
	minv_cod_clpv
	
from 
	saeminv
	inner join saedmov 
		on dmov_num_comp = minv_num_comp
where 
	minv_cod_tran in (
		select 
				t.tran_cod_tran
		from 
				saetran t, saedefi d
			where
                                    t.tran_cod_tran = d.defi_cod_tran and
                                    t.tran_cod_empr = $idempresa and
                                    --t.tran_cod_sucu = 1 and
                                    t.tran_cod_modu = 10 and
                                    d.defi_cod_empr = $idempresa and
                                    d.defi_tip_defi = '0' and
                                    d.defi_cod_modu = 10 
			)
			and minv_cod_empr = $idempresa
			and (minv_cod_clpv != 0 AND minv_cod_clpv is not null)
			and dmov_cod_prod = '$cod_prod'
			order by minv_fmov DESC
			limit 1";

		$cod_clpv=consulta_string($sqlcomp, 'minv_cod_clpv', $oIfx, '');
		$fecha_compra=consulta_string($sqlcomp, 'minv_fmov', $oIfx, '');

		$inf_comp='';
		if(!empty($cod_clpv)){
			$fec=date('m/y',strtotime($fecha_compra));
			$inf_comp=$cod_clpv.' '.$fec;
		}


            	//FORMATO ETIQUETA

	$sql="select ftrn_cod_html from saeftrn where ftrn_des_ftrn='CODIGO DE BARRAS' and ftrn_cod_modu=10 and 
	ftrn_cod_empr=$idempresa";

	$ftrn_barra=consulta_string($sql, 'ftrn_cod_html', $oIfx, '');
	$html_barra=consulta_string($sql, 'ftrn_cod_html', $oIfx, '');
	if(empty($ftrn_barra)){
		$html_barra='<img src="../crear_prod_ter_inv/libreria_QR/barras/' . $ImagenQRProd . '" style="width:' . $ancho_conv . 'px; height:' . $alto_conv . 'px; ">
		<p style="margin-top: 25px; font-size: size_prod !important">' . $nom_prod . '</p> 
		<h5 style="margin-top: -30px; font-size: size_empr !important"><b>' . $nom_empr . '</b></h5>';
	}
	else{

		//VALIDACION TIPO DE FORMATO
		$ctrl_pdf='N';
		if(preg_match("/tipoPdf/i",$html_barra)){
			$html_barra=str_replace("tipoPdf",'', $html_barra);
			$ctrl_pdf='S';
		}

		/*
		<div align="center"><font size="0.2"><b>LUBAMAQUI CIA. LTDA.</b></font><br>
		img_qr
		<br><font size="0.2">nombre_producto<br>aplicacion_producto<br>sucu_precio / sucu_costo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;fecha_comp</font></div>

		
		<h5 style="margin-top: -30px; font-size: size_empr !important"><b>nombre_empresa</b></h5>
		img_qr
		<p style="margin-top: 25px; font-size: size_prod !important">nombre_producto<br>aplicacion_producto</p> 
		<div style="font_size: size_prod">sucu_precio / sucu_costo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;fecha_comp</div>
		*/ 
		//$img_qr='<img src="../crear_prod_ter_inv/libreria_QR/barras/' . $ImagenQRProd . '" style="width:' . $ancho_conv . 'px; height:' . $alto_conv . 'px; ">';
	/*	$img_qr='<img src="../crear_prod_ter_inv/libreria_QR/barras/' . $ImagenQRProd . '" style="width:150px; height:50px; ">';
		$html_barra=str_replace('img_qr',$img_qr, $html_barra);
		$html_barra=str_replace('nombre_empresa',$nom_empr, $html_barra);
		$html_barra=str_replace('nombre_producto',$nom_prod, $html_barra);
        $html_barra=str_replace('aplicacion_producto_1',substr($apli_prod,0,30), $html_barra);
		$html_barra=str_replace('aplicacion_producto_2',substr($apli_prod,(strlen($apli_prod)-30),strlen($apli_prod)), $html_barra);
		$html_barra=str_replace('fecha_comp',$inf_comp, $html_barra);
		$html_barra=str_replace('sucu_precio',$nomc_pvp, $html_barra);
		$html_barra=str_replace('sucu_costo',$nomc_costo, $html_barra);

		*/
		

		
		$html_barra = '	<div style="text-align:center;font-size:7px;"><b>LUBAMAQUI CIA. LTDA.</b><br>
		<img src="../crear_prod_ter_inv/libreria_QR/barras/' . $ImagenQRProd . '" style="width:210px; height:55px; ">
		</div>';

		$html_barra .= '<div style="position:relative;text-align:center;font-size:8px; top:-11px; background-color:white; height:20px">
		'.$cod_prod.'
		</div>';
		

		$html_barra .= '<div style="position:relative; left:-0px; top:-12; text-align:center; font-size:7px;">'.$nom_prod.'<br>
		'.substr($apli_prod, 0, 30).'<br>
		'.substr($apli_prod, (strlen($apli_prod) - 30), strlen($apli_prod)).'<br>
		'.$nomc_pvp.'/'.$nomc_costo.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		'.$inf_comp.'
		</div>';
			
		
	}

            for ($i = 1; $i <= $can; $i++) {
                $table .= '<page backtop="0mm" backbottom="0mm" backleft="0mm" backright="0mm">';

                $table.=$html_barra;
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
if($ctrl_pdf=='S'){
	$html2pdf = new HTML2PDF('L', array(59.7,24.7), 'es', 'true', 'UTF-8',0);
	$html2pdf->pdf->SetDisplayMode('fullpage');
	$html2pdf->WriteHTML($table);
	$html2pdf->Output('etiquetas.pdf', '');

}
else{
	echo $table;exit;
}


//echo $table;exit;


function generar_cod_barra($cod_prod)
{
    //Definiciones
    global $DSN, $DSN_Ifx;
    session_start();

    $oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();


    $idempresa = $_SESSION['U_EMPRESA'];


    try {

        $sql_nom_prod = "SELECT prod_cod_barra from saeprod where prod_cod_prod = '$cod_prod' and prod_cod_empr=$idempresa";
        $prod_cod_barra = consulta_string($sql_nom_prod, 'prod_cod_barra', $oIfx, '');

        //VALIDACIONES CARACTERES NO PERMITIDOS
        $nom_arch = $cod_prod;
        $nom_arch = str_replace(':', '', $nom_arch);
        $nom_arch = str_replace('/', '', $nom_arch);
        $nom_arch = str_replace('<', '', $nom_arch);
        $nom_arch = str_replace('>', '', $nom_arch);
        $nom_arch = str_replace('?', '', $nom_arch);


        if (!empty($prod_cod_barra)) {
            $tempDir = '../crear_prod_ter_inv/libreria_QR/barras/' . $nom_arch . '.png';

            $colorFront = new BCGColor(0, 0, 0);
            $colorBack = new BCGColor(255, 255, 255);

            $code = new BCGcode128();
            $code->setScale(2);
            $code->setThickness(30);
            $code->setForegroundColor($colorFront);
            $code->setBackgroundColor($colorBack);
            $code->parse($prod_cod_barra);

            $drawing = new BCGDrawing('', $colorBack);
            $drawing->setBarcode($code);

            $drawing->draw();
            $drawing->finish(BCGDrawing::IMG_FORMAT_PNG, 100, $tempDir);
        }
    } catch (Exception $e) {
       echo $e->getMessage();
    }

    return $prod_cod_barra;
}
?>