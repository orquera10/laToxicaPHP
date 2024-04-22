<!-- Modal para agregar un nuevo turno -------------------------------------------------------------------------------->
<div class="modal" id="exampleModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Registrar Nuevo Evento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>

      <!-- llamada a la funcion para agregar evento -->
      <form name="formEvento" id="formEvento" action="nuevoEvento.php" class="form-horizontal" method="POST">
        <div class="form-group mt-3">
          <label for="evento" class="col-sm-12 control-label">Cliente</label>
          <div class="col-sm-12">
            <div class="d-flex">
              <!-- Input para el cliente -->
              <input type="text" class="form-control" name="evento" id="evento" placeholder="Cliente" disabled
                required />
              <!-- Botón para abrir el modal -->
              <button type="button" class="btn btn-primary ml-2" onclick="abrirModalCliente()">Buscar</button>
            </div>
            <!-- campo oculto para almacenar id de usuario seleccionado -->
            <input type="hidden" name="cliente_id" id="cliente_id" value="">
          </div>
        </div>

        <!-- Modal de clientes ----------------------------------------------------------------------------------------------->
        <div class="modal fade" id="clientesModal" tabindex="-1" aria-labelledby="clientesModalLabel"
          aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title fs-5" id="clientesModalLabel">Seleccionar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <div class="modal-body">
                <!-- Aquí puedes mostrar una lista de clientes -->
                <?php
                // Consulta SQL para seleccionar los nombres de los clientes
                $sql_clientes = "SELECT _id, NOMBRE FROM clientes";
                // Ejecutar la consulta
                $clientes = mysqli_query($con, $sql_clientes);
                if (mysqli_num_rows($clientes) > 0) {
                  echo '<ul>';
                  while ($fila_cliente = mysqli_fetch_assoc($clientes)) {
                    echo '<li><a href="#" class="seleccionar-cliente" data-id="' . $fila_cliente['_id'] . '">' . $fila_cliente['NOMBRE'] . '</a></li>';
                  }
                  echo '</ul>';
                } else {
                  echo "No se encontraron clientes en la base de datos.";
                }
                ?>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
        <!------------------------------------------------------------------------------------------------------------------->

        <!-- desplegables con las horas disponibles para turnos ------------------------------------------------------------->
        <div class="row m-0 p-0">
          <div class="form-group col-6">
            <label for="fecha_inicio" class="col-sm-12 control-label m-0 p-0  mb-2">Hora Entrada</label>
            <div class="col-sm-12 m-0 p-0">
              <select class="form-control" name="select_hora_inicio" id="select_hora_inicio">
                <?php for ($i = 15; $i <= 23; $i++) {
                  echo "<option value='$i:00'>$i:00</option>";
                } ?>
              </select>
            </div>
          </div>
          <div class="form-group col-6">
            <label for="fecha_fin" class="col-sm-12 control-label m-0 p-0 mb-2">Hora Salida</label>
            <div class="col-sm-12 m-0 p-0">
              <select class="form-control" name="select_hora_fin" id="select_hora_fin">
                <?php for ($i = 16; $i <= 24; $i++) {
                  echo "<option value='$i:00'>$i:00</option>";
                } ?>
              </select>
            </div>
          </div>
        </div>

        <!-- campos escondidos que contienen la fecha       -->
        <input type="hidden" class="form-control" name="hidden_hora_inicio" id="hidden_hora_inicio"
          placeholder="Fecha Inicio">
        <input type="hidden" class="form-control" name="hidden_hora_fin" id="hidden_hora_fin" placeholder="Fecha Final">
        <!------------------------------------------------------------------------------------------------------------------>

        <!-- Desplgable para obtener canchas ------------------------------------------------------------------------------->
        <div class="form-group">
          <label for="fecha_fin" class="col-sm-12 control-label">Cancha:</label>
          <div class="col-sm-12">
            <select name="canchas" id="canchas" class="col-md-12 form-control mb-3">
            </select>
          </div>
        </div>

        <!------------------------------------------------------------------------------------------------------------------>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar Evento</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Salir</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- abre el modal que muestra los clientes cargados --------------------------------------->
<script>
  function abrirModalCliente() {
    $('#clientesModal').modal('show');
  }
</script>