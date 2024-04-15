<?php
include('config.php');
// Verificar si se recibió el idProducto
if (isset($_POST['idProducto'])) {
    $idProducto = $_POST['idProducto'];

    // Consulta SQL para eliminar el producto de la tabla detalle_ticket
    $sql = "DELETE FROM detalle_ticket WHERE id_PRODUCTO = $idProducto";

    // Ejecutar la consulta SQL
    if (mysqli_query($con, $sql)) {
        echo "Producto eliminado correctamente";
    } else {
        echo "Error al eliminar el producto: " . mysqli_error($con);
    }
} else {
    echo "No se recibió el id del producto";
}
?>