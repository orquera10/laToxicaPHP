<div class="container d-flex justify-content-center mt-4">
  <!-- Botón para abrir el modal -->
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalVenta" style="min-width:30%" onclick="abrirModalVenta()">
    Agregar Venta
  </button>
</div>
<!-- Modal para agregar un nuevo turno -->
<div class="modal fade" id="modalVenta" tabindex="-1" role="dialog" aria-labelledby="modalVentaLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Nueva Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form name="formEvento" id="formEvento" action="nevaVenta.php" class="form-horizontal" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="evento" class="form-label">Cliente</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="evento" placeholder="Cliente" disabled required>
                            <button type="button" class="btn btn-primary" onclick="abrirModalCliente()">Buscar</button>
                        </div>
                        <input type="hidden" name="cliente_id" id="cliente_id">
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
    // Función para abrir el modal de venta
    function abrirModalVenta() {
        $('#modalVenta').modal('show'); // Abre el modal de venta
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