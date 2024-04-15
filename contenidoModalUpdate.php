<?php
include ('config.php');

// Obtener el idEvento desde $_POST
if (isset($_POST['idEvento'])) {
    $idEvento = $_POST['idEvento'];

    // Realizar la consulta SQL para obtener los productos correspondientes al idEvento
    // Asumiendo que tienes una relación entre las tablas eventos, ticket y detalle
    // También asumo que tienes una conexión establecida a la base de datos

    // Consulta SQL para obtener los productos
    $sql = "SELECT p.NOMBRE AS producto, d.CANTIDAD AS cantidad, p.PRECIO AS precio, d.CANTIDAD * p.PRECIO AS total, p._id AS idProducto
            FROM turnos e
            JOIN ticket t ON e._id = t.id_TURNO
            JOIN detalle_ticket d ON t._id = d.id_TICKET
            JOIN producto p ON d.id_PRODUCTO = p._id
            WHERE e._id = $idEvento";

    // Ejecutar la consulta SQL
    $result = mysqli_query($con, $sql);

    // Verificar si se obtuvieron resultados
    if (mysqli_num_rows($result) > 0) {
        // Construir el HTML de la tabla de productos
        $html = '';
        while ($row = mysqli_fetch_assoc($result)) {
            $html .= "<tr>";
            $html .= "<td>" . $row["producto"] . "</td>";
            $html .= "<td>" . $row["cantidad"] . "</td>";
            $html .= "<td>" . $row["precio"] . "</td>";
            $html .= "<td>" . $row["total"] . "</td>";
            $html .= "<td><button class='btn-danger btnEliminarProducto' data-idProducto='" . $row["idProducto"] . "'>Eliminar</button></td>";
            $html .= "</tr>";
        }
        // Imprimir el HTML
        echo $html;
    } else {
        // Si no se encontraron productos, imprimir un mensaje
        echo "<tr><td colspan='4'>No se encontraron productos para este evento</td></tr>";
    }
} else {
    // Si no se encontró el idEvento, imprimir un mensaje de error
    echo "<tr><td colspan='4'>No se encuentra variable idEvento</td></tr>";
}
?>