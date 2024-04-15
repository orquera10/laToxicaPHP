<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Mi Calendario:: Ing. Urian Viera</title>
  <link rel="stylesheet" type="text/css" href="css/fullcalendar.min.css">
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/home.css">
</head>

<body>

  <?php
  include ('config.php');

  $SqlEventos = ('SELECT turnos.*, clientes.NOMBRE as nombre_usuario, canchas.NOMBRE as nombre_cancha FROM turnos INNER JOIN clientes ON turnos.id_CLIENTE = clientes._id INNER JOIN canchas ON turnos.id_CANCHA = canchas._id');
  $resulEventos = mysqli_query($con, $SqlEventos);

  ?>

  <div class="mt-5"></div>

  <div class="container">
    <div class="row">
      <div class="col msjs">
        <?php
        include ('msjs.php');
        ?>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12 mb-3">
        <h3 class="text-center" id="title">Calendario de Eventos con PHP y MYSQL</h3>
      </div>
    </div>
  </div>

  <div id="calendar"></div>


  <?php
  include ('modalNuevoEvento.php');
  include ('modalUpdateEvento.php');
  ?>

  <script src="js/jquery-3.0.0.min.js"> </script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>

  <script type="text/javascript" src="js/moment.min.js"></script>
  <script type="text/javascript" src="js/fullcalendar.min.js"></script>
  <script src='locales/es.js'></script>

  <script type="text/javascript">
    $(document).ready(function () {
      $("#calendar").fullCalendar({
        header: {
          left: "prev,next today",
          center: "title",
          right: "month,agendaWeek,agendaDay"
        },

        locale: 'es',

        defaultView: "month",
        navLinks: true,
        // editable: true,
        eventLimit: true,
        selectable: true,
        selectHelper: false,

        //Nuevo Evento
        select: function (start, end) {
          $("#exampleModal").modal();

          // Construir fechas completas combinando las fechas seleccionadas del calendario con las horas seleccionadas
          var fechaInicio = start.format('DD-MM-YYYY');
          var fechaFin = end.format('DD-MM-YYYY');
          var fechaFinal = moment(fechaFin, "DD-MM-YYYY").subtract(1, 'days').format('DD-MM-YYYY');

          // Asignar valores a los campos de fecha y hora
          $("input[name=hidden_hora_inicio]").val(fechaInicio);
          $("input[name=hidden_hora_fin]").val(fechaFinal);
        },

        events: [
          <?php
          while ($dataEvento = mysqli_fetch_array($resulEventos)) { ?>
                                {
              _id: '<?php echo $dataEvento['_id']; ?>',
              title: '<?php echo $dataEvento['nombre_usuario']; ?>',
              start: '<?php echo $dataEvento['HORA_INICIO']; ?>',
              end: '<?php echo $dataEvento['HORA_FIN']; ?>',
              color: '<?php echo $dataEvento['COLOR']; ?>',
              cancha: '<?php echo $dataEvento['nombre_cancha']; ?>',
            },
          <?php } ?>
        ],


        //Eliminar Evento
        eventRender: function (event, element) {
          element
            .find(".fc-content")
            .prepend("<span id='btnCerrar'; class='closeon material-icons'>&#xe5cd;</span>");

          //Eliminar evento
          element.find(".closeon").on("click", function () {

            var pregunta = confirm("Deseas Borrar este Evento?");
            if (pregunta) {

              $("#calendar").fullCalendar("removeEvents", event._id);

              $.ajax({
                type: "POST",
                url: 'deleteEvento.php',
                data: { id: event._id },
                success: function () {
                  $(".alert-danger").show();

                  setTimeout(function () {
                    $(".alert-danger").slideUp(500);
                  }, 3000);

                }
              });
            }
          });
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

        //Modificar Evento del Calendario 
        eventClick: function (event) {
          var idEvento = event._id;
          $('input[name=idEvento').val(idEvento);
          $('label[name=evento').text(event.title);
          $('label[name=fecha_inicio').text(event.start.format('HH:mm'));
          $('label[name=fecha_fin').text(event.end.format("HH:mm"));
          $('label[name=cancha').text(event.cancha);

          // Enviar una solicitud AJAX para cargar los productos correspondientes al idEvento
          $.ajax({
            url: 'cargarProductosModalUpdate.php',
            type: 'POST',
            data: { idEvento: idEvento },
            success: function (response) {
              // Insertar los productos en la tabla dentro del modal
              $('#tablaProductos').html(response);
              // Abrir el modal
              $("#modalUpdateEvento").modal();
            },
            error: function (xhr, status, error) {
              console.error(xhr.responseText);
            }
          });
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
      $("#tablaProductos").load("contenidoModalUpdate.php", { idEvento: $('#idEvento').val() }, function (response, status, xhr) {
        if (status == "error") {
          console.error(xhr.responseText);
        }
      });
    }
    // Capturar el clic del botón "Eliminar"
    $(document).on("click", ".btnEliminarProducto", function () {
      // Obtener el idProducto desde el atributo data
      var idProducto = $(this).data("idproducto");

      // Realizar una solicitud AJAX para eliminar el producto
      $.ajax({
        url: 'deleteProductoEvento.php',
        method: 'POST',
        data: { idProducto: idProducto },
        success: function (response) {
          // Manejar la respuesta del servidor si es necesario
          console.log(response);
          // Actualizar la tabla de productos si se eliminó correctamente
          actualizarTablaProductos();
        },
        error: function (xhr, status, error) {
          // Manejar errores si los hay
          console.error(xhr.responseText);
        }
      });
    });

  </script>

</body>

</html>