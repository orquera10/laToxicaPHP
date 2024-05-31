<?php
// Incluir archivos necesarios
$pageTitle = "Informe Diario";
include 'header.php';
include 'headerUsuario.php';
include 'barraNavegacion.php';
include 'config.php'; // Suponiendo que aquí se encuentra la configuración de la conexión a la base de datos

$total_cancha = 0;
$extra = 0;
$senia = 0;
$total_productos = 0;
$total = 0;
$total_efectivo = 0;
$total_transferencia = 0;

// Inicializar $resultado como un array vacío
$resultado = [];
$fechaSeleccionada = date("d-m-Y");

$fechaSeleccionadaInicial = date('d-m-Y 07:00', strtotime($fechaSeleccionada));
$fechaSeleccionadaFinal = date('d-m-Y 04:00', strtotime('+1 day', strtotime($fechaSeleccionada)));

// Consulta para verificar si ya existe una entrada para la fecha seleccionada
$sql_verificar = "SELECT _id FROM `resumen_dias` WHERE `FECHA` = '$fechaSeleccionada'";
$resultado_verificar = mysqli_query($con, $sql_verificar);

// Verificar si la consulta devuelve filas
if (mysqli_num_rows($resultado_verificar) == 0) {
    // No hay entrada para esta fecha, proceder con la inserción
    $sql_insertar = "INSERT INTO `resumen_dias` (`FECHA`) VALUES ('$fechaSeleccionada')";
    mysqli_query($con, $sql_insertar);
    // Obtener el ID generado
    $id_resumen = mysqli_insert_id($con);
} else {
    // Ya existe una entrada en la tabla para la fecha seleccionada, obtener su ID
    $fila_resumen = mysqli_fetch_assoc($resultado_verificar);
    $id_resumen = $fila_resumen['_id'];
}


