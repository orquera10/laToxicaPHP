<?php
// Incluir archivo de configuración de la base de datos
include 'config.php';

// Verificar si se recibió un ID de cancha y un nuevo precio
if (isset($_POST['cancha_id']) && isset($_POST['nuevo_precio'])) {
    // Limpiar los datos recibidos para evitar inyección SQL
    $cancha_id = mysqli_real_escape_string($con, $_POST['cancha_id']);
    $nuevo_precio = mysqli_real_escape_string($con, $_POST['nuevo_precio']);

    // Consulta SQL para actualizar el precio en la base de datos
    $sql = "UPDATE canchas SET PRECIO = '$nuevo_precio' WHERE _id = '$cancha_id'";

    // Ejecutar la consulta y verificar si fue exitosa
    if (mysqli_query($con, $sql)) {
        echo "Precio actualizado correctamente.";
    } else {
        echo "Error al actualizar el precio: " . mysqli_error($con);
    }
} else {
    echo "No se recibieron datos válidos.";
}
?>
