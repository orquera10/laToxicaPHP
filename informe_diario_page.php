<?php
// Incluir archivos necesarios
$pageTitle = "Informe Diario";
include 'header.php';
include 'headerUsuario.php';
include 'barraNavegacion.php';
include 'config.php'; // Suponiendo que aquí se encuentra la configuración de la conexión a la base de datos

// Inicializar $resultado como un array vacío
$resultado = [];

// Verificar si se ha seleccionado una fecha
if (isset($_POST['fechaInforme'])) {
    $fechaSeleccionada = $_POST['fechaInforme'];
    // Convertir la fecha al formato dd-mm-aaaa usando STR_TO_DATE()
    $fechaSeleccionada = date('d-m-Y', strtotime($fechaSeleccionada));

    // Consulta para obtener los datos de la tabla de turnos y de la tabla de clientes
    $sql = "SELECT t.id_CLIENTE, c.NOMBRE AS nombre_cliente, t.FECHA, t.HORA_INICIO, t.HORA_FIN, cn.NOMBRE AS nombre_cancha, tk.TOTAL_CANCHA, tk.TOTAL_DETALLE, tk.TOTAL, tk.PAGO_EFECTIVO, tk.PAGO_TRANSFERENCIA 
            FROM turnos t
            INNER JOIN clientes c ON t.id_CLIENTE = c._id
            INNER JOIN canchas cn ON t.id_CANCHA = cn._id
            INNER JOIN ticket tk ON t._id = tk.id_TURNO
            WHERE t.FECHA = '$fechaSeleccionada' AND t.FINALIZADO = 1";

    // Ejecutar la consulta
    $resultado_query = mysqli_query($con, $sql);

    // Almacenar los resultados en un array
    while ($fila = mysqli_fetch_assoc($resultado_query)) {
        $resultado[] = $fila;
    }
}
?>

<div class="container">
    <form id="fechaForm" method="post" action="">
        <div class="form-group d-flex justify-content-center mt-4">
            <input type="date" class="form-control" id="fechaInforme" name="fechaInforme"
                value="<?php echo $fechaSeleccionada; ?>" onchange="document.getElementById('fechaForm').submit()">
        </div>
    </form>
    <div class="rounded tablaTurnosAll my-4 shadow p-2" style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr class="align-middle">
                    <th>ID Cliente</th>
                    <th>Nombre Cliente</th>
                    <th>Fecha</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Cancha</th>
                    <th>Total Cancha</th>
                    <th>Total Productos</th>
                    <th>Total</th>
                    <th>Pago Efectivo</th>
                    <th>Pago Transferencia</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Mostrar los datos en la tabla
                foreach ($resultado as $fila) {
                    echo "<tr class='align-middle'>";
                    echo "<td>" . $fila['id_CLIENTE'] . "</td>";
                    echo "<td>" . $fila['nombre_cliente'] . "</td>";
                    echo "<td>" . $fila['FECHA'] . "</td>";
                    echo "<td>" . $fila['HORA_INICIO'] . "</td>";
                    echo "<td>" . $fila['HORA_FIN'] . "</td>";
                    echo "<td>" . $fila['nombre_cancha'] . "</td>";
                    echo "<td>" . $fila['TOTAL_CANCHA'] . "</td>";
                    echo "<td>" . $fila['TOTAL_DETALLE'] . "</td>";
                    echo "<td style='font-weight: bold;font-size: 0.8rem;' >" . $fila['TOTAL'] . "</td>";
                    echo "<td>" . $fila['PAGO_EFECTIVO'] . "</td>";
                    echo "<td>" . $fila['PAGO_TRANSFERENCIA'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="font-weight:bold;">Totales</td>
                    <td><?php echo array_sum(array_column($resultado, 'TOTAL_CANCHA')); ?></td>
                    <td><?php echo array_sum(array_column($resultado, 'TOTAL_DETALLE')); ?></td>
                    <td style='font-weight: bold;font-size: 0.8rem;'><?php echo array_sum(array_column($resultado, 'TOTAL')); ?></td>
                    <td><?php echo array_sum(array_column($resultado, 'PAGO_EFECTIVO')); ?></td>
                    <td><?php echo array_sum(array_column($resultado, 'PAGO_TRANSFERENCIA')); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php
include 'common_scripts.php';
?>

</body>

</html>
