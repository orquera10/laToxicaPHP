<div class="modal modalUpdate" id="modalUpdateEvento" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Agregar productos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
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
        </div>
      </div>

      <div class="row m-0 p-0">
        <div class="col-4">
          <p>Total Cancha:</p>
        </div>
        <div class="col-8">
          <p>Total Productos:</p>
        </div>
        <!-- llamada a funciona para actualizar evento -->
        <!-- <form name="formEventoUpdate" id="formEventoUpdate" action="UpdateEvento.php" class="form-horizontal"
        method="POST">
        <input type="hidden" class="form-control" name="idEvento" id="idEvento">
        <div class="form-group">
          <label for="evento" class="col-sm-12 control-label">Nombre del Evento</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="evento" id="evento" placeholder="Nombre del Evento"
              required />
          </div>
        </div>
        <div class="form-group">
          <label for="fecha_inicio" class="col-sm-12 control-label">Fecha Inicio</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="fecha_inicio" id="fecha_inicio" placeholder="Fecha Inicio">
          </div>
        </div>
        <div class="form-group">
          <label for="fecha_fin" class="col-sm-12 control-label">Fecha Final</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="fecha_fin" id="fecha_fin" placeholder="Fecha Final">
          </div>
        </div>

        <div class="col-md-12 activado">

          <input type="radio" name="color_evento" id="orangeUpd" value="#FF5722" checked>
          <label for="orangeUpd" class="circu" style="background-color: #FF5722;"> </label>

          <input type="radio" name="color_evento" id="amberUpd" value="#FFC107">
          <label for="amberUpd" class="circu" style="background-color: #FFC107;"> </label>

          <input type="radio" name="color_evento" id="limeUpd" value="#8BC34A">
          <label for="limeUpd" class="circu" style="background-color: #8BC34A;"> </label>

          <input type="radio" name="color_evento" id="tealUpd" value="#009688">
          <label for="tealUpd" class="circu" style="background-color: #009688;"> </label>

          <input type="radio" name="color_evento" id="blueUpd" value="#2196F3">
          <label for="blueUpd" class="circu" style="background-color: #2196F3;"> </label>

          <input type="radio" name="color_evento" id="indigoUpd" value="#9c27b0">
          <label for="indigoUpd" class="circu" style="background-color: #9c27b0;"> </label>

        </div>


        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar Cambios de mi Evento</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
        </div>
      </form> -->

      </div>

    </div>
  </div>
</div>