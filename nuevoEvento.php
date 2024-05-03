<?php
date_default_timezone_set("America/Bogota");
setlocale(LC_ALL, "es_ES");

require ("config.php");

// Verificar si se ha enviado el cliente_id y si no está vacío
if (!isset($_REQUEST['cliente_id']) || empty($_REQUEST['cliente_id'])) {
    // Si no se ha proporcionado un cliente_id válido, redirigir con un mensaje de error
    header("Location:page_turnos.php?error=El nombre del cliente no se ha proporcionado correctamente");
    exit; // Terminar la ejecución del script
}
$cliente_id = intval($_REQUEST['cliente_id']);
$fecha = $_POST['hidden_hora_inicio'];

$id_cancha = intval($_REQUEST["canchas"]);

$hora_inicio = $_POST['select_hora_inicio'];
$hora_fin = $_POST['select_hora_fin'];

// Definir el color del evento basado en el nombre de la cancha
if ($id_cancha == 7) {
    $color_evento = "#FCC30C"; // Color púrpura para la cancha 1 y cancha 2
} elseif ($id_cancha == 5) {
    $color_evento = "#4ED8E1"; // Color verde para la Cancha 1
} elseif ($id_cancha == 6) {
    $color_evento = "#8FE14E"; // Color azul para la Cancha 2
} elseif ($id_cancha == 8) {
    $color_evento = "#EA7BF3"; // Color naranja para Cumpleaños
} else {
    $color_evento = "#4E9BE1"; // Color predeterminado azul
}

// Ajustar la hora final si id_cancha es 8 y sumar 3 horas a la hora_inicio
if ($id_cancha == 8) {
    // Verificar si la hora de inicio es mayor o igual a las 21:00
    if (strtotime($hora_inicio) > strtotime('21:00')) {
        // Si la hora de inicio es mayor o igual a las 21:00, redirigir con un mensaje de error
        header("Location:page_turnos.php?error=La hora de inicio debe ser anterior a las 21:00 para los eventos de cumpleaños");
        exit; // Terminar la ejecución del script
    }
    // Sumar 3 horas a la hora_inicio
    $hora_fin = date('H:i', strtotime($hora_inicio . ' +3 hours'));
    // Si la hora_fin es 0:00, cambiarla a 24:00
    if ($hora_fin == '00:00') {
        $hora_fin = '24:00';
    }
} else {
    // Verificar si la hora de inicio es menor que la hora de finalización
    if (strtotime($hora_inicio) >= strtotime($hora_fin)) {
        // Si la hora de inicio es mayor o igual que la hora de finalización, redirigir con un mensaje de error
        header("Location:page_turnos.php?error=La hora de inicio debe ser menor que la hora de finalización");
        exit; // Terminar la ejecución del script
    }
}

// Inicializar la consulta SQL
$sql = "";

if ($id_cancha == 7) {
    $sql = "SELECT t.* FROM turnos t
            INNER JOIN canchas c ON t.id_CANCHA = c._id
            WHERE t.FECHA = '$fecha' 
            AND ((t.HORA_INICIO < '$hora_fin' AND t.HORA_FIN > '$hora_inicio') 
            OR (t.HORA_INICIO <= '$hora_inicio' AND t.HORA_FIN >= '$hora_fin'))
            AND (t.id_CANCHA = $id_cancha OR c.NOMBRE = 'Cancha 1' OR c.NOMBRE = 'Cancha 2' OR c.NOMBRE = 'Cumpleaños')";
} elseif ($id_cancha == 8) {
    $sql = "SELECT t.* FROM turnos t
            INNER JOIN canchas c ON t.id_CANCHA = c._id
            WHERE t.FECHA = '$fecha' 
            AND ((t.HORA_INICIO < '$hora_fin' AND t.HORA_FIN > '$hora_inicio') 
            OR (t.HORA_INICIO <= '$hora_inicio' AND t.HORA_FIN >= '$hora_fin'))
            AND (t.id_CANCHA = $id_cancha OR c.NOMBRE = 'Cancha 1' OR c.NOMBRE = 'Cancha 2' OR c.NOMBRE = 'Cancha 1 y Cancha 2')";
} else {
    // Consulta SQL para verificar si hay solapamiento de turnos
    $sql = "SELECT t.* FROM turnos t
            INNER JOIN canchas c ON t.id_CANCHA = c._id
            WHERE t.FECHA = '$fecha' 
            AND ((t.HORA_INICIO < '$hora_fin' AND t.HORA_FIN > '$hora_inicio') 
            OR (t.HORA_INICIO <= '$hora_inicio' AND t.HORA_FIN >= '$hora_fin'))
            AND (t.id_CANCHA = $id_cancha OR c.NOMBRE = 'Cancha 1 y Cancha 2' OR c.NOMBRE = 'Cumpleaños')";
}

