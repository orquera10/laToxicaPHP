<!-- Modal para agregar productos -->
<div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-labelledby="modalAgregarProductoLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarProductoLabel">Agregar Nuevo Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulario para agregar un nuevo producto -->
                <form id="formAgregarProducto" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="nombreProducto" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="nombreProducto" name="nombreProducto">
                    </div>
                    <div class="mb-3">
                        <label for="precioProducto" class="form-label">Precio:</label>
                        <input type="number" class="form-control" id="precioProducto" name="precioProducto">
                    </div>
                    <!-- <div class="mb-3">
                        <label for="stockProducto" class="form-label">Stock:</label>
                        <input type="number" class="form-control" id="stockProducto" name="stockProducto">
                    </div> -->
                    <div class="mb-3">
                        <label for="imagenProducto" class="form-label">Imagen:</label>
                        <input type="file" class="form-control" id="imagenProducto" name="imagenProducto">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarProducto()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Función para abrir el modal de agregar productos
    function abrirModalAgregarProductos() {
        var modalAgregarProducto = new bootstrap.Modal(document.getElementById('modalAgregarProducto'));
        modalAgregarProducto.show();
    }

    // Función para guardar un nuevo producto
    function guardarProducto() {
        // Obtener los datos del formulario
        var nombre = document.getElementById('nombreProducto').value;
        
        var precio = document.getElementById('precioProducto').value;
        // var stock = document.getElementById('stockProducto').value;
        var imagen = document.getElementById('imagenProducto').files[0]; // Obtener la imagen seleccionada

        // Crear un objeto FormData para enviar los datos del formulario
        var formData = new FormData();
        formData.append('nombreProducto', nombre);
        
        formData.append('precioProducto', precio);
        // formData.append('stockProducto', stock);
        formData.append('imagenProducto', imagen); // Agregar la imagen al FormData

        // Realizar una solicitud AJAX para guardar el producto
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "guardar_producto.php", true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Obtener la respuesta del servidor
                var response = JSON.parse(xhr.responseText);
                // Mostrar una alerta con Sweet Alert
                if (response.success) {
                    Swal.fire({
                        title: '¡Producto guardado!',
                        text: response.message,
                        icon: 'success'
                    }).then(function () {
                        // Recargar la página después de guardar el producto
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error'
                    });
                }
            }
        };
        xhr.send(formData);
    }
</script>
