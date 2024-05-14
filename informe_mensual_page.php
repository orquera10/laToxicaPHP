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

    // Consulta para obtener el stock de cada producto en el mes seleccionado
    $sql = "SELECT p.NOMBRE, p.PRECIO, p.STOCK AS STOCK_ACTUAL, COALESCE(SUM(s.INGRESO), 0) AS INGRESO_TOTAL, COALESCE(SUM(s.EGRESO), 0) AS EGRESO_TOTAL,
                   COALESCE(SUM(s.INGRESO), 0) - COALESCE(SUM(s.EGRESO), 0) AS STOCK_FINAL,
                   p.STOCK + (COALESCE(SUM(s.INGRESO), 0) - COALESCE(SUM(s.EGRESO), 0)) AS STOCK_INICIAL
            FROM producto p
            LEFT JOIN stock s ON p._id = s.id_PRODUCTO
            LEFT JOIN resumen_dias rd ON s.id_DIA = rd._id
            WHERE STR_TO_DATE(rd.FECHA, '%d-%m-%Y') BETWEEN ? AND ?
            GROUP BY p._id";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $firstDayOfMonth, $lastDayOfMonth);
    $stmt->execute();
    $result = $stmt->get_result();
}

?>

<div class="container">
    <form method="GET" action="">
        <div class="form-group row formInfMensual my-4">
            <div class="col-md-5 row">
                <label for="date" class="col-12 col-form-label m-0">Seleccionar mes y año:</label>
                <div class="col-12 my-2">
                    <input type="month" id="date" name="date" class="form-control" value="<?php echo isset($selectedDate) ? $selectedDate : date('Y-m'); ?>"
                        min="2020-01" max="2025-12">
                </div>
                <div class="col-12 my-2">
                    <button type="submit" class="btn btn-primary w-100">Ver Informe</button>
                </div>
            </div>
        </div>
    </form>

    <?php if (isset($result)): ?>
        <h2>Informe de Stock - Mes <?php echo date('F Y', strtotime($selectedDate)); ?></h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Stock Inicial</th>
                    <th>Stock Actual</th>
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
                    echo "<td>" . $row['PRECIO'] . "</td>";
                    echo "<td>" . $row['STOCK_INICIAL'] . "</td>";
                    echo "<td>" . $row['STOCK_ACTUAL'] . "</td>";
                    echo "<td>" . $row['INGRESO_TOTAL'] . "</td>";
                    echo "<td>" . $row['EGRESO_TOTAL'] . "</td>";
                    echo "<td>" . $row['STOCK_FINAL'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
include 'common_scripts.php';
if (isset($stmt)) {
    $stmt->close();
}
?>

</body>

</html>
