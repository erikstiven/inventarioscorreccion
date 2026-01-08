<? /********************************************************************/ ?>
<? /* NO MODIFICAR ESTA SECCION*/ ?>
<? include_once('../_Modulo.inc.php');?>
<? include_once(HEADER_MODULO);?>
<? if ($ejecuta) { ?>
<? /********************************************************************/ ?>
<?
	if(isset($_REQUEST['mOp'])) $mOp=$_REQUEST['mOp'];
		else $mOp='';
	if(isset($_REQUEST['Id'])) $Id=$_REQUEST['Id'];
		else $Id='-1';
	if(isset($_REQUEST['empresa'])) $id_empresa=$_REQUEST['empresa'];
		else $id_empresa='';
	if(isset($_REQUEST['sucursal'])) $id_sucursal=$_REQUEST['sucursal'];
		else $id_sucursal='';
        if(isset($_REQUEST['cliente']))  $cliente=$_REQUEST['cliente'];
		else $cliente='';
?>
<script>

        function cargar_pedido(){           
            if(ProcesarFormulario()==true){
                parent.xajax_cargar_orden_compra( <?=$id_empresa?>, <?=$id_sucursal?>, <?=$cliente?>, xajax.getFormValues("form1"));
            }
        }

		
		function cerrar_ventana(){
			CloseAjaxWin();
		}	
		
		
		function cargar_oc_det(serial, empresa, sucursal){
                var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";
                var pagina   = '../inventario_compra/oc_detalle.php?sesionId=<?=session_id()?>&mOp=true&mVer=false&serial='+serial+'&empresa='+empresa+'&sucursal='+sucursal;
                window.open(pagina,"",opciones);
        }

</script>
<!-- Divs contenedores!-->
<div align="center">
    <form id="form1" name="form1" action="javascript:void(null);">
      <table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
        <tr>
          	<td valign="top" align="center">
                    <div id="divFormularioDetalle"></div>
         	</td>
        </tr>
        </tr>
      </table>
     </form>
</div>
<script>
xajax_orden_compra( <?=$id_empresa?>, <?=$id_sucursal?>, <?=$cliente?> );
</script>
<? /********************************************************************/ ?>
<? /* NO MODIFICAR ESTA SECCION*/ ?>
<? } ?>
<? include_once(FOOTER_MODULO); ?>
<? /********************************************************************/ ?>