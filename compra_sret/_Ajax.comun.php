<?php
/* ARCHIVO COMUN PARA LA EJECUCION DEL SERVIDOR AJAX DEL MODULO */

/***************************************************/
/* NO MODIFICAR */
include_once('../../Include/config.inc.php');
include_once(path(DIR_INCLUDE) . 'conexiones/db_conexion.php');
include_once(path(DIR_INCLUDE) . 'comun.lib.php');
include_once(path(DIR_INCLUDE) . 'Clases/Formulario/Formulario.class.php');
require_once(path(DIR_INCLUDE) . 'Clases/xajax/xajax_core/xajax.inc.php');
require_once(path(DIR_INCLUDE) . 'Clases/GeneraDetalleAsientoContable.class.php');
require_once(path(DIR_INCLUDE) . 'Clases/GeneraDetalleInventario.class.php');

include_once(path(DIR_INCLUDE) . 'comun.lib.rd.php');

/***************************************************/
/* INSTANCIA DEL SERVIDOR AJAX DEL MODULO*/
$xajax = new xajax('_Ajax.server.php');
$xajax->setCharEncoding('ISO-8859-1');
/***************************************************/
//	FUNCIONES PUBLICAS DEL SERVIDOR AJAX DEL MODULO 
//	Aqui registrar todas las funciones publicas del servidor ajax
//	Ejemplo,
//	$xajax->registerFunction("Nombre de la Funcion");
/***************************************************/
//	Fuciones de lista de pedido
$xajax->registerFunction("genera_formulario_pedido");
$xajax->registerFunction("agrega_modifica_grid");
$xajax->registerFunction("agrega_modifica_grid_update");
$xajax->registerFunction("total_grid_update");
$xajax->registerFunction("total_grid");
$xajax->registerFunction("mostrar_grid");
$xajax->registerFunction("mostrar_grid_ret");
$xajax->registerFunction("cancelar_pedido");
$xajax->registerFunction("elimina_detalle");
$xajax->registerFunction("actualiza_grid");
$xajax->registerFunction("guarda_pedido");
$xajax->registerFunction("cargar_lista_correo");
$xajax->registerFunction("orden_compra");
$xajax->registerFunction("cargar_orden_compra");
$xajax->registerFunction("reporte");
$xajax->registerFunction("cargar_secuencial_rete");

// F U N C I O N E S     P A R A     E L     
// S E C U E N C I A L     D E L      P E D I D O
$xajax->registerFunction("secuencial_pedido");
$xajax->registerFunction("cero_mas");

// CLIENTE NUEVO
$xajax->registerFunction("genera_formulario_cliente");
$xajax->registerFunction("guardar_cliente");

// forma de pago
$xajax->registerFunction("genera_formulario_forma_pago");
$xajax->registerFunction("agrega_modifica_grid_fp");
$xajax->registerFunction("mostrar_grid_fp");
$xajax->registerFunction("elimina_detalle_fp");
$xajax->registerFunction("total_grid_fp");
$xajax->registerFunction("formulario_detalle_fp");
$xajax->registerFunction("ocultar_detalle_fp");
$xajax->registerFunction("guardar_forma_pago");


//FUNCIONES ENVIO
$xajax->registerFunction("firmar");
$xajax->registerFunction("validaAutoriza");
$xajax->registerFunction("autorizaComprobante");
$xajax->registerFunction("actualizar_grid");
$xajax->registerFunction("update_comprobante");


// fp
$xajax->registerFunction("tipo_fp");
$xajax->registerFunction("calculo_fecha_fp");
$xajax->registerFunction("num_digito");

$xajax->registerFunction("elimina_detalle_ret");

//TIPO FACTURA
$xajax->registerFunction("tipo_factura");
$xajax->registerFunction("validar_factura");

// PORTAFOLIO
$xajax->registerFunction("genera_formulario_portafolio");
$xajax->registerFunction("cargar_productos");
$xajax->registerFunction("clave_acceso");

//ETIQUETAS

$xajax->registerFunction("formulario_etiqueta");
$xajax->registerFunction("enviar_etiquetas");
$xajax->registerFunction("cargar_tran");
$xajax->registerFunction("cargar_bode");
$xajax->registerFunction("cargar_fpago");

$xajax->registerFunction("agrega_modifica_grid_ret");
$xajax->registerFunction("orden_compra_reporte");
$xajax->registerFunction("orden_compra_reporte_det");
$xajax->registerFunction("generaReporteCompras");


$xajax->registerFunction("form_lote");
$xajax->registerFunction("procesar_lote");


$xajax->registerFunction("form_precio_inv");
$xajax->registerFunction("procesar_precio_inv");

$xajax->registerFunction("cargar_electronica");

$xajax->registerFunction("cargar_digito_ret");
$xajax->registerFunction("archivosAdjuntos");
$xajax->registerFunction("agrega_modifica_gridAdj");
$xajax->registerFunction("mostrar_gridAdj");
$xajax->registerFunction("elimina_detalleAdj");
$xajax->registerFunction("genera_pdf_doc");

$xajax->registerFunction("cargar_coti");
$xajax->registerFunction("vista_previa");

$xajax->registerFunction("clpv_reporte");
$xajax->registerFunction("producto_inventario");

$xajax->registerFunction("recepcion_compra");
$xajax->registerFunction("recepcion_compra_det");
$xajax->registerFunction("cargar_reco");

$xajax->registerFunction("datos_prod");
$xajax->registerFunction("cargar_rd");
$xajax->registerFunction("anio_fecha_abierto");
$xajax->registerFunction("recalcular_fpago");




$xajax->registerFunction("cargar_ord_compra");
$xajax->registerFunction("cargar_ord_compra_respaldo");
$xajax->registerFunction("guardar_precio_inv");

$xajax->registerFunction("modal_correo");
$xajax->registerFunction("envio_correo_sret");
$xajax->registerFunction("obtener_balanza_api");




// --------------------------------------------------------------------------------------
// Evaluacion control calidad
// --------------------------------------------------------------------------------------

$xajax->registerFunction("abrir_evaluacion");
$xajax->registerFunction("guardar_nueva_evaluacion");
$xajax->registerFunction("guardar_evaluacion");

// --------------------------------------------------------------------------------------
// FIN Evaluacion control calidad
// --------------------------------------------------------------------------------------



// --------------------------------------------------------------------------------------
// Funciones pollos campo balanza
// --------------------------------------------------------------------------------------

$xajax->registerFunction("modal_balanza");
$xajax->registerFunction("clpv_reporte_bal");
$xajax->registerFunction("producto_inventario_bal");
$xajax->registerFunction("abrir_modal_pesaje");
$xajax->registerFunction("agregar_pesaje");
$xajax->registerFunction("productos_pesados");
$xajax->registerFunction("eliminar_producto_agregado");
$xajax->registerFunction("realizar_calculos_bal");
$xajax->registerFunction("limpiar_todos_datos");
$xajax->registerFunction("procesar_info");
$xajax->registerFunction("abre_modal_historial_bal");
$xajax->registerFunction("consultar_movimientos_inv");
$xajax->registerFunction("modal_vista_previa_balanza");


$xajax->registerFunction("generar_tabla_amortizacion");
$xajax->registerFunction("finalizar_oc");
$xajax->registerFunction("cargar_coti_ext");


// --------------------------------------------------------------------------------------
// FIN Funciones pollos campo balanza
// --------------------------------------------------------------------------------------



/***************************************************/
