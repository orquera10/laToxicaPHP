<?php
    date_default_timezone_set("America/Bogota");
    setlocale(LC_ALL, "es_ES");

    require ("config.php");

    $evento = ucwords($_REQUEST['evento']);
    $fecha_inicio = $_POST['hidden_hora_inicio'];
    $fecha_fin = $_POST['hidden_hora_fin'];
    // $color_evento = $_REQUEST['color_evento'];
    $color_evento = "#2196F3";
    $cancha_evento = intval($_REQUEST["canchas"]);

    $hora_inicio = $_POST['select_hora_inicio'];
    $hora_fin = $_POST['select_hora_fin'];

    $hora_inicio = $fecha_inicio . " " . $hora_inicio;
    $hora_fin = $fecha_fin . " " . $hora_fin;

    // Formatear fechas en el formato adecuado (Y-m-d H:i:s)
    $fecha_inicio = date('Y-m-d H:i:s', strtotime($hora_inicio));
    $fecha_fin = date('Y-m-d H:i:s', strtotime($hora_fin));

    $InsertNuevoEvento = "INSERT INTO turnos (
        NOMBRE,
        HORA_INICIO,
        HORA_FIN,
        FECHA,
        _idCANCHA
    ) VALUES (
        '" . $evento . "',
        '" . $fecha_inicio . "',
        '" . $fecha_fin . "',
        '" . $color_evento . "',
        '" . $cancha_evento . "'
    )";

    $resultadoNuevoEvento = mysqli_query($con, $InsertNuevoEvento);

    header("Location:index.php?e=1");
?>