      </div><!-- /.container-fluid -->
    </section>
</div>
 <!-- /.content-wrapper -->
 <footer class="main-footer">
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.2.0
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<script src="../../validaciones/validacion.js"></script>
<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../../plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>

  
  $.widget.bridge('uibutton', $.ui.button);

  var sound = new Audio("../../dist/sound/barcode.wav");

  $(document).ready(function() {

    barcode.config.start = 0.1;
    barcode.config.end = 0.9;
    barcode.config.video = '#barcodevideo';
    barcode.config.canvas = '#barcodecanvas';
    barcode.config.canvasg = '#barcodecanvasg';
    barcode.setHandler(function(barcode) {
      $('#result').html(barcode);
    });
    barcode.init();

    $('#result').bind('DOMSubtreeModified', function(e) {
      sound.play();	
    });
});

function copiarContenido() {
            // Selecciona el contenido del div
            var contenido = document.getElementById('result').innerText;

            // Crea un elemento de texto temporal
            var elementoTemporal = document.createElement('textarea');
            elementoTemporal.value = contenido;

            // Añade el elemento temporal al documento
            document.body.appendChild(elementoTemporal);

            // Selecciona y copia el contenido del elemento temporal
            elementoTemporal.select();
            document.execCommand('copy');

            // Elimina el elemento temporal
            document.body.removeChild(elementoTemporal);

            alert('Contenido copiado!');
        }
</script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="../../plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="../../plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="../../plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="../../plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="../../plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="../../plugins/moment/moment.min.js"></script>
<script src="../../plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="../../plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="../../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../../dist/js/pages/dashboard.js"></script>
<!-- barcode -->
<script type="text/javascript" src="../../dist/js/barcode.js"></script>
<!-- DataTables  & Plugins -->
<script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../../plugins/jszip/jszip.min.js"></script>
<script src="../../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Select2 -->
<script src="../../plugins/select2/js/select2.full.min.js"></script>
<!-- dropzonejs -->
<script src="../../plugins/dropzone/min/dropzone.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<script>
  $(function () {
  //Initialize Select2 Elements
    $('.select2').select2()

  //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
  })

  function generarRandom(num) {
    const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    const charactersLength = characters.length;
    let result = "";
      for (let i = 0; i < num; i++) {
          result += characters.charAt(Math.floor(Math.random() * charactersLength));
      }

  return result;
}
  $("#generador_codigo_factura").val(generarRandom(14))  
</script>

<script>
    
    $(document).ready(function () {
        // Función para calcular el total y asignarlo al TD
        function actualizarTotal(fila) {
            var cantidad = fila.find('.cantidad-input').val();
            var precio = fila.find('td:eq(6)').text();
            var total = cantidad * precio;
            fila.find('.total-column').text(total);
            fila.find('.total-input').val(total);
            actualizarCampoTotalGlobal();
        }

        // Función para actualizar el campo total global
        function actualizarCampoTotalGlobal() {
            var totalGlobal = 0;

            // Suma todos los totales de las filas
            $('.total-column').each(function () {
                totalGlobal += parseFloat($(this).text()) || 0;
            });

            // Asigna el total global al campo de texto
            $('.campo-total-global').val(totalGlobal.toFixed(0));

            // Actualiza el campo de cambio
            actualizarCampoCambio();
        }

        // Escucha los cambios en los campos de cantidad
        $('.cantidad-input').on('input', function () {
            var fila = $(this).closest('tr');
            actualizarTotal(fila);
        });

        // Llama a la función al cargar la página para inicializar los totales
        $('.cantidad-input').each(function () {
            var fila = $(this).closest('tr');
            actualizarTotal(fila);
        });

        // Escucha los cambios en el campo "Recibido"
        $('#recibido').on('input', function () {
            actualizarCampoCambio();
        });

        // Llama a la función al cargar la página para inicializar el campo de cambio
        actualizarCampoCambio();
    });

    // Función para actualizar el campo de cambio
    function actualizarCampoCambio() {
        var totalCompra = parseFloat($('.campo-total-global').val()) || 0;
        var recibido = parseFloat($('#recibido').val()) || 0;

        // Calcula el cambio
        var cambio = recibido - totalCompra;

        // Actualiza el campo "Se devuelve"
        $('#se_devuelve').val(cambio.toFixed(0));
    }

  //  CONFIGURANDO TABLAS
  $(document).ready(function () {
    var table = $("#vBuscar").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false
    });
    table.buttons().container().appendTo('#vBuscar_wrapper .col-md-6:eq(0)');
});

  // MASCARAS DE DINERO
  document.addEventListener('DOMContentLoaded', function () {
        // Obtener los inputs de precio de compra y precio de venta
        var inputPrecioCompra = document.getElementById("producto_precio_compra");
        var inputPrecioVenta = document.getElementById("producto_precio_venta");

        // Escuchar el evento 'input' para actualizar el valor formateado para el precio de compra
        inputPrecioCompra.addEventListener("input", function(event) {
            // Obtener el valor actual del input
            var valor = event.target.value;

            // Remover cualquier caracter que no sea número
            valor = valor.replace(/[^\d]/g, '');

            // Añadir el signo de peso al inicio
            valor = "$" + valor;

            // Formatear el número con separador de miles
            valor = valor.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            // Asignar el valor formateado de vuelta al input
            event.target.value = valor;
        });

        // Escuchar el evento 'input' para actualizar el valor formateado para el precio de venta
        inputPrecioVenta.addEventListener("input", function(event) {
            // Obtener el valor actual del input
            var valor = event.target.value;

            // Remover cualquier caracter que no sea número
            valor = valor.replace(/[^\d]/g, '');

            // Añadir el signo de peso al inicio
            valor = "$" + valor;

            // Formatear el número con separador de miles
            valor = valor.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            // Asignar el valor formateado de vuelta al input
            event.target.value = valor;
        });

        // Prevenir el envío del formulario si el valor de alguno de los campos no es válido
        document.getElementById("formCaja").addEventListener("submit", function(event) {
            // Obtener el valor actual del input de precio de compra
            var valorCompra = inputPrecioCompra.value;

            // Obtener el valor actual del input de precio de venta
            var valorVenta = inputPrecioVenta.value;

            // Remover cualquier caracter que no sea número
            valorCompra = valorCompra.replace(/[^\d]/g, '');
            valorVenta = valorVenta.replace(/[^\d]/g, '');

            // Si alguno de los valores es vacío o no es un número válido, prevenir el envío del formulario
            if (valorCompra === '' || isNaN(parseInt(valorCompra)) || valorVenta === '' || isNaN(parseInt(valorVenta))) {
                event.preventDefault();
                alert("Ingrese un monto válido en precio de compra y precio de venta.");
            }
        });
    });
</script>
</body>
</html>