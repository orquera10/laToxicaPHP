<?php
include('config.php');

// Verificar si se recibió el idProducto y el idEvento
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

        // Consulta SQL para obtener la cantidad del producto en el detalle del ticket
        $sql_cantidad_producto = "SELECT CANTIDAD FROM detalle_ticket WHERE id_TICKET = $idTicket AND id_PRODUCTO = $idProducto";

        // Ejecutar la consulta SQL
        $result_cantidad_producto = mysqli_query($con, $sql_cantidad_producto);

        // Verificar si se encontró la cantidad del producto en el detalle del ticket
        if ($result_cantidad_producto && mysqli_num_rows($result_cantidad_producto) > 0) {
            $row_cantidad_producto = mysqli_fetch_assoc($result_cantidad_producto);
            $cantidad_producto = $row_cantidad_producto['CANTIDAD'];

            // Consulta SQL para eliminar el producto de la tabla detalle_ticket
            $sql_delete_producto = "DELETE FROM detalle_ticket WHERE id_TICKET = $idTicket AND id_PRODUCTO = $idProducto";

            // Ejecutar la consulta SQL
            if (mysqli_query($con, $sql_delete_producto)) {
                // Consulta SQL para obtener el stock actual del producto
                $sql_stock_actual = "SELECT STOCK FROM producto WHERE _id = $idProducto";

                // Ejecutar la consulta SQL
                $result_stock_actual = mysqli_query($con, $sql_stock_actual);

                // Verificar si se encontró el stock actual del producto
                if ($result_stock_actual && mysqli_num_rows($result_stock_actual) > 0) {
                    $row_stock_actual = mysqli_fetch_assoc($result_stock_actual);
                    $stock_actual = $row_stock_actual['STOCK'];

                    // Calcular el nuevo stock del producto
                    $nuevo_stock = $stock_actual + $cantidad_producto;

                    // Consulta SQL para actualizar el stock del producto
                    $sql_update_stock = "UPDATE producto SET STOCK = $nuevo_stock WHERE _id = $idProducto";

                    // Ejecutar la consulta SQL
                    if (mysqli_query($con, $sql_update_stock)) {
                        echo "Producto eliminado correctamente y stock actualizado";
                    } else {
                        echo "Error al actualizar el stock del producto: " . mysqli_error($con);
                    }
                } else {
                    echo "No se pudo obtener el stock actual del producto";
                }
            } else {
                echo "Error al eliminar el producto: " . mysqli_error($con);
            }
        } else {
            echo "No se encontró la cantidad del producto en el detalle del ticket";
        }
    } else {
        echo "No se encontró el ticket correspondiente al evento seleccionado";
    }
} else {
    echo "No se recibió el id del producto o el id del evento";
}

?>