// Ejecutar consulta
$resultado = mysqli_query($con, $sql);
if (mysqli_num_rows($resultado) > 0) {
    // Si hay algún solapamiento de turnos, redirigir con un mensaje de error
    header("Location:page_turnos.php?error=El nuevo turno se solapa con otro existente en ese horario.");
    exit; // Terminar la ejecución del script
}

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

// Definir el precio de la cancha basado en el id_cancha
if ($id_cancha == 8) {
    // Si es un cumpleaños, solo asignar el precio de la cancha
    $total_cancha = $fila['PRECIO'];
}


// Obtener la opción seleccionada para repetir el evento
$repetir = isset($_POST['repetir']) ? $_POST['repetir'] : '';

// Si no se selecciona ninguna opción para repetir el evento, insertar el turno único
if (empty($repetir)) {
    // Insertar el turno único en la base de datos con la misma información que el turno original
    $sql_insert_turno = "INSERT INTO turnos (
        HORA_INICIO,
        HORA_FIN,
        COLOR,
        FECHA,
        id_CANCHA,
        FINALIZADO,
        VENTA
    ) VALUES (
        '" . $hora_inicio . "',
        '" . $hora_fin . "',
        '" . $color_evento . "',
        '" . $fecha . "',
        '" . $id_cancha . "',
        0,
        0
    )";

    $resultado_insert_turno = mysqli_query($con, $sql_insert_turno);

    // Verificar si la inserción fue exitosa
    if (!$resultado_insert_turno) {
        // Si hubo un error al insertar el turno, redirigir con un mensaje de error
        header("Location:page_turnos.php?error=Error al insertar el turno: " . mysqli_error($con));
        exit; // Terminar la ejecución del script
    }

    // Obtener el ID del último turno insertado
    $id_turno = mysqli_insert_id($con);

    // Obtener la fecha del turno para el detalle
    $fecha_actual = date('d-m-Y', strtotime($fecha));

    // Insertar el ticket para el turno con el mismo total y fecha que el turno original
    $sql_insert_ticket = "INSERT INTO ticket (
        id_TURNO,
        id_CLIENTE,
        FECHA,
        TOTAL_CANCHA,
        TOTAL_DETALLE,
        TOTAL,
        PAGO_TRANSFERENCIA,
        PAGO_EFECTIVO
    ) VALUES (
        '$id_turno',
        '$cliente_id',
        '$fecha_actual',
        '$total_cancha',
        0,
        '$total_cancha',
        0,
        0
    )";

    $resultado_insert_ticket = mysqli_query($con, $sql_insert_ticket);

    // Verificar si la inserción fue exitosa
    if (!$resultado_insert_ticket) {
        // Si hubo un error al insertar el ticket, redirigir con un mensaje de error
        header("Location:page_turnos.php?error=Error al insertar el ticket para el turno: " . mysqli_error($con));
        exit; // Terminar la ejecución del script
    }

    // Redirigir a la página de turnos con un mensaje de éxito
    header("Location:page_turnos.php?e");
    exit; // Terminar la ejecución del script
}

// Inicializar la fecha de inicio y la fecha de fin para la repetición
$fecha_inicio_repetir = '';
$fecha_fin_repetir = '';

