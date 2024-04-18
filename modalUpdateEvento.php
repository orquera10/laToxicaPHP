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
      <div class="modal-header">
        <h5 class="modal-title">Agregar productos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row m-0 p-0">
          <div class="col-4 my-3">
            <div class="row m-0 p-0">
              <h5 class="mb-3">Datos Turno</h5>

              <div class="d-flex col-12">
                <p class="mr-2">Nombre:</p>
                <label id="evento" name="evento"></label>
              </div>
              <div class="d-flex col-12">
                <p class="mr-2">Hora Entrada:</p>
                <label id="fecha_inicio" name="fecha_inicio"></label>
              </div>
              <div class="d-flex col-12">
                <p class="mr-2">Hora Salida:</p>
                <label id="fecha_fin" name="fecha_fin"></label>
              </div>
              <div class="d-flex col-12">
                <p class="mr-2">Cancha:</p>
                <label id="cancha" name="cancha"></label>
              </div>
            </div>

            <!-- id evento oculto en el input -->
            <input type="hidden" class="form-control" name="idEvento" id="idEvento">
          </div>
          <div class="col-8 my-3">
            <h5 class="mb-3">Detalle</h5>
            <!-- Tabla para productos -->
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">Producto</th>
                  <th scope="col">Cantidad</th>
                  <th scope="col">Precio Unitario</th>
                  <th scope="col">Total</th>
                  <th scope="col"></th>
                </tr>
              </thead>
              <tbody id="tablaProductosDetalle">
                <!-- completar tabla con productos en detalle -->
              </tbody>
            </table>

            <div class="d-flex justify-content-center">
              <button class="btn btn-primary" onclick="abrirModalProductos()">Agregar</button>

              <!-- Modal de carga de productos-------------------------------------------------------------->
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
                      <h5>Productos</h5>

                      <div id="contenedorTarjetas" class="row">
                        <!-- Tarjetas -->
                        <?php foreach ($productos as $producto): ?>
                          <div class="col-md-4 mb-4">
                            <div class="card tarjeta" data-id="<?php echo $producto['id']; ?>"
                              data-nombre="<?php echo $producto['nombre']; ?>"
                              data-precio="<?php echo $producto['precio']; ?>">
                              <img class="card-img-top imagenProducto" src="<?php echo $producto['url_img']; ?>"
                                alt="<?php echo $producto['nombre']; ?>">
                              <div class="card-body">
                                <h5 class="card-title"><?php echo $producto['nombre']; ?></h5>
                                <p class="card-text"><?php echo $producto['descripcion']; ?></p>
                                <p class="card-text">$<?php echo $producto['precio']; ?></p>
                              </div>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>

                    </div>
                    <!-- Parte 2: Detalles del producto seleccionado -->
                    <div class="modal-footer">
                      <div class="col-md-6">
                        <label for="nombreProducto">Nombre del Producto:</label>
                        <input type="text" class="form-control" id="nombreProducto" readonly>
                        <input type="hidden" id="idProducto" name="idProducto">
                      </div>
                      <div class="col-md-6">
                        <label for="precioProducto">Precio:</label>
                        <input type="text" class="form-control" id="precioProducto" readonly>
                      </div>
                      <div class="col-md-12 mt-3">
                        <label for="cantidadProducto">Cantidad:</label>
                        <input type="number" class="form-control" id="cantidadProducto" value="1" min="1">
                      </div>
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
          <div class="col-4">
            <p>Total Cancha: <span name="total_cancha"></span></p>
          </div>
          <div class="col-8">
            <p>Total Productos: <span name="total_detalle"></span></p>
          </div>
        </div>
        <div class="row m-0 p-0" >
            <p>Total Productos: <span name="total"></span></p>                
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="abrirModalPago()">Finalizar Turno</button>
        </div>
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