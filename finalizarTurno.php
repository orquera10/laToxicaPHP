<?php
// Verificar si se recibieron los parámetros esperados
if (isset($_POST['idTurno']) && isset($_POST['pagoEfectivo']) && isset($_POST['pagoTransferencia']) && isset($_POST['nombrePagos']) && isset($_POST['montoTransferencias']) && isset($_POST['montoEfectivos'])) {
    // Establecer la conexión con la base de datos (reemplaza con tus propios datos)
    require ("config.php");

    // Obtener los valores de los parámetros
    $idTurno = $_POST['idTurno'];
    $pagoEfectivo = $_POST['pagoEfectivo'];
    $pagoTransferencia = $_POST['pagoTransferencia'];
    $nombrePagos = $_POST['nombrePagos'];
    $montoTransferencias = $_POST['montoTransferencias'];
    $montoEfectivos = $_POST['montoEfectivos'];

    // Obtener el TOTAL y _id de la tabla ticket para el idTurno proporcionado
    $sql_total = "SELECT TOTAL, _id FROM ticket WHERE id_TURNO = ?";
    $stmt_total = mysqli_prepare($con, $sql_total);
    mysqli_stmt_bind_param($stmt_total, "d", $idTurno);
    mysqli_stmt_execute($stmt_total);
    mysqli_stmt_bind_result($stmt_total, $total, $idTicket);
    mysqli_stmt_fetch($stmt_total);
    mysqli_stmt_close($stmt_total);

    // Validar la suma de pago_efectivo y pago_transferencia con el TOTAL
    if ($pagoEfectivo + $pagoTransferencia != $total) {
        // Si la suma no es igual al TOTAL, mostrar un mensaje de error
        echo json_encode(array("success" => false, "message" => "La suma de los pagos no coincide con el TOTAL."));
        exit(); // Terminar el script
    }

    // Obtener la fecha y hora actual
    $fecha_actual = date("d-m-Y H:i:s");

    // Iniciar una transacción
    mysqli_autocommit($con, false);

    // Preparar la consulta SQL para actualizar las tablas turno y ticket
    $sql_update = "UPDATE turnos AS t
            JOIN ticket AS ti ON t._id = ti.id_TURNO
            SET ti.PAGO_EFECTIVO = ?, 
                ti.PAGO_TRANSFERENCIA = ?,
                ti.FECHA = ?, 
                t.FINALIZADO = 1
            WHERE t._id = ?";
    $stmt_update = mysqli_prepare($con, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "ddsd", $pagoEfectivo, $pagoTransferencia, $fecha_actual, $idTurno);
    $success = mysqli_stmt_execute($stmt_update);

    // Verificar si la actualización fue exitosa
    if (!$success) {
        // Si hubo un error, hacer rollback y mostrar un mensaje de error
        mysqli_rollback($con);
        echo json_encode(array("success" => false, "message" => "Error al actualizar los datos: " . mysqli_error($con)));
        exit();
    }

    // Preparar la consulta SQL para insertar los detalles de pago en detalle_pago
    $sql_insert_detalle_pago = "INSERT INTO detalle_pago (id_TICKET, NOMBRE, TRANSFERENCIA, EFECTIVO) VALUES (?, ?, ?, ?)";
    $stmt_insert_detalle_pago = mysqli_prepare($con, $sql_insert_detalle_pago);

    // Iterar sobre los pagos y ejecutar la consulta para cada uno
    for ($i = 0; $i < count($nombrePagos); $i++) {
        mysqli_stmt_bind_param($stmt_insert_detalle_pago, "isdd", $idTicket, $nombrePagos[$i], $montoTransferencias[$i], $montoEfectivos[$i]);
        $success = mysqli_stmt_execute($stmt_insert_detalle_pago);
        // Verificar si hubo algún error en la ejecución de la consulta
        if (!$success) {
            // Si hay un error, hacer rollback y mostrar un mensaje de error
            mysqli_rollback($con);
            echo json_encode(array("success" => false, "message" => "Error al insertar detalles de pago: " . mysqli_error($con)));
            exit();
        }
    }

    // Obtener los productos relacionados con el id_TICKET desde la tabla detalle_ticket
    $sql_detalle_ticket = "SELECT id_PRODUCTO, CANTIDAD FROM detalle_ticket WHERE id_TICKET = ?";
    $stmt_detalle_ticket = mysqli_prepare($con, $sql_detalle_ticket);
    mysqli_stmt_bind_param($stmt_detalle_ticket, "i", $idTicket);
    mysqli_stmt_execute($stmt_detalle_ticket);
    $success = mysqli_stmt_bind_result($stmt_detalle_ticket, $idProducto, $cantidadProducto);

    if ($success) {
        // Preparar la consulta SQL para insertar registros en la tabla stock
        $sql_insert_stock = "INSERT INTO stock (id_PRODUCTO, TIPO, CANTIDAD) VALUES (?, 'EGRESO', ?)";
        $stmt_insert_stock = mysqli_prepare($con, $sql_insert_stock);

        // Iterar sobre los productos y ejecutar la consulta para cada uno
        while (mysqli_stmt_fetch($stmt_detalle_ticket)) {
            mysqli_stmt_bind_param($stmt_insert_stock, "ii", $idProducto, $cantidadProducto);
            $success = mysqli_stmt_execute($stmt_insert_stock);
            // Verificar si hubo algún error en la ejecución de la consulta
            if (!$success) {
                // Si hay un error, hacer rollback y mostrar un mensaje de error
                mysqli_rollback($con);
                echo json_encode(array("success" => false, "message" => "Error al insertar productos en la tabla stock: " . mysqli_error($con)));
                exit();
            }
        }
    } else {
        // Si hay un error, hacer rollback y mostrar un mensaje de error
        mysqli_rollback($con);
        echo json_encode(array("success" => false, "message" => "Error al insertar productos en la tabla stock: " . mysqli_error($con)));
        exit();
    }


    // Si llegamos aquí, todo se ejecutó correctamente, hacer commit
    mysqli_commit($con);

    // Cerrar la consulta de detalle_ticket
    mysqli_stmt_close($stmt_detalle_ticket);

    // Cerrar la declaración de inserción en stock
    mysqli_stmt_close($stmt_insert_stock);

    // Cerrar las declaraciones y la conexión
    mysqli_stmt_close($stmt_update);
    mysqli_stmt_close($stmt_insert_detalle_pago);
    mysqli_close($con);

    // Enviar una respuesta de éxito
    echo json_encode(array("success" => true));
} else {
    // Si no se recibieron todos los parámetros esperados, mostrar un mensaje de error
    echo json_encode(array("success" => false, "message" => "Error: Todos los parámetros necesarios no fueron proporcionados."));
}



?>