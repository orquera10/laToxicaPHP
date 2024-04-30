<?php
require ("config.php");

// Realizar la consulta para obtener los productos
$sql = "SELECT * FROM producto";
$resultado = mysqli_query($con, $sql);

// Verificar si la consulta fue exitosa
if ($resultado) {
  // Crear un array para almacenar los productos
  $productos = array();

  // Recorrer los resultados y almacenar cada producto en el array
  while ($row = mysqli_fetch_assoc($resultado)) {
    $producto = array(
      'id' => $row['_id'],
      'nombre' => $row['NOMBRE'],
      'descripcion' => $row['DESCRIPCION'],
      'url_img' => $row['URL_IMG'],
      'precio' => $row['PRECIO']
    );
    $productos[] = $producto;
  }

  // Liberar el resultado
  mysqli_free_result($resultado);

  // Cerrar la conexión
  mysqli_close($con);
} else {
  // Manejar el error si la consulta falla
  echo "Error al obtener los productos: " . mysqli_error($con);
}

?>


<div class="modal modalUpdate" id="modalUpdateEvento" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header hederModalUpdate colorModalUpdate">
        <h5 class="modal-title">Agregar productos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row m-0 p-0">
          <div class="col-12 col-md-4 my-3">
            <div class="row m-0 p-0 contLabelDatos">
              <h5 class="mb-3 p-0">Datos Turno</h5>

              <div class="d-flex col-12 p-0">
                <p class="mr-2">Nombre:</p>
                <label id="evento" name="evento" class="ms-2"></label>
              </div>
              <div class="d-flex col-12 p-0">
                <p class="mr-2">Hora Entrada:</p>
                <label id="fecha_inicio" name="fecha_inicio" class="ms-2"></label>
              </div>
              <div class="d-flex col-12 p-0">
                <p class="mr-2">Hora Salida:</p>
                <label id="fecha_fin" name="fecha_fin" class="ms-2"></label>
              </div>
              <div class="d-flex col-12 p-0">
                <p class="mr-2">Cancha:</p>
                <label id="cancha" name="cancha" class="ms-2"></label>
              </div>
            </div>

            <!-- id evento oculto en el input -->
            <input type="hidden" class="form-control" name="idEvento" id="idEvento">
          </div>

          <div class="col-12 col-md-8 my-3">
            <h5 class="mb-3">Detalle</h5>
            <!-- Tabla para productos -->
            <div class="styleTabla">
              <table class="table">
                <thead class="titulosColumna">
                  <tr>
                    <th scope="col"></th>
                    <th scope="col">Producto</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Total</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody id="tablaProductosDetalle">
                  <!-- completar tabla con productos en detalle -->
                </tbody>
              </table>
            </div>

            <div class="d-flex justify-content-center">
              <button class="btn" id="agregarProductoModalUpdate" onclick="abrirModalProductos()"><svg
                  xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
                  class="bi bi-plus-circle-fill iconAdd" viewBox="0 0 16 16">
                  <path
                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z" />
                </svg></button>

              <div class="modal fade" id="productosModal" tabindex="-1" role="dialog"
                aria-labelledby="productosModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Productos</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- Parte 1: Lista de productos -->
                    <div class="modal-body">
                      <div id="contenedorTarjetas" class="row mx-4 py-3">
                        <!-- Tarjetas -->
                        <?php foreach ($productos as $producto): ?>
                          <div class="col-12 col-sm-6 mb-2 col-md-4 col-lg-3 tarjeta"
                            data-id="<?php echo $producto['id']; ?>" data-nombre="<?php echo $producto['nombre']; ?>"
                            data-precio="<?php echo $producto['precio']; ?>">
                            <div class="card">
                              <img class="card-img-top imagenProducto m-auto" src="<?php echo $producto['url_img']; ?>"
                                alt="<?php echo $producto['nombre']; ?>">
                              <div class="card-body">
                                <p class="card-title"><?php echo $producto['nombre']; ?></p>
                                <p class="card-text"><?php echo $producto['descripcion']; ?></p>
                              </div>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                    <!-- Parte 2: Detalles del producto seleccionado -->
                    <div class="row m-4">
                      <div class="col-12 col-md-6">
                        <label for="nombreProducto">Nombre del Producto:</label>
                        <input type="text" class="form-control" id="nombreProducto" oninput="filtrarProductos()"
                          placeholder="Producto...">
                        <input type="hidden" id="idProducto" name="idProducto">
                      </div>
                      <div class="col-12 col-md-6">
                        <label for="precioProducto">Precio:</label>
                        <input type="text" class="form-control" id="precioProducto" disabled>
                      </div>
                      <div class="col-12 col-md-6 mt-3">
                        <label for="cantidadProducto">Cantidad:</label>
                        <input type="number" class="form-control" id="cantidadProducto" value="1" min="1">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary" id="agregarProducto">Agregar</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row m-0 p-0">
          <div class="col-12 col-md-4">
            <p class="text-md-end">Total Cancha: <span name="total_cancha"> $</span></p>
          </div>
          <div class="col-12 col-md-8">
            <p class="text-md-end">Total Productos: <span name="total_detalle"> $</span></p>
          </div>
        </div>
        <div class="row m-0 p-0">
          <p class="text-md-end h3 styleTotal">Total: <span name="total"> $</span></p>
        </div>
        
      </div>
      <div class="modal-footer colorModalUpdate footerModalUpdate">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" id="finalizarTurno" onclick="abrirModalPago()">Finalizar
            Turno</button>
        </div>
    </div>
  </div>
</div>

<?php
include ('modalFinalizarTurno.php');
?>

<!-- abre el modal que muestra los clientes cargados --------------------------------------->
<script>
  function abrirModalProductos() {
    $('#productosModal').modal('show');
  }
</script>

<script>
  function abrirModalPago() {

    $('#modalUpdateEvento').modal('hide');
    $('#modalPago').modal('show');
  }
</script>

<script>
  
  // Función para filtrar productos
  function filtrarProductos() {
    var input, filter, cards, card, title, i, txtValue;
    input = document.getElementById("nombreProducto");
    filter = input.value.toUpperCase();
    cards = document.getElementsByClassName("tarjeta");

    for (i = 0; i < cards.length; i++) {
      card = cards[i];
      title = card.getElementsByTagName("p")[0];
      txtValue = title.textContent || title.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        card.style.display = "block"; // Mostrar tarjeta
      } else {
        card.style.display = "none"; // Ocultar tarjeta
      }
    }
  }
</script>