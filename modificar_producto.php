<?php
// Incluir el archivo de configuración de la base de datos
include 'config.php';

// Verificar si se envió un formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir los datos del formulario
    $nombreProducto = $_POST['nombreProducto'];
    $descripcionProducto = $_POST['descripcionProducto'];
    $precioProducto = $_POST['precioProducto'];
    $stockProducto = $_POST['stockProducto'];
    $idProducto = $_POST['idProducto'];

    // Verificar si se ha cargado una nueva imagen
    if (isset($_FILES['imagenProducto']) && $_FILES['imagenProducto']['error'] === UPLOAD_ERR_OK) {
        // Obtener información de la imagen
        $imagenNombre = $_FILES['imagenProducto']['name'];
        $imagenTipo = $_FILES['imagenProducto']['type'];
        $imagenTamanio = $_FILES['imagenProducto']['size'];
        $imagenTempPath = $_FILES['imagenProducto']['tmp_name'];

        // Directorio donde se almacenarán las imágenes cargadas
        $directorioImagenes = "img/productos/";

        // Generar un nombre único para la imagen
        $imagenNombreUnico = uniqid() . '_' . $imagenNombre;

        // Mover la imagen al directorio de imágenes
        $imagenRutaCompleta = $directorioImagenes . $imagenNombreUnico;
        if (move_uploaded_file($imagenTempPath, $imagenRutaCompleta)) {
            // La imagen se movió correctamente, ahora puedes guardar la ruta en la base de datos
            // Actualizar la ruta de la imagen en la base de datos
            $sql = "UPDATE producto SET URL_IMG = '$imagenRutaCompleta' WHERE id = $idProducto";
            mysqli_query($con, $sql);

            // Si la consulta se realizó correctamente, podrías enviar una respuesta JSON de éxito
            $response = array(
                'success' => true,
                'message' => 'Imagen guardada correctamente.'
            );
            echo json_encode($response);
            exit;
        } else {
            // No se pudo mover la imagen al directorio especificado
            $response = array(
                'success' => false,
                'message' => 'Error al guardar la imagen.'
            );
            echo json_encode($response);
            exit;
        }
    } else {
        // No se ha cargado una nueva imagen, solo actualiza los otros campos del producto
        // Aquí deberías realizar la consulta SQL para actualizar los otros campos del producto
        $sql = "UPDATE producto SET NOMBRE = '$nombreProducto', DESCRIPCION = '$descripcionProducto', PRECIO = $precioProducto, STOCK = $stockProducto WHERE id = $idProducto";
        mysqli_query($con, $sql);

        // Si la consulta se realizó correctamente, podrías enviar una respuesta JSON de éxito
        $response = array(
            'success' => true,
            'message' => 'Producto actualizado correctamente.'
        );
        echo json_encode($response);
        exit;
    }
} else {
    // Si no se envió un formulario por el método POST, muestra un mensaje de error
    $response = array(
        'success' => false,
        'message' => 'Error: No se recibió ningún formulario.'
    );
    echo json_encode($response);
    exit;
}
?>