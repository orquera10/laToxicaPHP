
<!-- Modal -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPagoLabel">Detalles del Turno y Productos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Parte 1: Datos del Turno -->
        <div class="mb-3">
          <h5>Datos del Turno:</h5>
          <!-- Aquí puedes mostrar los datos del turno -->
          <p>Cliente: Nombre del Cliente</p>
          <p>Hora Entrada: 08:00 AM</p>
          <p>Hora Salida: 10:00 AM</p>
          <p>Cancha: Nombre de la Cancha</p>
        </div>

        <!-- Parte 2: Lista de Productos -->
        <div class="mb-3">
          <h5>Productos:</h5>
          <table class="table">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Producto 1</td>
                <td>$10.00</td>
                <td>2</td>
                <td>$20.00</td>
              </tr>
              <!-- Agrega más filas según los productos -->
            </tbody>
          </table>
        </div>

        <!-- Parte 3: Totales -->
        <div class="mb-3">
          <h5>Totales:</h5>
          <p>Total Cancha: $50.00</p>
          <p>Total Productos: $30.00</p>
          <p>Total General: $80.00</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <!-- Botón para realizar alguna acción, como guardar los datos -->
        <button type="button" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
