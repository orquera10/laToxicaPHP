<?php
include ("config.php");

// Consulta SQL para seleccionar los nombres de las canchas de fútbol
$sql = "SELECT _id, NOMBRE FROM canchas";

// Ejecutar la consulta
$canchas = mysqli_query($con, $sql);

// Obtener la fecha del campo oculto
$fecha_inicio = isset($_POST['fecha']) ? $_POST['fecha'] : '';

// Si la fecha de inicio está presente, la procesamos
if ($fecha_inicio) {
    // Convertir la fecha a un objeto DateTime
    $fecha_obj = new DateTime($fecha_inicio);

    // Obtener el día de la semana (1 para lunes, 7 para domingo)
    $dia_semana = $fecha_obj->format('N');

    // Verificar si es fin de semana (sábado o domingo)
    $es_fin_de_semana = $dia_semana >= 6;
} else {
    // Si no se proporcionó la fecha, asumimos que no es fin de semana
    $es_fin_de_semana = false;
}

$html = '';

if (mysqli_num_rows($canchas) > 0) {
    // Generar el código HTML de los options del desplegable
    while ($fila = mysqli_fetch_assoc($canchas)) {
        // Si es fin de semana, mostrar solo la opción "Cumpleaños"
        if ($es_fin_de_semana) {
            if ($fila['NOMBRE'] == "Cumpleaños") {
                $html .= '<option value="' . $fila['_id'] . '">' . $fila['NOMBRE'] . '</option>';
            }
        } else {
            // Si no es fin de semana, mostrar todas las opciones excepto "Cumpleaños"
            if ($fila['NOMBRE'] != "Cumpleaños") {
                $html .= '<option value="' . $fila['_id'] . '">' . $fila['NOMBRE'] . '</option>';
            }
        }
    }
} else {
    $html = "No se encontraron canchas de fútbol en la base de datos.";
}

// Devolver el HTML generado
echo $html;

?>