<?php
include('config.php');

// Verificar si se recibió el idProducto
if (isset($_POST['idProducto'], $_POST['idEvento'])) {
    $idProducto = $_POST['idProducto'];
    $idEvento = $_POST['idEvento'];

    // Consulta SQL para obtener el id_TICKET correspondiente al idEvento
    $sql_ticket = "SELECT _id FROM ticket WHERE id_TURNO = $idEvento";

    // Ejecutar la consulta SQL
    $result_ticket = mysqli_query($con, $sql_ticket);

    // Verificar si se encontró el id_TICKET
    if ($result_ticket && mysqli_num_rows($result_ticket) > 0) {
        $row = mysqli_fetch_assoc($result_ticket);
        $idTicket = $row['_id'];

        // Consulta SQL para eliminar el producto de la tabla detalle_ticket
        $sql = "DELETE FROM detalle_ticket WHERE id_TICKET = $idTicket AND id_PRODUCTO = $idProducto";

        // Ejecutar la consulta SQL
        if (mysqli_query($con, $sql)) {
            echo "Producto eliminado correctamente";
        } else {
            echo "Error al eliminar el producto: " . mysqli_error($con);
        }
    } else {
        echo "No se encontró el ticket correspondiente al evento seleccionado";
    }
} else {
    echo "No se recibió el id del producto o el id del evento";
}
?>
