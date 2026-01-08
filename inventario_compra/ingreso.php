<? /********************************************************************/ ?>
<? /* NO MODIFICAR ESTA SECCION*/ ?>
<? include_once('../_Modulo.inc.php');?>
<? include_once(HEADER_MODULO);?>
<? if ($ejecuta) { ?>
<? /********************************************************************/ ?>

<script src="js/jquery.min.js" type="text/javascript"></script>
<!-- ejecuta la funci�n mostrar una vez que se carga la p�gina  -->
<script language="javascript">
        window.onload = function() {
            cambiarPestanna('pestanas', 'pestana3');
        }
</script>
<!-- FUNCIONES PARA MANEJO DE PESTA�AS  -->

<!--CSS-->  
    <link rel="stylesheet" type="text/css" href="<?=$_COOKIE["JIREH_INCLUDE"]?>css/bootstrap-3.3.7-dist/css/bootstrap.min.css" media="screen" /><link type="text/css" href="css/style.css" rel="stylesheet"></link>
    <link type="text/css" href="css/style.css" rel="stylesheet"></link>
    <link rel="stylesheet" href="media/css/bootstrap.css">
    <link rel="stylesheet" href="media/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="media/font-awesome/css/font-awesome.css">

    <!--Javascript-->  
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.js"></script>  
    <script src="media/js/jquery-1.10.2.js"></script>
    <script src="media/js/jquery.dataTables.min.js"></script>
    <script src="media/js/dataTables.bootstrap.min.js"></script>          
    <script src="media/js/bootstrap.js"></script>
    <script type="text/javascript" language="javascript" src="<?=$_COOKIE["JIREH_INCLUDE"]?>css/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
	

<script type="text/javascript">

    function cambiarPestanna(pestannas, pestanna) {
            // Obtiene los elementos con los identificadores pasados.
            pestanna = document.getElementById(pestanna.id);
            //alert(pestanna);
            listaPestannas = document.getElementById(pestannas.id);

            // Obtiene las divisiones que tienen el contenido de las pesta�as.
            cpestanna = document.getElementById('c' + pestanna.id);
            tpestanna = document.getElementById('t' + pestanna.id);
            listacPestannas = document.getElementById('contenido' + pestannas.id);

            i = 0;
            // Recorre la lista ocultando todas las pesta�as y restaurando el fondo
            // y el padding de las pesta�as.

            while (typeof listacPestannas.getElementsByTagName('div')[i] != 'undefined') {
                $(document).ready(function() {
                    if (listacPestannas.getElementsByTagName('div')[i].id == "cpestana1"
                            || listacPestannas.getElementsByTagName('div')[i].id == "cpestana2"
                            || listacPestannas.getElementsByTagName('div')[i].id == "tpestana1"
                            || listacPestannas.getElementsByTagName('div')[i].id == "tpestana2"
                            || listacPestannas.getElementsByTagName('div')[i].id == "cpestana3"
                            || listacPestannas.getElementsByTagName('div')[i].id == "tpestana3")
                    {
                        $(listacPestannas.getElementsByTagName('div')[i]).css('display', 'none');
                    }
                });
                i += 1;
            }

            i = 0;
            while (typeof listaPestannas.getElementsByTagName('li')[i] != 'undefined') {
                $(document).ready(function() {
                    $(listaPestannas.getElementsByTagName('li')[i]).css('background', '');
                    $(listaPestannas.getElementsByTagName('li')[i]).css('padding-bottom', '');
                });
                i += 1;
            }

            $(document).ready(function() {
                // Muestra el contenido de la pesta�a pasada como parametro a la funcion,
                // cambia el color de la pesta�a y aumenta el padding para que tape el
                // borde superior del contenido que esta justo debajo y se vea de este
                // modo que esta seleccionada.
                //alert("recupera");
                $(cpestanna).css('display', '');
                $(tpestanna).css('display', '');
                $(pestanna).css('background', '#3783FE');
                $(pestanna).css('padding-bottom', '2px');
            });
            // var prueba = document.getElementById('divMateriaPrima');
            // alert(prueba);
            //alert("d");
        }
        
</script>

<!-- ESTILO PARA MANEJO DE PESTA�AS-->
<style type="text/css">
        /*PARA CREACION DE PESTA?AS*/
        .contenedor{
            width: 98%;
            margin: auto;
            background-color: #EBEBEB;
            color: bisque;
            padding: 10px 10px 10px 10px;
            border-radius: 10px;
            box-shadow: 0 10px 10px 0px rgba(0, 0, 0, 0.8);
        }

        .contenedorConsulta{
            width: 300px;
            margin: auto;
            background-color: #EBEBEB;
            color: bisque;
            padding: 5px 15px 25px 25px;
            border-radius: 10px;
            //box-shadow: 0 10px 10px 0px rgba(0, 0, 0, 0.8);
        }

        #pestanas {
            background-color:#EBEBEB;
            float: top;
            font-size: 3ex;
            font-weight: bold;
        }

        #pestanas ul{
            margin-left: -40px;
        }

        #pestanas li{
            list-style-type: none;
            float: left;
            text-align: center;
            margin: 0px 2px -2px -0px;
            background: #A6C4E1;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
            border: 2px #808080;
            border-bottom: dimgray;
            padding: 0px 10px 0px 10px;
        }

        #pestanas a:link{
            text-decoration: none;
            color: white;
        }

        #contenidopestanas{
            clear: both;
            background: #D3D3D3;
            padding: 10px 0px 10px 10px;
            border-radius: 5px;
            border-top-left-radius: 0px;
            border: 2px  #808080;
        }

        /*FIN DE CREACION DE PESTA?AS*/

