<?php
// Incluir archivos necesarios
$pageTitle = "Productos";
include 'header.php';
include 'headerUsuario.php';
include 'barraNavegacion.php';
include 'config.php'; // Suponiendo que aquí se encuentra la configuración de la conexión a la base de datos

// Variable para almacenar la consulta SQL
$sql = "SELECT * FROM producto";

// Variable para almacenar el resultado de la consulta SQL
$result = mysqli_query($con, $sql);

// Variable para almacenar los resultados de la búsqueda
$productos = [];

// Verificar si se obtuvieron resultados
if (mysqli_num_rows($result) > 0) {
    // Almacenar los productos en un arreglo
    while ($row = mysqli_fetch_assoc($result)) {
        $productos[] = $row;
    }
}
?>

<div class="container">
    <div class="row m-0 p-0">
        <div class="col-12 col-md-3 mt-4">
            <input type="text" class="form-control" id="buscarProducto" placeholder="Buscar por nombre">
        </div>
    </div>
    <!-- Campo de búsqueda -->
    <div class="rounded tablaTurnosAll tablaProductos my-4 shadow py-2 px-4">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Mostrar los productos en la tabla
                foreach ($productos as $producto) {
                    echo "<tr class='align-middle'>";
                    echo "<td><img src='" . $producto['URL_IMG'] . "' alt='" . $producto['NOMBRE'] . "' style='max-width: 100px; max-height: 100px;'></td>";
                    echo "<td>" . $producto['_id'] . "</td>";
                    echo "<td>" . $producto['NOMBRE'] . "</td>";
                    echo "<td>" . $producto['DESCRIPCION'] . "</td>";
                    echo "<td>" . $producto['PRECIO'] . " $</td>";
                    echo "<td>" . $producto['STOCK'] . "</td>";
                    echo "<td>";
                    echo "<a href='editar_producto.php?id=" . $producto['_id'] . "'><i class='fas fa-edit iconEditProducto'></i></a>";
                    echo "<a href='eliminar_producto.php?id=" . $producto['_id'] . "'><i class='fas fa-trash-alt iconTrashProducto'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        <button class="btn" id="agregarProducto" onclick="abrirModalAgregarProductos()">
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
    // Función para filtrar productos por nombre
    document.getElementById("buscarProducto").addEventListener("keyup", function () {
        var filtro = this.value.toLowerCase();
        var filas = document.querySelectorAll(".tablaProductos tbody tr");
        filas.forEach(function (fila) {
            var nombre = fila.getElementsByTagName("td")[2].textContent.toLowerCase();
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