<?php
// Incluir archivos necesarios
$pageTitle = "Canchas";
include 'header.php';
include 'headerUsuario.php';
include 'barraNavegacion.php';
include 'config.php'; // Suponiendo que aquí se encuentra la configuración de la conexión a la base de datos

// Variable para almacenar la consulta SQL
$sql = "SELECT * FROM canchas WHERE _id <> 9";

// Variable para almacenar el resultado de la consulta SQL
$result = mysqli_query($con, $sql);

// Variable para almacenar los resultados de la búsqueda
$canchas = [];

// Verificar si se obtuvieron resultados
if (mysqli_num_rows($result) > 0) {
    // Almacenar las canchas en un arreglo
    while ($row = mysqli_fetch_assoc($result)) {
        $canchas[] = $row;
    }
}
?>

<div class="container">
    <div class="row tarjetasCanchas">
        <?php foreach ($canchas as $cancha): ?>
            <div class="col-md-3 my-5">
                <div class="card">
                    <img src="img/canchas/cancha_(<?php echo $cancha['_id']; ?>).png" class="card-img-top"
                        alt="Imagen de la cancha <?php echo $cancha['_id']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $cancha['NOMBRE']; ?></h5>
                        <p class="card-text"><?php echo $cancha['DESCRIPCION']; ?></p>

                        <div class="form-group row align-items-center">
                            <div class="col-4">
                                <label for="precio_<?php echo $cancha['_id']; ?>" class="mb-0">Precio:</label>
                            </div>
                            <div class="col-8">
                                <input type="number" class="form-control" id="precio_<?php echo $cancha['_id']; ?>"
                                    value="<?php echo $cancha['PRECIO']; ?>">
                            </div>
                        </div>
                        <button class="btn btn-primary btn-modificar-precio mt-4"
                            data-cancha-id="<?php echo $cancha['_id']; ?>">Modificar</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
include 'common_scripts.php';
?>

<script>
    // Script para manejar la modificación de precios
    document.addEventListener('DOMContentLoaded', function () {
        var botonesModificar = document.querySelectorAll('.btn-modificar-precio');
        botonesModificar.forEach(function (boton) {
            boton.addEventListener('click', function () {
                var canchaId = this.getAttribute('data-cancha-id');
                var nuevoPrecio = document.getElementById('precio_' + canchaId).value;

                // Enviar la solicitud AJAX para modificar el precio
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "modificar_precio_cancha.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // Manejar la respuesta del servidor
                        if (xhr.responseText.includes("actualizado correctamente")) {
                            // Mostrar una notificación con SweetAlert2
                            Swal.fire({
                                icon: 'success',
                                title: '¡Precio actualizado!',
                                text: xhr.responseText,
                            }).then(function () {
                                // Recargar la página
                                window.location.reload();
                            });
                        } else {
                            // Mostrar una notificación de error con SweetAlert2
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseText,
                            });
                        }
                    }
                };
                xhr.send("cancha_id=" + encodeURIComponent(canchaId) + "&nuevo_precio=" + encodeURIComponent(nuevoPrecio));
            });
        });
    });
</script>

</body>

</html>