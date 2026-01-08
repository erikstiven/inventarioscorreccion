<? /* * ***************************************************************** */ ?>
<? /* NO MODIFICAR ESTA SECCION */ ?>
<? include_once('../_Modulo.inc.php'); ?>
<? include_once(HEADER_MODULO); ?>
<? if ($ejecuta) { ?>
    <?
    unset($_SESSION['claveAccesoExterno']);
    if (isset($_GET['clave_acceso'])) {
        $clave_acceso = $_GET['clave_acceso'];
        $_SESSION['claveAccesoExterno'] = $clave_acceso;
    }
    ?>
    <? /*     * ***************************************************************** */ ?>

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/dataTables/dataTables.buttons.min.css" media="screen">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>bower_components/Ionicons/css/ionicons.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>bower_components/select2/dist/css/select2.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skinsfolder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/dataTables/dataTables.bootstrap.min.css" media="screen">


    <!--JavaScript-->
    <script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.buttons.min.js"></script>
    <script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.buttons.flash.min.js"></script>
    <script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.jszip.min.js"></script>
    <script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.pdfmake.min.js"></script>
    <script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.vfs_fonts.js"></script>
    <script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.buttons.html5.min.js"></script>
    <script type="text/javascript" language="JavaScript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.buttons.print.min.js"></script>

    <!-- Select2 -->
    <script src="<?= $_COOKIE["JIREH_COMPONENTES"] ?>bower_components/select2/dist/js/select2.full.min.js"></script>

    <!-- AdminLTE App -->
    <script src="<?= $_COOKIE["JIREH_COMPONENTES"] ?>dist/js/adminlte.min.js"></script>

    <!--CSS-->
    <link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/bootstrap-3.3.7-dist/css/bootstrap.css" media="screen">
    <link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/bootstrap-3.3.7-dist/css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/treeview/css/bootstrap-treeview.css" media="screen">
    <link rel="stylesheet" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/dataTables/dataTables.bootstrap.min.css">

    <style>
        .input-group-addon.primary {
            color: rgb(255, 255, 255);
            background-color: rgb(50, 118, 177);
            border-color: rgb(40, 94, 142);
        }
    </style>

    <?php

    unset($_SESSION['num_comp_edit']);

    if (isset($_GET['minv_cod_edit'])) {
        $minv_cod_edit = $_GET['minv_cod_edit'];
        $_SESSION['num_comp_edit'] = $minv_cod_edit;
    }
    ?>


    <script>
        function asignarValorIvaSub(tipo_ret) {
            var iva = document.getElementById('iva_total').value;
            var total_factura = document.getElementById('total_fac').value;
            if (!iva) {
                iva = 0
            }
            if (!total_factura) {
                total_factura = 0
            }

            if (iva) {
                // Subtotal
                if (tipo_ret == 1) {
                    document.getElementById('ret_base').value = total_factura;
                } else
                    // Iva
                    if (tipo_ret == 2) {
                        document.getElementById('ret_base').value = iva;
                    }
                    // alert(iva + ' - ' + total_factura);
            } else {
                alert('Ingresa almenos un producto');
            }
        }


        function cargar_secuencial_rete() {
            xajax_cargar_secuencial_rete(xajax.getFormValues("form1"));
        }

        function genera_formulario() {
            //alert('hola');
            xajax_genera_formulario_pedido();
        }

        /*function autocompletar(empresa, event) {
            if (event.keyCode == 13 || event.keyCode == 115) { // F4
                var cliente_nom = document.getElementById('cliente_nombre').value;
                var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=830, height=380, top=255, left=130";
                var pagina   = '../inventario_compra/buscar_cliente.php?sesionId=<?= session_id() ?>&mOp=true&mVer=false&cliente=' + cliente_nom;
                window.open(pagina, "", opciones);
            }
        }
        */
        function autocompletar(empresa, event) {
            if (event.keyCode == 115 || event.keyCode == 13) { // F4
                $("#ModalClpv").modal("show");
                xajax_clpv_reporte(xajax.getFormValues("form1"));
            }
        }

        function autocompletar_btn(empresa) {
            $("#ModalClpv").modal("show");
            xajax_clpv_reporte(xajax.getFormValues("form1"));

        }


        function autocompletar_lista(empresa, event) {
            if (event.keyCode == 115 || event.keyCode == 13) { // F4
                $("#ModalClpv").modal("show");
                xajax_clpv_reporte_lista(xajax.getFormValues("form1"));
            }
        }

        function autocompletar_btn_lista(empresa) {
            $("#ModalClpv").modal("show");
            xajax_clpv_reporte_lista(xajax.getFormValues("form1"));

        }

        function cargar_secuencial() {
            var sucursal = document.getElementById("sucursal").value;
            xajax_genera_formulario_pedido(sucursal, 'nuevo', xajax.getFormValues("form1"));
        }


        function guardar_precios(id_op) {
            var tran = document.getElementById('tran').value;
            if (tran == '') {
                alert('Seleccione un tipo de transaccion');
            } else {
                xajax_guardar_precio_inv(id_op, xajax.getFormValues("form1"));
            }
        }

        function guardar_pedido(id_op) {

            if (ProcesarFormulario() == true) {
                Swal.fire({
                    title: 'Desea Guardar...??',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar',
                    allowOutsideClick: false,
                    width: '40%',
                }).then((result) => {
                    if (result.value) {
                        var ctrl = document.getElementById("ctrl").value;
                        if (ctrl == 1) {
                            // jsShowWindowLoad();
                            document.getElementById("ctrl").value = 2;
                            xajax_guarda_pedido(id_op, xajax.getFormValues("form1"));


                        } else {
                            var codigo = document.getElementById("nota_compra").value;
                            var cont = codigo.length;
                            if (cont > 0) {
                                alert('!!!!....Error La Compra ya esta Ingresado....!!!!!...');
                            } else {
                                alert('Procesando Informacion...');
                            } // fin if
                        }
                    }
                })
            }
        }



        function cancelar_pedido() {
            confirmar = confirm("Deseas Guardar los cambios..?");
            if (confirmar) {
                guardar_pedido();
            } else {
                genera_formulario();
            }
        }

        function fecha_pago(num) {
            if (num == 1) {
                var fecha_pago = document.getElementById('fecha_entrega').value;
                document.getElementById('fecha_final').value = fecha_pago;
            } else {
                var fecha_pago = document.getElementById('fecha_final').value;
                document.getElementById('fecha_entrega').value = fecha_pago;
            }
            var fecha_entrega = document.getElementById('fecha_entrega').value;
            var fecha_pedido = document.getElementById('fecha_pedido').value;
            if (fecha_pedido > fecha_entrega) {
                var fecha_actual = new Date();
                document.getElementById('fecha_final').value = fecha_pedido;
                document.getElementById('fecha_entrega').value = fecha_pedido;
                alert('La fecha de entrega no puede ser menor a la fecha de pedido');
            }
        }


        function totales() {
            // IMPRIME EL TOTAL DEL PEDIDO
            // descuento general
            if (!document.getElementById("descuento_general")) {
                var desc = 0;
            } else {
                var desc = document.getElementById("descuento_general").value;
            }
            // flete
            if (!document.getElementById("flete")) {
                var flete = 0;
            } else {
                var flete = document.getElementById("flete").value;
                if (flete == '') {
                    flete = 0;
                }
            }
            // otros
            if (!document.getElementById("otros")) {
                var otro = 0;
            } else {
                var otro = document.getElementById("otros").value;
                if (otro == '') {
                    otro = 0;
                }
            }
            //anticipo
            if (!document.getElementById("anticipo")) {
                var anticipo = 0;
            } else {
                var anticipo = document.getElementById("anticipo").value;
                if (anticipo == '') {
                    anticipo = 0;
                }
            }
            xajax_total_grid(desc, flete, otro, anticipo, xajax.getFormValues("form1"));
            //alert('dentro de')
        }

        function cargar_descuento(desc, fac, iva) {
            // descuento
            var a = document.getElementById("descuento_general").value;
            if (a == '') {
                a = 0;
                document.getElementById("descuento_general").value = a;
            }

            if (desc < a) {
                alert('El valor maximo de descuento para este usuario es de ' + desc + ' %');
                a = desc;
                document.getElementById("descuento_general").value = desc;
            }
            xajax_agrega_modifica_grid_update(a, xajax.getFormValues("form1"));
        }


        function cargar_descuento_oc(desc) {
            xajax_agrega_modifica_grid_update(desc, xajax.getFormValues("form1"));
        }


        function cerrar_ventana() {
            CloseAjaxWin();
        }

        function focus_ruc() {
            var ruc = document.getElementById("ruc");
            ruc.focus();
            var value = ruc.value;
            ruc.value = "";
            ruc.value = value;
        }

        function tecla_ruc(event) {
            // F4 115
            // ENTER 13
            if (event.keyCode == 13) {
                var sucursal = document.getElementById("sucursal").value;
                xajax_genera_formulario_pedido(sucursal, 'cargar_ruc', xajax.getFormValues("form1"));
            }
        }

        function cargar_tran() {
            xajax_cargar_tran(xajax.getFormValues("form1"));
        }

        function eliminar_lista_tran() {
            var sel = document.getElementById("tran");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }

        function anadir_elemento_tran(x, i, elemento) {
            var lista = document.form1.tran;
            var option = new Option(elemento, i);
            lista.options[x] = option;
        }

        function cargar_bode() {
            xajax_cargar_bode(xajax.getFormValues("form1"));
        }

        function eliminar_lista_bode() {
            var sel = document.getElementById("bodega");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }

        function anadir_elemento_bode(x, i, elemento) {
            var lista = document.form1.bodega;
            var option = new Option(elemento, i);
            lista.options[x] = option;
        }

        function cargar_fpago() {
            xajax_cargar_fpago(xajax.getFormValues("form1"));
        }

        function eliminar_lista_fpago() {
            var sel = document.getElementById("forma_pago_prove");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }

        function anadir_elemento_fpago(x, i, elemento) {
            var lista = document.form1.forma_pago_prove;
            var option = new Option(elemento, i);
            lista.options[x] = option;
        }

        function autocompletar_producto(event, para) {
            var bodega = document.getElementById("bodega").value;
            var sucursal = document.getElementById("sucursal").value;
            var precio = 1;
            if (event.keyCode == 13 || event.keyCode == 115) { // F4
                if (bodega != '' && sucursal != '' && precio != '') {
                    var producto = document.getElementById('producto').value;
                    var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=1000, height=390, top=200, left=130";
                    var pagina = '../inventario_compra/view/_buscar_productos.php?sesionId=<?= session_id() ?>&sucursal=' + sucursal + '&bodega=' + bodega + '&producto=' + encodeURIComponent(producto) + '&precio=' + precio;
                    // AjaxWin('<?= $_COOKIE["JIREH_INCLUDE"] ?>', pagina, 'DetalleShow', 'iframe', 'Listado Productos', '980', '500', '0', '0', '0', '0');

                    $("#ModalProd").modal("show");
                    xajax_producto_inventario(xajax.getFormValues("form1"));
                } else {
                    alert("Ingrese Cliente - Bodega - Tipo de Precio");
                }
            }

        }

        /*
                function autocompletar_producto(empresa, event, op) {
                    if (event.keyCode == 115 || event.keyCode == 13) { // F4
                        $("#ModalProd").modal("show");
                        xajax_producto_inventario( xajax.getFormValues("form1") );
                    }
                }
        */
        function autocompletar_producto_btn(empresa) {
            $("#ModalProd").modal("show");
            xajax_producto_inventario(xajax.getFormValues("form1"));
        }

        function cargar_producto() {
            var bodega = document.getElementById('bodega').value;
            var prod_cod = document.getElementById('codigo_producto').value;
            var cant = document.getElementById('cantidad').value;
            var costo = document.getElementById('costo').value;
            var iva = document.getElementById('iva').value;


            var lote_visible = document.getElementById('lote_prod').style.display;
            var serie_visible = document.getElementById('serie_prod').style.display;


            var lote = document.getElementById('lote_prod').value;
            var serie = document.getElementById('serie_prod').value;
            var fecha_ela = document.getElementById('fecha_ela').value;
            var fecha_cad = document.getElementById('fecha_cad').value;

            var control_lote = 'S'
            var control_serie = 'S'


            if (lote_visible == 'block') {
                if (fecha_ela == '' || fecha_cad == '' || lote == '') {
                    alert('Debe llenar todos los campos: Lote, Fecha Elaboracion y Fecha Caducidad');
                    control_lote = 'N'
                }
            }

            if (serie_visible == 'block') {
                if (serie == '') {
                    alert('Debe llenar todos los campos: Serie');
                    control_serie = 'N'
                }
            }

            if (control_lote == 'S' && control_serie == 'S' && bodega != '' && prod_cod != '' && cant > 0 && costo > 0) {
                xajax_agrega_modifica_grid(0, 0, '', xajax.getFormValues("form1"));

            } else {
                alert('Por favor seleccione Bodega - Producto - Cantidad - Costo - Impuesto....!!!!');
            }
        }

        function cargar_update_cant(id) {
            var a = document.getElementById(id + "_cantidad").value;
            xajax_actualiza_grid(id, xajax.getFormValues("form1"));
        }


        function limpiar_prod() {
            foco('producto');

            document.getElementById("lote_prod").value = '';
            document.getElementById("serie_prod").value = '';
            document.getElementById("fecha_ela").value = '';
            document.getElementById("fecha_cad").value = '';
            document.getElementById("producto").value = '';
            document.getElementById("cantidad").value = 1;
            document.getElementById("codigo_producto").value = '';
            document.getElementById("costo").value = 0;
            document.getElementById("iva").value = 0;
            document.getElementById("cuenta_inv").value = '';
            document.getElementById("cuenta_iva").value = '';
            //document.getElementById("lote").value = '';
            //document.getElementById("codigo_barra").value = '';

            document.getElementById("lote_prod").style.display = 'none';
            document.getElementById("serie_prod").style.display = 'none';
            /*
            document.getElementById("fecha_ela").style.display = 'none';
            document.getElementById("fecha_cad").style.display = 'none';
            */

        }

        function foco(idElemento) {
            document.getElementById(idElemento).focus();
        }

        // busqueda de autorizacion proveedor
        function auto_proveedor(empresa, event) {
            if (event.keyCode == 115) { // F4
                var serie_prove = document.getElementById('serie_prove').value;
                var prove = document.getElementById('cliente').value;
                var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";
                var pagina = '../inventario_compra/buscar_auto_prove.php?sesionId=<?= session_id() ?>&mOp=true&mVer=false&serie=' + serie_prove + '&prove=' + prove;
                window.open(pagina, "", opciones);
            }
        }

        // FORMAS PAGO
        // F O R M A    D E    P A G O
        function anadir_detalle_fp(sucursal) {
            if (ProcesarFormulario() == true) {
                xajax_formulario_detalle_fp('nuevo', sucursal, xajax.getFormValues("form1"));
            }
        }

        function anadir_forma_pago() {
            if (ProcesarFormulario() == true) {
                xajax_agrega_modifica_grid_fp(0, '', xajax.getFormValues("form1"));
            }
        }

        function calculo_fecha_fp() {
            xajax_calculo_fecha_fp(xajax.getFormValues("form1"));
        }

        function totales_fp() {
            xajax_total_grid_fp();
        }

        function limpiar_fp() {
            document.getElementById("dias_fp").value = 0;
            document.getElementById("porcentaje").value = 0;
            document.getElementById("valor").value = 0;
            document.getElementById("forma_pago").value = "";
            ocultar();
        }

        function ocultar() {
            xajax_ocultar_detalle_fp();
        }

        function guardar_forma_pago() {
            xajax_guardar_forma_pago(xajax.getFormValues("form1"));
        }

        function tipo_fp() {
            xajax_tipo_fp(xajax.getFormValues("form1"));
        }

        function num_digito() {
            xajax_num_digito(xajax.getFormValues("form1"));
        }

        //FUNCIONES SRI 
        function firmar(nombre_archivo, clave_acceso, ruc, id_docu, correo, clpv, fact, ejer, asto, fecha) {
            xajax_firmar(nombre_archivo, clave_acceso, ruc, id_docu, correo, clpv, fact, ejer, asto, fecha);
        }

        function validaAutoriza(nombre_archivo, clave_acceso, id_docu, correo, clpv, fact, ejer, asto, fecha) {
            //   alert("asdasdasdasd");
            xajax_validaAutoriza(nombre_archivo, clave_acceso, id_docu, correo, clpv, fact, ejer, asto, fecha);
        }

        function update_comprobante(numeroAutorizacion, fechaAutorizacion, id_docu) {
            //alert("asdasdasdasdasdasd");
            xajax_update_comprobante(numeroAutorizacion, fechaAutorizacion, id_docu);
        }

        function autorizaComprobante(clave_acceso, id_docu, correo, clpv, fact, ejer, asto, fecha) {
            //  alert("asdasdasdasd");
            xajax_autorizaComprobante(clave_acceso, id_docu, correo, clpv, fact, ejer, asto, fecha);
        }


        //TIPO DE FACTURA
        function cargar_factura() {
            var op = document.getElementById("tipo_factura").value;
            if (op != '') {
                var clpv = document.getElementById("cliente").value;
                if (clpv != '') {
                    xajax_tipo_factura(xajax.getFormValues("form1"));
                } else {
                    alert('Elija Proveedor para Continuar...');
                    document.getElementById("cliente_nombre").focus();
                }
            }
        }


        //TIPO DE FACTURA
        function validar_fact() {
            //   xajax_validar_factura(xajax.getFormValues("form1"));
        }

        function generar_pdf() {
            if (ProcesarFormulario() == true) {
                var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=.370, top=255, left=130";
                var pagina = '../../Include/documento_pdf3.php?sesionId=<?= session_id() ?>';
                //         var pagina = '../pedido/vista_previa.php?sesionId=<?= session_id() ?>&codigo='+codigo;
                //AjaxWin('<?= $_COOKIE["JIREH_INCLUDE"] ?>', '/documento_pdf3.php?sesionId=<?= session_id() ?>&mOp=true&mVer=false, 'DetalleShow', 'iframe', 'Pedidos', '590', '200', '10', '10', '1', '1');
                window.open(pagina, "", opciones);
            }
        }

        function generar_pdf_compras() {
            var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=.370, top=255, left=130";
            var pagina = '../../Include/documento_pdf3.php?sesionId=<?= session_id() ?>';
            //         var pagina = '../pedido/vista_previa.php?sesionId=<?= session_id() ?>&codigo='+codigo;
            //AjaxWin('<?= $_COOKIE["JIREH_INCLUDE"] ?>', '/documento_pdf3.php?sesionId=<?= session_id() ?>&mOp=true&mVer=false, 'DetalleShow', 'iframe', 'Pedidos', '590', '200', '10', '10', '1', '1');
            window.open(pagina, "", opciones);
        }

        function cargar_lista_correo() {
            //alert("asdasda");
            xajax_cargar_lista_correo(xajax.getFormValues("form1"));
        }

        function eliminar_lista_correo() {
            // alert("asd");
            var sel = document.getElementById("correo_prove");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }

        function anadir_elemento_correo(x, i, elemento) {
            var lista = document.form1.correo_prove;
            var option = new Option(elemento, i);
            lista.options[x] = option;
        }


        function validaAutorizacion(tipo, opcion) {
            // alert('adasdasd');
            var auto_prove = document.getElementById('auto_prove').value;
            var tamano = auto_prove.length;

            console.log(tamano);
            switch (tipo) {
                case 'electronica':
                    <?php
                    $u_pais_dig_autoe = '0';
                    if ($_SESSION['U_PAIS_DIG_AUTOE']) {
                        $u_pais_dig_autoe = $_SESSION['U_PAIS_DIG_AUTOE'];
                    }
                    ?>
                    var num_dig = <?php echo $u_pais_dig_autoe; ?>;
                    if (opcion == 'escribir' && (tamano > num_dig)) {
                        var mensaje = 'LA LONGITUD MAXIMA ES ' + num_dig + ' DIGITOS';
                        var tipo = "info";
                        alerts(mensaje, tipo);

                        document.getElementById('auto_prove').value = auto_prove.substring(0, num_dig);
                    } else if (opcion == 'enfoque' && tamano < num_dig) {
                        var mensaje = 'LA LONGITUD MAXIMA ES ' + num_dig + ' DIGITOS';
                        var tipo = "info";
                        alerts(mensaje, tipo);

                        document.getElementById('auto_prove').value = '';
                        document.getElementById('auto_prove').focus();
                    }
                    break;
                case 'impresa':
                    <?php
                    $u_pais_dig_autop = '0';
                    if ($_SESSION['U_PAIS_DIG_AUTOP']) {
                        $u_pais_dig_autop = $_SESSION['U_PAIS_DIG_AUTOP'];
                    }
                    ?>
                    var num_dig = <?php echo $u_pais_dig_autop; ?>;
                    if (opcion == 'escribir' && tamano > num_dig) {
                        var mensaje = 'LA LONGITUD MAXIMA ES ' + num_dig + ' DIGITOS';
                        var tipo = "info";
                        alerts(mensaje, tipo);
                        document.getElementById('auto_prove').value = auto_prove.substring(0, num_dig);
                    } else if (opcion == 'enfoque' && tamano < num_dig) {
                        var mensaje = 'LA LONGITUD MAXIMA ES ' + num_dig + ' DIGITOS';
                        var tipo = "info";
                        alerts(mensaje, tipo);

                        document.getElementById('auto_prove').value = '';
                        document.getElementById('auto_prove').focus();
                    }
                    break
            }

        }

        function validaSerie(opcion, tipo_fact) {

            var serie_prove = document.getElementById('serie_prove').value;
            var tamano = serie_prove.length;

            if (tipo_fact == 1) {
                //electronico
                <?php
                $u_pais_dig_sere = '0';
                if ($_SESSION['U_PAIS_DIG_SERE']) {
                    $u_pais_dig_sere = $_SESSION['U_PAIS_DIG_SERE'];
                }
                ?>
                var num_dig = <?php echo $u_pais_dig_sere; ?>;
            } else {
                // preimpresa
                <?php
                $u_pais_dig_serp = '0';
                if ($_SESSION['U_PAIS_DIG_SERP']) {
                    $u_pais_dig_serp = $_SESSION['U_PAIS_DIG_SERP'];
                }
                ?>
                var num_dig = <?php echo $u_pais_dig_serp; ?>;
            }

            if (opcion == 'escribir' && tamano > num_dig) {
                var mensaje = 'LA LONGITUD MAXIMA ES ' + num_dig + ' DIGITOS';
                var tipo = "info";
                alerts(mensaje, tipo);

                document.getElementById('serie_prove').value = serie_prove.substring(0, num_dig);
                document.getElementById('serie_prove').focus();
            } else if (opcion == 'enfoque' && tamano < num_dig) {
                var mensaje = 'LA LONGITUD MAXIMA ES ' + num_dig + ' DIGITOS';
                var tipo = "info";
                alerts(mensaje, tipo);

                document.getElementById('serie_prove').value = '';
                document.getElementById('serie_prove').focus();
            }

        }

        function validaFactura(tipo, opcion) {
            var factura = document.getElementById('factura').value;
            var tamano = factura.length;

            switch (tipo) {
                case 'electronica':
                    <?php
                    $u_pais_dig_face = '0';
                    if ($_SESSION['U_PAIS_DIG_FACE']) {
                        $u_pais_dig_face = $_SESSION['U_PAIS_DIG_FACE'];
                    }
                    ?>
                    var num_dig = <?php echo $u_pais_dig_face; ?>;
                    if (opcion == 'escribir' && tamano > num_dig) {
                        var mensaje = 'LA LONGITUD MAXIMA ES ' + num_dig + ' DIGITOS';
                        var tipo = "info";
                        alerts(mensaje, tipo);
                        document.getElementById('factura').value = factura.substring(0, num_dig);
                    } else if (opcion == 'enfoque' && tamano < num_dig)
                        num_digito();
                    break;
                case 'impresa':
                    <?php
                    $u_pais_dig_facp = '0';
                    if ($_SESSION['U_PAIS_DIG_FACP']) {
                        $u_pais_dig_facp = $_SESSION['U_PAIS_DIG_FACP'];
                    }
                    ?>
                    var num_dig = <?php echo $u_pais_dig_facp; ?>;
                    if (opcion == 'escribir' && tamano > num_dig) {
                        var mensaje = 'LA LONGITUD MAXIMA ES ' + num_dig + ' DIGITOS';
                        var tipo = "info";
                        alerts(mensaje, tipo);
                        document.getElementById('factura').value = factura.substring(0, num_dig);
                    } else if (opcion == 'enfoque' && tamano < num_dig) {
                        num_digito();
                        validar_fact();
                    }
                    break
            }
        }

        function reporte_retencionInve() {
            //alert("Por favor primero guarde la factura");
            var nota_compra = document.getElementById('nota_compra').value;
            if (nota_compra != '')
                xajax_reporte(xajax.getFormValues("form1"));
            else
                alert("Por favor primero guarde la Factura");
        }

        function cargar_oc() {
            var empresa = <?= $_SESSION['U_EMPRESA'] ?>;
            var sucu = document.getElementById('sucursal').value;

            var clpv = document.getElementById('cliente').value;
            if (clpv != '')
                AjaxWin('<?= $_COOKIE["JIREH_INCLUDE"] ?>', '../inventario_compra/orden_compra.php?sesionId=<?= session_id() ?>&mOp=true&mVer=false&empresa=' + empresa + '&sucursal=' + sucu + '&cliente=' + clpv, 'DetalleShow', 'iframe', 'ORDEN DE COMPRA', '700', '200', '10', '10', '1', '1');
            else
                alert("Ingrese un cliente ... ");
        }

        function cargar_portafolio(empresa, sucursal) {
            var cliente = document.getElementById("cliente").value;
            if (cliente != '') {
                AjaxWin('<?= $_COOKIE["JIREH_INCLUDE"] ?>', '../recepcion_compra/portafolio.php?sesionId=<?= session_id() ?>&mOp=false&mVer=false&cliente=' + cliente + '&empresa=' + empresa + '&sucursal=' + sucursal, 'DetalleShow', 'iframe', 'Portafolio-Productos', '980', '500', '0', '0', '0', '0');
            } else {
                alert("Ingrese Cliente para generar Portafolio");
            }
        }

        function redireccionar() {
            var url = "https://declaraciones.sri.gob.ec/tuportal-internet/";
            window.open(url, '_blank');
            //location.href=pagina
        }

        function clave_acceso_sri(tipo, leerxml, codigoPrincipal) {
            var select_tip = $("#tran option:selected").val();
            if (select_tip != '') {
                if (tipo == 1) {
                    var clave = document.getElementById("clave_acceso_").value;
                    document.getElementById("tipo_factura").value = 1;
                    alerts('Factura Electronica', 'success');
                    document.getElementById("cliente").value = 0;
                    xajax_tipo_factura(xajax.getFormValues("form1"));
                } else {
                    var clave = document.getElementById("clave_acceso").value;

                }
                if (clave.length == 49) {
                    xajax_clave_acceso(xajax.getFormValues("form1"), tipo, leerxml, codigoPrincipal);
                } else {
                    var mensaje = "Debe ingresar los 49 digitos de la clave de acceso";
                    var tipo = 'info';
                    alerts(mensaje, tipo);
                }
            } else {
                var mensaje = "Debe seleccionar el Tipo";
                var tipo = 'warning';
                alerts(mensaje, tipo);
            }
        }

        function clave_valida_proveedor(tipo, leerxml, codigoPrincipal) {
            var select_tip = $("#tran option:selected").val();
            if (select_tip != '') {
                alerts('Procesando ... ', 'success');
                if (tipo == 1) {
                    var clave = document.getElementById("clave_acceso_").value;
                    document.getElementById("tipo_factura").value = 1;
                    document.getElementById("cliente").value = 0;

                } else {
                    var clave = document.getElementById("clave_acceso").value;
                }
                if (clave.length == 49) {
                    xajax_validar_proveedor(xajax.getFormValues("form1"), tipo, leerxml, codigoPrincipal);
                } else {
                    var mensaje = "Debe ingresar los 49 digitos de la clave de acceso";
                    var tipo = 'info';
                    alerts(mensaje, tipo);
                }
            } else {
                var mensaje = "Debe seleccionar el Tipo";
                var tipo = 'warning';
                alerts(mensaje, tipo);
            }
        }

        function formulario_etiqueta() {
            //alert('h');
            var codigo = document.getElementById("codMinv").value;
            if (codigo != '') {
                AjaxWin('<?= $_COOKIE["JIREH_INCLUDE"] ?>', '../inventario_compra/etiqueta.php?sesionId=<?= session_id() ?>&id=' + codigo, 'DetalleShow', 'iframe', 'Generar Etiquetas', '900', '300', '10', '10', '1', '1');
            } else {
                alert('Ingrese Compra para continuar...');
            }
        }


        function cod_retencion(empresa, event) {
            if (event.keyCode == 13 || event.keyCode == 115) { // F4
                var codret = '';
                codret = document.getElementById('cod_ret').value;
                var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";
                var pagina = '../inventario_compra/buscar_codret.php?sesionId=<?= session_id() ?>&mOp=true&mVer=false&codret=' + codret + '&empresa=' + empresa;
                window.open(pagina, "", opciones);
            }
        }


        function anadir_ret() {
            xajax_agrega_modifica_grid_ret(0, xajax.getFormValues("form1"));
        }





        function vista_previa_(id, empr, sucu) {
            var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";
            var pagina = '../reporte_movimiento_inv/vista_previa.php?sesionId=<?= session_id() ?>&codigo=' + id + '&empr=' + empr + '&sucu=' + sucu;
            window.open(pagina, "", opciones);
        }


        function impresion_mov() {
            var id = document.getElementById('codMinv').value;
            var empr = 0;
            var sucu = document.getElementById('sucursal').value;
            //var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";
            //var pagina = '../inventario_compra/vista_previa_inv.php?sesionId=<?= session_id() ?>&codigo='+id+'&empr='+empr+'&sucu='+sucu;
            //window.open(pagina,"",opciones);	

            if (id != '') {
                xajax_vista_previa(xajax.getFormValues("form1"), id);
            } else {
                alert('Ingrese La Compra de Inventario para continuar...!');
            }
        }




        function totales_oc(desc) {
            // IMPRIME EL TOTAL DEL PEDIDO

            // flete
            if (!document.getElementById("flete")) {
                var flete = 0;
            } else {
                var flete = document.getElementById("flete").value;
                if (flete == '') {
                    flete = 0;
                }
            }
            // otros
            if (!document.getElementById("otros")) {
                var otro = 0;
            } else {
                var otro = document.getElementById("otros").value;
                if (otro == '') {
                    otro = 0;
                }
            }
            //anticipo
            if (!document.getElementById("anticipo")) {
                var anticipo = 0;
            } else {
                var anticipo = document.getElementById("anticipo").value;
                if (anticipo == '') {
                    anticipo = 0;
                }
            }
            xajax_total_grid(desc, flete, otro, anticipo, xajax.getFormValues("form1"));
        }




        function orden_compra_consulta() {
            xajax_orden_compra_reporte(xajax.getFormValues("form1"));
        }


        function abre_modal() {
            $("#mostrarmodal").modal("show");
        }


        function cargar_oc_det_gen(serial, empresa, sucursal) {
            xajax_orden_compra_reporte_det(serial, empresa, sucursal, xajax.getFormValues("form1"));
        }


        function abre_modal2() {
            $("#mostrarmodal2").modal("show");
        }

        function generaReporteCompras() {
            var cliente = document.getElementById('cliente').value;
            if (cliente != '') {
                $("#miModal").modal("show");
                xajax_generaReporteCompras(xajax.getFormValues("form1"));
            } else {
                alert('Ingrese Proveedor para continuar...!');
            }
        }


        function elimina_detalle(cont) {
            var empresa = document.getElementById("empresa").value;
            xajax_elimina_detalle(cont, empresa, xajax.getFormValues("form1"));
        }


        function agregar_detalle(i, id) {
            xajax_form_lote(id, xajax.getFormValues("form1"));
        }


        function abre_modal3() {
            $("#mostrarmodal3").modal("show");
        }

        function procesar_lote(id) {
            xajax_procesar_lote(id, xajax.getFormValues("form1"));
            $("#mostrarmodal3").modal("hide");
        }


        function precio_inv(id) {
            xajax_form_precio_inv(id, xajax.getFormValues("form1"));
        }

        function abre_modal4() {
            $("#mostrarmodal4").modal("show");
        }

        function procesar_precio(id) {
            xajax_procesar_precio_inv(id, xajax.getFormValues("form1"));
            $("#mostrarmodal4").modal("hide");
        }


        function cargar_electronica() {
            xajax_cargar_electronica(xajax.getFormValues("form1"));
        }


        function cargar_digito_ret() {
            xajax_cargar_digito_ret(xajax.getFormValues("form1"));
        }


        function archivosAdjuntos() {
            var id = document.getElementById("cliente").value;
            if (id != '') {
                document.getElementById("miAdjunto").innerHTML = '';
                xajax_archivosAdjuntos(xajax.getFormValues("form1"));
            } else {
                alert('Seleccione Proveedor para continuar..');
            }
        }


        function recalcular_fpago(tipo) {
            xajax_recalcular_fpago(xajax.getFormValues("form1"), tipo);
        }

        function anio_fecha_abierto() {
            xajax_anio_fecha_abierto(xajax.getFormValues("form1"));
        }



        function validar_fecha_elaboracion() {
            var fecha_elaboracion = document.getElementById('fecha_ela').value;

            var date = new Date();
            var day_actual = date.getDate()
            var month_actual = date.getMonth() + 1;
            var year_actual = date.getFullYear();
            const arrayFecha = fecha_elaboracion.split("-");
            var year_form = arrayFecha[0];
            var month_form = arrayFecha[1];
            var day_form = arrayFecha[2];
            // if (year_form > year_actual || month_form > month_actual) {
            //     alert('La fecha de elaboracion no puede ser mayor al ultimo dia del mes actual');
            //     document.getElementById('fecha_ela').value = "'" + year_actual + "-" + month_actual + "-" + day_actual + "'"
            // }
        }

        function validar_fecha_caducidad() {
            var fecha_caducidad = document.getElementById('fecha_cad').value;
            var fecha_elaboracion = document.getElementById('fecha_ela').value;

            const arrayFechaElaboracion = fecha_elaboracion.split("-");
            var year_ela = arrayFechaElaboracion[0];
            var month_ela = arrayFechaElaboracion[1];
            var day_ela = arrayFechaElaboracion[2];
            const arrayFechaCaducidad = fecha_caducidad.split("-");
            var year_cad = arrayFechaCaducidad[0];
            var month_cad = arrayFechaCaducidad[1];
            var day_cad = arrayFechaCaducidad[2];

            if (year_ela > year_cad || month_ela > month_cad || (day_ela > day_cad && month_ela == month_cad)) {
                alert('La fecha de caducidad no puede ser menor a la fecha de elaboracion');
                document.getElementById('fecha_cad').value = "'" + year_ela + "-" + month_ela + "-" + day_ela + "'"
            }
        }



        var validar_fecha_lote = 'N';

        function vaciar_validacion_fecha() {
            validar_fecha_lote = 'N';
        }

        function habilita(tipo) {

            document.getElementById("serie_prod").value = '';
            document.getElementById("lote_prod").value = '';
            document.getElementById("fecha_ela").value = '';
            document.getElementById("fecha_cad").value = '';

            // lote
            if (tipo == 1) {
                validar_fecha_lote = 'S'

                document.getElementById("lote_prod").style.display = 'block';
                document.getElementById("lote_prod_txt").style.display = 'block';
                document.getElementById("cantidad").readOnly = false;


                document.getElementById("serie_prod").style.display = 'none';
                document.getElementById("serie_prod_txt").style.display = 'none';

                document.getElementById("lblfecha_ela").style.display = 'block';
                document.getElementById("fecha_ela").style.display = 'block';

                document.getElementById("lblfecha_cad").style.display = 'block';
                document.getElementById("fecha_cad").style.display = 'block';

                document.getElementById("mac_prod_txt").style.display = 'none';
                document.getElementById("mac_ad_prod").style.display = 'none';

                // serie
            } else if (tipo == 3) {
                document.getElementById("serie_prod").style.display = 'block';
                document.getElementById("serie_prod_txt").style.display = 'block';
                document.getElementById("cantidad").value = 1;
                document.getElementById("cantidad").readOnly = true;

                document.getElementById("lote_prod").style.display = 'none';
                document.getElementById("lote_prod_txt").style.display = 'none';

                document.getElementById("lblfecha_ela").style.display = 'none';
                document.getElementById("fecha_ela").style.display = 'none';

                document.getElementById("lblfecha_cad").style.display = 'none';
                document.getElementById("fecha_cad").style.display = 'none';

                document.getElementById("mac_prod_txt").style.display = 'none';
                document.getElementById("mac_ad_prod").style.display = 'none';

                // ni lote ni serie
            } else if (tipo == 47) {
                document.getElementById("serie_prod").style.display = 'block';
                document.getElementById("serie_prod_txt").style.display = 'block';
                document.getElementById("cantidad").value = 1;
                document.getElementById("cantidad").readOnly = true;

                document.getElementById("mac_prod_txt").style.display = 'block';
                document.getElementById("mac_ad_prod").style.display = 'block';

                document.getElementById("lote_prod").style.display = 'none';
                document.getElementById("lote_prod_txt").style.display = 'none';

                document.getElementById("lblfecha_ela").style.display = 'none';
                document.getElementById("fecha_ela").style.display = 'none';

                document.getElementById("lblfecha_cad").style.display = 'none';
                document.getElementById("fecha_cad").style.display = 'none';

                // ni lote ni serie
            } else {
                validar_fecha_lote = 'N'

                document.getElementById("lote_prod").style.display = 'none';
                document.getElementById("lote_prod_txt").style.display = 'none';
                document.getElementById("serie_prod").style.display = 'none';
                document.getElementById("serie_prod_txt").style.display = 'none';
                document.getElementById("cantidad").readOnly = false;


                document.getElementById("lblfecha_ela").style.display = 'none';
                document.getElementById("fecha_ela").style.display = 'none';

                document.getElementById("lblfecha_cad").style.display = 'none';
                document.getElementById("fecha_cad").style.display = 'none';

                document.getElementById("mac_prod_txt").style.display = 'none';
                document.getElementById("mac_ad_prod").style.display = 'none';

            }

        }


        function procesoSerie() {
            var factura = document.getElementById('factura').value;
            var tran = document.getElementById('tran').value;
            // alert(factura+' - '+tran);
            if (factura != '' && tran != '') {
                AjaxWin('<?= $_COOKIE["JIREH_INCLUDE"] ?>', '../inventario_serie_compra/inventario.php?&factura=' + factura + '&tran=' + tran + '&sesionId=<?= session_id() ?>&mOp=false&mVer=false&id=', 'DetalleShow', 'iframe', 'Proceso / Serie inventario', '1100', '500', '0', '0', '0', '0');
            } else {
                alert('Guarde una factura para continuar');
            }
        }

        function abre_modal5() {
            $("#mostrarmodal5").modal("show");
        }


        function agregarArchivo() {
            xajax_agrega_modifica_gridAdj(0, xajax.getFormValues("form1"), '', '');
        }


        function impresion_asto() {
            var sucursal = document.getElementById('sucursal').value;
            var empresa = <? echo  $_SESSION['U_EMPRESA']; ?>;
            var ejer_cod = document.getElementById('ejercicio').value;
            var prdo_cod = document.getElementById('periodo').value;
            var asto_cod = document.getElementById('asiento').value;
            var cod_prove = document.getElementById("cliente").value;

            xajax_genera_pdf_doc(empresa, sucursal, asto_cod, ejer_cod, prdo_cod);
        }



        function cargar_coti() {
            xajax_cargar_coti(xajax.getFormValues("form1"));
        }


        function fecha_final_rs(fec) {
            document.form1.fecha_final.value = fec;
        }



        function datos_clpv(cod, cli, ruc, dir, tel, cel, vend, cont, pre, fpago, tpago, fec, auto, serie, fec_venc, dia, contr, ini, fin, cuenta, correo) {

            document.form1.cliente.value = cod;
            document.form1.cliente_nombre.value = cli;
            document.form1.ruc.value = ruc;
            document.form1.tipo_pago.value = tpago;
            document.form1.forma_pago1.value = fpago;
            //document.form1.auto_prove.value 		= auto;
            //document.form1.fecha_validez.value 	= fec;
            //document.form1.serie_prove.value 		= serie;

            var f1 = fec_venc;
            var f2 = new Date();
            if (f1 > f2) {
                document.form1.fecha_entrega.value = fec_venc;
            }

            if (dia == 0) {
                var fecha_compra = document.getElementById('fecha_pedido').value;
                document.form1.fecha_entrega.value = fecha_compra;
            }

            document.form1.fecha_final.value = fec_venc;
            document.form1.plazo.value = dia;
            document.form1.dias_fp.value = dia;
            document.form1.contri_prove.value = contr;
            document.form1.cuenta_prove.value = cuenta;
            document.form1.dir_prove.value = dir;
            document.form1.tel_prove.value = tel;
            document.form1.correo_prove.value = correo;

            recalcular_fpago(2);

            document.form1.producto.focus();
            $("#ModalClpv").modal("hide");
        }

        function datos_clpv_lista(cod, cli, ruc, dir, tel, cel, vend, cont, pre, fpago, tpago, fec, auto, serie, fec_venc, dia, contr, ini, fin, cuenta, correo) {

            document.form1.cliente_codigo_listac.value = cod;
            document.form1.cliente_nombre_listac.value = cli;
            $("#ModalClpv").modal("hide");
        }

        function datos_prod(i, lote, serie, mac) {
            console.log(lote + ' - ' + serie + ' - ' + mac);
            xajax_datos_prod(i, lote, serie, mac, xajax.getFormValues("form1"));
        }


        function datos_prod2(l, s) {
            $("#ModalProd").modal("hide");
        }

        function cargar_recepcion() {
            var cliente = document.getElementById('cliente').value;
            if (cliente != '') {
                $("#ModalRECO").modal("show");
                xajax_recepcion_compra(xajax.getFormValues("form1"));
            } else {
                Swal.fire({
                    type: 'warning',
                    title: 'Por favor Seleccion Suplidor...',
                    showConfirmButton: false,
                    timer: 1600
                })
            }
        }

        function cargar_reco_det_gen(serial, empresa, sucursal) {
            $("#ModalRECOD").modal("show");
            xajax_recepcion_compra_det(serial, empresa, sucursal, xajax.getFormValues("form1"));
        }

        function cargar_reco(empresa, sucursal, cliente) {
            xajax_cargar_reco(empresa, sucursal, cliente, xajax.getFormValues("form1"));
            $("#ModalRECO").modal("hide");
        }


        function cargar_fecha_reco(a) {
            document.form1.fecha_pedido.value = a;
            document.form1.fecha_entrega.value = a;
            document.form1.fecha_inicio.value = a;
            document.form1.fecha_final.value = a;
        }


        //alertas
        function alerts(mensaje, tipo) {
            if (tipo == 'success') {
                Swal.fire({
                    type: tipo,
                    title: mensaje,
                    showCancelButton: false,
                    showConfirmButton: false,
                    timer: 2000,
                    width: '600',
                })
            } else {

                Swal.fire({
                    type: tipo,
                    title: mensaje,
                    showCancelButton: false,
                    showConfirmButton: true,
                    width: '600',

                })
            }

        }


        function valida_existe_factura() {
            var clave = document.getElementById('clave_acceso_').value;
            xajax_valida_existe_factura(xajax.getFormValues("form1"), clave);
        }

        function alert_existe_factura() {
            Swal.fire({
                title: "Factura ya ingresada!",
                text: "Esta factura ya fue ingresada!",
                type: "warning",
            });
        }

        function crear_proveedor() {

            Swal.fire({
                title: 'El proveedor no se encuentra registrado en el sistema, ¿Desea crearlo?',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Crear Proveedor',
                allowOutsideClick: false,
                width: '40%',
            }).then((result) => {
                if (result.value) {
                    window.open('../ficha_proveedor/ficha_proveedor.php?sesionId=<?= session_id() ?>', '_blank');


                }
            });
        }




        function alert_validacion_ride() {

            var leerxml = 0;
            var codigoPrincipal = 0;
            validarTran = document.getElementById('tran').value;

            if (validarTran == '') {
                Swal.fire({
                    title: 'Seleccione Tipo!',
                    text: '',
                    icon: 'error'
                })
            } else {
                Swal.fire({
                    title: "Confirmacion",
                    text: "Desea cargar la informacion desde el xml!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#519361',
                    cancelButtonColor: '#32abff',
                    confirmButtonText: 'Si, Desde XML!',
                    cancelButtonText: 'No, Desde orden compra!',
                    closeOnConfirm: false,
                    closeOnCancel: false
                }).then(function(isConfirm) {
                    console.log(isConfirm['value']);
                    if (isConfirm['value'] == true) {
                        Swal.fire({
                            title: "Tipo Codigo Producto",
                            text: "Desea por codigo Principal o codigo Auxiliar!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: '#32abff',
                            cancelButtonColor: '#32abff',
                            confirmButtonText: 'Codigo Principal',
                            cancelButtonText: 'Codigo Auxiliar',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        }).then(function(isConfirm) {
                            if (isConfirm['value'] == true) {
                                Swal.fire({
                                    title: 'Se llenara la informacion por el codigo Principal!',
                                    text: '',
                                    icon: 'success'
                                }).then(function() {

                                    leerxml = 1;
                                    codigoPrincipal = 1;

                                    clave_acceso_sri(1, leerxml, codigoPrincipal)
                                });
                            } else {
                                Swal.fire({
                                    title: 'Se llenara la informacion por el codigo Auxiliar!',
                                    text: '',
                                    icon: 'success'
                                }).then(function() {

                                    leerxml = 1;
                                    codigoPrincipal = 0;

                                    clave_acceso_sri(1, leerxml, codigoPrincipal)
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Carga por Orden de Compra!',
                            text: 'Se cargara la informacion desde la orden de compra',
                            icon: 'success'
                        }).then(function() {
                            clave_acceso_sri(1, leerxml, codigoPrincipal)
                        });
                    }
                })
            }

        }





        function validar_proveedor() {

            var leerxml = 0;
            var codigoPrincipal = 0;
            leerxml = 1;
            codigoPrincipal = 1;
            clave_valida_proveedor(1, leerxml, codigoPrincipal)


        }


        function cargar_rd() {
            xajax_cargar_rd(xajax.getFormValues("form1"));
        }





        // carga imagen a servidor
        function upload_image(id) { //Funcion encargada de enviar el archivo via AJAX
            $(".upload-msg").text('Cargando...');
            var inputFileImage = document.getElementById(id);
            var file = inputFileImage.files[0];
            var data = new FormData();
            data.append(id, file);

            $.ajax({
                url: "upload.php?id=" + id, // Url to which the request is send
                type: "POST", // Type of request to be send, called as method
                data: data, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                contentType: false, // The content type used when sending data to the server.
                cache: false, // To unable request pages to be cached
                processData: false, // To send DOMDocument or non processed data file it is set to false
                success: function(data) // A function to be called if request succeeds
                {
                    $(".upload-msg").html(data);
                    window.setTimeout(function() {
                        $(".alert-dismissible").fadeTo(500, 0).slideUp(500, function() {
                            $(this).remove();
                        });
                    }, 5000);
                }
            });
        }


        function consultar() {
            // COMERCIAL
            //jsShowWindowLoad();
            xajax_cargar_ord_compra(xajax.getFormValues("form1"));
            xajax_cargar_ord_compra_respaldo(xajax.getFormValues("form1"));
        }


        function consultar_compras() {
            // COMERCIAL
            //jsShowWindowLoad();
            xajax_consultar_compras(xajax.getFormValues("form1"));
        }


        function f_filtro_ejercicio(data) {
            //alert(data);
            xajax_f_filtro_ejercicio(xajax.getFormValues("form1"), data);
        }

        function f_filtro_periodo(data) {
            xajax_f_filtro_periodo(xajax.getFormValues("form1"), data);
        }


        function seleccionaItem(empr, sucu, ejer, mes, asto) {
            $("#miModal2").modal("show");
            $("#divInfo").html('');
            $("#divDirectorio").html('');
            $("#divRetencion").html('');
            $("#divDiario").html('');
            $("#divAdjuntos").html('');
            xajax_verDiarioContable(xajax.getFormValues("form1"), empr, sucu, ejer, mes, asto);
        }

        function vista_previa_diario(idempresa, sucursal, cod_prove, asto_cod, ejer_cod, prdo_cod) {
            xajax_genera_pdf_doc_compras(idempresa, sucursal, asto_cod, ejer_cod, prdo_cod);
            // var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";
            // var pagina = '../contabilidad_comprobante/vista_previa.php?sesionId=<?= session_id() ?>&sucursal='+  sucursal+'&cod_prove='+cod_prove+'&asto='+asto_cod+'&ejer='+ejer_cod+'&mes='+prdo_cod;
            // window.open(pagina, "", opciones);
        }

        function genera_documento(tipo_documento, id, clavAcce, clpv, num_fact, ejer, asto, fec_emis, sucu) {
            xajax_genera_documento(tipo_documento, id, clavAcce, clpv, num_fact, ejer, asto, fec_emis, sucu);
        }

        function recalcular_precio_venta(nomp_cod_nomp) {
            xajax_recalcular_precio_venta(nomp_cod_nomp, xajax.getFormValues("form1"));
        }


        // --------------------------------------------------------------------------------------
        // Evaluacion control calidad
        // --------------------------------------------------------------------------------------

        function abrir_evaluacion() {
            $("#ModalEval").modal("show");
            xajax_abrir_evaluacion(xajax.getFormValues("form1"), 1);
        }

        function guardar_nueva_evaluacion(cont) {
            $("#ModalEval").modal("hide");
            xajax_guardar_nueva_evaluacion(xajax.getFormValues("form1"), cont);
        }

        function cargar_novedades_recepcion() {
            document.getElementById('novedades_class').style.display = "block";
        }

        function guardar_evaluacion(id) {
            xajax_guardar_evaluacion(xajax.getFormValues("form1"), id);
        }

        function generar_pdf_recepcion() {
            var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=.370, top=255, left=130";
            var pagina = '../../Include/documento_pdf.php?sesionId=<?= session_id() ?>';
            window.open(pagina, "", opciones)
        }

        // --------------------------------------------------------------------------------------
        // FIN Evaluacion control calidad
        // --------------------------------------------------------------------------------------



        // --------------------------------------------------------------------------------------
        // Tomar las series de cada producto de la orden de compra
        // --------------------------------------------------------------------------------------


        function agregar_series(num_comp_oc, cod_prod_oc, dmov_cod, $cont_datagrid) {
            xajax_agregar_series(num_comp_oc, cod_prod_oc, dmov_cod, $cont_datagrid, xajax.getFormValues("form1"));
        }

        function abre_modal_codigo_unico() {
            $("#mostrarModalCodUnic").modal("show");
        }

        function cierra_modal_codigo_unico() {
            $("#mostrarModalCodUnic").modal("hide");
        }

        function consultar_codigo_unico(num_comp_oc, cod_prod_oc, dmov_cod, $cont_datagrid) {
            xajax_consultar_codigo_unico(num_comp_oc, cod_prod_oc, dmov_cod, $cont_datagrid, xajax.getFormValues("form1"));
        }

        function verificar_codigos_u_insertados(num_comp_oc, cod_prod_oc, dmov_cod, cont_datagrid) {
            xajax_verificar_codigos_u_insertados(num_comp_oc, cod_prod_oc, dmov_cod, cont_datagrid, xajax.getFormValues("form1"));
        }

        function eliminar_codigo_unico_recep(num_comp_oc, cod_prod_oc, dmov_cod, num_codigo_unico, cont_datagrid) {
            xajax_eliminar_codigo_unico_recep(num_comp_oc, cod_prod_oc, dmov_cod, num_codigo_unico, cont_datagrid, xajax.getFormValues("form1"));
        }

        function finalizar_oc(num_comp_oc) {
            Swal.fire({
                title: 'Estas seguro que deseas cerrar esta orden de compra ?',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                allowOutsideClick: false,
                width: '40%',
            }).then((result) => {
                if (result.value) {
                    xajax_finalizar_oc(num_comp_oc, xajax.getFormValues("form1"));
                    $("#ModalRECO").modal("hide");
                }
            });
        }

        // --------------------------------------------------------------------------------------
        // FIN Tomar las series de cada producto de la orden de compra
        // --------------------------------------------------------------------------------------


        // --------------------------------------------------------------------------------------
        // cargar compra de inventario cuando se vaya a modificar
        // --------------------------------------------------------------------------------------
        function cargar_invetario_compra_ad(num_comp_edit) {
            xajax_cargar_invetario_compra_ad(num_comp_edit, xajax.getFormValues("form1"));
        }

        function cargar_info_fact_ad(num_comp_edit) {
            xajax_cargar_info_fact_ad(num_comp_edit, xajax.getFormValues("form1"));
        }

        function eliminar_movimiento(num_comp, cod_empr, cod_sucu, asto_cod_asto, cod_prdo, cod_ejer, cod_tran, minv_cod_clpv) {
            xajax_eliminar_movimiento(xajax.getFormValues("form1"), num_comp, cod_empr, cod_sucu, asto_cod_asto, cod_prdo, cod_ejer, cod_tran, minv_cod_clpv);
        }
        // --------------------------------------------------------------------------------------
        // FIn cargar compra de inventario cuando se vaya a modificar
        // --------------------------------------------------------------------------------------



        // -----------------------------------------------------------------------------------------
        // Cierre de anticipo en compras de inventario
        // -----------------------------------------------------------------------------------------

        function cerrar_anticipo_modulo() {
            //var nota_compra = document.getElementById('nota_compra').value;
            codpedi = 0;
            tipo_solicitud = 0;
            //num_comp_cierre_ant = 753;
            var num_comp_cierre_ant = document.getElementById('codMinv').value;
            var nota_compra = document.getElementById('nota_compra').value;
            if (nota_compra != '') {

                alertSwal('Cargando Diario', 'success');
                var pagina = '../comprobante_base/comprobante.php?sesionId=<?= session_id() ?>&codigo_solicitud=' + codpedi + '&tipo_solicitud=' + tipo_solicitud + '&num_comp_cierre_ant=' + num_comp_cierre_ant;
                var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";
                window.open(pagina, "", opciones);

            } else {
                alert("Por favor primero guarde la Factura");
            }
        }

        // -----------------------------------------------------------------------------------------
        // FIN Cierre de anticipo en compras de inventario
        // -----------------------------------------------------------------------------------------

        function refreshTablaIn() {
            parent.consultar();
        }
    </script>



    <!--DIBUJA FORMULARIO FILTRO-->

    <body>
        <div class="container-fluid">
            <form id="form1" name="form1" action="javascript:void(null);" novalidate="novalidate">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#divCompraMenu" aria-controls="divCompraMenu" role="tab" data-toggle="tab">COMPRA</a></li>
                        <li role="presentation"><a href="#divPagoMenu" aria-controls="divPagoMenu" role="tab" data-toggle="tab">FORMA PAGO</a></li>
                        <li role="presentation"><a href="#divRetencionMenu" aria-controls="divRetencionMenu" role="tab" data-toggle="tab">RETENCION</a></li>
                        <li role="presentation"><a href="#divListaCompras" aria-controls="divListaCompras" role="tab" data-toggle="tab">LISTA COMPRAS</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="divCompraMenu">
                            <div id="divFormularioCabecera"></div>
                            <div id="divFormularioDetalle" class="table-responsive"></div>
                            <div id="divTotal"></div>
                            <div id="divFormularioDetalle2"></div>
                            <div id="divFormularioModalCodigoUnico" class="table-responsive" style="text-align: center"></div>

                        </div>
                        <div role="tabpanel" class="tab-pane" id="divPagoMenu">
                            <div id="divFormularioFp" class="table-responsive"></div>
                            <div id="divFormularioDetalleFP_DET" class="table-responsive"></div>
                            <div id="divFormularioDetalle_FP" class="table-responsive"></div>
                            <div id="divTotalFP" class="table-responsive"></div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="divRetencionMenu">
                            <div id="divFormularioRET" class="table-responsive"></div>
                            <div id="divFormularioCabeceraRET" class="table-responsive"></div>
                            <div id="divFormularioDetalleRET" class="table-responsive"></div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="divListaCompras">
                            <div id="divFormularioCebeceraC" class="table-responsive"></div>
                            <div id="divFormularioDetalleC" class="table-responsive"></div>
                        </div>
                    </div>
                </div>



                <div style="width: 100%;">
                    <div id="extra"></div>
                    <div id="extra2"></div>
                    <div id="extra3"></div>
                    <div id="precio_modal"></div>
                    <div id="miAdjunto"></div>
                    <div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>

                    <div class="modal fade" id="ModalClpv" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
                    <div class="modal fade" id="ModalProd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true"></div>
                    <div class="modal fade" id="ModalGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true"></div>
                    <div class="modal fade" id="ModalRECO" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true"></div>
                    <div class="modal fade" id="ModalRECOD" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true"></div>
                    <div class="modal fade" id="ModalEval" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true"></div>

                </div>

                <div class="modal fade" id="miModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">DIARIO CONTABLE <span id="divTituloAsto"></span></h4>
                            </div>
                            <div class="modal-body">
                                <div>
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active"><a href="#divInfo" aria-controls="divInfo" role="tab" data-toggle="tab">Informacion</a></li>
                                        <li role="presentation"><a href="#divDirectorio" aria-controls="divDirectorio" role="tab" data-toggle="tab">Directorio</a></li>
                                        <li role="presentation"><a href="#divRetencion" aria-controls="divRetencion" role="tab" data-toggle="tab">Retencion</a></li>
                                        <li role="presentation"><a href="#divDiario" aria-controls="divDiario" role="tab" data-toggle="tab">Diario</a></li>
                                        <li role="presentation"><a href="#divAdjuntos" aria-controls="divAdjuntos" role="tab" data-toggle="tab">Adjuntos</a></li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="divInfo">...</div>
                                        <div role="tabpanel" class="tab-pane" id="divDirectorio">...</div>
                                        <div role="tabpanel" class="tab-pane" id="divRetencion">...</div>
                                        <div role="tabpanel" class="tab-pane" id="divDiario">...</div>
                                        <div role="tabpanel" class="tab-pane" id="divAdjuntos">...</div>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>


            </form>
        </div>
        <div id="divGrid"></div>
        <br><br><br><br><br><br><br>
    </body>
    <script>
        genera_formulario();

        function init() {
            var table = $('#tbclientes').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        title: 'Lista de Compras',
                        titleAttr: 'Click para Copiar',
                        text: '<div class="contenedor_copiar"><i class="fa fa-clipboard copiar"></i><label class="labe"></label></div>',
                        exportOptions: {
                            format: {
                                body: function(data, row, column, node) {
                                    var retorno = "",
                                        tag, respuesta = "",
                                        reponer = [];

                                    tag = $(node).find('input');
                                    if (tag.length > 0) {
                                        retorno = retorno + ($(tag).map(function() {
                                            return $(this).val();
                                        }).get().join(','));
                                    }

                                    respuesta = (retorno != "") ? retorno : $.trim($(node).text());
                                    for (i = 0; i < reponer.length; i++) {
                                        $(node).append(reponer[i]);
                                    }

                                    return respuesta;
                                }
                            },
                        }
                    }, {
                        extend: 'excelHtml5',
                        title: 'Lista de Compras',
                        titleAttr: 'Click para descargar como Excel',
                        text: '<div class="contenedor_excel"><i class="fa fa-file-excel-o excel"></i><label class="labe"></label></div>',
                        exportOptions: {
                            format: {
                                body: function(data, row, column, node) {
                                    var retorno = "",
                                        tag, respuesta = "",
                                        reponer = [];

                                    tag = $(node).find('input');
                                    if (tag.length > 0) {
                                        retorno = retorno + ($(tag).map(function() {
                                            return $(this).val();
                                        }).get().join(','));
                                    }

                                    respuesta = (retorno != "") ? retorno : $.trim($(node).text());
                                    for (i = 0; i < reponer.length; i++) {
                                        $(node).append(reponer[i]);
                                    }

                                    return respuesta;
                                }
                            },
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        title: 'Lista de Compras',
                        titleAttr: 'Click para descargar como CSV',
                        text: '<div class="contenedor_csv"><i class="fa fa-file-text-o csv"></i><label class="labe"></label></div>',
                        exportOptions: {
                            format: {
                                body: function(data, row, column, node) {
                                    var retorno = "",
                                        tag, respuesta = "",
                                        reponer = [];

                                    tag = $(node).find('input');
                                    if (tag.length > 0) {
                                        retorno = retorno + ($(tag).map(function() {
                                            return $(this).val();
                                        }).get().join(','));
                                    }

                                    respuesta = (retorno != "") ? retorno : $.trim($(node).text());
                                    for (i = 0; i < reponer.length; i++) {
                                        $(node).append(reponer[i]);
                                    }

                                    return respuesta;
                                }
                            },
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: function() {
                            return "Lista de Compras";
                        },
                        orientation: 'landscape',
                        pageSize: 'A2',
                        text: '<div class="contenedor_pdf"><i class="fa fa-file-pdf-o pdf"></i><label class="labe"></label></div>',
                        exportOptions: {
                            format: {
                                body: function(data, row, column, node) {
                                    var retorno = "",
                                        tag, respuesta = "",
                                        reponer = [];

                                    tag = $(node).find('input');
                                    if (tag.length > 0) {
                                        retorno = retorno + ($(tag).map(function() {
                                            return $(this).val();
                                        }).get().join(','));
                                    }

                                    respuesta = (retorno != "") ? retorno : $.trim($(node).text());
                                    for (i = 0; i < reponer.length; i++) {
                                        $(node).append(reponer[i]);
                                    }

                                    return respuesta;
                                }
                            },
                        }
                    }, {
                        extend: 'print',
                        title: 'Lista de Compras',
                        titleAttr: 'Click para Imprimir',
                        text: '<div class="contenedor_imprimir"><i class="fa fa-print imprimir"></i><label class="labe"></label></div>',
                        exportOptions: {
                            format: {
                                body: function(data, row, column, node) {
                                    var retorno = "",
                                        tag, respuesta = "",
                                        reponer = [];

                                    tag = $(node).find('input');
                                    if (tag.length > 0) {
                                        retorno = retorno + ($(tag).map(function() {
                                            return $(this).val();
                                        }).get().join(','));
                                    }

                                    respuesta = (retorno != "") ? retorno : $.trim($(node).text());
                                    for (i = 0; i < reponer.length; i++) {
                                        $(node).append(reponer[i]);
                                    }

                                    return respuesta;
                                }
                            },
                        }
                    },
                ],
                processing: "<i class='fa fa-spinner fa-spin' style='font-size:24px; color: #34495e;'></i>",
                "language": {
                    "search": "<i class='fa fa-search'></i>",
                    "searchPlaceholder": "Buscar",
                    'paginate': {
                        'previous': 'Anterior',
                        'next': 'Siguiente'
                    },
                    "zeroRecords": "No se encontro datos",
                    "info": "Mostrando _START_ a _END_ de  _TOTAL_ Total",
                    "infoEmpty": "",
                    "infoFiltered": "(Mostrando _MAX_ Registros Totales)",
                },
                "paging": true,
                "ordering": true,
                "info": true,
                "pageLength": 1000
            });
            table.search().draw();
        }


        function init2() {

            var table = $('#tbcompras').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        title: 'Lista de Compras',
                        titleAttr: 'Click para Copiar',
                        text: '<div class="contenedor_copiar"><i class="fa fa-clipboard copiar"></i><label class="labe"></label></div>',
                        exportOptions: {
                            format: {
                                body: function(data, row, column, node) {
                                    var retorno = "",
                                        tag, respuesta = "",
                                        reponer = [];

                                    tag = $(node).find('input');
                                    if (tag.length > 0) {
                                        retorno = retorno + ($(tag).map(function() {
                                            return $(this).val();
                                        }).get().join(','));
                                    }

                                    respuesta = (retorno != "") ? retorno : $.trim($(node).text());
                                    for (i = 0; i < reponer.length; i++) {
                                        $(node).append(reponer[i]);
                                    }

                                    return respuesta;
                                }
                            },
                        }
                    }, {
                        extend: 'excelHtml5',
                        title: 'Lista de Compras',
                        titleAttr: 'Click para descargar como Excel',
                        text: '<div class="contenedor_excel"><i class="fa fa-file-excel-o excel"></i><label class="labe"></label></div>',
                        exportOptions: {
                            format: {
                                body: function(data, row, column, node) {
                                    var retorno = "",
                                        tag, respuesta = "",
                                        reponer = [];

                                    tag = $(node).find('input');
                                    if (tag.length > 0) {
                                        retorno = retorno + ($(tag).map(function() {
                                            return $(this).val();
                                        }).get().join(','));
                                    }

                                    respuesta = (retorno != "") ? retorno : $.trim($(node).text());
                                    for (i = 0; i < reponer.length; i++) {
                                        $(node).append(reponer[i]);
                                    }

                                    return respuesta;
                                }
                            },
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        title: 'Lista de Compras',
                        titleAttr: 'Click para descargar como CSV',
                        text: '<div class="contenedor_csv"><i class="fa fa-file-text-o csv"></i><label class="labe"></label></div>',
                        exportOptions: {
                            format: {
                                body: function(data, row, column, node) {
                                    var retorno = "",
                                        tag, respuesta = "",
                                        reponer = [];

                                    tag = $(node).find('input');
                                    if (tag.length > 0) {
                                        retorno = retorno + ($(tag).map(function() {
                                            return $(this).val();
                                        }).get().join(','));
                                    }

                                    respuesta = (retorno != "") ? retorno : $.trim($(node).text());
                                    for (i = 0; i < reponer.length; i++) {
                                        $(node).append(reponer[i]);
                                    }

                                    return respuesta;
                                }
                            },
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: function() {
                            return "Lista de Compras";
                        },
                        orientation: 'landscape',
                        pageSize: 'A2',
                        text: '<div class="contenedor_pdf"><i class="fa fa-file-pdf-o pdf"></i><label class="labe"></label></div>',
                        exportOptions: {
                            format: {
                                body: function(data, row, column, node) {
                                    var retorno = "",
                                        tag, respuesta = "",
                                        reponer = [];

                                    tag = $(node).find('input');
                                    if (tag.length > 0) {
                                        retorno = retorno + ($(tag).map(function() {
                                            return $(this).val();
                                        }).get().join(','));
                                    }

                                    respuesta = (retorno != "") ? retorno : $.trim($(node).text());
                                    for (i = 0; i < reponer.length; i++) {
                                        $(node).append(reponer[i]);
                                    }

                                    return respuesta;
                                }
                            },
                        }
                    }, {
                        extend: 'print',
                        title: 'Lista de Compras',
                        titleAttr: 'Click para Imprimir',
                        text: '<div class="contenedor_imprimir"><i class="fa fa-print imprimir"></i><label class="labe"></label></div>',
                        exportOptions: {
                            format: {
                                body: function(data, row, column, node) {
                                    var retorno = "",
                                        tag, respuesta = "",
                                        reponer = [];

                                    tag = $(node).find('input');
                                    if (tag.length > 0) {
                                        retorno = retorno + ($(tag).map(function() {
                                            return $(this).val();
                                        }).get().join(','));
                                    }

                                    respuesta = (retorno != "") ? retorno : $.trim($(node).text());
                                    for (i = 0; i < reponer.length; i++) {
                                        $(node).append(reponer[i]);
                                    }

                                    return respuesta;
                                }
                            },
                        }
                    },
                ],
                processing: "<i class='fa fa-spinner fa-spin' style='font-size:24px; color: #34495e;'></i>",
                "language": {
                    "search": "<i class='fa fa-search'></i>",
                    "searchPlaceholder": "Buscar",
                    'paginate': {
                        'previous': 'Anterior',
                        'next': 'Siguiente'
                    },
                    "zeroRecords": "No se encontro datos",
                    "info": "Mostrando _START_ a _END_ de  _TOTAL_ Total",
                    "infoEmpty": "",
                    "infoFiltered": "(Mostrando _MAX_ Registros Totales)",
                },
                "paging": true,
                "ordering": true,
                "info": true,
                "pageLength": 1000
            });
            table.search().draw();
        }

        function init_prod() {
            var search = '<?= $ruc ?>';
            var table = $('#tbclientesProd').DataTable({
                dom: 'Bfrtip',
                processing: "<i class='fa fa-spinner fa-spin' style='font-size:24px; color: #34495e;'></i>",
                "language": {
                    "search": "<i class='fa fa-search'></i>",
                    "searchPlaceholder": "Buscar",
                    'paginate': {
                        'previous': 'Anterior',
                        'next': 'Siguiente'
                    },
                    "zeroRecords": "No se encontro datos",
                    "info": "Mostrando _START_ a _END_ de  _TOTAL_ Total",
                    "infoEmpty": "",
                    "infoFiltered": "(Mostrando _MAX_ Registros Totales)",
                },
                "paging": true,
                "ordering": true,
                "info": true,
            });
            table.buttons().remove();
            table.search(search).draw();
        }
    </script>
    <? /*     * ***************************************************************** */ ?>
    <? /* NO MODIFICAR ESTA SECCION */ ?>
<? } ?>
<? include_once(FOOTER_MODULO); ?>
<? /* * ***************************************************************** */ ?>