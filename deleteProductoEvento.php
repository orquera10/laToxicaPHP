<?php
include ('config.php');

// Verificar si se recibió el idProducto
if (isset($_POST['idProducto'], $_POST['idEvento'])) {
    $idProducto = $_POST['idProducto'];
    $idEvento = $_POST['idEvento'];

    // Consulta SQL para obtener el id_TICKET correspondiente al idEvento
    $sql_ticket = "SELECT _id, TOTAL_CANCHA FROM ticket WHERE id_TURNO = $idEvento";

    // Ejecutar la consulta SQL
    $result_ticket = mysqli_query($con, $sql_ticket);

    // Verificar si se encontró el id_TICKET
    if ($result_ticket && mysqli_num_rows($result_ticket) > 0) {
        $row = mysqli_fetch_assoc($result_ticket);
        $idTicket = $row['_id'];
        $total_cancha = $row['TOTAL_CANCHA'];

        // Consulta SQL para obtener el precio del producto
        $sql_precio_producto = "SELECT PRECIO FROM producto WHERE _id = $idProducto";

        // Ejecutar la consulta SQL
        $result_precio_producto = mysqli_query($con, $sql_precio_producto);

        // Verificar si se encontró el precio del producto
        if ($result_precio_producto && mysqli_num_rows($result_precio_producto) > 0) {
            $row_precio_producto = mysqli_fetch_assoc($result_precio_producto);
            $precio_producto = $row_precio_producto['PRECIO'];

            // Consulta SQL para eliminar el producto de la tabla detalle_ticket
            $sql_delete_producto = "DELETE FROM detalle_ticket WHERE id_TICKET = $idTicket AND id_PRODUCTO = $idProducto";

            // Ejecutar la consulta SQL
            if (mysqli_query($con, $sql_delete_producto)) {
                // Actualizar el total de los productos en el ticket
                $sql_total_productos = "SELECT SUM(producto.PRECIO * detalle_ticket.CANTIDAD) AS total 
                                        FROM detalle_ticket 
                                        INNER JOIN producto ON detalle_ticket.id_PRODUCTO = producto._id 
                                        WHERE detalle_ticket.id_TICKET = $idTicket";
                $result_total_productos = mysqli_query($con, $sql_total_productos);
                $fila_total_productos = mysqli_fetch_assoc($result_total_productos);
                $total_productos = $fila_total_productos['total'];

                // Actualizar el total del ticket
                $sql_update_total = "UPDATE ticket SET TOTAL_DETALLE = '$total_productos' WHERE _id = $idTicket";
                mysqli_query($con, $sql_update_total);

                // Actualizar el total general del ticket
                $total_general = ($total_productos !== null) ? $total_productos : $total_cancha;
                $sql_update_total_general = "UPDATE ticket SET TOTAL = '$total_general' WHERE _id = $idTicket";
                mysqli_query($con, $sql_update_total_general);

                echo "Producto eliminado correctamente";
            } else {
                echo "Error al eliminar el producto: " . mysqli_error($con);
            }
        } else {
            echo "No se encontró el precio del producto";
        }
    } else {
        echo "No se encontró el ticket correspondiente al evento seleccionado";
    }
} else {
    echo "No se recibió el id del producto o el id del evento";
}

?>