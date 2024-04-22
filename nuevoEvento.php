<?php
date_default_timezone_set("America/Bogota");
setlocale(LC_ALL, "es_ES");

require("config.php");

$cliente_id = intval($_REQUEST['cliente_id']);
$fecha = $_POST['hidden_hora_inicio'];

$color_evento = "#2196F3"; //color predeterminado azul
$id_cancha = intval($_REQUEST["canchas"]);

$hora_inicio = $_POST['select_hora_inicio'];
$hora_fin = $_POST['select_hora_fin'];

// Verificar si la hora de inicio es menor que la hora de finalización
if (strtotime($hora_inicio) >= strtotime($hora_fin)) {
    // Si la hora de inicio es mayor o igual que la hora de finalización, redirigir con un mensaje de error
    header("Location:index.php?error=La hora de inicio debe ser menor que la hora de finalización");
    exit; // Terminar la ejecución del script
}

// Inicializar la consulta SQL
$sql = "";

if ($id_cancha == 7) {
    $sql = "SELECT t.* FROM turnos t
            INNER JOIN canchas c ON t.id_CANCHA = c._id
            WHERE t.FECHA = '$fecha' 
            AND ((t.HORA_INICIO < '$hora_fin' AND t.HORA_FIN > '$hora_inicio') 
            OR (t.HORA_INICIO <= '$hora_inicio' AND t.HORA_FIN >= '$hora_fin'))
            AND (t.id_CANCHA = $id_cancha OR c.NOMBRE = 'Cancha 1' OR c.NOMBRE = 'Cancha 2')";
} else {
    // Consulta SQL para verificar si hay solapamiento de turnos
    $sql = "SELECT t.* FROM turnos t
            INNER JOIN canchas c ON t.id_CANCHA = c._id
            WHERE t.FECHA = '$fecha' 
            AND ((t.HORA_INICIO < '$hora_fin' AND t.HORA_FIN > '$hora_inicio') 
            OR (t.HORA_INICIO <= '$hora_inicio' AND t.HORA_FIN >= '$hora_fin'))
            AND (t.id_CANCHA = $id_cancha OR c.NOMBRE = 'Cancha 1 y Cancha 2')";
}

// Ejecutar consulta
$resultado = mysqli_query($con, $sql);
if (mysqli_num_rows($resultado) > 0) {
    // Si hay algún solapamiento de turnos, redirigir con un mensaje de error
    header("Location:index.php?error=El nuevo turno se solapa con otro existente en ese horario.");
    exit; // Terminar la ejecución del script
}

$InsertNuevoEvento = "INSERT INTO turnos (
        id_CLIENTE,
        HORA_INICIO,
        HORA_FIN,
        COLOR,
        FECHA,
        id_CANCHA,
        FINALIZADO
    ) VALUES (
        '" . $cliente_id . "',
        '" . $hora_inicio . "',
        '" . $hora_fin . "',
        '" . $color_evento . "',
        '" . $fecha . "',
        '" . $id_cancha . "',
        0
    )";

$resultadoNuevoEvento = mysqli_query($con, $InsertNuevoEvento);

// Verificar si la inserción fue exitosa
if (!$resultadoNuevoEvento) {
    // Si hubo un error al insertar el evento, redirigir con un mensaje de error
    header("Location:index.php?error=Error al insertar el nuevo turno: " . mysqli_error($con));
    exit;
}

// Obtener el ID del último turno insertado
$id_turno = mysqli_insert_id($con);

// Obtener la fecha del turno para el detalle
$fecha_actual = date('Y-m-d', strtotime($hora_inicio));

// Preparar la consulta SQL para obtener el precio de la cancha con el ID dado
$sql = "SELECT PRECIO FROM canchas WHERE _id = $id_cancha";
// Ejecutar la consulta SQL
$resultado = mysqli_query($con, $sql);
$fila = mysqli_fetch_assoc($resultado);
$precio_cancha = $fila['PRECIO'];

// Convierte las cadenas de tiempo en marcas de tiempo UNIX
$timestamp_inicio = strtotime($hora_inicio);
$timestamp_fin = strtotime($hora_fin);
// Calcula la diferencia en segundos
$diferencia_segundos = $timestamp_fin - $timestamp_inicio;
// Convierte la diferencia de segundos a horas (redondeado al número entero más cercano)
$diferencia_horas = round($diferencia_segundos / 3600); // 3600 segundos en una hora

//TOTAL A PAGAR CALCULADO CON LAS HORAS Y EL PRECIO DE LAS CANCHAS
$total_cancha = $diferencia_horas * $precio_cancha;

// Consulta para insertar un nuevo detalle con total 0 y la fecha del turno
$sql_insert_ticket = "INSERT INTO ticket (id_TURNO, FECHA, TOTAL_CANCHA, TOTAL_DETALLE, TOTAL, PAGO_TRANSFERENCIA, PAGO_EFECTIVO) VALUES ('$id_turno', '$fecha_actual', '$total_cancha', 0, '$total_cancha', 0, 0)";


$resultadoNuevoTicket = mysqli_query($con, $sql_insert_ticket);

header("Location:index.php?e=1");

?>