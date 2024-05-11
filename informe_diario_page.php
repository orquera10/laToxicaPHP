<?php
// Incluir archivos necesarios
$pageTitle = "Informe Diario";
include 'header.php';
include 'headerUsuario.php';
include 'barraNavegacion.php';
include 'config.php'; // Suponiendo que aquí se encuentra la configuración de la conexión a la base de datos

$total_cancha = 0;
$total_productos = 0;
$total = 0;
$total_efectivo = 0;
$total_transferencia = 0;

// Inicializar $resultado como un array vacío
$resultado = [];
$fechaSeleccionada = date("d-m-Y");

$fechaSeleccionadaInicial = date('d-m-Y 07:00', strtotime($fechaSeleccionada));
$fechaSeleccionadaFinal = date('d-m-Y 04:00', strtotime('+1 day', strtotime($fechaSeleccionada)));

// Verificar si se ha seleccionado una fecha
if (isset($_POST['fechaInforme'])) {
    $fechaSeleccionada = $_POST['fechaInforme'];

    // Convertir la fecha al formato dd-mm-aaaa usando STR_TO_DATE()
    $fechaSeleccionadaInicial = date('d-m-Y 07:00', strtotime($fechaSeleccionada));

    $fechaSeleccionadaFinal = date('d-m-Y 04:00', strtotime('+1 day', strtotime($fechaSeleccionada)));

    $sql = "SELECT tk.id_CLIENTE, 
    c.NOMBRE AS nombre_cliente, 
    t.FECHA AS fecha_reserva, 
    t.HORA_INICIO, 
    t.HORA_FIN, 
    cn.NOMBRE AS nombre_cancha, 
    tk.TOTAL_CANCHA, 
    tk.FECHA AS fecha_ticket, 
    tk.TOTAL_DETALLE, 
    tk.TOTAL, 
    tk.PAGO_EFECTIVO, 
    tk.PAGO_TRANSFERENCIA 
    FROM turnos t
    INNER JOIN ticket tk ON t._id = tk.id_TURNO
    INNER JOIN clientes c ON tk.id_CLIENTE = c._id
    INNER JOIN canchas cn ON t.id_CANCHA = cn._id
    WHERE STR_TO_DATE(tk.FECHA, '%d-%m-%Y %H:%i') BETWEEN STR_TO_DATE('$fechaSeleccionadaInicial', '%d-%m-%Y %H:%i') AND STR_TO_DATE('$fechaSeleccionadaFinal', '%d-%m-%Y %H:%i')
    AND t.FINALIZADO = 1;
    ";

    // Ejecutar la consulta
    $resultado_query = mysqli_query($con, $sql);

    // Almacenar los resultados en un array
    while ($fila = mysqli_fetch_assoc($resultado_query)) {
        $resultado[] = $fila;
    }
}
?>

