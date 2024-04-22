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
    
    // Obtener el TOTAL de la tabla ticket para el idTurno proporcionado
    $sql_total = "SELECT TOTAL FROM ticket WHERE id_TURNO = ?";
    $stmt_total = mysqli_prepare($con, $sql_total);
    mysqli_stmt_bind_param($stmt_total, "d", $idTurno);
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
    if(mysqli_stmt_execute($stmt)) {
        // Si la actualización fue exitosa, enviar un JSON con "success" como true
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