</style> 
	
<script type="text/javascript" src="jquery.tablescroll.js"></script>
<link rel="stylesheet" type = "text/css" href="estilos.css">
<link rel="stylesheet" type = "text/css" href="css/estilo.css">
	
<script type="text/javascript">
        function cargar_scroll(){
		jQuery(document).ready(function($)
                {
                        $('#thetable2').tableScroll({height:350});
                });
        }

        function cargar_scroll_sub(){
		jQuery(document).ready(function($)
                {
                        $('#thetable2_s').tableScroll({height:350});
                });
        }
</script>

<script>
	
	function genera_formulario(){
		xajax_genera_formulario();
	}                

        function cargar_sucu(){
		xajax_genera_formulario( 'sucursal', xajax.getFormValues("form1") );
	}
        
        function cargar_tran(){
		xajax_genera_formulario( 'tran', xajax.getFormValues("form1") );
	}
        
        function autocompletar(empresa, event, op) {
            if (event.keyCode == 13 || event.keyCode == 115) { // F4
                var cliente_nom = '';
                if(op==0){
                     cliente_nom = document.getElementById('cliente_nombre').value;
                }else{
                     cliente_nom = document.getElementById('clpv_nom').value;
                }
                var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";
                var pagina = '../comprob_ingreso/buscar_cliente.php?sesionId=<?= session_id() ?>&mOp=true&mVer=false&cliente=' + cliente_nom+'&empresa='+empresa+'&op='+op;
                window.open(pagina, "", opciones);
            }
        }
        
        function guardar(){
            if (ProcesarFormulario()==true){
                xajax_guardar( xajax.getFormValues("form1") );
            }
        }

        function consultar(){
                xajax_consultar( xajax.getFormValues("form1") );
        }

        function cerrar_ventana(){
		CloseAjaxWin();
	}       

        function cargar_modifi(id_p, empresa ){
                AjaxWin('<?=$_COOKIE["JIREH_INCLUDE"]?>','../config_proc/modificar.php?sesionId=<?=session_id()?>&mOp=true&mVer=false&id_p='+id_p+'&empresa='+empresa,'DetalleShow','iframe','Modificar Procesos','1400','300','10','10','1','1');
        }        
          
    
        function anadir_mp(){
                xajax_agrega_modifica_grid_mp(0, 0, xajax.getFormValues("form1"));
        }
        
        function cargar_grid_mp(){
                xajax_cargar_grid_mp(0, xajax.getFormValues("form1"));
        }
        
        function cargar_grid_in(){
                xajax_cargar_grid_in(0, xajax.getFormValues("form1"));
        }
        
        function anadir_in(){
                xajax_agrega_modifica_grid_in(0, 0, xajax.getFormValues("form1"));
        }
        
        
        function facturas(empresa, event ) {
            if (event.keyCode == 13 || event.keyCode == 115) { // F4
                var factura = document.getElementById('factura').value;
                if( factura.length == 0){
                    factura = '';
                }
                var sucu    = document.getElementById('sucursal').value;
                var clpv    = document.getElementById('clpv_cod').value;
                var tran    = document.getElementById('tran').value;
                var det     = document.getElementById('det_dir').value;

                var coti    = document.getElementById('cotizacion').value;
                var mone    = document.getElementById('moneda').value;

                var array   = [factura, sucu, clpv, empresa, tran, det, coti, mone]; 
                AjaxWin('<?=$_COOKIE["JIREH_INCLUDE"]?>','../comprob_ingreso/buscar_factura.php?sesionId=<?=session_id()?>&mOp=true&mVer=false&array='+array,'DetalleShow','iframe','FACTURAS','800','300','10','10','1','1');
            }
        }
		
		function facturas_ben(empresa, event ) {
			var factura = document.getElementById('factura').value;
			if( factura.length == 0){
				factura = '';
			}
			var sucu    = document.getElementById('sucursal').value;
			var clpv    = document.getElementById('clpv_cod').value;
			var tran    = document.getElementById('tran').value;
			var det     = document.getElementById('det_dir').value;

			var coti    = document.getElementById('cotizacion').value;
			var mone    = document.getElementById('moneda').value;

			var array   = [factura, sucu, clpv, empresa, tran, det, coti, mone]; 
			AjaxWin('<?=$_COOKIE["JIREH_INCLUDE"]?>','../comprob_ingreso/buscar_factura.php?sesionId=<?=session_id()?>&mOp=true&mVer=false&array='+array,'DetalleShow','iframe','FACTURAS','800','300','10','10','1','1');  
        }
          
        function cod_retencion(empresa, event) {
            if (event.keyCode == 13 || event.keyCode == 115) { // F4
                var codret = '';
                codret = document.getElementById('cod_ret').value;
                var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";
                var pagina = '../comprob_ingreso/buscar_codret.php?sesionId=<?= session_id() ?>&mOp=true&mVer=false&codret=' + codret+'&empresa='+empresa;
                window.open(pagina, "", opciones);
            }
        }
        
        function fact_retencion(empresa, event ) {
            if (event.keyCode == 13 || event.keyCode == 115) { // F4
                var factura = document.getElementById('fact_ret').value;
                if( factura.length == 0){
                    factura = '';
                }
                var sucu    = document.getElementById('sucursal').value;
                var clpv    = document.getElementById('clpv_cod').value;
                var cod_ret = document.getElementById('cod_ret').value;
                var array   = [factura, sucu, clpv, empresa, cod_ret]; 
                AjaxWin('<?=$_COOKIE["JIREH_INCLUDE"]?>','../comprob_ingreso/buscar_fact_ret.php?sesionId=<?=session_id()?>&mOp=true&mVer=false&array='+array,'DetalleShow','iframe','FACTURAS','800','300','10','10','1','1');
            }
        }
        
        
        function anadir_ret(){
                xajax_agrega_modifica_grid_ret(0, xajax.getFormValues("form1"));
        }
        
        function auto_dasi(empresa, event, op) {
            if (event.keyCode == 13 || event.keyCode == 115) { // F4
                if(op==0){                     
					 var cod = document.getElementById('cod_cta').value;
                }else{
                     var nom = document.getElementById('nom_cta').value;
                }
                var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";
                var pagina = '../comprob_ingreso/buscar_cuentas.php?sesionId=<?= session_id() ?>&mOp=true&mVer=false&cuenta=' + nom+'&empresa='+empresa+'&op='+op+'&codigo='+cod;
                window.open(pagina, "", opciones);
            }
        }
        
        function anadir_dasi(){
                xajax_agrega_modifica_grid_dia(0, xajax.getFormValues("form1"));
        }
        
        function numero_ret(){
                xajax_numero_ret(  xajax.getFormValues("form1"));
        }
    
        function total_diario(){
                xajax_total_diario(  xajax.getFormValues("form1"));
        }
        
        function cargar_detalle(){
                var msn = document.getElementById('detalle').value;
                document.getElementById('det_dir').value 		= msn.toUpperCase();
                document.getElementById('ret_det').value 		= msn.toUpperCase();
				document.getElementById('detalla_diario').value = msn.toUpperCase();
				document.getElementById('detalle').value        = msn.toUpperCase();
        }
        
        
        function vista_previa() {
            var sucursal  = document.getElementById("sucursal").value;
            var cod_prove = document.getElementById("cliente").value;
            var asto_cod  = document.getElementById("asto_cod").value;
            var ejer_cod  = document.getElementById("ejer_cod").value;
            var prdo_cod  = document.getElementById("prdo_cod").value;
        
            var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";
            var pagina = '../comprob_ingreso/vista_previa.php?sesionId=<?= session_id() ?>&sucursal='+  sucursal+'&cod_prove='+cod_prove+'&asto='+asto_cod+'&ejer='+ejer_cod+'&mes='+prdo_cod;
            window.open(pagina, "", opciones);
        }
		
		function vista_previa_pago() {
            var sucursal  = document.getElementById("sucursal").value;
            var cod_prove = document.getElementById("cliente").value;
            var asto_cod  = document.getElementById("asto_cod").value;
            var ejer_cod  = document.getElementById("ejer_cod").value;
            var prdo_cod  = document.getElementById("prdo_cod").value;
        
            var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";
            var pagina = '../comprob_ingreso/vista_previa_pago.php?sesionId=<?= session_id() ?>&sucursal='+  sucursal+'&cod_prove='+cod_prove+'&asto='+asto_cod+'&ejer='+ejer_cod+'&mes='+prdo_cod;
            window.open(pagina, "", opciones);
        }


        function cargar_coti(){
            xajax_cargar_coti(xajax.getFormValues("form1"));
        }

		
		function anadir_dir(){
            xajax_agrega_modifica_grid_dir_ori(0, xajax.getFormValues("form1") );
        }

		
		function numero_depo(){
                xajax_numero_depo(  xajax.getFormValues("form1"));
        }
		
		
		function centro_costo_cuen(id){
			if(id=='S'){
				document.getElementById('ccosn').value    = '';
				document.getElementById('ccosn').disabled = false;
			}else if(id=='N'){
				document.getElementById('ccosn').value    = '';
				document.getElementById('ccosn').disabled = true;
			}
		}
		
		
		function centro_actividad(id){
			if(id=='S'){
				document.getElementById('actividad').value    = '';
				document.getElementById('actividad').disabled = false;
			}else if(id=='N'){
				document.getElementById('actividad').value    = '';
				document.getElementById('actividad').disabled = true;
			}
		}
		
		
		function modificar_valor( id, empresa, sucursal ){
				xajax_form_modificar_valor(  id, empresa, sucursal, xajax.getFormValues("form1"));
		}
		
		function abre_modal(){				
            $("#mostrarmodal").modal("show");
        }
		
		
		
