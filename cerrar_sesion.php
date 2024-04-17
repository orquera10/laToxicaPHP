<?php
// Iniciar la sesión si no está iniciada
session_start();

// Destruir la sesión
session_destroy();

// Eliminar la cookie de usuario manualmente
setcookie("USUARIO", "", time() - 3600); // Establece el tiempo de expiración en el pasado para eliminar la cookie

// Eliminar la cookie de tipo de usuario manualmente
setcookie("TIPO", "", time() - 3600); // Establece el tiempo de expiración en el pasado para eliminar la cookie

// Redirigir al usuario a la página de inicio
header("Location: login_page.php");
exit(); // Asegurar que el script se detenga después de redirigir
?>
