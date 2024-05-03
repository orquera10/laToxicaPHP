<?php
$pageTitle = "Turnos";
include 'header.php';
?>

<?php
include ('config.php');

$SqlEventos = "SELECT 
                  turnos.*, 
                  clientes.NOMBRE as nombre_usuario, 
                  canchas.NOMBRE as nombre_cancha,
                  ticket.id_CLIENTE,
                  ticket.TOTAL_CANCHA,
                  ticket.TOTAL_DETALLE,
                  ticket.TOTAL
                FROM turnos 
                INNER JOIN canchas ON turnos.id_CANCHA = canchas._id
                LEFT JOIN ticket ON turnos._id = ticket.id_TURNO
                LEFT JOIN clientes ON ticket.id_CLIENTE = clientes._id";

$resulEventos = mysqli_query($con, $SqlEventos);
?>



<div class="container">
  <div class="row">
    <div class="col msjs position-absolute top-0 start-50 translate-middle-x" style="z-index: 1000;">
      <?php
      include ('msjs.php');
      ?>
    </div>
  </div>
</div>

<?php
include ('headerUsuario.php');
include ('barraNavegacion.php');
?>

<?php
include ('modalNuevaVenta.php');
?>
<div class="d-flex justify-content-center">
  <div id="calendar" class="m-4 calendario"></div>
</div>



<?php
include ('modalNuevoEvento.php');
include ('modalUpdateEvento.php');
?>

<?php
include 'common_scripts.php';
?>