</script>

<!--DIBUJA FORMULARIO FILTRO-->
<body onload='javascript:cambiarPestanna(pestanas, pestana1);' >
<div align="center">
    <form id="form1" name="form1" action="javascript:void(null);">
          <table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                <tr>
                    <td valign="top">
                        <div class="contenedor">
                            <div id="pestanas">
                                <ul id=lista>
                                    <div id="divFormularioCabecera"></div>
                                <ul>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                        <td>
                            <div class="contenedor">
                                <div id="divResumen"></div>
                                <div id="pestanas">
                                    <ul id=lista>
                                        <li id="pestana1"><a href='javascript:cambiarPestanna(pestanas,pestana1);'>DIRECTORIO</a></li>
                                        <li id="pestana2"><a href='javascript:cambiarPestanna(pestanas,pestana2);'>RETENCION</a></li>
                                        <li id="pestana3"><a href='javascript:cambiarPestanna(pestanas,pestana3);'>DIARIO</a></li>
                                    </ul>
                                </div>
                                <div id="contenidopestanas">
                                     <div id="cpestana1"></div>
                                     <div id="tpestana1" style="width:99%; height:490px; overflow: scroll;">
                                         <table width="100%">
                                             <tr>
                                                 <td  valign="top"><div id="divFormDir"></div></td>
                                             </tr>
                                             <tr>
                                                 <td  valign="top"><div id="divDir"></div></td>
                                             </tr>
                                             <tr>
                                                 <td  valign="top"><div id="divTotDir"></div></td>
                                             </tr>
                                         </table>
                                     </div>
                                     <div id="cpestana2"></div>
                                     <div id="tpestana2" style="width:99%; height:490px; overflow: scroll;">
                                         <table width="100%">
                                             <tr>
                                                 <td  valign="top"><div id="divFormRet"></div></td>
                                             </tr>
                                             <tr>
                                                 <td  valign="top"><div id="divRet"></div></td>
                                             </tr>
                                             <tr>
                                                 <td  valign="top"><div id="divTotRet"></div></td>
                                             </tr>
                                         </table>
                                     </div>
                                     <div id="cpestana3"></div>
                                     <div id="tpestana3" style="width:99%; height:490px; overflow: scroll;">
                                         <<table width="100%">
                                             <tr>
                                                 <td  valign="top"><div id="divFormDiario"></div></td>
                                             </tr>
                                             <tr>
                                                 <td  valign="top"><div id="divDiario"></div></td>
                                             </tr>
                                             <tr>
                                                 <td  valign="top"><div id="divTotDiario"></div></td>
                                             </tr>
                                         </table>
                                     </div>
                                </div>
                            </div>
                        </td>
                </tr>
          </table>
     </form>
</div>
<div id="divGrid" ></div>
<div id="miModal"    class="col-md-12" ></div>
</body>
<script>genera_formulario();/*genera_detalle();genera_form_detalle();*/</script>
<? /********************************************************************/ ?>
<? /* NO MODIFICAR ESTA SECCION*/ ?>
<? } ?>
<? include_once(FOOTER_MODULO); ?>
<? /********************************************************************/ ?>