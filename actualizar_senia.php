<?php
// Conexión a la base de datos (usando tus datos de conexión)
require("config.php");

// Verificar si se reciben los datos esperados
if (isset($_POST['dinero_senia']) && isset($_POST['idEvento']) && isset($_POST['idCliente'])) {
    // Obtener los valores recibidos por AJAX
    $dinero_senia = $_POST['dinero_senia'];
    $idEvento = $_POST['idEvento'];
    $idCliente = $_POST['idCliente'];
    $fecha = date('d-m-Y H:i:s'); // Formato de fecha y hora para MySQL

    // Obtener el total actual del ticket
    $sql_total = "SELECT TOTAL FROM ticket WHERE ID_TURNO = $idEvento";
    $resultado = mysqli_query($con, $sql_total);

    if ($resultado) {
        $row = mysqli_fetch_assoc($resultado);
        $total_actual = $row['TOTAL'];
        mysqli_free_result($resultado);

        // Calcular el nuevo total sumando el monto extra al total actual
        $nuevo_total = $total_actual - $dinero_senia;

        // Actualizar el valor SENIA y el TOTAL en la tabla ticket
        $sql_update = "UPDATE ticket SET SENIA = SENIA + $dinero_senia, TOTAL = $nuevo_total WHERE ID_TURNO = $idEvento";

        if (mysqli_query($con, $sql_update)) {
            // Inserción en la tabla senias
            $sql_insert_senia = "INSERT INTO senias (MONTO, FECHA, id_CLIENTE, id_TURNO) VALUES ('$dinero_senia', '$fecha', '$idCliente', '$idEvento')";
            if (mysqli_query($con, $sql_insert_senia)) {
                echo "La seña se inserto correctamente.";
            } else {
                // Si hay un error al insertar la seña
                echo "Error al insertar la seña: " . mysqli_error($con);
            }
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