<script type="text/javascript">
  $(document).ready(function () {
    $("#calendar").fullCalendar({
      header: {
        left: "prev,next today",
        center: "title",
        right: "month,agendaWeek,agendaDay"
      },

      minTime: '09:00:00', // Configuración para empezar a mostrar horarios desde el mediodía
      maxTime: '24:00:00', // Configuración para terminar de mostrar horarios a medianoche

      locale: 'es',

      selectable: true, // Habilitar la selección en dispositivos táctiles

      defaultView: (window.innerWidth < 768) ? 'agendaDay' : 'month',
      navLinks: true,
      // editable: true,
      eventLimit: true,
      selectable: true,
      selectHelper: false,

      //Nuevo Evento
      select: function (start, end) {

        // Construir fechas completas combinando las fechas seleccionadas del calendario con las horas seleccionadas
        var fechaInicio = start.format('DD-MM-YYYY');
        // var fechaFin = end.format('DD-MM-YYYY');
        // var fechaFinal = moment(fechaFin, "DD-MM-YYYY").subtract(1, 'days').format('DD-MM-YYYY');

        // Asignar valores a los campos de fecha y hora
        $("input[name=hidden_hora_inicio]").val(fechaInicio);
        // $("input[name=hidden_hora_fin]").val(fechaFinal);

        $.ajax({
          url: 'cargarDesplegableCancha.php',
          type: 'POST',
          data: { fecha: fechaInicio },
          success: function (response) {
            $('#canchas').html(response);
          },
          error: function (xhr, status, error) {
            console.error(xhr.responseText);
          }
        });

        $("#exampleModal").modal("show");
        // myModal.addEventListener('shown.bs.modal', () => {
        //   myInput.focus()
        // })
      },

      events: [
        <?php
        while ($dataEvento = mysqli_fetch_array($resulEventos)) {
          // Concatenar la fecha y la hora de inicio y fin
          $start = date('Y-m-d H:i:s', strtotime($dataEvento['FECHA'] . ' ' . $dataEvento['HORA_INICIO']));
          $end = date('Y-m-d H:i:s', strtotime($dataEvento['FECHA'] . ' ' . $dataEvento['HORA_FIN']));

          ?>
                                {
            _id: '<?php echo $dataEvento['_id']; ?>',
            title: '<?php echo $dataEvento['nombre_usuario']; ?>',
            start: '<?php echo $start; ?>',
            end: '<?php echo $end; ?>',
            color: '<?php echo $dataEvento['COLOR']; ?>',
            cancha: '<?php echo $dataEvento['nombre_cancha']; ?>',
            finalizado: '<?php echo $dataEvento['FINALIZADO']; ?>',
            total_cancha: '<?php echo $dataEvento['TOTAL_CANCHA']; ?>',
            total_detalle: '<?php echo $dataEvento['TOTAL_DETALLE']; ?>',
            total: '<?php echo $dataEvento['TOTAL']; ?>'
          },
        <?php } ?>
      ],



      //Eliminar Evento
      eventRender: function (event, element) {
        // Convertir la propiedad 'finalizado' a un número entero
        var finalizado = parseInt(event.finalizado);
        // Verificar si el evento no está finalizado
        if (finalizado !== 1) {
          element
            .find(".fc-content")
            .prepend("<span id='btnCerrar' class='closeon material-icons'>&#xe5cd;</span>");

          //Eliminar evento
          element.find(".closeon").on("click", function () {
            // Desactivar eventos de clic en otros elementos
            $('body').addClass('modal-open');

            Swal.fire({
              title: '¿Deseas borrar este evento?',
              text: "Esta acción no se puede deshacer",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Sí, borrar',
              cancelButtonText: 'Cancelar',
              allowOutsideClick: false // Evitar cerrar el modal haciendo clic fuera de él
            }).then((result) => {
              if (result.isConfirmed) {
                $("#calendar").fullCalendar("removeEvents", event._id);

                $.ajax({
                  type: "POST",
                  url: 'deleteEvento.php',
                  data: { id: event._id },
                  success: function () {
                    // Habilitar eventos de clic en otros elementos
                    $('body').removeClass('modal-open');

                    Swal.fire({
                      icon: 'success',
                      title: '¡Evento eliminado!',
                      text: 'El evento ha sido eliminado correctamente.',
                      showConfirmButton: false,
                      timer: 1500
                    }).then(() => {
                      // Redireccionar a page_turnos.php
                      window.location.href = 'page_turnos.php';
                    });
                  },
                  error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    // Habilitar eventos de clic en otros elementos
                    $('body').removeClass('modal-open');

                    Swal.fire({
                      icon: 'error',
                      title: '¡Error!',
                      text: 'Hubo un problema al eliminar el evento.'
                    });
                  }
                });
              } else {
                // Habilitar eventos de clic en otros elementos
                $('body').removeClass('modal-open');
              }
            });
          });
        }
      },


      // //Moviendo Evento Drag - Drop
      // eventDrop: function (event, delta) {
      //   var idEvento = event._id;
      //   var start = (event.start.format('DD-MM-YYYY'));
      //   var end = (event.end.format("DD-MM-YYYY"));

      //   $.ajax({
      //     url: 'drag_drop_evento.php',
      //     data: 'start=' + start + '&end=' + end + '&idEvento=' + idEvento,
      //     type: "POST",
      //     success: function (response) {
      //       // $("#respuesta").html(response);
      //     }
      //   });
      // },

      //Abrir ventana modal para visualizar datos del turno y productos que contiene 
      eventClick: function (event) {
        var idEvento = event._id;
        $('input[name=idEvento').val(idEvento);
        $('label[name=evento').text(event.title);
        $('label[name=fecha_inicio').text(event.start.format('HH:mm'));
        $('label[name=fecha_fin').text(event.end.format("HH:mm"));
        $('label[name=cancha').text(event.cancha);

        $('.colorModalUpdate').css('background-color', event.color);

        // $('span[name=total_cancha').text(event.total_cancha);
        // $('span[name=total_detalle').text(event.total_detalle);
        // $('span[name=total').text(event.total);

        var finalizado = parseInt(event.finalizado);
        // Verificar si el evento no está finalizado
        if (finalizado !== 1) {
          $('#agregarProductoModalUpdate').show();
          $('#finalizarTurno').show();

          // Enviar una solicitud AJAX para cargar los productos correspondientes al idEvento en la vista de detalle
          $.ajax({
            url: 'cargarProductos.php',
            type: 'POST',
            data: { idEvento: idEvento },
            success: function (response) {
              actualizarTotales();
              // Insertar los productos en la tabla dentro del modal
              $('#tablaProductosDetalle').html(response);
              // Abrir el modal
              $("#modalUpdateEvento").modal("show");
            },
            error: function (xhr, status, error) {
              console.error(xhr.responseText);
            }
          });

        } else {
          $('#agregarProductoModalUpdate').hide();
          $('#finalizarTurno').hide();


          // Enviar una solicitud AJAX para cargar los productos correspondientes al idEvento en la vista de detalle
          $.ajax({
            url: 'cargarProductos.php',
            type: 'POST',
            data: { idEvento: idEvento },
            success: function (response) {
              actualizarTotales();
              // Insertar los productos en la tabla dentro del modal
              $('#tablaProductosDetalle').html(response);
              $('.btnEliminarProducto').hide();
              // Abrir el modal
              $("#modalUpdateEvento").modal("show");
            },
            error: function (xhr, status, error) {
              console.error(xhr.responseText);
            }
          });

        }

      },
    });

    //Oculta mensajes de Notificacion
    setTimeout(function () {
      $(".alert").slideUp(300);
    }, 3000);
  });
