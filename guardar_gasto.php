<?php
include ('config.php');

// Verificar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el nombre de usuario de las cookies
    $nombreUsuario = isset($_COOKIE['USUARIO']) ? $_COOKIE['USUARIO'] : '';
    // Extraer el nombre de usuario de la cookie
    $nombreUsuario = explode('|', $nombreUsuario)[0];

    // Obtener el _id del usuario basado en el nombre de usuario
    $sql_id_usuario = "SELECT _id FROM usuarios WHERE USUARIO = ?";
    $stmt_id_usuario = mysqli_prepare($con, $sql_id_usuario);
    mysqli_stmt_bind_param($stmt_id_usuario, "s", $nombreUsuario);
    mysqli_stmt_execute($stmt_id_usuario);
    $resultado_id_usuario = mysqli_stmt_get_result($stmt_id_usuario);

    // Verificar si se encontró el usuario
    if (mysqli_num_rows($resultado_id_usuario) > 0) {
        // Si se encontró el usuario, obtener su _id
        $fila_usuario = mysqli_fetch_assoc($resultado_id_usuario);
        $id_usuario = $fila_usuario['_id'];

        // Obtener los datos del formulario
        $nombreGasto = $_POST['nombreGasto'] ?? '';
        $montoGasto = $_POST['montoGasto'] ?? '';
        $fechaGasto = $_POST['fechaGasto'] ?? '';

        // Verificar si los campos obligatorios están vacíos
        if (empty($nombreGasto) || empty($montoGasto) || empty($fechaGasto)) {
            // Si faltan campos obligatorios, devolver un mensaje de error
            echo json_encode(['status' => 'error', 'message' => 'Por favor, complete todos los campos obligatorios.']);
            exit;
        }

        // Convertir la fecha al formato 'd-m-Y'
        $fechaGastoFormatted = date('d-m-Y H:i', strtotime($fechaGasto));

        // Preparar la consulta SQL para insertar el nuevo gasto
        $sql = "INSERT INTO gastos (NOMBRE, MONTO, FECHA, id_USUARIO) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $nombreGasto, $montoGasto, $fechaGastoFormatted, $id_usuario);

        // Ejecutar la consulta SQL
        if (mysqli_stmt_execute($stmt)) {
            // Si la inserción es exitosa, devolver un mensaje de éxito
            echo json_encode(['status' => 'success', 'message' => 'El gasto se ha registrado exitosamente.']);
        } else {
            // Si hay un error al ejecutar la consulta SQL, devolver un mensaje de error
            echo json_encode(['status' => 'error', 'message' => 'Error al registrar el gasto: ' . mysqli_stmt_error($stmt)]);
        }
    } else {
        // Si no se encuentra el usuario, devolver un mensaje de error
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado.']);
    }
} else {
    // Si la solicitud no es POST, devolver un mensaje de error
    echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida.']);
}

// Cerrar la conexión
mysqli_close($con);
?>

