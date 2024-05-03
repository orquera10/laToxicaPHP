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
          <div>
            <label for="repetirEvento" class="form-label">Repetir</label>
          </div>
          <div class="d-grid gap-2">
            <div class="btn-group" role="group" aria-label="Basic radio toggle button group" id="repetirEvento">
              <input type="radio" class="btn-check" name="repetir" id="unMes" value="unMes" autocomplete="off">
              <label class="btn btn-primary" for="unMes">Un mes</label>

              <input type="radio" class="btn-check" name="repetir" id="tresMeses" value="tresMeses" autocomplete="off">
              <label class="btn btn-primary" for="tresMeses">Tres meses</label>

              <input type="radio" class="btn-check" name="repetir" id="seisMeses" value="seisMeses" autocomplete="off">
              <label class="btn btn-primary" for="seisMeses">Seis meses</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar Evento</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
include ('modalCliente.php');
include ('modalNuevoCliente.php');
?>

<!-- Script para abrir el modal de clientes -->
<script>
  function abrirModalCliente() {
    $('#clientesModal').modal('show');
  }
  function abrirModalNuevoCliente() {
    $('#modalNuevoCliente').modal('show');
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