</script>

<!-- Script para manejar la selección de cliente ------------------------------------------->
<script>
  // Función para manejar la selección de cliente
  $(document).on('click', '.seleccionar-cliente', function () {
    var cliente_id = $(this).data('id');
    var cliente_nombre = $(this).text();
    // Actualizar el valor del input del cliente
    $('#evento').val(cliente_nombre);
    // Opcional: Puedes guardar el ID del cliente en un campo oculto si lo necesitas
    $('#cliente_id').val(cliente_id);
    // Cerrar solo el modal de selección de clientes
    $('#clientesModal').modal('hide');
  });
</script>

<!-- Script para eliminar un producto del evento --------------------------------------------->
<script>
  function actualizarTablaProductos() {
    $("#tablaProductosDetalle").load("cargarProductos.php", { idEvento: $('#idEvento').val() }, function (response, status, xhr) {
      if (status == "error") {
        console.error(xhr.responseText);
      }
    });
  }
  function cargarTotal(tipoTotal) {
    $('span[name=' + tipoTotal + ']').load("cargarTotal.php", { idEvento: $('#idEvento').val(), tipoTotal: tipoTotal }, function (response, status, xhr) {
      if (status == "error") {
        console.error(xhr.responseText);
      }
    });
  }

  function actualizarTotales() {
    cargarTotal('total_cancha');
    cargarTotal('total_detalle');
    cargarTotal('total');
  }

  // Capturar el clic del botón "Eliminar"
  $(document).on("click", ".btnEliminarProducto", function () {
    // Obtener el idProducto desde el atributo data
    var idProducto = $(this).data("idproducto");
    var idEvento = $('#idEvento').val();

    // Realizar una solicitud AJAX para eliminar el producto
    $.ajax({
      url: 'deleteProductoEvento.php',
      method: 'POST',
      data: { idProducto: idProducto, idEvento: idEvento },
      success: function (response) {
        // Actualizar la tabla de productos si se eliminó correctamente
        actualizarTablaProductos();
        actualizarTotales();
      },
      error: function (xhr, status, error) {
        // Manejar errores si los hay
        console.error(xhr.responseText);
      }
    });
  });

</script>

<!-- script para al hacer click en una tarjeta y obtener datos del producto seleccionado ----------------->
<script>
  $(document).ready(function () {
    // Al hacer clic en una tarjeta
    $('.tarjeta').click(function () {
      // Quitar la clase 'selected' de todas las tarjetas
      $('.tarjeta').removeClass('selected');

      // Agregar la clase 'selected' a la tarjeta clicada
      $(this).addClass('selected');

      // Obtener los datos del producto seleccionado
      var nombre = $(this).data('nombre');
      var precio = $(this).data('precio');
      var idProducto = $(this).data('id');

      // Mostrar los detalles del producto en los cuadros de texto
      $('#nombreProducto').val(nombre);
      $('#precioProducto').val(precio);
      $('#idProducto').val(idProducto);
    });
  });
</script>

