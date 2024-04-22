<?php
date_default_timezone_set("America/Bogota");
setlocale(LC_ALL, "es_ES");

require ("config.php");

$cliente_id = intval($_REQUEST['cliente_id']);
$fecha_inicio = $_POST['hidden_hora_inicio'];
$fecha_fin = $_POST['hidden_hora_fin'];
// $color_evento = $_REQUEST['color_evento'];
$color_evento = "#2196F3"; //color predetermindado azul
$id_cancha = intval($_REQUEST["canchas"]);

$hora_inicio = $_POST['select_hora_inicio'];
$hora_fin = $_POST['select_hora_fin'];

$hora_inicial = $fecha_inicio . " " . $hora_inicio;
$hora_final = $fecha_fin . " " . $hora_fin;

// Formatear fechas en el formato adecuado (Y-m-d H:i:s)
$hora_inicial = date('Y-m-d H:i:s', strtotime($hora_inicial));
$hora_final = date('Y-m-d H:i:s', strtotime($hora_final));

$InsertNuevoEvento = "INSERT INTO turnos (
        id_CLIENTE,
        HORA_INICIO,
        HORA_FIN,
        COLOR,
        id_CANCHA,
        FINALIZADO
    ) VALUES (
        '" . $cliente_id . "',
        '" . $hora_inicial . "',
        '" . $hora_final . "',
        '" . $color_evento . "',
        '" . $id_cancha . "',
        TRUE
    )";

$resultadoNuevoEvento = mysqli_query($con, $InsertNuevoEvento);

// Crear detalle del turno y asignarle el id

// Obtener el ID del último turno insertado
$id_turno = mysqli_insert_id($con);

// Obtener la fecha del turno para el detalle
$fecha_actual = date('Y-m-d', strtotime($hora_inicial));

// Prepara la consulta SQL para obtener el precio de la cancha con el ID dado
$sql = "SELECT PRECIO FROM canchas WHERE _id = $id_cancha";
// Ejecuta la consulta SQL
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
$sql_insert_ticket = "INSERT INTO ticket (id_TURNO, FECHA, TOTAL_CANCHA, TOTAL_DETALLE, TOTAL, PAGO_TRANSFERENCIA, PAGO_EFECTIVO) VALUES ('$id_turno','$fecha_actual','$total_cancha', 0, '$total_cancha', 0, 0)";
$resultadoNuevoTicket = mysqli_query($con, $sql_insert_ticket);

header("Location:index.php?e=1");

?>