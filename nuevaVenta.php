<?php
// Verificar si la solicitud es de tipo POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Incluir la conexión a la base de datos
    include ('config.php');


    // Obtener los datos de la venta del formulario
    $idCliente = isset($_POST['cliente_id_evento_venta']) && !empty($_POST['cliente_id_evento_venta']) ? $_POST['cliente_id_evento_venta'] : 1;

    $pagoTransferencia = $_POST['pagoTransfVenta'];
    $pagoEfectivo = $_POST['pagoEfectivoVenta'];
    $total = $_POST['totalVenta'];

    // Calcular la suma de los pagos
    $sumaPagos = $pagoTransferencia + $pagoEfectivo;

    // Verificar si el total coincide con la suma de los pagos
    if ($total != 0 && $total == $sumaPagos) {
        // Calcular la fecha actual
        $fecha = date("d-m-Y H:i:s");

        // Insertar la nueva venta en la tabla de tickets
        $sql_insert_ticket = "INSERT INTO ticket (id_CLIENTE, id_TURNO, FECHA, TOTAL_CANCHA, TOTAL_DETALLE, TOTAL, PAGO_TRANSFERENCIA, PAGO_EFECTIVO) 
                    VALUES ('$idCliente', 1 , '$fecha', 0, $total, $total, $pagoTransferencia, $pagoEfectivo)";

        if ($con->query($sql_insert_ticket) === TRUE) {
            // Obtener el ID del último ticket insertado
            $id_ticket = $con->insert_id;

            // Obtener los detalles de los productos agregados en el formulario
            $detalleProductos = json_decode($_POST['detalleProductos'], true);

            // Insertar cada producto en la tabla detalle_ticket y reducir el stock en la tabla producto
            foreach ($detalleProductos as $detalle) {
                $id_producto = $detalle['id'];
                $cantidad = $detalle['cantidad'];

                // Obtener el precio del producto de la tabla producto
                $sql_precio_producto = "SELECT PRECIO FROM producto WHERE _id = '$id_producto'";
                $result_precio_producto = $con->query($sql_precio_producto);
                $row_precio_producto = $result_precio_producto->fetch_assoc();
                $precio_producto = $row_precio_producto['PRECIO'];

                // Insertar el detalle del producto en detalle_ticket con el precio correspondiente
                $sql_insert_detalle = "INSERT INTO detalle_ticket (id_TICKET, ID_PRODUCTO, PRECIO, CANTIDAD) 
                                            VALUES ('$id_ticket', '$id_producto', '$precio_producto', '$cantidad')";

                if ($con->query($sql_insert_detalle)) {
                    // Reducir el stock del producto en la tabla producto
                    $sql_update_stock = "UPDATE producto SET STOCK = STOCK - $cantidad WHERE _id = '$id_producto'";
                    if (!$con->query($sql_update_stock)) {
                        // Enviar una respuesta de error si falla la actualización del stock
                        echo json_encode(array("success" => false, "message" => "Error al actualizar el stock del producto: " . $con->error));
                        exit;
                    }
                } else {
                    // Enviar una respuesta de error si falla la inserción del detalle
                    echo json_encode(array("success" => false, "message" => "Error al insertar el detalle del producto: " . $con->error));
                    exit;
                }
            }

            // Enviar una respuesta de éxito al cliente
            echo json_encode(array("success" => true));
        } else {
            // Enviar una respuesta de error si falla la inserción de la venta
            echo json_encode(array("success" => false, "message" => "Error al insertar el ticket: " . $con->error));
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