<!-- Script para manejar el evento de clic del botón "Agregar" en el modal de productos -->
<script>
  $(document).ready(function () {
    // Evento de clic del botón "Agregar" en el modal de productos
    $('#agregarProducto').click(function () {
      // Obtener los detalles del producto seleccionado
      var idEvento = $('#idEvento').val();
      var idProducto = $('#idProducto').val();
      var cantidad = $('#cantidadProducto').val();

      // Validar la cantidad
      if (cantidad <= 0) {
        alert('La cantidad debe ser mayor que cero.');
        return;
      }

      // Realizar la petición AJAX para insertar el detalle del ticket
      $.ajax({
        url: 'agregarProductoTicket.php', // URL del script PHP que insertará el detalle del ticket
        method: 'POST',
        data: {
          idEvento: idEvento,
          idProducto: idProducto,
          cantidad: cantidad
        },
        success: function (response) {
          var jsonResponse = JSON.parse(response);
          // Manejar la respuesta del servidor
          if (jsonResponse.success) {
            // Actualizar la tabla de productos en el modal
            actualizarTablaProductos();
            actualizarTotales();

            // Cerrar el modal de productos
            $('#productosModal').modal('hide');
          } else {
            // Error al agregar el producto al ticket
            alert('Error al agregar el producto al ticket: ' + response.message);
          }
        },
        error: function () {
          alert('Error de conexión con el servidor.');
        }
      });
    });
  });
</script>

<!-- script para reestablecer el modal de agregar productos ---------------------------->
<script>
  $(document).ready(function () {
    // Evento que se activa cuando se muestra el modal de productos
    $('#productosModal').on('show.bs.modal', function () {
      // Restablecer la selección de tarjetas
      $('.tarjeta').removeClass('selected');

      // Restablecer los valores de los campos
      $('#nombreProducto').val('');
      $('#precioProducto').val('');
      $('#cantidadProducto').val('1');
    });
  });
</script>

<!-- Script para actualizar productos en el modal de pago --------------------------------------------->
<script>
  $('#modalPago').on('show.bs.modal', function () {
    idEvento = $('input[name=idEvento').val();
    $.ajax({
      url: 'cargarProductos.php',
      type: 'POST',
      data: { idEvento: idEvento },
      success: function (response) {
        actualizarTotales();
        // Insertar los productos en la tabla dentro del modal
        $('#tablaProductosPago').html(response);
        $('.btnEliminarProducto').hide();
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
      }
    });
    actualizarTotales();
  });

</script>

<!-- Guardar detalle del pago por transferencia y efectivo -->
<script>
  $(document).ready(function () {
    $('#btnFinalizar').click(function () {
      // Obtener los valores de los campos
      var idEvento = $('#idEvento').val();
      var pagoEfectivo = $('#pagoEfectivo').val();
      var pagoTransf = $('#pagoTransf').val();

      // Realizar la solicitud AJAX
      $.ajax({
        url: 'finalizarTurno.php', // Ruta al script PHP
        type: 'POST',
        data: {
          idTurno: idEvento,
          pagoEfectivo: pagoEfectivo,
          pagoTransferencia: pagoTransf
        },
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            // Mostrar un mensaje de éxito utilizando SweetAlert2
            Swal.fire({
              icon: 'success',
              title: '¡Turno finalizado!',
              text: 'El turno se finalizó correctamente.',
              confirmButtonText: 'Aceptar'
            }).then((result) => {
              // Redirigir al usuario al page_turnos.php
              window.location.href = 'page_turnos.php';
            });
          } else {
            // Mostrar un mensaje de error utilizando SweetAlert2
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Ocurrió un error al finalizar el turno: ' + response.message,
              confirmButtonText: 'Aceptar'
            });
          }
        },
        error: function (xhr, status, error) {
          // Mostrar un mensaje de error en caso de problemas con la solicitud AJAX
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al procesar la solicitud: ' + error,
            confirmButtonText: 'Aceptar'
          });
        }
      });
    });
  });
</script>

<script>
  // Restablecer la visibilidad de todas las tarjetas al abrir el modal para cargar un producto a un turno
  $('#productosModal').on('shown.bs.modal', function () {
    $('.tarjeta').show();
  });
</script>

</body>

</html>