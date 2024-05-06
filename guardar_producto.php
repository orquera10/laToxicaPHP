<?php
// Incluir archivo de configuración de la base de datos
include 'config.php';

// Verificar si se enviaron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = $_POST['nombreProducto'];
    $descripcion = $_POST['descripcionProducto'];
    $precio = $_POST['precioProducto'];
    $stock = $_POST['stockProducto'];

    // Preparar la consulta SQL para insertar el nuevo producto
    $sql = "INSERT INTO producto (NOMBRE, DESCRIPCION, PRECIO, STOCK, VISIBLE) VALUES ('$nombre', '$descripcion', '$precio', '$stock', 1)";

    // Ejecutar la consulta SQL
    if (mysqli_query($con, $sql)) {
        echo json_encode(array("success" => true, "message" => "Producto agregado correctamente"));
    } else {
        echo json_encode(array("success" => false, "message" => "Error al agregar el producto: " . mysqli_error($con)));
    }

    // Cerrar la conexión a la base de datos
    mysqli_close($con);
}
?>
