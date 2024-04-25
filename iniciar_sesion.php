<?php
include ('config.php');

$usuario = $_POST['Usuario'];
$clave = $_POST['Clave'];

$sql = "SELECT * FROM usuarios WHERE USUARIO='$usuario' AND CLAVE='$clave'";
$consulta = mysqli_query($con, $sql);
$existe = mysqli_num_rows($consulta);

if ($existe == 1) {
    // Obtener el tipo de usuario de la fila de la base de datos
    $usuario_info = mysqli_fetch_assoc($consulta);
    $tipo_usuario = $usuario_info['TIPO'];

    // Crear una firma para las cookies de usuario y tipo de usuario
    $firma_usuario = hash_hmac('sha256', $usuario, 'clave_secreta');
    $firma_tipo = hash_hmac('sha256', $tipo_usuario, 'clave_secreta');

    // Establecer cookies para el usuario y el tipo de usuario junto con la firma
    setcookie("USUARIO", $usuario . '|' . $firma_usuario, time() + 3600, '/', '', true, true);
    setcookie("TIPO", $tipo_usuario . '|' . $firma_tipo, time() + 3600, '/', '', true, true);

    // Redireccionar a otra página
    header("Location: page_turnos.php");
    exit(); // Asegurarse de que el script se detenga después de redireccionar
} else {
    // Si el usuario o la clave son incorrectos, mostrar un alert y redirigir a la página de inicio de sesión
    echo "<script>alert('Verifique que el usuario y la clave sean correctos'); window.location='index.php';</script>";
}
?>