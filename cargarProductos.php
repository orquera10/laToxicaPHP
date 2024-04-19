<?php
include('config.php');

// Obtener el idEvento desde $_POST
if (isset($_POST['idEvento'])) {
    $idEvento = $_POST['idEvento'];
    $includeButtons = isset($_POST['includeButtons']) ? $_POST['includeButtons'] : false;

    // Consulta preparada SQL para obtener los productos
    $sql = "SELECT p.NOMBRE AS producto, d.CANTIDAD AS cantidad, p.PRECIO AS precio, d.CANTIDAD * p.PRECIO AS total, p._id AS idProducto
            FROM turnos e
            JOIN ticket t ON e._id = t.id_TURNO
            JOIN detalle_ticket d ON t._id = d.id_TICKET
            JOIN producto p ON d.id_PRODUCTO = p._id
            WHERE e._id = ?";

    // Preparar la declaración SQL
    $stmt = mysqli_prepare($con, $sql);

    // Vincular parámetros a la declaración SQL
    mysqli_stmt_bind_param($stmt, "i", $idEvento);

    // Ejecutar la consulta preparada
    mysqli_stmt_execute($stmt);

    // Obtener resultados de la consulta
    $result = mysqli_stmt_get_result($stmt);

    // Verificar si se obtuvieron resultados
    if (mysqli_num_rows($result) > 0) {
        // Construir el HTML de la tabla de productos
        $html = '';
        while($row = mysqli_fetch_assoc($result)) {
            $html .= "<tr>";
            $html .= "<td>" . htmlspecialchars($row["producto"]) . "</td>";
            $html .= "<td>" . htmlspecialchars($row["cantidad"]) . "</td>";
            $html .= "<td>" . htmlspecialchars($row["precio"]) . "</td>";
            $html .= "<td>" . htmlspecialchars($row["total"]) . "</td>";
            if ($includeButtons) {
                $html .= "<td><button class='btn btn-danger btnEliminarProducto' data-idProducto='" . $row["idProducto"] . "'>Eliminar</button></td>";
            }
            $html .= "</tr>";
        }
        // Imprimir el HTML
        echo $html;
    } else {
        // Si no se encontraron productos, imprimir un mensaje
        echo "<tr><td colspan='4'>No se encontraron productos para este evento</td></tr>";
    }

    // Cerrar la declaración SQL
    mysqli_stmt_close($stmt);
} else {
    // Si no se encontraron productos, imprimir un mensaje
    echo "<tr><td colspan='4'>No se encuentra variable idEvento</td></tr>";
}
?>