<div class="container tablasInformes">
    <p class="mt-5 h4" >Informe Diario</p>
    <!-- Formulario para seleccionar la fecha -->
    <form id="fechaForm" method="post" action="" class="my-4">
        <div class="form-group mt-4 d-flex align-items-center">
            <p class="my-0 me-2 p-0" style="font-weight: bold; font-size: 0.8rem" >Seleccionar fecha: </p>
            <input type="date" class="form-control despFecha" id="fechaInforme" name="fechaInforme"
                value="<?php echo date('Y-m-d', strtotime($fechaSeleccionada)); ?>"
                onchange="document.getElementById('fechaForm').submit()">
        </div>
    </form>
    <div class="row mt-5">
        <div class="col-12">
            <p>Detalle de ventas</p>
            <!-- Tabla para mostrar los turnos -->
            <div class="rounded tablaTurnosAll my-4 shadow py-2 px-4" style="overflow-x: auto;">
                <table class="table">
                    <!-- Cabecera de la tabla -->
                    <thead>
                        <tr class="align-middle">
                            <th>ID Cliente</th>
                            <th>Nombre Cliente</th>
                            <th>Fecha Pago</th>
                            <th>Cancha</th>
                            <th>Fecha Reserva</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                            <th>Total Cancha</th>
                            <th>Total Productos</th>
                            <th>Total</th>
                            <th>Pago Efectivo</th>
                            <th>Pago Transferencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Consulta para obtener los datos de la tabla ticket -->
                        <?php
                        $sqlTurnos = "SELECT tk.id_CLIENTE, 
                                        c.NOMBRE AS nombre_cliente, 
                                        t.FECHA AS fecha_reserva, 
                                        t.HORA_INICIO, 
                                        t.HORA_FIN, 
                                        cn.NOMBRE AS nombre_cancha, 
                                        tk.TOTAL_CANCHA, 
                                        tk.FECHA AS fecha_ticket, 
                                        tk.TOTAL_DETALLE, 
                                        tk.TOTAL, 
                                        tk.PAGO_EFECTIVO, 
                                        tk.PAGO_TRANSFERENCIA 
                                    FROM turnos t
                                    INNER JOIN ticket tk ON t._id = tk.id_TURNO
                                    INNER JOIN clientes c ON tk.id_CLIENTE = c._id
                                    INNER JOIN canchas cn ON t.id_CANCHA = cn._id
                                    WHERE STR_TO_DATE(tk.FECHA, '%d-%m-%Y %H:%i') BETWEEN STR_TO_DATE('$fechaSeleccionadaInicial', '%d-%m-%Y %H:%i') AND STR_TO_DATE('$fechaSeleccionadaFinal', '%d-%m-%Y %H:%i')
                                    AND t.FINALIZADO = 1";

                        // Ejecutar la consulta
                        $resultado_turnos = mysqli_query($con, $sqlTurnos);

                        // Mostrar los resultados en la tabla
                        while ($filaTurno = mysqli_fetch_assoc($resultado_turnos)) {
                            // Mostrar los datos en la fila
                            echo "<tr class='align-middle'>";
                            echo "<td>" . $filaTurno['id_CLIENTE'] . "</td>";
                            echo "<td>" . $filaTurno['nombre_cliente'] . "</td>";
                            echo "<td>" . $filaTurno['fecha_ticket'] . "</td>";
                            echo "<td>" . $filaTurno['nombre_cancha'] . "</td>";
                            echo "<td>" . $filaTurno['fecha_reserva'] . "</td>";
                            echo "<td>" . $filaTurno['HORA_INICIO'] . "</td>";
                            echo "<td>" . $filaTurno['HORA_FIN'] . "</td>";
                            echo "<td>" . $filaTurno['TOTAL_CANCHA'] . "</td>";
                            echo "<td>" . $filaTurno['TOTAL_DETALLE'] . "</td>";
                            echo "<td style='font-weight: bold;font-size: 0.8rem;'>" . $filaTurno['TOTAL'] . "</td>";
                            echo "<td>" . $filaTurno['PAGO_EFECTIVO'] . "</td>";
                            echo "<td>" . $filaTurno['PAGO_TRANSFERENCIA'] . "</td>";
                            echo "</tr>";

                            // Sumar a los totales
                            $total_cancha += $filaTurno['TOTAL_CANCHA'];
                            $total_productos += $filaTurno['TOTAL_DETALLE'];
                            $total += $filaTurno['TOTAL'];
                            $total_efectivo += $filaTurno['PAGO_EFECTIVO'];
                            $total_transferencia += $filaTurno['PAGO_TRANSFERENCIA'];
                        }
                        ?>
                    </tbody>
                    <!-- Pie de la tabla -->
                    <tfoot>
                        <tr>
                            <td colspan="6"></td>
                            <td style='font-weight: bold;font-size: 0.8rem;'>Totales</td>
                            <td style='font-weight: bold;font-size: 0.8rem;'><?php echo $total_cancha; ?></td>
                            <td style='font-weight: bold;font-size: 0.8rem;'><?php echo $total_productos; ?></td>
                            <td style='font-weight: bold;font-size: 1rem;'><?php echo $total; ?></td>
                            <td style='font-weight: bold;font-size: 0.8rem;'><?php echo $total_efectivo; ?></td>
                            <td style='font-weight: bold;font-size: 0.8rem;'><?php echo $total_transferencia; ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <p>Detalle de stock</p>
            <!-- Tabla para mostrar la información de los productos -->
            <div class="tablaProductosStock rounded tablaTurnosAll my-4 shadow py-2 px-4" style="overflow-x: auto;">
                <table class="table">
                    <!-- Cabecera de la tabla -->
                    <thead>
                        <tr class="align-middle">
                            <th>ID Producto</th>
                            <th>Nombre Producto</th>
                            <th>Precio</th>
                            <th>Cantidad Vendida</th>
                            <th>Stock Actual</th> <!-- Nueva columna para mostrar el stock -->
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Consulta para obtener los datos de la tabla detalle_ticket -->
                        <?php
                        $sqlProductos = "SELECT detalle_ticket.id_PRODUCTO, 
                                        producto.NOMBRE AS nombre_producto, 
                                        producto.PRECIO, 
                                        SUM(detalle_ticket.CANTIDAD) AS total_cantidad,
                                        SUM(detalle_ticket.PRECIO * detalle_ticket.CANTIDAD) AS total_producto,
                                        producto.STOCK  -- Agregar el campo STOCK
                                 FROM detalle_ticket 
                                 INNER JOIN ticket ON detalle_ticket.id_TICKET = ticket._id 
                                 INNER JOIN producto ON detalle_ticket.id_PRODUCTO = producto._id 
                                 WHERE STR_TO_DATE(ticket.FECHA, '%d-%m-%Y %H:%i') BETWEEN STR_TO_DATE('$fechaSeleccionadaInicial', '%d-%m-%Y %H:%i') AND STR_TO_DATE('$fechaSeleccionadaFinal', '%d-%m-%Y %H:%i')
                                 GROUP BY detalle_ticket.id_PRODUCTO";

                        // Variables para calcular el total general
                        $totalGeneral = 0;

                        // Ejecutar la consulta
                        $resultado_productos = mysqli_query($con, $sqlProductos);

                        // Mostrar los resultados en la tabla
                        while ($filaProducto = mysqli_fetch_assoc($resultado_productos)) {
                            // Calcular el total por producto
                            $totalProducto = $filaProducto['PRECIO'] * $filaProducto['total_cantidad'];

                            // Sumar al total general
                            $totalGeneral += $totalProducto;

                            // Mostrar los datos en la fila
                            echo "<tr class='align-middle'>";
                            echo "<td>" . $filaProducto['id_PRODUCTO'] . "</td>";
                            echo "<td>" . $filaProducto['nombre_producto'] . "</td>";
                            echo "<td>" . $filaProducto['PRECIO'] . "</td>";
                            echo "<td>" . $filaProducto['total_cantidad'] . "</td>";
                            echo "<td>" . $filaProducto['STOCK'] . "</td>"; // Mostrar el stock
                            echo "<td style='font-weight: bold;font-size: 0.8rem;'>" . $totalProducto . "</td>";
                            echo "</tr>";
                        }
                        ?>
                        <!-- Total general -->
                        <tr class='align-middle'>
                            <td colspan="4"></td>
                            <td style='font-weight: bold; font-size: 0.8rem;'>Total General</td>
                            <td style='font-weight: bold; font-size: 1rem;'><?php echo $totalGeneral; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>




</div>


<?php
include 'common_scripts.php';
?>

</body>

</html>
