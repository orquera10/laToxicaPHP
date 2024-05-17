<?php
// Incluir archivos necesarios
$pageTitle = "Informe de Stock";
include 'header.php';
include 'headerUsuario.php';
include 'barraNavegacion.php';
include 'config.php'; // Suponiendo que aquí se encuentra la configuración de la conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['date'])) {
    // Obtener la fecha seleccionada
    $selectedDate = $_GET['date'];

    // Obtener el primer y último día del mes seleccionado
    $firstDayOfMonth = date('Y-m-01', strtotime($selectedDate));
    $lastDayOfMonth = date('Y-m-t', strtotime($selectedDate));

    $selectedDatePeriodo = date('m-Y', strtotime($selectedDate));

    // Calcular el siguiente periodo
    $selectedDatePeriodoSiguiente = date('m-Y', strtotime("+1 month", strtotime($selectedDate)));

    // Variable para almacenar el _id del periodo actual
    $periodoActualID = null;

    // Verificar si el periodo actual ya existe en la tabla periodo
    $sqlCheckPeriodoActual = "SELECT _id FROM periodo WHERE FECHA = ?";
    $stmtCheckPeriodoActual = $con->prepare($sqlCheckPeriodoActual);
    $stmtCheckPeriodoActual->bind_param("s", $selectedDatePeriodo);
    $stmtCheckPeriodoActual->execute();
    $resultCheckPeriodoActual = $stmtCheckPeriodoActual->get_result();

    if ($resultCheckPeriodoActual->num_rows > 0) {
        // Si el periodo actual existe, obtener su _id
        $rowCheckPeriodoActual = $resultCheckPeriodoActual->fetch_assoc();
        $periodoActualID = $rowCheckPeriodoActual['_id'];
    } else {
        // Si no existe, se puede manejar un error o insertar el periodo actual si necesario
        // En este ejemplo, asumimos que el periodo actual debe existir previamente
        die("El periodo actual no existe en la base de datos.");
    }

    // Variable para almacenar el _id del periodo
    $periodoSiguienteId = null;

    // Verificar si el siguiente periodo ya existe en la tabla periodo
    $sqlCheckPeriodo = "SELECT _id FROM periodo WHERE FECHA = ?";
    $stmtCheckPeriodo = $con->prepare($sqlCheckPeriodo);
    $stmtCheckPeriodo->bind_param("s", $selectedDatePeriodoSiguiente);
    $stmtCheckPeriodo->execute();
    $resultCheckPeriodo = $stmtCheckPeriodo->get_result();

    if ($resultCheckPeriodo->num_rows > 0) {
        // Si el periodo existe, obtener su _id
        $rowCheckPeriodo = $resultCheckPeriodo->fetch_assoc();
        $periodoSiguienteId = $rowCheckPeriodo['_id'];
    } else {
        // Si no existe, insertar el nuevo periodo en la tabla periodo
        $sqlInsertPeriodo = "INSERT INTO periodo (FECHA) VALUES (?)";
        $stmtInsertPeriodo = $con->prepare($sqlInsertPeriodo);
        $stmtInsertPeriodo->bind_param("s", $selectedDatePeriodoSiguiente);
        $stmtInsertPeriodo->execute();

        // Obtener el _id del nuevo periodo insertado
        $periodoSiguienteId = $stmtInsertPeriodo->insert_id;
    }

    // Consulta para obtener la lista completa de productos
    $sqlAllProducts = "SELECT sm.id_PRODUCTO, sm.STOCK_INICIAL FROM stock_mes sm 
                    LEFT JOIN periodo pd ON sm.id_PERIODO = pd._id
                    WHERE pd.FECHA = '$selectedDatePeriodo'";
    $resultAllProducts = $con->query($sqlAllProducts);
    $allProducts = [];
    while ($row = $resultAllProducts->fetch_assoc()) {
        $allProducts[] = $row;
    }

    // Consulta para obtener el stock de cada producto en el mes seleccionado
    $sql = "SELECT p._id, p.NOMBRE, COALESCE(sm.STOCK_INICIAL, 0) AS STOCK_INICIAL, COALESCE(SUM(s.INGRESO), 0) AS INGRESO_TOTAL, COALESCE(SUM(s.EGRESO), 0) AS EGRESO_TOTAL,
                   STOCK_INICIAL + (COALESCE(SUM(s.INGRESO), 0) - COALESCE(SUM(s.EGRESO), 0)) AS STOCK_FINAL
            FROM producto p
            LEFT JOIN stock s ON p._id = s.id_PRODUCTO
            LEFT JOIN resumen_dias rd ON s.id_DIA = rd._id
            LEFT JOIN stock_mes sm ON p._id = sm.id_PRODUCTO
            LEFT JOIN periodo pd ON sm.id_PERIODO = pd._id
            WHERE pd.FECHA = ?
            AND STR_TO_DATE(rd.FECHA, '%d-%m-%Y') BETWEEN ? AND ?
            GROUP BY p._id";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sss", $selectedDatePeriodo, $firstDayOfMonth, $lastDayOfMonth);
    $stmt->execute();
    $result = $stmt->get_result();
}

?>

