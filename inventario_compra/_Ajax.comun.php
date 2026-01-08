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
$xajax->registerFunction("procesoSerie");
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

$xajax->registerFunction("cargar_rd");
$xajax->registerFunction("datos_prod");
$xajax->registerFunction("anio_fecha_abierto");
$xajax->registerFunction("recalcular_fpago");


$xajax->registerFunction("cargar_ord_compra");
$xajax->registerFunction("cargar_ord_compra_respaldo");
$xajax->registerFunction("guardar_precio_inv");

$xajax->registerFunction("valida_existe_factura");
$xajax->registerFunction("consultar_compras");
$xajax->registerFunction("f_filtro_ejercicio");
$xajax->registerFunction("f_filtro_periodo");
$xajax->registerFunction("clpv_reporte_lista");
$xajax->registerFunction("verDiarioContable");
$xajax->registerFunction("genera_documento");
$xajax->registerFunction("genera_pdf_doc_compras");
$xajax->registerFunction("recalcular_precio_venta");


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
// Tomar las series de cada producto de la orden de compra
// --------------------------------------------------------------------------------------
$xajax->registerFunction("agregar_series");
$xajax->registerFunction("consultar_codigo_unico");
$xajax->registerFunction("verificar_codigos_u_insertados");
$xajax->registerFunction("eliminar_codigo_unico_recep");
$xajax->registerFunction("finalizar_oc");
// --------------------------------------------------------------------------------------
// FIN Tomar las series de cada producto de la orden de compra
// --------------------------------------------------------------------------------------



// --------------------------------------------------------------------------------------
// cargar compra de inventario cuando se vaya a modificar
// --------------------------------------------------------------------------------------
$xajax->registerFunction("cargar_invetario_compra_ad");
$xajax->registerFunction("cargar_info_fact_ad");
$xajax->registerFunction("eliminar_movimiento");
// --------------------------------------------------------------------------------------
// FIN cargar compra de inventario cuando se vaya a modificar
// --------------------------------------------------------------------------------------






// -----------------------------------------------------------------------------------------
// Cierre de anticipo en compras de inventario
// -----------------------------------------------------------------------------------------
$xajax->registerFunction("cerrar_anticipo_modulo");
// -----------------------------------------------------------------------------------------
// FIN Cierre de anticipo en compras de inventario
// -----------------------------------------------------------------------------------------

$xajax->registerFunction("validar_proveedor");



/***************************************************/
