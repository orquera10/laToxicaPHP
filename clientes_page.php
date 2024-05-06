<?php
// Incluir archivos necesarios
$pageTitle = "Clientes";
include 'header.php';
include 'headerUsuario.php';
include 'barraNavegacion.php';
include 'config.php'; // Suponiendo que aquí se encuentra la configuración de la conexión a la base de datos

// Variable para almacenar la consulta SQL
$sql = "SELECT * FROM clientes";

// Variable para almacenar el resultado de la consulta SQL
$result = mysqli_query($con, $sql);

// Variable para almacenar los resultados de la búsqueda
$clientes = [];

// Verificar si se obtuvieron resultados
if (mysqli_num_rows($result) > 0) {
    // Almacenar los clientes en un arreglo
    while ($row = mysqli_fetch_assoc($result)) {
        $clientes[] = $row;
    }
}
?>

<div class="container">
    <div class="row m-0 p-0">
        <div class="col-12 col-md-3 mt-4">
            <input type="text" class="form-control" id="buscarCliente" placeholder="Buscar por nombre">
        </div>
    </div>
    <!-- Campo de búsqueda -->
    <div class="rounded tablaTurnosAll tablaProductos tablaClientes my-4 shadow py-2 px-4">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Mostrar los clientes en la tabla
                foreach ($clientes as $cliente) {
                    echo "<tr class='align-middle'>";
                    echo "<td>" . $cliente['_id'] . "</td>";
                    echo "<td>" . $cliente['NOMBRE'] . "</td>";
                    echo "<td>" . $cliente['MAIL'] . "</td>";
                    echo "<td>" . $cliente['TELEFONO'] . "</td>";
                    echo "<td>";
                    echo "<a href='editar_cliente.php?id=" . $cliente['_id'] . "'><i class='fas fa-edit iconEditProducto'></i></a>";
                    echo "<a href='eliminar_cliente.php?id=" . $cliente['_id'] . "'><i class='fas fa-trash-alt iconTrashProducto'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        <button class="btn" id="agregarCliente" onclick="abrirModalAgregarCliente()">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor"
                class="bi bi-plus-circle-fill iconAdd" viewBox="0 0 16 16">
                <path
                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z" />
            </svg>
        </button>
    </div>
</div>

<?php
include 'common_scripts.php';
?>

<script>
    // Función para filtrar clientes por nombre
    document.getElementById("buscarCliente").addEventListener("keyup", function () {
        var filtro = this.value.toLowerCase();
        var filas = document.querySelectorAll(".tablaClientes tbody tr");
        filas.forEach(function (fila) {
            var nombre = fila.getElementsByTagName("td")[1].textContent.toLowerCase();
            if (nombre.includes(filtro)) {
                fila.style.display = "table-row";
            } else {
                fila.style.display = "none";
            }
        });
    });
</script>

</body>

</html>
