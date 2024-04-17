<?php
// Conectar a la base de datos (configura tus propias credenciales)
require ("config.php");

// Verificar si se reciben los datos del formulario
if (isset($_POST['idEvento'], $_POST['idProducto'], $_POST['cantidad'])) {
    // Recoger los datos del formulario
    $idEvento = $_POST['idEvento'];
    $idProducto = $_POST['idProducto'];
    $cantidad = $_POST['cantidad'];

    // Consulta SQL para obtener el id_TICKET correspondiente al id_TURNO
    $sql_ticket = "SELECT _id FROM ticket WHERE id_TURNO = '$idEvento'";
    $result_ticket = mysqli_query($con, $sql_ticket);

    // Verificar si se encontró el id_TICKET
    if (mysqli_num_rows($result_ticket) > 0) {
        $row = mysqli_fetch_assoc($result_ticket);
        $idTicket = $row['_id'];

        // Consulta SQL para verificar si el producto ya existe en el ticket
        $sql_exist = "SELECT * FROM detalle_ticket WHERE id_TICKET = '$idTicket' AND id_PRODUCTO = '$idProducto'";
        $result_exist = mysqli_query($con, $sql_exist);

        // Si el producto ya existe en el ticket, aumentar la cantidad
        if (mysqli_num_rows($result_exist) > 0) {
            $row_exist = mysqli_fetch_assoc($result_exist);
            $cantidad_existente = $row_exist['CANTIDAD'];

            // Calcular la nueva cantidad
            $nueva_cantidad = $cantidad_existente + $cantidad;

            // Actualizar la cantidad del producto en el ticket
            $sql_update = "UPDATE detalle_ticket SET CANTIDAD = '$nueva_cantidad' WHERE id_TICKET = '$idTicket' AND id_PRODUCTO = '$idProducto'";
            $result_update = mysqli_query($con, $sql_update);

            if ($result_update) {
                $response = array("success" => true, "message" => "Cantidad del producto actualizada con éxito.");
                echo json_encode($response);
            } else {
                $response = array("success" => false, "message" => "Error al actualizar la cantidad del producto: " . mysqli_error($con));
                echo json_encode($response);
            }
        } else {
            // El producto no existe en el ticket, insertarlo
            $sql_insert = "INSERT INTO detalle_ticket (id_TICKET, id_PRODUCTO, CANTIDAD) VALUES ('$idTicket', '$idProducto', '$cantidad')";
            $result_insert = mysqli_query($con, $sql_insert);

            if ($result_insert) {
                $response = array("success" => true, "message" => "Producto agregado al ticket con éxito.");
                echo json_encode($response);
            } else {
                $response = array("success" => false, "message" => "Error al agregar el producto al ticket: " . mysqli_error($con));
                echo json_encode($response);
            }
        }
    } else {
        // Error: No se encontró el id_TICKET correspondiente al id_TURNO
        $response = array("success" => false, "message" => "No se encontró el ticket correspondiente al evento seleccionado.");
        echo json_encode($response);
    }

    // Cerrar la conexión
    mysqli_close($con);
} else {
    // Si no se reciben los datos del formulario, devolver un mensaje de error
    $response = array("success" => false, "message" => "No se recibieron los datos del formulario.");
    echo json_encode($response);
}
?>