<div class="container my-4">
    <form method="GET" action="">
        <div class="form-group row formInfMensual my-4">
            <div class="col-md-5 row">
                <label for="date" class="col-12 col-form-label m-0">Seleccionar mes y año:</label>
                <div class="col-12 my-2">
                    <input type="month" id="date" name="date" class="form-control"
                        value="<?php echo isset($selectedDate) ? $selectedDate : date('Y-m'); ?>" min="2020-01"
                        max="2025-12">
                </div>
                <div class="col-12 my-2">
                    <button type="submit" class="btn btn-primary w-100">Ver Informe</button>
                </div>
            </div>
        </div>
    </form>

    <?php if (isset($result)): ?>
        <p class="tituloInformeMes">Informe de Stock - <?php echo date('m Y', strtotime($selectedDate)); ?></p>
        <div class="rounded tablaTurnosAll tablaMes my-4 shadow py-2 px-4">
            <table class="table tablaStockMes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Stock Inicial</th>
                        <th>Ingreso</th>
                        <th>Egreso</th>
                        <th>Stock Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $returnedProductIds = [];
                    while ($row = $result->fetch_assoc()) {
                        $returnedProductIds[] = (int) $row['_id']; // Convertir a entero
                        echo "<tr>";
                        echo "<td>" . $row['_id'] . "</td>";
                        echo "<td>" . $row['NOMBRE'] . "</td>";
                        echo "<td>" . $row['STOCK_INICIAL'] . "</td>";
                        echo "<td>" . $row['INGRESO_TOTAL'] . "</td>";
                        echo "<td>" . $row['EGRESO_TOTAL'] . "</td>";
                        echo "<td>" . $row['STOCK_FINAL'] . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        // Realizar una consulta para obtener el valor de FINALIZADO
        $sqlFinalizado = "SELECT FINALIZADO FROM `periodo` WHERE `FECHA` = ?";
        $stmtFinalizado = $con->prepare($sqlFinalizado);
        $stmtFinalizado->bind_param("s", $selectedDatePeriodo);
        $stmtFinalizado->execute();
        $resultado_finalizado = $stmtFinalizado->get_result();

        // Verificar si se obtuvo el resultado de la consulta
        if ($resultado_finalizado) {
            // Obtener el valor de FINALIZADO
            $fila_finalizado = mysqli_fetch_assoc($resultado_finalizado);
            $finalizado = $fila_finalizado['FINALIZADO'];

            // Determinar si el botón debe estar habilitado o deshabilitado
            $disabled = ($finalizado == 1) ? "disabled" : "";
        } else {
            // En caso de error en la consulta, asumir que el botón está deshabilitado
            $disabled = "disabled";
        }
        ?>
        <div class="row">
            <div class="col-md-5 my-3">
                <button class="btn btn-primary w-100" id="btnCerrarMes" <?php echo $disabled; ?>>Cerrar Mes</button>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    var allProducts = <?php echo json_encode($allProducts); ?>;
    var returnedProductIds = <?php echo json_encode($returnedProductIds); ?>;
</script>

<?php
include 'common_scripts.php';
if (isset($stmt)) {
    $stmt->close();
}
?>

<script>
    $(document).ready(function () {

        $('#btnCerrarMes').click(function () {
            var totalFilas = $('.tablaStockMes tbody tr').length;
            var filasGuardadas = 0;

            var promises = [];

            var idPeriodoSiguiente = <?php echo $periodoSiguienteId; ?>; // Obtener el ID de resumen_dias
            var idPeriodoActual = <?php echo $periodoActualID; ?>; // Obtener el ID de resumen_dias

            $('.tablaStockMes tbody tr').each(function () {
                var stokInicial = $(this).find('td:eq(5)').text();
                var idProducto = $(this).find('td:eq(0)').text();

                var request = $.ajax({
                    url: 'cerrar_mes.php',
                    type: 'POST',
                    data: {
                        stokInicial: stokInicial,
                        id_producto: idProducto,
                        id_periodo_siguiente: idPeriodoSiguiente,
                        id_periodo_actual: idPeriodoActual
                    },
                    dataType: 'json'
                });

                promises.push(request);
            });

            // Convertir el valor de id_PRODUCTO a entero para cada objeto en allProducts
            allProducts.forEach(function (product) {
                product.id_PRODUCTO = parseInt(product.id_PRODUCTO);
                product.STOCK_INICIAL = parseInt(product.STOCK_INICIAL);
            });

            // Filtrar los productos que no están en returnedProductIds
            var missingProducts = allProducts.filter(function (product) {
                return !returnedProductIds.includes(product.id_PRODUCTO);
            });


            // Hacer solicitud AJAX adicional para productos faltantes
            if (missingProducts.length > 0) {
                missingProducts.forEach(function (product) {
                    var request = $.ajax({
                        url: 'cerrar_mes.php',
                        type: 'POST',
                        data: {
                            stokInicial: product.STOCK_INICIAL, // Asumiendo que el stock inicial es 0 si no se encuentra
                            id_producto: product.id_PRODUCTO,
                            id_periodo_siguiente: idPeriodoSiguiente,
                            id_periodo_actual: idPeriodoActual
                        },
                        dataType: 'json'
                    });

                    promises.push(request);
                });
            }

            $.when.apply($, promises).then(function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Se cerró correctamente el mes'
                }).then(function () {
                    location.reload();
                });
            }, function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ha ocurrido un error al procesar la solicitud'
                });
            });

        });

    });
</script>

</body>

</html>