<?php
// Incluir el archivo de configuración de la base de datos
include 'config.php';

// Verificar si se envió un formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir los datos del formulario
    $nombreProducto = $_POST['nombreProducto'];
    $precioProducto = $_POST['precioProducto'];
    $stockProducto = $_POST['stockProducto'];

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

            // Consulta SQL para insertar el nuevo producto en la base de datos
            $sql = "INSERT INTO producto (NOMBRE, PRECIO, STOCK, URL_IMG) 
                    VALUES ('$nombreProducto', $precioProducto, $stockProducto, '$imagenRutaCompleta')";

            // Ejecutar la consulta
            if (mysqli_query($con, $sql)) {
                // Si la consulta se realizó correctamente, enviar una respuesta de éxito
                $response = array(
                    'success' => true,
                    'message' => 'Producto agregado correctamente.'
                );
                echo json_encode($response);
                exit;
            } else {
                // Si hubo un error en la consulta, enviar un mensaje de error
                $response = array(
                    'success' => false,
                    'message' => 'Error al agregar el producto a la base de datos.'
                );
                echo json_encode($response);
                exit;
            }
        } else {
            // No se pudo mover la imagen al directorio especificado
            $response = array(
                'success' => false,
                'message' => 'Error al mover la imagen al directorio de imágenes.'
            );
            echo json_encode($response);
            exit;
        }
    } else {
        // Si no se cargó una imagen, enviar un mensaje de error
        $response = array(
            'success' => false,
            'message' => 'Por favor, seleccione una imagen para el producto.'
        );
        echo json_encode($response);
        exit;
    }
} else {
    // Si no se envió un formulario por el método POST, mostrar un mensaje de error
    $response = array(
        'success' => false,
        'message' => 'Error: No se recibió ningún formulario.'
    );
    echo json_encode($response);
    exit;
}
?>
