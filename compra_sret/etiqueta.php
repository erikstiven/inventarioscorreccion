<? /********************************************************************/ ?>
<? /* NO MODIFICAR ESTA SECCION*/ ?>
<? include_once('../_Modulo.inc.php');?>
<? include_once(HEADER_MODULO);?>
<? if ($ejecuta) { ?>
    <? /********************************************************************/ ?>
<?
    if (isset($_REQUEST['id']))
        $id = $_REQUEST['id'];
    else
        $id = '';
    ?>
<script type="text/javascript" src="dist/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="alerts/jquery.alerts.js"></script>

<link rel="stylesheet" type="text/css" href="alerts/jquery.alerts.css"/>

<script>

    function genera_formulario(){
        
        xajax_formulario_etiqueta(<?=$id?>);
    }

    function guardar(){
        //jAlert('Cargar datos', 'Alert Dialog');
        if (ProcesarFormulario()==true){
            xajax_guardar_especialidadm( xajax.getFormValues("form1"), <?=$id?>);
        }
    }

    function editar_especialidad(id){
        xajax_editarm(xajax.getFormValues("form1"), id, <?=$id?>);
    }

    function eliminar_medi(id){
        xajax_eliminar_medi(xajax.getFormValues("form1"), id, <?=$id?>);
    }


    function modificar(id){
        xajax_modificarm(xajax.getFormValues("form1"), id, <?=$id?>);
    }

    function cargar_lista(id){
        parent.xajax_cargar_lista_especialidadm(xajax.getFormValues("form1"), <?=$id?>, id);
    }
    
    
    function procesar(){
        xajax_enviar_etiquetas(xajax.getFormValues("form1"));   
	}
	
	function etiquetasPrint(){
		var op = document.getElementById('etiquetam').value;            
		var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=850, height=500, top=180, left=290";
		var pagina = '../inventario_compra/code_.php?sesionId=<?= session_id() ?>&mOp=true&mVer=false&id=' + op;
		window.open(pagina, "", opciones);
	}
        
        
        

</script>

<!--DIBUJA FORMULARIO FILTRO-->
<div align="center">
    <form id="form1" name="form1" action="javascript:void(null);">
        <table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
            <tr>
				<td>
					<div id="dive"></div>
				</td>
            </tr>
        </table>
    </form>
</div>
<div id="divGrid" ></div>
<script>genera_formulario();/*genera_detalle();genera_form_detalle();*/</script>
    <? /********************************************************************/ ?>
    <? /* NO MODIFICAR ESTA SECCION*/ ?>
<? } ?>
<? include_once(FOOTER_MODULO); ?>
<? /********************************************************************/ ?>