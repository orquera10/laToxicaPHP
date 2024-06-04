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
                    <!-- Campos del formulario -->
                    <div class="mb-3">
                        <label for="editarNombreProducto" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="editarNombreProducto" name="editarNombreProducto">
                    </div>
                    <div class="mb-3">
                        <label for="editarPrecioProducto" class="form-label">Precio:</label>
                        <input type="number" class="form-control" id="editarPrecioProducto" name="editarPrecioProducto">
                    </div>
                    <div class="mb-3 row">
                        <div class="col-4">
                            <label for="editarStockProducto" class="form-label">Stock Actual:</label>
                            <input type="number" class="form-control" id="editarStockProducto"
                                name="editarStockProducto" disabled>
                        </div>
                        <div class="col-4">
                            <label for="agregarAlStock" class="form-label">Agregar al Stock:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="agregarAlStock" name="agregarAlStock">
                                <button class="btn btn-outline-secondary" type="button"
                                    id="btnAgregarStock">Agregar</button>
                            </div>
                        </div>
                        <div class="col-4">
                            <label for="quitarAlStock" class="form-label">Quitar al Stock:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="quitarAlStock" name="quitarAlStock">
                                <button class="btn btn-outline-secondary" type="button"
                                    id="btnQuitarAlStock">Quitar</button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <!-- Input oculto para almacenar el valor de agregarAlStock -->
                        <input type="hidden" id="agregarAlStockHidden" name="agregarAlStockHidden">
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
                <!-- Botón de guardar cambios -->
                <button type="button" class="btn btn-primary" id="btnGuardarCambios">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Función para abrir el modal de edición
    function abrirModalEditar(producto) {
        // Llenar los campos del formulario con la información del producto seleccionado
        document.getElementById('editarNombreProducto').value = producto['NOMBRE'];
        document.getElementById('editarPrecioProducto').value = producto['PRECIO'];
        document.getElementById('editarStockProducto').value = producto['STOCK'];
        document.getElementById('idProductoEditar').value = producto['_id'];
        document.getElementById('quitarAlStock').value = producto[''];
        document.getElementById('agregarAlStock').value = producto[''];

        // Abrir el modal de edición
        var modal = new bootstrap.Modal(document.getElementById('modalEditarProducto'));
        modal.show();
    }

    // Función para guardar los cambios del producto y agregar stock
    document.getElementById('btnGuardarCambios').addEventListener('click', function () {
        // Obtener los valores del formulario
        var nombre = document.getElementById('editarNombreProducto').value;
        var precio = document.getElementById('editarPrecioProducto').value;
        var stock = document.getElementById('editarStockProducto').value;
        var idProducto = document.getElementById('idProductoEditar').value;
        var imagenProducto = document.getElementById('editarImagenProducto').files[0]; // Obtener el archivo de imagen seleccionado
        var agregarAlStock = parseFloat(document.getElementById('agregarAlStockHidden').value);

        // Crear un objeto FormData con los datos del formulario
        var formData = new FormData();
        formData.append('nombreProducto', nombre);
        formData.append('precioProducto', precio);
        formData.append('stockProducto', stock);
        formData.append('idProducto', idProducto);
        formData.append('imagenProducto', imagenProducto); // Agregar la imagen al FormData

        // Realizar una solicitud AJAX para guardar los cambios del producto
        $.ajax({
            url: 'modificar_producto.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                var agregarAlStock = parseFloat(document.getElementById('agregarAlStockHidden').value);

                // Verificar si el valor ingresado es válido
                if (!isNaN(agregarAlStock) && agregarAlStock !== 0) {
                    // Realizar una solicitud AJAX para agregar el stock
                    $.ajax({
                        url: 'agregar_stock.php',
                        type: 'POST',
                        data: {
                            id_producto: idProducto,
                            cantidad: agregarAlStock
                        },
                        success: function (response) {
                            if (agregarAlStock > 0) {
                                // Mostrar una alerta con Sweet Alert si la solicitud fue exitosa
                                Swal.fire({
                                    title: '¡Cambios guardados!',
                                    text: 'Se modificó correctamente el producto y se agregó stock.',
                                    icon: 'success'
                                }).then(function () {
                                    // Actualizar el valor del input oculto
                                    document.getElementById('agregarAlStockHidden').value = 0;
                                    // Recargar la página después de guardar los cambios
                                    window.location.reload();
                                });
                            } else {
                                // Mostrar una alerta con Sweet Alert si la solicitud fue exitosa
                                Swal.fire({
                                    title: '¡Cambios guardados!',
                                    text: 'Se modificó correctamente el producto y se quitó stock.',
                                    icon: 'success'
                                }).then(function () {
                                    // Actualizar el valor del input oculto
                                    document.getElementById('agregarAlStockHidden').value = 0;
                                    // Recargar la página después de guardar los cambios
                                    window.location.reload();
                                });
                            }

                        },
                        error: function () {
                            // Mostrar una alerta con Sweet Alert si hay un error en la solicitud
                            Swal.fire({
                                title: 'Error',
                                text: 'Ha ocurrido un error al agregar el stock',
                                icon: 'error'
                            });
                        }
                    });
                } else {
                    // Mostrar una alerta con Sweet Alert si no hay stock para agregar
                    Swal.fire({
                        title: '¡Cambios guardados!',
                        text: 'Se modificó correctamente el producto.',
                        icon: 'success'
                    }).then(function () {
                        // Recargar la página después de guardar los cambios
                        window.location.reload();
                    });
                }
            },
            error: function () {
                // Mostrar una alerta con Sweet Alert si hay un error en la solicitud de modificar producto
                Swal.fire({
                    title: 'Error',
                    text: 'Ha ocurrido un error al guardar los cambios del producto',
                    icon: 'error'
                });
            }
        });

    });




    // Función para agregar stock al producto
    document.getElementById('btnAgregarStock').addEventListener('click', function () {
        // Obtener el valor ingresado en el campo de agregar al stock
        var agregarAlStock = parseFloat(document.getElementById('agregarAlStock').value);

        // Obtener el valor actual del stock
        var stockActual = parseFloat(document.getElementById('editarStockProducto').value);

        // Verificar si el valor ingresado es válido
        if (!isNaN(agregarAlStock)) {
            // Sumar el valor ingresado al stock actual
            var nuevoStock = stockActual + agregarAlStock;

            // Actualizar el valor del campo de stock
            document.getElementById('editarStockProducto').value = nuevoStock;
            // Actualizar el valor del input oculto
            document.getElementById('agregarAlStockHidden').value = agregarAlStock;

            // Limpiar el campo de agregar al stock
            document.getElementById('agregarAlStock').value = '';
        } else {
            // Mostrar un mensaje de error si el valor ingresado no es válido
            alert('Por favor, ingrese un número válido.');
        }
    });


    // Función para quitar stock al producto
    document.getElementById('btnQuitarAlStock').addEventListener('click', function () {
        // Obtener el valor ingresado en el campo de agregar al stock
        var quitarAlStock = parseFloat(document.getElementById('quitarAlStock').value);
        quitarAlStock = -quitarAlStock

        // Obtener el valor actual del stock
        var stockActual = parseFloat(document.getElementById('editarStockProducto').value);

        // Verificar si el valor ingresado es válido
        if (!isNaN(quitarAlStock)) {
            // Sumar el valor ingresado al stock actual
            var nuevoStock = stockActual + quitarAlStock;

            // Actualizar el valor del campo de stock
            document.getElementById('editarStockProducto').value = nuevoStock;
            // Actualizar el valor del input oculto
            document.getElementById('agregarAlStockHidden').value = quitarAlStock;

            // Limpiar el campo de agregar al stock
            document.getElementById('quitarAlStock').value = '';
        } else {
            // Mostrar un mensaje de error si el valor ingresado no es válido
            alert('Por favor, ingrese un número válido.');
        }
    });

</script>