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

    // Consulta para obtener el stock de cada producto en el mes seleccionado
    $sql = "SELECT p.NOMBRE, COALESCE(sm.STOCK_INICIAL, 0) AS STOCK_INICIAL, COALESCE(SUM(s.INGRESO), 0) AS INGRESO_TOTAL, COALESCE(SUM(s.EGRESO), 0) AS EGRESO_TOTAL,
                   COALESCE(SUM(s.INGRESO), 0) - COALESCE(SUM(s.EGRESO), 0) AS STOCK_FINAL
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
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Stock Inicial</th>
                        <th>Ingreso</th>
                        <th>Egreso</th>
                        <th>Stock Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
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
        $sqlFinalizado = "SELECT FINALIZADO FROM `periodo` WHERE `FECHA` = '$selectedDatePeriodo'";
        $resultado_finalizado = mysqli_query($con, $sqlFinalizado);

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

<?php
include 'common_scripts.php';
if (isset($stmt)) {
    $stmt->close();
}
?>

</body>

