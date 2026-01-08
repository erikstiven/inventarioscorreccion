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

    <!-- AXIOS -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>


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


    <script>
        function cargar_secuencial_rete() {
            xajax_cargar_secuencial_rete(xajax.getFormValues("form1"));
        }

        function genera_formulario() {
            //alert('hola');
            xajax_genera_formulario_pedido();
        }

        
        function refreshTablaIn(){
            parent.consultar();

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
                            jsShowWindowLoad();
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

        function cerrarModal() {
            $("#mostrarmodal").html("");
            $("#mostrarmodal").modal("hide");
        }

        function cerrarModalcorreo() {
            $("#mostrarmodalcorreo").html("");
            $("#mostrarmodalcorreo").modal("hide");
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

        function autocompletar_producto(empresa, event, op) {
            if (event.keyCode == 13 || event.keyCode == 115) { // F4
                $("#ModalProd").modal("show");
                xajax_producto_inventario(xajax.getFormValues("form1"));
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
            /*
            document.getElementById("cuenta_inv").value = '';
            document.getElementById("cuenta_iva").value = '';
            document.getElementById("lote").value = '';
            document.getElementById("codigo_barra").value = '';
            */

            document.getElementById("lote_prod").style.display = 'none';
            document.getElementById("serie_prod").style.display = 'none';
            document.getElementById("fecha_ela").style.display = 'none';
            document.getElementById("fecha_cad").style.display = 'none';
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
                var pagina = '../compra_sret/buscar_auto_prove.php?sesionId=<?= session_id() ?>&mOp=true&mVer=false&serie=' + serie_prove + '&prove=' + prove;
                window.open(pagina, "", opciones);
            }
        }

        // FORMAS PAGO
        // F O R M A    D E    P A G O
        function anadir_detalle_fp(sucursal) {
            if (ProcesarFormulario() == true) {
                generar_tabla_amortizacion();
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
                AjaxWin('<?= $_COOKIE["JIREH_INCLUDE"] ?>', '../compra_sret/orden_compra.php?sesionId=<?= session_id() ?>&mOp=true&mVer=false&empresa=' + empresa + '&sucursal=' + sucu + '&cliente=' + clpv, 'DetalleShow', 'iframe', 'ORDEN DE COMPRA', '700', '200', '10', '10', '1', '1');
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

        function clave_acceso_sri(tipo) {
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
                    xajax_clave_acceso(xajax.getFormValues("form1"), tipo);
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
                AjaxWin('<?= $_COOKIE["JIREH_INCLUDE"] ?>', '../compra_sret/etiqueta.php?sesionId=<?= session_id() ?>&id=' + codigo, 'DetalleShow', 'iframe', 'Generar Etiquetas', '900', '300', '10', '10', '1', '1');
            } else {
                alert('Ingrese Compra para continuar...');
            }
        }


        function cod_retencion(empresa, event) {
            if (event.keyCode == 13 || event.keyCode == 115) { // F4
                var codret = '';
                codret = document.getElementById('cod_ret').value;
                var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";
                var pagina = '../compra_sret/buscar_codret.php?sesionId=<?= session_id() ?>&mOp=true&mVer=false&codret=' + codret + '&empresa=' + empresa;
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

        function abre_modal_correo() {
            $("#mostrarmodalcorreo").modal("show");
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

        function envioCorreo() {
            xajax_modal_correo(xajax.getFormValues("form1"));
        }

        function enviar_correo() {
            var cor = document.getElementById('correo').value;

            var msj = document.getElementById('mensaje').value;

            var asu = document.getElementById('asunto').value;


            if (cor == '') {
                alerts('Ingrese el correo', 'warning');
            } else if (asu == '') {
                alerts('Ingrese el asunto', 'warning');
            } else if (msj == '') {

                alerts('Ingrese un mensaje', 'warning');
            } else {

                Swal.fire({
                    title: 'Desea enviar el correo a:',
                    text: cor,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar',
                    allowOutsideClick: false,
                    width: '25%',
                }).then((result) => {
                    if (result.value) {
                        //jsShowWindowLoad();
                        xajax_envio_correo_sret(xajax.getFormValues("form1"));

                    }
                })



            }


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

        function cargar_coti_ext() {

            xajax_cargar_coti_ext(xajax.getFormValues("form1"));

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
            if (year_form > year_actual || month_form > month_actual) {
                alert('La fecha de elaboracion no puede ser mayor al ultimo dia del mes actual');
                document.getElementById('fecha_ela').value = "'" + year_actual + "-" + month_actual + "-" + day_actual + "'"
            }
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

            if (tipo == 1) {
                validar_fecha_lote = 'S'
                document.getElementById("fela_txt").style.display = 'block';
                document.getElementById("fcad_etiq").style.display = 'block';
                document.getElementById("fela_etiq").style.display = 'block';
                document.getElementById("fcad_txt").style.display = 'block';
                document.getElementById("lote_prod").style.display = 'block';
                document.getElementById("lote_prod_txt").style.display = 'block';

                document.getElementById("cantidad").readOnly = false;
                document.getElementById("serie_prod").style.display = 'none';
                document.getElementById("serie_prod_txt").style.display = 'none';


                document.getElementById("mac_prod_txt").style.display = 'none';
                document.getElementById("mac_ad_prod").style.display = 'none';

            } else if (tipo == 3) {
                document.getElementById("serie_prod").style.display = 'block';
                document.getElementById("serie_prod_txt").style.display = 'block';

                document.getElementById("cantidad").value = 1;
                document.getElementById("cantidad").readOnly = true;
                document.getElementById("fela_txt").style.display = 'none';
                document.getElementById("fcad_etiq").style.display = 'none';
                document.getElementById("fela_etiq").style.display = 'none';
                document.getElementById("fcad_txt").style.display = 'none';
                document.getElementById("lote_prod").style.display = 'none';
                document.getElementById("lote_prod_txt").style.display = 'none';

                document.getElementById("mac_prod_txt").style.display = 'none';
                document.getElementById("mac_ad_prod").style.display = 'none';

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
                document.getElementById("fela_txt").style.display = 'none';
                document.getElementById("fcad_etiq").style.display = 'none';
                document.getElementById("fela_etiq").style.display = 'none';
                document.getElementById("fcad_txt").style.display = 'none';
                document.getElementById("lote_prod").style.display = 'none';
                document.getElementById("lote_prod_txt").style.display = 'none';
                document.getElementById("serie_prod").style.display = 'none';
                document.getElementById("serie_prod_txt").style.display = 'none';


                document.getElementById("mac_prod_txt").style.display = 'none';
                document.getElementById("mac_ad_prod").style.display = 'none';


                document.getElementById("cantidad").readOnly = false;
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
            document.form1.producto.focus();
            $("#ModalClpv").modal("hide");
        }

        function datos_prod(i, lote, serie, mac) {
            console.log(lote + ' - ' + serie);
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

        function obtener_balanza_api() {

            var codigo_producto_lb = document.getElementById("codigo_producto").value;
            var codigo_bodega_lb = document.getElementById("bodega").value;

            if (codigo_producto_lb == '' || codigo_bodega_lb == '') {
                alert('Seleccione Bodega y Producto para continuar');
            } else {

                var url = "_balanza.php?&bodega=" + codigo_bodega_lb + "&producto=" + codigo_producto_lb;

                $.ajax({
                    url: url,
                    type: 'POST',
                }).done(function(data) {
                    const result = JSON.parse(data);
                    if (result.bandera === 1) {
                        var url_api_local = result.url_api_result;
                        var siglas_unidad = result.unid_result;
                        siglas_unidad = siglas_unidad.toUpperCase();

                        $.ajax({
                            url: url_api_local,
                            type: 'GET',
                        }).done(function(result_balanza) {
                            var peso = result_balanza.peso; // Valor que tiene de peso en la balanza
                            var medida = result_balanza.medida; // Medida que esta usando para el pesaje LB, KG, G, ETC
                            medida = medida.toUpperCase();

                            var cantidad_result = 0;

                            if (siglas_unidad === medida) {
                                cantidad_result = peso;
                            } else {
                                if (medida === 'KG') {
                                    // Convertir de kilos a Libras
                                    cantidad_result = peso * 2.20462;
                                } else if (medida === 'LB') {
                                    // Convertir de Libras a Kilos
                                    cantidad_result = peso * 0.453592;
                                } else {
                                    cantidad_result = 0;
                                    alert('La medida de la balanza debe ser entre KG y LB', 'error');
                                }
                            }


                            cantidad_result = cantidad_result.toFixed(2);

                            if (cantidad_result > 0) {
                                alert('Peso recibido correctamente', 'success');
                                document.getElementById('cantidad').value = cantidad_result;
                            }

                        }).fail(function(error) {
                            alert('No se conecto a la balanza', 'error');
                        });


                    } else {
                        alert('No se encontro la balanza', 'error');
                    }
                }).fail(function(error) {
                    alert('Error al conectarse a la balanza', 'error');
                });


            }



            //http://192.168.1.8:3000/api/v1/balanza/obtener/prueba
            /*
            const corsAnywhere = 'http://192.168.1.8:3000/api/v1/balanza/obtener/prueba';
            fetch(corsAnywhere, {
                    method: 'GET',
                    headers: new Headers({
                        'Content-Type': 'application/json',
                        'Access-Control-Allow-Origin': '*',
                    }),
                })
                .then((response) => response.json())
                .then((data) => {
                    // console.log(data['peso'])
                    var peso = data['peso'];
                    document.getElementById('cantidad').value = peso;
                })
                .catch((err) => console.log(err));
            */


            /*
                        myHeaders = new Headers();
                        lat2 = -0.1365128;
                        lon2 = -78.469389;
                        var lat = lat2;
                        var lon = lon2;
                        var url = 'http://api.openweathermap.org/data/2.5/weather?lat=' + lat + '&lon=' + lon + '&units=metric&cnt=7&lang=en&appid=753983d1101525edd0545786efa2b53d';
                        var myInit = {
                            method: 'GET',
                            headers: myHeaders,
                            mode: 'cors',
                            type: 'json',
                            cache: 'default'
                        };

                        var myRequest = new Request(url, myInit);

                        fetch(myRequest, myInit)
                            .then(function(response) {
                                response.json().then(function(data) {
                                    console.log(data);
                                });
                            });

                        
                
                        */

            /*
            xajax_obtener_balanza_api(xajax.getFormValues("form1"));
            */
        }

        // --------------------------------------------------------------------------------------
        // FIN Evaluacion control calidad
        // --------------------------------------------------------------------------------------



        // -----------------------------------------------------------------------------------------
        // FUNCIONES POLLOS CAMPO BALANZA
        // -----------------------------------------------------------------------------------------

        function modal_balanza() {
            xajax_modal_balanza(xajax.getFormValues("form1"));
        }

        function autocompletar_btn_bal(empresa) {
            $("#ModalClpv").modal("show");
            xajax_clpv_reporte_bal(xajax.getFormValues("form1"));
        }

        function datos_clpv_bal(cod, cli, ruc, dir, tel, cel, vend, cont, pre, fpago, tpago, fec, auto, serie, fec_venc, dia, contr, ini, fin, cuenta, correo) {
            document.form1.codigo_proveedor_bal.value = cod;
            document.form1.cliente_nombre_bal.value = cli;
            $("#ModalClpv").modal("hide");
        }

        function autocompletar_producto_btn_bal() {
            var bodega = document.getElementById('bodega_bal').value;
            if (bodega != '') {
                $("#ModalProd").modal("show");
                xajax_producto_inventario_bal(xajax.getFormValues("form1"));
            } else {
                alert('Seleccione una bodega antes de escoger el producto');
            }

        }

        function datos_prod_bal(cod_prod, nom_prod, unid_medi) {
            document.form1.codigo_producto_bal.value = cod_prod;
            document.form1.producto_bal.value = nom_prod;
            $("#ModalProd").modal("hide");
        }

        function abrir_modal_pesaje() {
            var bodega = document.getElementById('bodega_bal').value;
            var producto_bal = document.getElementById('producto_bal').value;
            var codigo_producto_bal = document.getElementById('codigo_producto_bal').value;
            if (bodega != '' && producto_bal != '' && codigo_producto_bal != '') {
                xajax_abrir_modal_pesaje(xajax.getFormValues("form1"));
            } else {
                alert('Debe seleccionar Bodega y Producto a Pesar');
            }
        }

        function abre_modal_balanza() {
            $("#mostrarModalBalanza").modal("show");
        }

        function cerrar_modal_balanza() {
            $("#mostrarModalBalanza").modal("hide");
        }

        function obtener_balanza_api_peso() {

            var codigo_producto_lb = document.getElementById("codigo_producto_bal").value;
            var codigo_bodega_lb = document.getElementById("bodega_bal").value;

            if (codigo_producto_lb == '' || codigo_bodega_lb == '') {
                alert('Seleccione Bodega y Producto para continuar');
            } else {

                var url = "_balanza.php?&bodega=" + codigo_bodega_lb + "&producto=" + codigo_producto_lb;

                $.ajax({
                    url: url,
                    type: 'POST',
                }).done(function(data) {
                    const result = JSON.parse(data);
                    if (result.bandera === 1) {
                        var url_api_local = result.url_api_result;
                        var siglas_unidad = result.unid_result;
                        siglas_unidad = siglas_unidad.toUpperCase();

                        $.ajax({
                            url: url_api_local,
                            type: 'GET',
                        }).done(function(result_balanza) {
                            var peso = result_balanza.peso; // Valor que tiene de peso en la balanza
                            var medida = result_balanza.medida; // Medida que esta usando para el pesaje LB, KG, G, ETC
                            medida = medida.toUpperCase();

                            var cantidad_result = 0;

                            if (siglas_unidad === medida) {
                                cantidad_result = peso;
                            } else {
                                if (medida === 'KG') {
                                    // Convertir de kilos a Libras
                                    cantidad_result = peso * 2.20462;
                                } else if (medida === 'LB') {
                                    // Convertir de Libras a Kilos
                                    cantidad_result = peso * 0.453592;
                                } else {
                                    cantidad_result = 0;
                                    swal_mensaje('La medida de la balanza debe ser entre KG y LB', 'error');
                                }
                            }


                            cantidad_result = cantidad_result.toFixed(2);

                            if (cantidad_result > 0) {
                                swal_mensaje('Peso recibido correctamente', 'success');
                                document.getElementById('cantidad_bal_peso').value = cantidad_result;
                            }

                        }).fail(function(error) {
                            swal_mensaje('No se conecto a la balanza', 'error');
                        });


                    } else {
                        swal_mensaje('No se encontro la balanza', 'error');
                    }
                }).fail(function(error) {
                    swal_mensaje('Error al conectarse a la balanza', 'error');
                });


            }
        }

        function swal_mensaje(mensaje, tipo_mensaje) {
            Swal.fire({
                type: tipo_mensaje,
                title: mensaje,
                showConfirmButton: false,
                timer: 500
            })
        }

        function agregar_pesaje() {
            var cantidad_bal_peso = document.getElementById('cantidad_bal_peso').value;
            var numero_jaula_peso = document.getElementById('numero_jaula_peso').value;
            var numero_pollos_peso = document.getElementById('numero_pollos_peso').value;
            if (cantidad_bal_peso != '' && numero_jaula_peso != '' && numero_pollos_peso != '') {
                xajax_agregar_pesaje(xajax.getFormValues("form1"));
            } else {
                alert('Debe llenar Cantidad, Jaula y numero Pollos');
            }
        }

        function productos_pesados() {
            xajax_productos_pesados(xajax.getFormValues("form1"));
        }

        function eliminar_producto_agregado(dbalpc_cod_dbalpc) {
            Swal.fire({
                title: 'Estas seguro que deseas eliminar este producto',
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
                    xajax_eliminar_producto_agregado(dbalpc_cod_dbalpc, xajax.getFormValues("form1"));
                }
            });
        }

        function realizar_calculos_bal() {
            xajax_realizar_calculos_bal(xajax.getFormValues("form1"));
        }

        function limpiar_todos_datos() {
            Swal.fire({
                title: 'Estas seguro que deseas eliminar todos los datos pesados pendientes?',
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
                    xajax_limpiar_todos_datos(xajax.getFormValues("form1"));
                }
            });
        }

        function procesar_info() {
            var precio_bal = document.getElementById('precio_bal').value;
            var codigo_proveedor_bal = document.getElementById('codigo_proveedor_bal').value;
            var cliente_nombre_bal = document.getElementById('cliente_nombre_bal').value;
            var codigo_producto_bal = document.getElementById('codigo_producto_bal').value;
            var producto_bal = document.getElementById('producto_bal').value;

            if (precio_bal > 0 && codigo_proveedor_bal != '' && cliente_nombre_bal != '' && codigo_producto_bal != '' && producto_bal != '') {
                xajax_procesar_info(xajax.getFormValues("form1"));
                swal_mensaje('Informacion Procesada', 'success');
            } else {
                alert('Debe ingresar Precio, Producto y Proveedor');
            }
        }

        function abre_modal_historial_bal() {
            xajax_abre_modal_historial_bal(xajax.getFormValues("form1"));
        }

        function consultar_movimientos_inv() {
            xajax_consultar_movimientos_inv(xajax.getFormValues("form1"));
        }

        function abre_modal_balanza2() {
            $("#mostrarModalBalanza2").modal("show");
        }

        function cerrar_modal_balanza2() {
            $("#mostrarModalBalanza2").modal("hide");
        }

        function modal_vista_previa_balanza(cod_minv, empresa, sucursal) {
            xajax_modal_vista_previa_balanza(cod_minv, empresa, sucursal, xajax.getFormValues("form1"));
        }

        function vista_previa_detalle_bal() {

            var codigo = 47;
            var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";
            var pagina = '../compra_sret/vista_previa_bal.php?sesionId=<?= session_id() ?>&codigo=' + codigo;
            window.open(pagina, "", opciones);
        }

        // -----------------------------------------------------------------------------------------
        // FIN FUNCIONES POLLOS CAMPO BALANZA
        // -----------------------------------------------------------------------------------------


        // ------------------------------------------------------------------------------------------
        // Tabla amortizacion
        // ------------------------------------------------------------------------------------------
        function mostrar_boton_amort() {
            var dias_cuotas_fp = document.getElementById('dias_cuotas_fp').value;
            var cuotas_fp = document.getElementById('cuotas_fp').value;

            if (dias_cuotas_fp == 0 || dias_cuotas_fp == '') {
                alert('Ingrese Dias Cuota antes de las N. Cuotas');
                document.getElementById('cuotas_fp').value = 0;
                document.getElementById('div_boton_amort').style.display = 'none';
            } else {
                if (cuotas_fp > 0) {
                    //document.getElementById('div_boton_amort').style.display = 'block';
                    document.getElementById('div_boton_amort').style.display = 'none';
                } else {
                    alert('El numero de cuotas debe ser mayor a 0');
                    document.getElementById('div_boton_amort').style.display = 'none';
                }
            }
            // xajax_mostrar_boton_amort(xajax.getFormValues("form1"));
        }

        function generar_tabla_amortizacion() {
            xajax_generar_tabla_amortizacion(xajax.getFormValues("form1"));
        }

        // ------------------------------------------------------------------------------------------
        // FIN Tabla amortizacion
        // ------------------------------------------------------------------------------------------


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

                        <?php
                        if ($_SESSION['EMPRESA_RUC'] == 1705565933001) {
                            echo '<li role="presentation"><a href="#divBalanza" aria-controls="divBalanza" role="tab" data-toggle="tab">BALANZA</a></li>';
                        }
                        ?>
                        <!--
                        <li role="presentation"><a href="#divBalanza" aria-controls="divBalanza" role="tab" data-toggle="tab">BALANZA</a></li>
                        -->
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="divCompraMenu">
                            <div id="divFormularioCabecera"></div>
                            <div id="divFormularioDetalle" class="table-responsive"></div>
                            <div id="divTotal"></div>
                            <div id="divFormularioDetalle2" class="table-responsive"></div>
                            <div id="divFormularioDetalleModal" class="table-responsive"></div>

                        </div>
                        <div role="tabpanel" class="tab-pane" id="divPagoMenu">
                            <div id="divFormularioFp" class="table-responsive"></div>
                            <div id="divFormularioDetalleFP_DET" class="table-responsive"></div>
                            <div id="divFormularioDetalle_FP" class="table-responsive"></div>
                            <div id="divTotalFP" class="table-responsive"></div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="divBalanza">
                            <div id="divFormularioBAL" class="table-responsive"></div>
                            <div id="divFormularioDetalleBAL" class="table-responsive"></div>
                            <div id="divFormularioModal" class="table-responsive"></div>
                            <div id="divFormularioModal2" class="table-responsive"></div>
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

                <div class="col-md-12">
                    <div id="divCorreos" class="table-responsive"></div>
                </div>

            </form>
        </div>
        <div id="divGrid"></div>
        <br><br><br><br><br><br><br>
    </body>
    <script>
        genera_formulario();


        function init() {
            var search = '<?= $ruc ?>';
            var table = $('#tbclientes').DataTable({
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

            table.search(search).draw();
        }

        function init_prod() {
            var search = '<?= $ruc ?>';
            var table = $('#tbclientes_prod').DataTable({
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

            table.search(search).draw();
        }
    </script>
    <? /*     * ***************************************************************** */ ?>
    <? /* NO MODIFICAR ESTA SECCION */ ?>
<? } ?>
<? include_once(FOOTER_MODULO); ?>
<? /* * ***************************************************************** */ ?>