<?
if (isset($_REQUEST['bode'])) {
    $bodega = $_REQUEST['bode'];
} else {
    $bodega = '';
}

if (isset($_REQUEST['prod_nom']))
    $prod_nom = $_REQUEST['prod_nom'];
else
    $prod_nom = '';
?>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cuentas Contables</title>
    <!--CSS-->
    <link rel="stylesheet" href="media/css/bootstrap.css">
    <link rel="stylesheet" href="media/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="media/font-awesome/css/font-awesome.css">
    <!--Javascript-->
    <script src="media/js/jquery-1.10.2.js"></script>
    <script src="media/js/jquery.dataTables.min.js"></script>
    <script src="media/js/dataTables.bootstrap.min.js"></script>
    <script src="media/js/bootstrap.js"></script>
    <script src="media/js/lenguajeusuario.js"></script>
    <script src="js/teclaEvent.js" type="text/javascript"></script>
    <script>
        shortcut.add("Esc", function() {
            close();
        });

        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });


        function seleccionaItem(cod_bode) {
             window.opener.document.form1.producto.value = cod_bode;
            window.close();
        }
    </script>
</head>

<body>
    <div class="container-fluid">
        <div class="col-md-12 table-responsive">
            <input type="hidden" name="bodega_pro" id="bodega_pro" value="<?= $bodega ?>">
            <input type="hidden" name="prod_nom" id="prod_nom" value="<?= $prod_nom ?>">
            <table id="divCuentasContables" class="table table-striped table-bordered table-hover table-condensed" cellspacing="0" width="100%">
                <thead>
                    <tr class="info">
                        <th class="fecha_letra">No-</th>
                        <th>Bodega</th>
						<th>Codigo</th>
                        <th>Producto</th>
                        <th>Referencia</th>
                        <th>Tipo</th>
                        <th>Unidad Medida</th>
                        <th>lotes</th>
                        <th>Series</th>
                        <th>Stock</th>     
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                <tr class="info">
                        <th class="fecha_letra">No-</th>
                        <th>Bodega</th>
						<th>Codigo</th>
                        <th>Producto</th>
                        <th>Referencia</th>
                        <th>Tipo</th>
                        <th>Unidad Medida</th>
                        <th>lotes</th>
                        <th>Series</th>
                        <th>Stock</th>     
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>

</html>