// Verificar si se ha seleccionado una fecha
if (isset($_POST['fechaInforme'])) {
    $fechaSeleccionada = $_POST['fechaInforme'];
    // Formatear la fecha al formato "d-m-Y"
    $fechaSeleccionada = date('d-m-Y', strtotime($fechaSeleccionada));

    // Consulta para verificar si ya existe una entrada para la fecha seleccionada
    $sql_verificar = "SELECT _id FROM `resumen_dias` WHERE `FECHA` = '$fechaSeleccionada'";
    $resultado_verificar = mysqli_query($con, $sql_verificar);

    // Verificar si la consulta devuelve filas
    if (mysqli_num_rows($resultado_verificar) == 0) {
        // No hay entrada para esta fecha, proceder con la inserción
        $sql_insertar = "INSERT INTO `resumen_dias` (`FECHA`) VALUES ('$fechaSeleccionada')";
        mysqli_query($con, $sql_insertar);
        // Obtener el ID generado
        $id_resumen = mysqli_insert_id($con);
    } else {
        // Ya existe una entrada en la tabla para la fecha seleccionada, obtener su ID
        $fila_resumen = mysqli_fetch_assoc($resultado_verificar);
        $id_resumen = $fila_resumen['_id'];
    }

    // Convertir la fecha al formato dd-mm-aaaa usando STR_TO_DATE()
    $fechaSeleccionadaInicial = date('d-m-Y 07:00', strtotime($fechaSeleccionada));

    $fechaSeleccionadaFinal = date('d-m-Y 04:00', strtotime('+1 day', strtotime($fechaSeleccionada)));

    $sql = "SELECT tk._id AS id_TICKET, 
    c.NOMBRE AS nombre_cliente, 
    t.FECHA AS fecha_reserva, 
    t.HORA_INICIO, 
    t.HORA_FIN, 
    cn.NOMBRE AS nombre_cancha, 
    tk.SENIA,
    tk.TOTAL_CANCHA,
    tk.EXTRA,  
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
    <p class="mt-5 h4">Informe Diario</p>
    <!-- Formulario para seleccionar la fecha -->
    <form id="fechaForm" method="post" action="" class="my-4">
        <div class="form-group mt-4 d-flex align-items-center">
            <p class="my-0 me-2 p-0" style="font-weight: bold; font-size: 0.8rem">Seleccionar fecha: </p>
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
                            <th>ID Ticket</th>
                            <th>Nombre Cliente</th>
                            <th>Fecha Pago</th>
                            <th>Cancha</th>
                            <th>Fecha Reserva</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                            <th>Seña</th>
                            <th>Total Cancha</th>
                            <th>Extra</th>
                            <th>Total Productos</th>
                            <th>Total</th>
                            <th>Pago Efectivo</th>
                            <th>Pago Transferencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Consulta para obtener los datos de la tabla ticket -->
                        <?php
                        $sqlTurnos = "SELECT tk._id AS id_TICKET, 
                                        c.NOMBRE AS nombre_cliente, 
                                        t.FECHA AS fecha_reserva, 
                                        t.HORA_INICIO, 
                                        t.HORA_FIN, 
                                        cn.NOMBRE AS nombre_cancha, 
                                        tk.SENIA, 
                                        tk.TOTAL_CANCHA,
                                        tk.EXTRA, 
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
                            echo "<td><a href='#' class='id-ticket-link' data-id-ticket='" . $filaTurno['id_TICKET'] . "'>" . $filaTurno['id_TICKET'] . "</a></td>";
                            echo "<td>" . $filaTurno['nombre_cliente'] . "</td>";
                            echo "<td>" . $filaTurno['fecha_ticket'] . "</td>";
                            echo "<td>" . $filaTurno['nombre_cancha'] . "</td>";
                            echo "<td>" . $filaTurno['fecha_reserva'] . "</td>";
                            echo "<td>" . $filaTurno['HORA_INICIO'] . "</td>";
                            echo "<td>" . $filaTurno['HORA_FIN'] . "</td>";
                            echo "<td>" . $filaTurno['SENIA'] . "</td>";
                            echo "<td>" . $filaTurno['TOTAL_CANCHA'] . "</td>";
                            echo "<td>" . $filaTurno['EXTRA'] . "</td>";
                            echo "<td>" . $filaTurno['TOTAL_DETALLE'] . "</td>";
                            echo "<td style='font-weight: bold;font-size: 0.8rem;'>" . $filaTurno['TOTAL'] +  $filaTurno['SENIA'] . "</td>";
                            echo "<td>" . $filaTurno['PAGO_EFECTIVO'] . "</td>";
                            echo "<td>" . $filaTurno['PAGO_TRANSFERENCIA'] . "</td>";
                            echo "</tr>";

                            // Sumar a los totales
                            $total_cancha += $filaTurno['TOTAL_CANCHA'];
                            $extra += $filaTurno['EXTRA'];
                            $senia += $filaTurno['SENIA'];
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
                            <td colspan="7"></td>
                            <td style='font-weight: bold;font-size: 0.8rem;'>Totales</td>
                            <td style='font-weight: bold;font-size: 0.8rem;'><?php echo $total_cancha; ?></td>
                            <td style='font-weight: bold;font-size: 0.8rem;'><?php echo $extra; ?></td>
                            
                            <td style='font-weight: bold;font-size: 0.8rem;'><?php echo $total_productos; ?></td>
                            <td style='font-weight: bold;font-size: 1rem;'><?php echo $total + $senia; ?></td>
                            <td style='font-weight: bold;font-size: 0.8rem;'><?php echo $total_efectivo; ?></td>
                            <td style='font-weight: bold;font-size: 0.8rem;'><?php echo $total_transferencia; ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Tabla para mostrar los gastos -->
        <div class="col-md-6">
            <p>Detalle de gastos</p>
            <div class="tablaGastos rounded tablaTurnosAll my-4 shadow py-2 px-4" style="overflow-x: auto;">
                <table class="table">
                    <!-- Cabecera de la tabla -->
                    <thead>
                        <tr class="align-middle">
                            <th>ID</th>
                            <th>ID Usuario</th>
                            <th>Nombre</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Consulta para obtener los gastos para la fecha seleccionada
                        $sqlGastos = "SELECT _id, id_USUARIO, NOMBRE, FECHA, MONTO FROM gastos WHERE STR_TO_DATE(FECHA, '%d-%m-%Y %H:%i') BETWEEN STR_TO_DATE('$fechaSeleccionadaInicial', '%d-%m-%Y %H:%i') AND STR_TO_DATE('$fechaSeleccionadaFinal', '%d-%m-%Y %H:%i')";

                        // Ejecutar la consulta
                        $resultado_gastos = mysqli_query($con, $sqlGastos);

                        // Inicializar la variable para el total general de los montos
                        $totalGastos = 0;

                        // Mostrar los resultados en la tabla
                        while ($filaGasto = mysqli_fetch_assoc($resultado_gastos)) {
                            // Sumar el monto al total general
                            $totalGastos += $filaGasto['MONTO'];

                            // Mostrar los datos en la fila
                            echo "<tr class='align-middle'>";
                            echo "<td>" . $filaGasto['_id'] . "</td>";
                            echo "<td>" . $filaGasto['id_USUARIO'] . "</td>";
                            echo "<td>" . $filaGasto['NOMBRE'] . "</td>";
                            echo "<td>" . $filaGasto['FECHA'] . "</td>";
                            echo "<td>" . $filaGasto['MONTO'] . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                    <!-- Pie de la tabla -->
                    <tfoot>
                        <tr class="align-middle">
                            <td colspan="4" style="text-align: right; font-weight: bold; font-size: 0.8rem;">Total
                                General</td>
                            <td style='font-weight: bold;font-size: 1rem;'><?php echo $totalGastos; ?></td>
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
                                        SUM(detalle_ticket.PRECIO * detalle_ticket.CANTIDAD) AS total_producto
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

                            echo "<td style='font-weight: bold;font-size: 0.8rem;'>" . $totalProducto . "</td>";
                            echo "</tr>";
                        }
                        ?>
                        <!-- Total general -->
                        <tr class='align-middle'>
                            <td colspan="3"></td>
                            <td style='font-weight: bold; font-size: 0.8rem;'>Total General</td>
                            <td style='font-weight: bold; font-size: 1rem;'><?php echo $totalGeneral; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <?php
        // Realizar una consulta para obtener el valor de FINALIZADO
        $sqlFinalizado = "SELECT FINALIZADO FROM `resumen_dias` WHERE `FECHA` = '$fechaSeleccionada'";
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
        <div class="col-md-12">
            <p class="h2">Ingresos</p>
            <p><span style="font-weight: bold;">Total en Cancha:</span> <?php echo $total_cancha; ?> $</p>
            <p><span style="font-weight: bold;">Total en Productos:</span> <?php echo $total_productos; ?> $</p>
            <p><span style="font-weight: bold;">Total en Extras:</span> <?php echo $extra; ?> $</p>
            
            <p style="font-weight: bold; font-size:1.4rem"><span>Total General:</span> <?php echo $total+$senia; ?> $</p>

            <!-- Agregar totales de gastos -->
            <?php
            $total_gastos = 0;
            $sqlGastos = "SELECT _id AS id_gasto, FECHA AS fecha_gasto, MONTO AS monto_gasto
                          FROM gastos
                          WHERE STR_TO_DATE(FECHA, '%d-%m-%Y %H:%i') BETWEEN STR_TO_DATE('$fechaSeleccionadaInicial', '%d-%m-%Y %H:%i') AND STR_TO_DATE('$fechaSeleccionadaFinal', '%d-%m-%Y %H:%i')";
            $resultado_gastos = mysqli_query($con, $sqlGastos);

            while ($filaGasto = mysqli_fetch_assoc($resultado_gastos)) {
                $total_gastos += $filaGasto['monto_gasto'];
            }
            ?>

            <p class="h2">Gastos</p>
            <p style="font-weight: bold; font-size:1.4rem"><span>Total de Gastos:</span> <?php echo $total_gastos; ?> $</p>

            <!-- Calcular y mostrar la diferencia -->
            <p class="h2">Diferencia</p>
            <p style="font-weight: bold; font-size:1.4rem"><span>Diferencia entre Ingresos y Gastos:</span>
                <?php echo $total + $senia - $total_gastos; ?> $</p>

        </div>
        <!-- Botón Cerrar Día con la propiedad disabled según el valor de FINALIZADO -->
        <div class="row">
            <div class="col-md-6 my-4">
                <button class="btn btn-primary" id="btnCerrarDia" <?php echo $disabled; ?>>Cerrar Día</button>
            </div>
        </div>
    </div>
</div>

<!-- Agregar un Modal HTML -->
<div class="modal fade" id="detalleTicketModal" tabindex="-1" aria-labelledby="detalleTicketModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detalleTicketModalLabel">Detalles del Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detallePagoTableBody">

            </div>
        </div>
    </div>
</div>

<?php
include 'common_scripts.php';
?>

<script>
    $(document).ready(function () {
        // Manejar el clic en los enlaces de ID Ticket
        $('.id-ticket-link').click(function (e) {
            e.preventDefault(); // Prevenir el comportamiento predeterminado del enlace

            // Obtener el ID del ticket del atributo data
            var idTicket = $(this).data('id-ticket');

            // Enviar una solicitud AJAX para obtener los detalles del ticket
            $.ajax({
                url: 'obtener_detalles_ticket.php',
                type: 'GET',
                data: { id: idTicket },
                success: function (response) {
                    // Mostrar los detalles del ticket en el modal
                    $('#detallePagoTableBody').html(response);
                    $('#detalleTicketModal').modal('show');
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Error al obtener los detalles del ticket.');
                }
            });
        });

        $('#btnCerrarDia').click(function () {
            var totalFilas = $('.tablaProductosStock tbody tr').length - 1; // Excluir la última fila
            var filasGuardadas = 0;
            var idResumen = <?php echo $id_resumen; ?>; // Obtener el ID de resumen_dias

            $('.tablaProductosStock tbody tr').each(function (index) {
                // Verificar si es la última fila
                if (index === totalFilas) return;

                var idProducto = $(this).find('td:eq(0)').text(); // Obtener el ID del producto
                var cantidadVendida = $(this).find('td:eq(3)').text(); // Obtener la cantidad vendida

                // Realizar una solicitud AJAX para guardar en la tabla de stock
                $.ajax({
                    url: 'quitar_stock.php',
                    type: 'POST',
                    data: {
                        id_resumen: idResumen, // Enviar el ID de resumen_dias
                        id_producto: idProducto,
                        egreso: cantidadVendida
                    },
                    dataType: 'json',
                    success: function (response) {
                        filasGuardadas++;
                        if (filasGuardadas === totalFilas) {
                            // Cambiar el valor de FINALIZADO a 1 en la tabla resumen_dias
                            $.ajax({
                                url: 'actualizar_finalizado_dia.php',
                                type: 'POST',
                                data: { id_resumen: idResumen },
                                success: function (response) {
                                    // Mostrar SweetAlert si todas las filas se han guardado correctamente
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: 'Se cerro correctamente el dia'
                                    }).then(function () {
                                        // Recargar la página
                                        location.reload();
                                    });
                                },
                                error: function () {
                                    // Mostrar SweetAlert si hay un error al actualizar FINALIZADO
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Ha ocurrido un error al actualizar FINALIZADO'
                                    });
                                }
                            });
                        }
                    },
                    error: function () {
                        // Mostrar SweetAlert si hay un error en la solicitud AJAX
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ha ocurrido un error al procesar la solicitud'
                        });
                    }
                });
            });
        });




    });
</script>


</body>

</html>