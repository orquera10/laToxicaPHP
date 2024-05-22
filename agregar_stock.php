<?php

include 'config.php';

// Obtener la fecha actual en formato 'd-m-Y'
$fecha_actual = date('d-m-Y');

// Verificar si ya existe una entrada para la fecha actual
$sql = "SELECT _id FROM resumen_dias WHERE FECHA = '$fecha_actual'";
$result = $con->query($sql);

if ($result->num_rows == 0) {
    // Si no existe una entrada para la fecha actual, crear una nueva entrada
    $sql_nueva_entrada = "INSERT INTO resumen_dias (FECHA) VALUES ('$fecha_actual')";
    if ($con->query($sql_nueva_entrada) === TRUE) {
        // Obtener el ID de la nueva entrada
        $id_dia = $con->insert_id;
    } else {
        echo "Error al crear una nueva entrada de stock: " . $con->error;
        exit;
    }
} else {
    // Si ya existe una entrada para la fecha actual, obtener su ID
    $row = $result->fetch_assoc();
    $id_dia = $row['_id'];
}

// Obtener los datos del formulario (reemplaza estos nombres de acuerdo a tu formulario)
$id_producto = $_POST['id_producto'];
$cantidad = $_POST['cantidad'];

// Verificar si la cantidad es negativa
if ($cantidad < 0) {
    // Si la cantidad es negativa, registrar como EGRESO y convertir la cantidad a positiva
    $cantidad = abs($cantidad);
    $sql_insertar_stock = "INSERT INTO stock (id_DIA, id_PRODUCTO, EGRESO) VALUES ('$id_dia', '$id_producto', '$cantidad')";
} else {
    // Si la cantidad es positiva, registrar como INGRESO
    $sql_insertar_stock = "INSERT INTO stock (id_DIA, id_PRODUCTO, INGRESO) VALUES ('$id_dia', '$id_producto', '$cantidad')";
}

if ($con->query($sql_insertar_stock) === TRUE) {
    echo "El stock se agregó correctamente";
} else {
    echo "Error al agregar el stock: " . $con->error;
}

// Cerrar la conexión
$con->close();
?>
