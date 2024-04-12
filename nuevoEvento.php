<?php
    date_default_timezone_set("America/Bogota");
    setlocale(LC_ALL, "es_ES");

    require ("config.php");

    $evento = ucwords($_REQUEST['evento']);
    $fecha_inicio = $_POST['hidden_hora_inicio'];
    $fecha_fin = $_POST['hidden_hora_fin'];
    $color_evento = $_REQUEST['color_evento'];

    // Imprimir contenido de las variables para verificar
    error_log("Contenido de \$evento: " . $evento);
    error_log("Contenido de \$fechaInicio: " . $fecha_inicio);
    error_log("Contenido de \$fechaFin: " . $fecha_fin);
    error_log("Contenido de \$color_evento: " . $color_evento);

    // Formatear fechas en el formato adecuado (Y-m-d H:i:s)
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