// Calcular la fecha de inicio y la fecha de fin para la repetición según la opción seleccionada
switch ($repetir) {
    case 'unMes':
        // La fecha de inicio para repetir es el mismo día que el turno original
        $fecha_inicio_repetir = date('Y-m-d', strtotime($fecha));

        // La fecha de fin para repetir es 28 días después del turno original (4 semanas)
        $fecha_fin_repetir = date('Y-m-d', strtotime('+28 days', strtotime($fecha)));
        break;

    case 'tresMeses':
        // La fecha de inicio para repetir es el mismo día que el turno original
        $fecha_inicio_repetir = date('Y-m-d', strtotime($fecha));

        // La fecha de fin para repetir es 84 días después del turno original (12 semanas)
        $fecha_fin_repetir = date('Y-m-d', strtotime('+84 days', strtotime($fecha)));
        break;

    case 'seisMeses':
        // La fecha de inicio para repetir es el mismo día que el turno original
        $fecha_inicio_repetir = date('Y-m-d', strtotime($fecha));

        // La fecha de fin para repetir es 168 días después del turno original (24 semanas)
        $fecha_fin_repetir = date('Y-m-d', strtotime('+168 days', strtotime($fecha)));
        break;

    default:
        // Si no se selecciona ninguna opción válida, redirigir con un mensaje de error
        header("Location:page_turnos.php?error=La opción de repetición seleccionada no es válida");
        exit; // Terminar la ejecución del script
}

// Insertar los turnos repetidos en la base de datos
// Iterar sobre las fechas de inicio y fin y crear los turnos repetidos para cada fecha
for ($fecha_actual = $fecha_inicio_repetir; $fecha_actual <= $fecha_fin_repetir; $fecha_actual = date('Y-m-d', strtotime('+1 week', strtotime($fecha_actual)))) {
    // Insertar el turno repetido en la base de datos con la misma información que el turno original
    $sql_insert_turno_repetido = "INSERT INTO turnos (
        HORA_INICIO,
        HORA_FIN,
        COLOR,
        FECHA,
        id_CANCHA,
        FINALIZADO,
        VENTA
    ) VALUES (
        '" . $hora_inicio . "',
        '" . $hora_fin . "',
        '" . $color_evento . "',
        '" . date('d-m-Y', strtotime($fecha_actual)) . "',
        '" . $id_cancha . "',
        0,
        0
    )";

    $resultado_insert_turno_repetido = mysqli_query($con, $sql_insert_turno_repetido);

    // Verificar si la inserción fue exitosa
    if (!$resultado_insert_turno_repetido) {
        // Si hubo un error al insertar el turno repetido, redirigir con un mensaje de error
        header("Location:page_turnos.php?error=Error al insertar el turno repetido: " . mysqli_error($con));
        exit; // Terminar la ejecución del script
    }

    // Obtener el ID del turno repetido insertado
    $id_turno_repetido = mysqli_insert_id($con);

    // Insertar el ticket para el turno repetido con el mismo total y fecha que el turno original
    $sql_insert_ticket_repetido = "INSERT INTO ticket (
        id_TURNO,
        id_CLIENTE,
        FECHA,
        TOTAL_CANCHA,
        TOTAL_DETALLE,
        TOTAL,
        PAGO_TRANSFERENCIA,
        PAGO_EFECTIVO
    ) VALUES (
        '$id_turno_repetido',
        '$cliente_id',
        '" . date('d-m-Y', strtotime($fecha_actual)) . "',
        '$total_cancha',
        0,
        '$total_cancha',
        0,
        0
    )";

    $resultado_insert_ticket_repetido = mysqli_query($con, $sql_insert_ticket_repetido);

    // Verificar si la inserción fue exitosa
    if (!$resultado_insert_ticket_repetido) {
        // Si hubo un error al insertar el ticket para el turno repetido, redirigir con un mensaje de error
        header("Location:page_turnos.php?error=Error al insertar el ticket para el turno repetido: " . mysqli_error($con));
        exit; // Terminar la ejecución del script
    }
}

// Redirigir a la página de turnos con un mensaje de éxito
header("Location:page_turnos.php?success=Los eventos repetidos se insertaron correctamente.");
exit; // Terminar la ejecución del script

?>