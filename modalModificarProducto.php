<!-- Modal para editar producto -->
<div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarProducto" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="editarNombreProducto" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="editarNombreProducto" name="editarNombreProducto">
                    </div>
                    <div class="mb-3">
                        <label for="editarDescripcionProducto" class="form-label">Descripción:</label>
                        <textarea class="form-control" id="editarDescripcionProducto" name="editarDescripcionProducto" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editarPrecioProducto" class="form-label">Precio:</label>
                        <input type="number" class="form-control" id="editarPrecioProducto" name="editarPrecioProducto">
                    </div>
                    <div class="mb-3">
                        <label for="editarStockProducto" class="form-label">Stock:</label>
                        <input type="number" class="form-control" id="editarStockProducto" name="editarStockProducto">
                    </div>
                    <div class="mb-3">
                        <label for="editarImagenProducto" class="form-label">Imagen:</label>
                        <input type="file" class="form-control" id="editarImagenProducto" name="editarImagenProducto">
                    </div>
                    <input type="hidden" id="idProductoEditar" name="idProductoEditar">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardarCambiosProducto()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
    function abrirModalEditar(producto) {
        // Llenar los campos del formulario con la información del producto seleccionado
        document.getElementById('editarNombreProducto').value = producto['NOMBRE'];
        document.getElementById('editarDescripcionProducto').value = producto['DESCRIPCION'];
        document.getElementById('editarPrecioProducto').value = producto['PRECIO'];
        document.getElementById('editarStockProducto').value = producto['STOCK'];
        document.getElementById('idProductoEditar').value = producto['_id'];

        // Abrir el modal de edición
        var modal = new bootstrap.Modal(document.getElementById('modalEditarProducto'));
        modal.show();
    }
</script>

<script>
    function guardarCambiosProducto() {
        // Obtener los valores del formulario
        var nombre = document.getElementById('editarNombreProducto').value;
        var descripcion = document.getElementById('editarDescripcionProducto').value;
        var precio = document.getElementById('editarPrecioProducto').value;
        var stock = document.getElementById('editarStockProducto').value;
        var idProducto = document.getElementById('idProductoEditar').value;
        var imagenProducto = document.getElementById('editarImagenProducto').files[0]; // Obtener el archivo de imagen seleccionado

        // Crear un objeto FormData con los datos del formulario
        var formData = new FormData();
        formData.append('nombreProducto', nombre);
        formData.append('descripcionProducto', descripcion);
        formData.append('precioProducto', precio);
        formData.append('stockProducto', stock);
        formData.append('idProducto', idProducto);
        formData.append('imagenProducto', imagenProducto); // Agregar la imagen al FormData

        // Realizar una solicitud AJAX para guardar los cambios del producto
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "modificar_producto.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Obtener la respuesta del servidor
                var response = xhr.responseText;
                // Mostrar una alerta con Sweet Alert
                Swal.fire({
                    title: '¡Cambios guardados!',
                    text: 'Se modificó correctamente el producto',
                    icon: 'success'
                }).then(function() {
                    // Recargar la página después de guardar los cambios
                    window.location.reload();
                });
            }
        };
        xhr.send(formData);
    }
</script>
