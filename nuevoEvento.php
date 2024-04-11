<?php
date_default_timezone_set("America/Bogota");
setlocale(LC_ALL,"es_ES");

require("config.php");

$evento = ucwords($_REQUEST['evento']);
$fecha_inicio = $_REQUEST['fecha_inicio'];
$fecha_fin = $_REQUEST['fecha_fin'];
$color_evento = $_REQUEST['color_evento'];

// Convertir fecha de inicio y fin al formato adecuado (Y-m-d H:i:s)
$fecha_inicio = date('Y-m-d H:i:s', strtotime($fecha_inicio));
$fecha_fin = date('Y-m-d H:i:s', strtotime($fecha_fin));

$InsertNuevoEvento = "INSERT INTO eventoscalendar (
    evento,
    fecha_inicio,
    fecha_fin,
    color_evento
) VALUES (
    '" . $evento . "',
    '" . $fecha_inicio . "',
    '" . $fecha_fin . "',
    '" . $color_evento . "'
)";

$resultadoNuevoEvento = mysqli_query($con, $InsertNuevoEvento);

header("Location:index.php?e=1");
?>