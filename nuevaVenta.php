<?php
// Verificar si la solicitud es de tipo POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Incluir la conexión a la base de datos
    include ('config.php');
    date_default_timezone_set('America/Argentina/Buenos_Aires');

    // Obtener los datos de la venta del formulario
    $idCliente = $_POST['cliente_id_evento_venta'];
    $pagoTransferencia = $_POST['pagoTransfVenta'];
    $pagoEfectivo = $_POST['pagoEfectivoVenta'];
    $total = $_POST['totalVenta'];

    // Calcular la suma de los pagos
    $sumaPagos = $pagoTransferencia + $pagoEfectivo;

    // Verificar si el total coincide con la suma de los pagos
    if ($total != 0 && $total == $sumaPagos) {
        // Calcular la fecha actual
        $fecha = date("d-m-Y H:i");

        // Insertar un nuevo turno con FINALIZADO en 1
        $sql_insert_turno = "INSERT INTO turnos (FINALIZADO,id_CANCHA,VENTA) VALUES (1,9,1)";
        if ($con->query($sql_insert_turno) === TRUE) {
            // Obtener el ID del último turno insertado
            $id_turno = $con->insert_id;

            // Insertar la nueva venta en la tabla de tickets
            $sql = "INSERT INTO ticket (id_CLIENTE, id_TURNO, FECHA, TOTAL_CANCHA, TOTAL_DETALLE, TOTAL, PAGO_TRANSFERENCIA, PAGO_EFECTIVO) 
                    VALUES ('$idCliente', '$id_turno', '$fecha', 0, $total, $total, $pagoTransferencia, $pagoEfectivo)";

            if ($con->query($sql) === TRUE) {
                // Enviar una respuesta al cliente
                echo json_encode(array("success" => true));
            } else {
                // Enviar una respuesta de error al cliente si falla la inserción de la venta
                echo json_encode(array("success" => false, "message" => "No ingreso el Cliente"));
            }
        } else {
            // Enviar una respuesta de error al cliente si falla la creación del turno
            echo json_encode(array("success" => false, "message" => "Error al crear un nuevo turno: " . $con->error));
        }

        // Cerrar la conexión
        $con->close();
    } else {
        // Enviar una respuesta de error si el total no coincide con la suma de los pagos
        echo json_encode(array("success" => false, "message" => "El total no coincide con la suma del pago por transferencia y el pago en efectivo"));
    }
} else {
    // Enviar una respuesta de error si la solicitud no es de tipo POST
    echo json_encode(array("success" => false, "message" => "La solicitud debe ser de tipo POST"));
}
?>
