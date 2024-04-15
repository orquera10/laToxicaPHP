<div class="modal modalUpdate" id="modalUpdateEvento" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Agregar productos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
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
            <tbody id="tablaProductos">
              <!-- completar tabla con productos en detalle -->
            </tbody>
          </table>

          <div class="d-flex justify-content-center">
            <button class="btn btn-primary">Agregar</button>
          </div>
        </div>
      </div>

      <div class="row m-0 p-0">
        <div class="col-4">
          <p>Total Cancha:</p>
        </div>
        <div class="col-8">
          <p>Total Productos:</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- abre el modal que muestra los clientes cargados --------------------------------------->
<script>
  function abrirModalProductos() {
    $('#productosModal').modal('show');
  }
</script>