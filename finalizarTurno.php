<?php
// Verificar si se recibieron los parámetros esperados
if (isset($_POST['idTurno']) && isset($_POST['pagoEfectivo']) && isset($_POST['pagoTransferencia'])) {
    // Establecer la conexión con la base de datos (reemplaza con tus propios datos)
    require ("config.php");

    // Obtener los valores de los parámetros
    $idTurno = $_POST['idTurno'];
    $pagoEfectivo = $_POST['pagoEfectivo'];
    $pagoTransferencia = $_POST['pagoTransferencia'];

    // Valor del campo COLOR
    $color = "#E1574E";

    // Obtener el idTicket de la tabla ticket para el idTurno proporcionado
    $sql_id_ticket = "SELECT _id FROM ticket WHERE id_TURNO = ?";
    $stmt_id_ticket = mysqli_prepare($con, $sql_id_ticket);
    mysqli_stmt_bind_param($stmt_id_ticket, "d", $idTurno);
    mysqli_stmt_execute($stmt_id_ticket);
    mysqli_stmt_bind_result($stmt_id_ticket, $idTicket);
    mysqli_stmt_fetch($stmt_id_ticket);
    mysqli_stmt_close($stmt_id_ticket);

    // Validar si se obtuvo el idTicket
    if (!$idTicket) {
        // Si no se encontró el idTicket, mostrar un mensaje de error
        echo json_encode(array("success" => false, "message" => "No se encontró el id del ticket para el turno proporcionado."));
        exit(); // Terminar el script
    }

    // Obtener el TOTAL de la tabla ticket para el idTicket proporcionado
    $sql_total = "SELECT TOTAL FROM ticket WHERE _id = ?";
    $stmt_total = mysqli_prepare($con, $sql_total);
    mysqli_stmt_bind_param($stmt_total, "d", $idTicket);
    mysqli_stmt_execute($stmt_total);
    mysqli_stmt_bind_result($stmt_total, $total);
    mysqli_stmt_fetch($stmt_total);
    mysqli_stmt_close($stmt_total);

    // Validar la suma de pago_efectivo y pago_transferencia con el TOTAL
    if ($pagoEfectivo + $pagoTransferencia != $total) {
        // Si la suma no es igual al TOTAL, mostrar un mensaje de error
        echo json_encode(array("success" => false, "message" => "La suma de los pagos no coincide con el TOTAL."));
        exit(); // Terminar el script
    }

    // Preparar la consulta SQL para actualizar las tablas turno y ticket
    $sql = "UPDATE turnos AS t
            JOIN ticket AS ti ON t._id = ti.id_TURNO
            SET ti.PAGO_EFECTIVO = ?, 
                ti.PAGO_TRANSFERENCIA = ?,
                t.COLOR = ?,
                t.FINALIZADO = 1
            WHERE t._id = ?";

    // Preparar la declaración SQL
    $stmt = mysqli_prepare($con, $sql);

    // Vincular los parámetros con la declaración SQL
    mysqli_stmt_bind_param($stmt, "ddsd", $pagoEfectivo, $pagoTransferencia, $color, $idTurno);

    // Ejecutar la consulta
    if (mysqli_stmt_execute($stmt)) {
        // Si la actualización fue exitosa, proceder a actualizar el stock de productos
        // Consultar la tabla detalle_ticket para obtener los productos vendidos y sus cantidades
        $sql_detalle = "SELECT id_PRODUCTO, CANTIDAD FROM detalle_ticket WHERE id_TICKET = ?";
        $stmt_detalle = mysqli_prepare($con, $sql_detalle);
        mysqli_stmt_bind_param($stmt_detalle, "d", $idTicket);
        mysqli_stmt_execute($stmt_detalle);
        mysqli_stmt_bind_result($stmt_detalle, $idProducto, $cantidad);

        // Actualizar el stock de productos
        while (mysqli_stmt_fetch($stmt_detalle)) {
            // Consultar el stock actual del producto
            $sql_stock = "SELECT STOCK FROM producto WHERE _id = ?";
            $stmt_stock = mysqli_prepare($con, $sql_stock);
            mysqli_stmt_bind_param($stmt_stock, "d", $idProducto);
            mysqli_stmt_execute($stmt_stock);
            mysqli_stmt_bind_result($stmt_stock, $stock);
            mysqli_stmt_fetch($stmt_stock);

            // Calcular el nuevo stock
            $nuevoStock = $stock - $cantidad;

            // Actualizar el stock en la tabla productos
            $sql_actualizar_stock = "UPDATE producto SET STOCK = ? WHERE _id = ?";
            $stmt_actualizar_stock = mysqli_prepare($con, $sql_actualizar_stock);
            mysqli_stmt_bind_param($stmt_actualizar_stock, "dd", $nuevoStock, $idProducto);
            mysqli_stmt_execute($stmt_actualizar_stock);
            mysqli_stmt_close($stmt_actualizar_stock);

            // Liberar recursos después de cada iteración
            mysqli_stmt_close($stmt_stock);
        }


        // Liberar recursos
        mysqli_stmt_close($stmt_detalle);

        // Enviar una respuesta JSON indicando éxito
        echo json_encode(array("success" => true));
    } else {
        // Si hubo un error, enviar un JSON con "success" como false y el mensaje de error
        echo json_encode(array("success" => false, "message" => "Error al actualizar los datos: " . mysqli_error($con)));
    }

    // Cerrar la declaración SQL y la conexión
    mysqli_stmt_close($stmt);
    mysqli_close($con);
} else {
    // Si no se recibieron todos los parámetros esperados, mostrar un mensaje de error
    echo json_encode(array("success" => false, "message" => "Error: Todos los parámetros necesarios no fueron proporcionados."));
}
?>