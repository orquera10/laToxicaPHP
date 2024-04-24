<!-- Modal para agregar un nuevo turno -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Registrar Nuevo Evento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="formEvento" id="formEvento" action="nuevoEvento.php" class="form-horizontal" method="POST">
        <div class="modal-body">
          <div class="mb-3">
            <label for="evento" class="form-label">Cliente</label>
            <div class="input-group">
              <input type="text" class="form-control" id="evento" placeholder="Cliente" disabled required>
              <button type="button" class="btn btn-primary" onclick="abrirModalCliente()">Buscar</button>
            </div>
            <input type="hidden" name="cliente_id" id="cliente_id">
          </div>
          <!-- Aquí el contenido del modal -->
          <div class="row mb-3">
            <div class="col">
              <label for="select_hora_inicio" class="form-label">Hora Entrada</label>
              <select class="form-select" name="select_hora_inicio" id="select_hora_inicio">
                <?php for ($i = 15; $i <= 23; $i++) {
                  echo "<option value='$i:00'>$i:00</option>";
                } ?>
              </select>
            </div>
            <div class="col">
              <label for="select_hora_fin" class="form-label">Hora Salida</label>
              <select class="form-select" name="select_hora_fin" id="select_hora_fin">
                <?php for ($i = 16; $i <= 24; $i++) {
                  echo "<option value='$i:00'>$i:00</option>";
                } ?>
              </select>
            </div>
          </div>
          <!-- campos escondidos que contienen la fecha       -->
          <input type="hidden" class="form-control" name="hidden_hora_inicio" id="hidden_hora_inicio"
            placeholder="Fecha Inicio">
          
          <div class="mb-3">
            <label for="canchas" class="form-label">Cancha</label>
            <select name="canchas" id="canchas" class="form-select">
              <!-- Opciones de canchas cargadas dinámicamente -->
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar Evento</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Salir</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de clientes -->
<div class="modal fade" id="clientesModal" tabindex="-1" aria-labelledby="clientesModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="clientesModalLabel">Seleccionar Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col">
            <!-- Campo de búsqueda -->
            <div class="input-group mb-3">
              <input type="text" class="form-control" id="buscarCliente" placeholder="Buscar cliente...">
              <button class="btn btn-primary" type="button" id="btnBuscarCliente">Buscar</button>
            </div>
          </div>
        </div>
        <!-- Contenido dinámico de la lista de clientes -->
        <?php
        // Consulta SQL para seleccionar los nombres de los clientes
        $sql_clientes = "SELECT _id, NOMBRE FROM clientes";
        // Ejecutar la consulta
        $clientes = mysqli_query($con, $sql_clientes);
        if (mysqli_num_rows($clientes) > 0) {
          echo '<ul class="list-group listaNombres">';
          while ($fila_cliente = mysqli_fetch_assoc($clientes)) {
            echo '<li class="list-group-item nombreCliente"><a href="#" class="seleccionar-cliente" data-id="' . $fila_cliente['_id'] . '">' . $fila_cliente['NOMBRE'] . '</a></li>';
          }
          echo '</ul>';
        } else {
          echo "<p class='text-center'>No se encontraron clientes en la base de datos.</p>";
        }
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<!-- Script para abrir el modal de clientes -->
<script>
  function abrirModalCliente() {
    $('#clientesModal').modal('show');
  }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
      // Obtener el campo de búsqueda y la lista de nombres de clientes
      const campoBuscar = document.getElementById('buscarCliente');
      const listaNombres = document.querySelectorAll('.nombreCliente');

      // Agregar un evento de escucha al campo de búsqueda
      campoBuscar.addEventListener('input', function () {
        const textoBuscar = campoBuscar.value.trim().toLowerCase();

        // Iterar sobre la lista de nombres y ocultar aquellos que no coincidan con la búsqueda
        listaNombres.forEach(function (nombre) {
          const nombreCliente = nombre.textContent.trim().toLowerCase();
          if (nombreCliente.includes(textoBuscar)) {
            nombre.style.display = 'block';
          } else {
            nombre.style.display = 'none';
          }
        });
      });
    });
  </script>