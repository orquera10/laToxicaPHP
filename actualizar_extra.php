<?php
// Conexión a la base de datos (usando tus datos de conexión config.php)
require("config.php");

// Verificar si se reciben los datos esperados
if (isset($_POST['dinero_extra']) && isset($_POST['idEvento'])) {
    // Obtener los valores recibidos por AJAX
    $dinero_extra = $_POST['dinero_extra'];
    $idEvento = $_POST['idEvento'];

    // Obtener el total actual del ticket
    $sql_total = "SELECT TOTAL FROM ticket WHERE ID_TURNO = $idEvento";
    $resultado = mysqli_query($con, $sql_total);

    if ($resultado) {
        $row = mysqli_fetch_assoc($resultado);
        $total_actual = $row['TOTAL'];
        mysqli_free_result($resultado);

        // Calcular el nuevo total sumando el monto extra al total actual
        $nuevo_total = $total_actual + $dinero_extra;

        // Actualizar el valor EXTRA y el TOTAL en la tabla ticket
        $sql_update = "UPDATE ticket SET EXTRA = EXTRA + $dinero_extra, TOTAL = $nuevo_total WHERE ID_TURNO = $idEvento";

        if (mysqli_query($con, $sql_update)) {
            // La actualización en la tabla ticket fue exitosa
            echo "La base de datos se actualizó correctamente.";
        } else {
            // Si hay un error en la actualización en la tabla ticket
            echo "Error al actualizar la tabla ticket: " . mysqli_error($con);
        }
    } else {
        // Si hay un error al obtener el total actual del ticket
        echo "Error al obtener el total actual del ticket: " . mysqli_error($con);
    }

    // Cerrar la conexión a la base de datos
    mysqli_close($con);
} else {
    // Si no se reciben los datos esperados
    echo "Error: Datos faltantes.";
}
?>

